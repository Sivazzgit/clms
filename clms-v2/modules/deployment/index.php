<?php
/**
 * CLMS 2.0 — Deployment Plans List
 */
Auth::requireAuth();
$companyId = $_SESSION['company_id'];
$user      = Auth::user();

if (Auth::hasRole('gate_staff')) Helpers::redirect('/dashboard');

$filterStatus = Helpers::clean($_GET['status'] ?? '');
$where = ['dp.company_id=?'];
$bind  = [$companyId];

if (Auth::hasRole('contractor') && !Auth::hasRole('hr_admin')) {
    $where[] = 'dp.vendor_id=?';
    $bind[]  = $user['vendor_id'];
}
if ($filterStatus) { $where[] = 'dp.status=?'; $bind[] = $filterStatus; }

$whereStr = implode(' AND ', $where);
$total    = (int) DB::value("SELECT COUNT(*) FROM deployment_plans dp WHERE $whereStr", $bind);
$pager    = Helpers::paginate($total);

$plans = DB::rows(
    "SELECT dp.*, v.name AS vendor_name, s.name AS section_name, sh.code AS shift_code, i.indent_no,
            u.full_name AS submitted_by_name,
            (SELECT COUNT(*) FROM deployment_plan_employees dpe WHERE dpe.plan_id=dp.id) AS emp_count
     FROM deployment_plans dp
     JOIN vendors v   ON v.id=dp.vendor_id
     JOIN sections s  ON s.id=dp.section_id
     JOIN shifts sh   ON sh.id=dp.shift_id
     JOIN indents i   ON i.id=dp.indent_id
     JOIN users u     ON u.id=dp.submitted_by
     WHERE $whereStr
     ORDER BY dp.plan_date DESC, dp.submitted_at DESC
     LIMIT ? OFFSET ?",
    [...$bind, $pager['perPage'], $pager['offset']]
);

$statuses = ['submitted','ic_reviewed','hod_accepted','plant_approved','rejected'];
ob_start();
?>
<div class="page-header">
  <div>
    <h1 class="page-title">Deployment Plans</h1>
    <p class="page-sub" style="color:var(--clr-text-muted);margin:0;"><?= number_format($total) ?> plan(s)</p>
  </div>
  <?php if (Auth::hasRole('contractor')): ?>
  <div class="page-actions">
    <a href="/deployment/create" class="btn btn-primary">New Deployment Plan</a>
  </div>
  <?php endif; ?>
</div>

<div class="card">
  <div class="table-toolbar">
    <form method="GET" action="/deployment" style="display:flex;gap:var(--space-3)">
      <select name="status" class="form-control" style="width:auto" onchange="this.form.submit()">
        <option value="">All Status</option>
        <?php foreach ($statuses as $st): ?>
          <option value="<?= $st ?>" <?= $filterStatus===$st?'selected':'' ?>><?= ucwords(str_replace('_',' ',$st)) ?></option>
        <?php endforeach; ?>
      </select>
      <?php if ($filterStatus): ?><a href="/deployment" class="btn btn-ghost">Clear</a><?php endif; ?>
    </form>
  </div>
  <div class="table-wrapper">
    <table class="data-table">
      <thead>
        <tr><th>Indent</th><th>Vendor</th><th>Section</th><th>Shift</th><th>Date</th><th>Employees</th><th>Status</th><th>Actions</th></tr>
      </thead>
      <tbody>
        <?php foreach ($plans as $p): ?>
        <tr>
          <td><?= Helpers::h($p['indent_no']) ?></td>
          <td><?= Helpers::h($p['vendor_name']) ?></td>
          <td><?= Helpers::h($p['section_name']) ?></td>
          <td><?= Helpers::h($p['shift_code']) ?></td>
          <td><?= Helpers::dateDisplay($p['plan_date']) ?></td>
          <td><?= $p['emp_count'] ?></td>
          <td><span class="badge badge-pending"><?= ucwords(str_replace('_',' ',$p['status'])) ?></span></td>
          <td>
            <a href="/deployment/<?= $p['id'] ?>/view" class="btn btn-secondary btn-sm">View</a>
          </td>
        </tr>
        <?php endforeach; ?>
        <?php if (!$plans): ?>
          <tr><td colspan="8" style="text-align:center;color:var(--clr-text-muted)">No deployment plans found.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?php
$pageContent = ob_get_clean();
$pageTitle   = 'Deployment Plans';
$activeMenu  = 'deployment';
$breadcrumbs = [['label'=>'Deployment']];
include CLMS_ROOT . '/templates/base.html.php';
