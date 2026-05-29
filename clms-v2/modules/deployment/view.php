<?php
/**
 * CLMS 2.0 — Deployment Plan Detail View
 */
Auth::requireAuth();
$companyId = $_SESSION['company_id'];
$user      = Auth::user();

$planId = (int)($_GET['id'] ?? 0);
$plan   = DB::row(
    "SELECT dp.*, v.name AS vendor_name, s.name AS section_name, sh.code AS shift_code,
            i.indent_no, u.full_name AS submitted_by_name
     FROM deployment_plans dp
     JOIN vendors  v  ON v.id=dp.vendor_id
     JOIN sections s  ON s.id=dp.section_id
     JOIN shifts   sh ON sh.id=dp.shift_id
     JOIN indents  i  ON i.id=dp.indent_id
     JOIN users    u  ON u.id=dp.submitted_by
     WHERE dp.id=? AND dp.company_id=?",
    [$planId,$companyId]
);
if (!$plan) Helpers::redirect('/deployment');

// Restrict contractor to own plans
if (Auth::hasRole('contractor') && !Auth::hasRole('hr_admin') && $plan['vendor_id'] != $user['vendor_id']) {
    Helpers::redirect('/deployment');
}

$employees = DB::rows(
    "SELECT e.employee_code, e.first_name, e.last_name FROM deployment_plan_employees dpe
     JOIN employees e ON e.id=dpe.employee_id WHERE dpe.plan_id=?",
    [$planId]
);

$approvals = DB::rows(
    "SELECT da.*, u.full_name AS actor_name FROM deployment_approvals da
     JOIN users u ON u.id=da.actor_id WHERE da.plan_id=? ORDER BY da.acted_at ASC",
    [$planId]
);

$flash = $_SESSION['flash'] ?? null; unset($_SESSION['flash']);
ob_start();
?>
<?php if ($flash): ?>
  <div class="alert alert-<?= $flash['type']==='success'?'success':'danger' ?>" style="margin-bottom:var(--space-4)"><?= Helpers::h($flash['msg']) ?></div>
<?php endif; ?>

<div class="page-header">
  <div>
    <h1 class="page-title">Deployment Plan — <?= Helpers::h($plan['indent_no']) ?></h1>
    <span class="badge badge-pending"><?= ucwords(str_replace('_',' ',$plan['status'])) ?></span>
  </div>
  <div class="page-actions">
    <?php if ($plan['status']==='submitted' && Auth::hasRole('contractor')): ?>
      <a href="<?= APP_BASE ?>/deployment/<?= $planId ?>/edit" class="btn btn-secondary">Edit</a>
    <?php endif; ?>
    <a href="<?= APP_BASE ?>/deployment" class="btn btn-ghost">Back</a>
  </div>
</div>

<div class="form-grid-2" style="margin-bottom:var(--space-4)">
  <div class="card">
    <div class="card-header"><h3 class="card-title">Plan Info</h3></div>
    <div class="card-body">
      <dl class="detail-list">
        <dt>Indent</dt>    <dd><?= Helpers::h($plan['indent_no']) ?></dd>
        <dt>Vendor</dt>    <dd><?= Helpers::h($plan['vendor_name']) ?></dd>
        <dt>Section</dt>   <dd><?= Helpers::h($plan['section_name']) ?></dd>
        <dt>Shift</dt>     <dd><?= Helpers::h($plan['shift_code']) ?></dd>
        <dt>Plan Date</dt> <dd><?= Helpers::dateDisplay($plan['plan_date']) ?></dd>
        <dt>Submitted By</dt><dd><?= Helpers::h($plan['submitted_by_name']) ?></dd>
        <dt>Status</dt>    <dd><?= ucwords(str_replace('_',' ',$plan['status'])) ?></dd>
      </dl>
    </div>
  </div>

  <div class="card">
    <div class="card-header"><h3 class="card-title">Approval History</h3></div>
    <div class="table-wrapper">
      <table class="data-table">
        <thead><tr><th>Step</th><th>Action</th><th>By</th><th>Time</th><th>Remarks</th></tr></thead>
        <tbody>
          <?php foreach ($approvals as $a): ?>
          <tr>
            <td><?= Helpers::h($a['step']) ?></td>
            <td><?= ucwords($a['action']) ?></td>
            <td><?= Helpers::h($a['actor_name']) ?></td>
            <td><?= date('d-M-y H:i', strtotime($a['acted_at'])) ?></td>
            <td><?= Helpers::h($a['remarks'] ?? '—') ?></td>
          </tr>
          <?php endforeach; ?>
          <?php if (!$approvals): ?><tr><td colspan="5" style="color:var(--clr-text-muted)">No approvals yet.</td></tr><?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- Employees -->
