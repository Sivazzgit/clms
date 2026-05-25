<?php
/**
 * CLMS 2.0 — Register of Adult Contract Workers
 */
Auth::requireRole('hr_admin');
$companyId = $_SESSION['company_id'];

$vendorId = (int)($_GET['vendor_id'] ?? 0);
$statusF  = Helpers::clean($_GET['status'] ?? 'active');

$where = ['e.company_id=?'];
$bind  = [$companyId];
if ($vendorId) { $where[] = 'e.vendor_id=?'; $bind[] = $vendorId; }
if ($statusF)  { $where[] = 'e.status=?';    $bind[] = $statusF; }
$whereStr = implode(' AND ', $where);

$employees = DB::rows(
    "SELECT e.employee_code, e.first_name, e.last_name, e.dob, e.gender,
            e.aadhaar_no, e.pf_uan, e.esi_no, e.doj_vendor,
            v.name AS vendor_name, lc.name AS category_name, e.status
     FROM employees e
     JOIN vendors v            ON v.id=e.vendor_id
     LEFT JOIN labour_categories lc ON lc.id=e.category_id
     WHERE $whereStr ORDER BY e.first_name, e.last_name",
    $bind
);

$vendors = DB::rows("SELECT id,name FROM vendors WHERE company_id=? AND status='active' ORDER BY name", [$companyId]);

ob_start();
?>
<div class="page-header">
  <div><h1 class="page-title">Register of Adult Workers</h1></div>
  <div class="page-actions">
    <button onclick="window.print()" class="btn btn-secondary">Print</button>
    <a href="<?= APP_BASE ?>/reports" class="btn btn-ghost">← Reports</a>
  </div>
</div>

<div class="card">
  <div class="table-toolbar">
    <form method="GET" action="<?= APP_BASE ?>/reports/adult-workers" style="display:flex;gap:var(--space-3);flex-wrap:wrap">
      <select name="vendor_id" class="form-control" style="width:auto">
        <option value="">All Vendors</option>
        <?php foreach ($vendors as $v): ?><option value="<?= $v['id'] ?>" <?= $vendorId==$v['id']?'selected':'' ?>><?= Helpers::h($v['name']) ?></option><?php endforeach; ?>
      </select>
      <select name="status" class="form-control" style="width:auto">
        <option value="">All Status</option>
        <option value="active"      <?= $statusF==='active'?'selected':'' ?>>Active</option>
        <option value="inactive"    <?= $statusF==='inactive'?'selected':'' ?>>Inactive</option>
        <option value="separated"   <?= $statusF==='separated'?'selected':'' ?>>Separated</option>
      </select>
      <button type="submit" class="btn btn-secondary">Apply</button>
    </form>
  </div>
  <div class="table-wrapper" style="overflow-x:auto">
    <table class="data-table">
      <thead>
        <tr><th>#</th><th>Code</th><th>Name</th><th>DOB</th><th>Gender</th><th>Vendor</th><th>Category</th><th>DoJ</th><th>Aadhaar</th><th>PF UAN</th><th>ESI No.</th></tr>
      </thead>
      <tbody>
        <?php foreach ($employees as $i => $e): ?>
        <tr>
          <td><?= $i+1 ?></td>
          <td><?= Helpers::h($e['employee_code']) ?></td>
          <td><?= Helpers::h($e['first_name'].' '.$e['last_name']) ?></td>
          <td><?= $e['dob'] ? Helpers::dateDisplay($e['dob']) : '—' ?></td>
          <td><?= ucfirst($e['gender'] ?? '—') ?></td>
          <td><?= Helpers::h($e['vendor_name']) ?></td>
          <td><?= Helpers::h($e['category_name'] ?? '—') ?></td>
          <td><?= $e['doj_vendor'] ? Helpers::dateDisplay($e['doj_vendor']) : '—' ?></td>
          <td><?= Helpers::h($e['aadhaar_no'] ?? '—') ?></td>
          <td><?= Helpers::h($e['pf_uan'] ?? '—') ?></td>
          <td><?= Helpers::h($e['esi_no'] ?? '—') ?></td>
        </tr>
        <?php endforeach; ?>
        <?php if (!$employees): ?><tr><td colspan="11" style="text-align:center;color:var(--clr-text-muted)">No employees found.</td></tr><?php endif; ?>
      </tbody>
    </table>
    <div style="padding:var(--space-3);color:var(--clr-text-muted);font-size:var(--text-sm)">Total: <?= count($employees) ?></div>
  </div>
</div>

<?php
$pageContent = ob_get_clean();
$pageTitle   = 'Adult Workers Register';
$activeMenu  = 'reports';
$breadcrumbs = [['label'=>'Reports','url'=>'/reports'],['label'=>'Adult Workers']];
include CLMS_ROOT . '/templates/base.html.php';
