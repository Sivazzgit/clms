<?php
/**
 * CLMS 2.0 — Indent View
 */
Auth::requireAuth();
$companyId = $_SESSION['company_id'];
$id = (int)($_GET['id'] ?? 0);

$indent = DB::row(
    "SELECT i.*, s.name AS section_name, u.full_name AS created_by_name
     FROM indents i
     JOIN sections s ON s.id = i.section_id
     JOIN users u    ON u.id = i.created_by
     WHERE i.id=? AND i.company_id=?",
    [$id, $companyId]
);
if (!$indent) { http_response_code(404); include CLMS_ROOT.'/modules/errors/404.php'; exit; }

$lines = DB::rows(
    "SELECT il.*, sh.name AS shift_name, sh.code AS shift_code, lc.name AS category_name, lc.code AS category_code,
            v.name AS preferred_vendor
     FROM indent_lines il
     JOIN shifts sh ON sh.id = il.shift_id
     JOIN labour_categories lc ON lc.id = il.category_id
     LEFT JOIN vendors v ON v.id = il.preferred_vendor_id
     WHERE il.indent_id=?",
    [$id]
);

$approvals = DB::rows(
    "SELECT ia.*, u.full_name AS actor_name
     FROM indent_approvals ia
     JOIN users u ON u.id = ia.actor_id
     WHERE ia.indent_id=? ORDER BY ia.step, ia.acted_at",
    [$id]
);

$assignments = DB::rows(
    "SELECT iva.*, v.name AS vendor_name, lc.name AS category_name, sh.code AS shift_code
     FROM indent_vendor_assignments iva
     JOIN vendors v ON v.id = iva.vendor_id
     JOIN indent_lines il ON il.id = iva.indent_line_id
     JOIN labour_categories lc ON lc.id = il.category_id
     JOIN shifts sh ON sh.id = il.shift_id
     WHERE iva.indent_id=?",
    [$id]
);

$flash = $_SESSION['flash'] ?? null; unset($_SESSION['flash']);

ob_start();
?>
<?php if ($flash): ?>
  <div class="alert alert-<?= $flash['type'] === 'success' ? 'success' : 'danger' ?>" style="margin-bottom:var(--space-4)"><?= Helpers::h($flash['msg']) ?></div>
<?php endif; ?>

<div class="page-header">
  <div>
    <h1 class="page-title"><?= Helpers::h($indent['indent_no']) ?></h1>
    <p class="page-sub" style="color:var(--clr-text-muted);margin:0;">
      <?= Helpers::h($indent['section_name']) ?> &bull;
      <?= Helpers::dateDisplay($indent['start_date']) ?> – <?= Helpers::dateDisplay($indent['end_date']) ?>
    </p>
  </div>
  <div class="page-actions">
    <?php if (in_array($indent['status'],['draft','submitted']) && (Auth::hasRole('section_incharge')||Auth::hasRole('hr_admin'))): ?>
      <a href="/indent/<?= $id ?>/edit" class="btn btn-secondary">Edit</a>
    <?php endif; ?>
    <?php if ($indent['status'] === 'draft' && (Auth::hasRole('section_incharge')||Auth::hasRole('hr_admin'))): ?>
      <form method="POST" action="/indent/action" style="display:inline">
        <input type="hidden" name="csrf_token" value="<?= Helpers::h($_SESSION['csrf_token']) ?>">
        <input type="hidden" name="indent_id" value="<?= $id ?>">
        <input type="hidden" name="action"    value="submit">
        <button type="submit" class="btn btn-primary">Submit for Approval</button>
      </form>
    <?php endif; ?>
  </div>
</div>