<div class="card" style="margin-bottom:var(--space-4)">
  <div class="card-header"><h3 class="card-title">Employees (<?= count($employees) ?>)</h3></div>
  <div class="table-wrapper">
    <table class="data-table">
      <thead><tr><th>#</th><th>Code</th><th>Name</th></tr></thead>
      <tbody>
        <?php foreach ($employees as $i => $e): ?>
        <tr>
          <td><?= $i+1 ?></td>
          <td><?= Helpers::h($e['employee_code']) ?></td>
          <td><?= Helpers::h($e['first_name'].' '.$e['last_name']) ?></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<!-- Action Buttons -->
<?php if ($plan['status']==='submitted' && Auth::hasRole('section_incharge')): ?>
<div class="card">
  <div class="card-body">
    <form method="POST" action="<?= APP_BASE ?>/deployment/action">
      <input type="hidden" name="csrf_token" value="<?= Helpers::h($_SESSION['csrf_token']) ?>">
      <input type="hidden" name="plan_id" value="<?= $planId ?>">
      <div class="form-group" style="max-width:400px">
        <label class="form-label">Remarks</label>
        <textarea name="remarks" class="form-control" rows="2"></textarea>
      </div>
      <div style="display:flex;gap:var(--space-3)">
        <button type="submit" name="action" value="ic_review"  class="btn btn-success">IC Review Done</button>
        <button type="submit" name="action" value="reject"     class="btn btn-danger"
                onclick="return confirm('Reject this plan?')">Reject</button>
      </div>
    </form>
  </div>
</div>
<?php endif; ?>

<?php if ($plan['status']==='ic_reviewed' && Auth::hasRole('hod')): ?>
<div class="card">
  <div class="card-body">
    <form method="POST" action="<?= APP_BASE ?>/deployment/action">
      <input type="hidden" name="csrf_token" value="<?= Helpers::h($_SESSION['csrf_token']) ?>">
      <input type="hidden" name="plan_id" value="<?= $planId ?>">
      <div class="form-group" style="max-width:400px">
        <label class="form-label">Remarks</label>
        <textarea name="remarks" class="form-control" rows="2"></textarea>
      </div>
      <div style="display:flex;gap:var(--space-3)">
        <button type="submit" name="action" value="hod_accept" class="btn btn-success">Accept</button>
        <button type="submit" name="action" value="reject"     class="btn btn-danger"
                onclick="return confirm('Reject?')">Reject</button>
      </div>
    </form>
  </div>
</div>
<?php endif; ?>

<?php if ($plan['status']==='hod_accepted' && Auth::hasRole('plant_head')): ?>
<div class="card">
  <div class="card-body">
    <form method="POST" action="<?= APP_BASE ?>/deployment/action">
      <input type="hidden" name="csrf_token" value="<?= Helpers::h($_SESSION['csrf_token']) ?>">
      <input type="hidden" name="plan_id" value="<?= $planId ?>">
      <div class="form-group" style="max-width:400px">
        <label class="form-label">Remarks</label>
        <textarea name="remarks" class="form-control" rows="2"></textarea>
      </div>
      <div style="display:flex;gap:var(--space-3)">
        <button type="submit" name="action" value="plant_approve" class="btn btn-success">Final Approve</button>
        <button type="submit" name="action" value="reject"        class="btn btn-danger"
                onclick="return confirm('Reject?')">Reject</button>
      </div>
    </form>
  </div>
</div>
<?php endif; ?>

<?php
$pageContent = ob_get_clean();
$pageTitle   = 'Deployment Plan';
$activeMenu  = 'deployment';
$breadcrumbs = [['label'=>'Deployment','url'=>'/deployment'],['label'=>'Plan Detail']];
include CLMS_ROOT . '/templates/base.html.php';
