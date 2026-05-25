<?php
/**
 * CLMS 2.0 — Register of Adult Contract Workers
 */
Auth::requireRole('hr_admin');
$companyId = $_SESSION['company_id'];

$vendorId  = (int)($_GET['vendor_id']  ?? 0);
$sectionId = (int)($_GET['section_id'] ?? 0);
$statusF   = Helpers::clean($_GET['status'] ?? 'active');

$where = ['e.company_id=?'];
$bind  = [$companyId];
if ($vendorId)  { $where[] = 'e.vendor_id=?';  $bind[] = $vendorId; }
if ($sectionId) { $where[] = 'e.section_id=?'; $bind[] = $sectionId; }
if ($statusF)   { $where[] = 'e.status=?';     $bind[] = $statusF; }
$whereStr = implode(' AND ', $where);

$employees = DB::rows(
    "SELECT e.employee_code, e.first_name, e.last_name, e.date_of_birth, e.gender,
            e.aadhar_no, e.pf_account_no, e.esi_no, e.date_of_joining,
            v.name AS vendor_name, s.name AS section_name, lc.name AS category_name, e.status
     FROM employees e
     JOIN vendors v         ON v.id=e.vendor_id
     JOIN sections s        ON s.id=e.section_id
     JOIN labour_categories lc ON lc.id=e.category_id
     WHERE $whereStr ORDER BY e.first_name, e.last_name",
    $bind
);

$vendors  = DB::rows("SELECT id,name FROM vendors WHERE company_id=? AND status='active' ORDER BY name", [$companyId]);
$sections = DB::rows("SELECT id,name FROM sections WHERE company_id=? AND is_active=1 ORDER BY name", [$companyId]);

ob_start();
?>
<div class="page-header">
  <div><h1 class="page-title">Register of Adult Workers</h1></div>
  <div class="page-actions">
    <button onclick="window.print()" class="btn btn-secondary">Print</button>
    <a href="/reports" class="btn btn-ghost">← Reports</a>
  </div>
</div>

<div class="card">
  <div class="table-toolbar">
    <form method="GET" action="/reports/adult-workers" style="display:flex;gap:var(--space-3);flex-wrap:wrap">
      <select name="vendor_id" class="form-control" style="width:auto">
        <option value="">All Vendors</option>
        <?php foreach ($vendors as $v): ?><option value="<?= $v['id'] ?>" <?= $vendorId==$v['id']?'selected':'' ?>><?= Helpers::h($v['name']) ?></option><?php endforeach; ?>
      </select>
      <select name="section_id" class="form-control" style="width:auto">
        <option value="">All Sections</option>
        <?php foreach ($sections as $s): ?><option value="<?= $s['id'] ?>" <?= $sectionId==$s['id']?'selected':'' ?>><?= Helpers::h($s['name']) ?></option><?php endforeach; ?>
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
        <tr><th>#</th><th>Code</th><th>Name</th><th>DOB</th><th>Gender</th><th>Vendor</th><th>Section</th><th>Category</th><th>DoJ</th><th>Aadhaar</th><th>PF No.</th><th>ESI No.</th></tr>
      </thead>
      <tbody>
        <?php foreach ($employees as $i => $e): ?>
        <tr>
          <td><?= $i+1 ?></td>
          <td><?= Helpers::h($e['employee_code']) ?></td>
          <td><?= Helpers::h($e['first_name'].' '.$e['last_name']) ?></td>
          <td><?= $e['date_of_birth'] ? Helpers::dateDisplay($e['date_of_birth']) : '—' ?></td>
          <td><?= ucfirst($e['gender'] ?? '—') ?></td>
          <td><?= Helpers::h($e['vendor_name']) ?></td>
          <td><?= Helpers::h($e['section_name']) ?></td>
          <td><?= Helpers::h($e['category_name']) ?></td>
          <td><?= $e['date_of_joining'] ? Helpers::dateDisplay($e['date_of_joining']) : '—' ?></td>
          <td><?= Helpers::h($e['aadhar_no'] ?? '—') ?></td>
          <td><?= Helpers::h($e['pf_account_no'] ?? '—') ?></td>
          <td><?= Helpers::h($e['esi_no'] ?? '—') ?></td>
        </tr>
        <?php endforeach; ?>
        <?php if (!$employees): ?><tr><td colspan="12" style="text-align:center;color:var(--clr-text-muted)">No employees found.</td></tr><?php endif; ?>
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