<!-- Status Badge -->
<div style="margin-bottom:var(--space-4)">
  <?php
  $stClass = match($indent['status']) {
    'rejected','cancelled' => 'badge-rejected',
    'contractor_confirmed' => 'badge-active',
    default => 'badge-pending',
  };
  ?>
  <span class="badge <?= $stClass ?>" style="font-size:1rem;padding:.4rem .8rem">
    <?= ucwords(str_replace('_',' ', $indent['status'])) ?>
  </span>
  <?php if ($indent['is_urgent']): ?>
    <span class="badge badge-rejected" style="margin-left:var(--space-2)">Urgent</span>
  <?php endif; ?>
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:var(--space-4);margin-bottom:var(--space-4)">
  <div class="card">
    <div class="card-header"><h3 class="card-title">Details</h3></div>
    <div class="card-body">
      <table style="width:100%;border-collapse:collapse;font-size:var(--text-sm)">
        <tr><th style="padding:4px 8px;text-align:left;color:var(--clr-text-muted)">Section</th><td><?= Helpers::h($indent['section_name']) ?></td></tr>
        <tr><th style="padding:4px 8px;text-align:left;color:var(--clr-text-muted)">Period</th><td><?= Helpers::dateDisplay($indent['start_date']) ?> – <?= Helpers::dateDisplay($indent['end_date']) ?></td></tr>
        <tr><th style="padding:4px 8px;text-align:left;color:var(--clr-text-muted)">Type</th><td><?= ucfirst($indent['indent_type'] ?? 'range') ?></td></tr>
        <tr><th style="padding:4px 8px;text-align:left;color:var(--clr-text-muted)">Nature of Work</th><td><?= Helpers::h($indent['nature_of_work'] ?? '—') ?></td></tr>
        <tr><th style="padding:4px 8px;text-align:left;color:var(--clr-text-muted)">Remarks</th><td><?= Helpers::h($indent['remarks'] ?? '—') ?></td></tr>
        <tr><th style="padding:4px 8px;text-align:left;color:var(--clr-text-muted)">Created By</th><td><?= Helpers::h($indent['created_by_name']) ?></td></tr>
        <tr><th style="padding:4px 8px;text-align:left;color:var(--clr-text-muted)">Created At</th><td><?= Helpers::dateDisplay($indent['created_at']) ?></td></tr>
      </table>
    </div>
  </div>

  <!-- Approval Flow -->
  <div class="card">
    <div class="card-header"><h3 class="card-title">Approval History</h3></div>
    <div class="card-body">
      <?php if ($approvals): ?>
        <?php foreach ($approvals as $ap): ?>
        <div style="padding:var(--space-2) 0;border-bottom:var(--border-base)">
          <div style="display:flex;justify-content:space-between">
            <strong><?= ucwords(str_replace('_',' ',$ap['action'])) ?></strong>
            <span style="font-size:var(--text-xs);color:var(--clr-text-muted)"><?= Helpers::dateDisplay($ap['acted_at']) ?></span>
          </div>
          <div style="font-size:var(--text-sm);color:var(--clr-text-muted)"><?= Helpers::h($ap['actor_name']) ?></div>
          <?php if ($ap['remarks']): ?>
            <div style="font-size:var(--text-sm);font-style:italic"><?= Helpers::h($ap['remarks']) ?></div>
          <?php endif; ?>
        </div>
        <?php endforeach; ?>
      <?php else: ?>
        <p style="color:var(--clr-text-muted)">No approval actions yet.</p>
      <?php endif; ?>
    </div>
  </div>
</div>

<!-- Lines -->
<div class="card" style="margin-bottom:var(--space-4)">
  <div class="card-header"><h3 class="card-title">Manpower Requirements</h3></div>
  <div class="table-wrapper">
    <table class="data-table">
      <thead><tr><th>Shift</th><th>Category</th><th>Required</th><th>Preferred Vendor</th></tr></thead>
      <tbody>
        <?php foreach ($lines as $ln): ?>
        <tr>
          <td><?= Helpers::h($ln['shift_code'].' — '.$ln['shift_name']) ?></td>
          <td><?= Helpers::h($ln['category_code'].' — '.$ln['category_name']) ?></td>
          <td><?= $ln['required_count'] ?></td>
          <td><?= Helpers::h($ln['preferred_vendor'] ?? '—') ?></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<?php if ($assignments): ?>
<div class="card">
  <div class="card-header"><h3 class="card-title">Vendor Assignments</h3></div>
  <div class="table-wrapper">
    <table class="data-table">
      <thead><tr><th>Vendor</th><th>Shift</th><th>Category</th><th>Assigned</th><th>Confirmed</th><th>Status</th></tr></thead>
      <tbody>
        <?php foreach ($assignments as $asgn): ?>
        <tr>
          <td><?= Helpers::h($asgn['vendor_name']) ?></td>
          <td><?= Helpers::h($asgn['shift_code']) ?></td>
          <td><?= Helpers::h($asgn['category_name']) ?></td>
          <td><?= $asgn['assigned_count'] ?></td>
          <td><?= $asgn['confirmed_count'] ?? '—' ?></td>
          <td><span class="badge badge-pending"><?= ucfirst($asgn['status']) ?></span></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>
<?php endif; ?>

<?php
$pageContent = ob_get_clean();
$pageTitle   = $indent['indent_no'];
$activeMenu  = 'indent';
$breadcrumbs = [['label' => 'Indents', 'url' => '/indent'], ['label' => $indent['indent_no']]];
include CLMS_ROOT . '/templates/base.html.php';
