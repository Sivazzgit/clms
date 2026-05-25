<?php
/**
 * CLMS 2.0 — Form 12-A (Half-yearly return of workmen employed)
 * Shows unique workers employed in the half-year period.
 */
Auth::requireRole('hr_admin');
$companyId = $_SESSION['company_id'];

$year     = (int)($_GET['year']   ?? date('Y'));
$half     = (int)($_GET['half']   ?? (date('n') <= 6 ? 1 : 2));
$vendorId = (int)($_GET['vendor_id'] ?? 0);

$fromDate = $half === 1 ? "$year-01-01" : "$year-07-01";
$toDate   = $half === 1 ? "$year-06-30" : "$year-12-31";

$where = ['a.company_id=?','a.attendance_date BETWEEN ? AND ?','a.is_present=1'];
$bind  = [$companyId,$fromDate,$toDate];
if ($vendorId) { $where[] = 'a.vendor_id=?'; $bind[] = $vendorId; }
$whereStr = implode(' AND ', $where);

$workers = DB::rows(
    "SELECT e.employee_code, e.first_name, e.last_name, e.dob, e.gender,
            v.name AS vendor_name, lc.name AS category_name,
            COUNT(DISTINCT a.attendance_date) AS days_worked,
            MIN(a.attendance_date) AS first_day, MAX(a.attendance_date) AS last_day
     FROM attendance a
     JOIN employees e       ON e.id=a.employee_id
     JOIN vendors v         ON v.id=a.vendor_id
     LEFT JOIN labour_categories lc ON lc.id=e.category_id
     WHERE $whereStr
     GROUP BY e.id, e.employee_code, e.first_name, e.last_name, e.dob, e.gender, v.name, lc.name
     ORDER BY v.name, e.first_name",
    $bind
);

$vendors = DB::rows("SELECT id,name FROM vendors WHERE company_id=? AND status='active' ORDER BY name", [$companyId]);
$company = DB::row("SELECT name, address, gstin FROM companies WHERE id=?", [$companyId]);

ob_start();
?>
<div class="page-header">
  <div>
    <h1 class="page-title">Form 12-A</h1>
    <p style="color:var(--clr-text-muted);margin:0">Half-yearly return — <?= $fromDate ?> to <?= $toDate ?></p>
  </div>
  <div class="page-actions">
    <button onclick="window.print()" class="btn btn-secondary">Print</button>
    <a href="<?= APP_BASE ?>/reports" class="btn btn-ghost">← Reports</a>
  </div>
</div>

<div class="card">
  <div class="table-toolbar">
    <form method="GET" action="<?= APP_BASE ?>/reports/form12a" style="display:flex;gap:var(--space-3)">
      <input type="number" name="year" class="form-control" value="<?= $year ?>" min="2000" max="2100" style="width:80px">
      <select name="half" class="form-control" style="width:auto">
        <option value="1" <?= $half==1?'selected':'' ?>>Jan–Jun</option>
        <option value="2" <?= $half==2?'selected':'' ?>>Jul–Dec</option>
      </select>
      <select name="vendor_id" class="form-control" style="width:auto">
        <option value="">All Vendors</option>
        <?php foreach ($vendors as $v): ?><option value="<?= $v['id'] ?>" <?= $vendorId==$v['id']?'selected':'' ?>><?= Helpers::h($v['name']) ?></option><?php endforeach; ?>
      </select>
      <button type="submit" class="btn btn-secondary">Apply</button>
    </form>
  </div>
  <div class="table-wrapper">
    <table class="data-table">
      <thead>
        <tr><th>#</th><th>Code</th><th>Name</th><th>DOB</th><th>Gender</th><th>Category</th><th>Contractor</th><th>Days Worked</th><th>From</th><th>To</th></tr>
      </thead>
      <tbody>
        <?php foreach ($workers as $i => $w): ?>
        <tr>
          <td><?= $i+1 ?></td>
          <td><?= Helpers::h($w['employee_code']) ?></td>
          <td><?= Helpers::h($w['first_name'].' '.$w['last_name']) ?></td>
          <td><?= $w['dob'] ? Helpers::dateDisplay($w['dob']) : '—' ?></td>
          <td><?= ucfirst($w['gender'] ?? '—') ?></td>
          <td><?= Helpers::h($w['category_name']) ?></td>
          <td><?= Helpers::h($w['vendor_name']) ?></td>
          <td><?= $w['days_worked'] ?></td>
          <td><?= Helpers::dateDisplay($w['first_day']) ?></td>
          <td><?= Helpers::dateDisplay($w['last_day']) ?></td>
        </tr>
        <?php endforeach; ?>
        <?php if (!$workers): ?>
          <tr><td colspan="10" style="text-align:center;color:var(--clr-text-muted)">No workers found for the period.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
    <div style="padding:var(--space-3);color:var(--clr-text-muted);font-size:var(--text-sm)">Total Workers: <?= count($workers) ?></div>
  </div>
</div>

<?php
$pageContent = ob_get_clean();
$pageTitle   = 'Form 12-A';
$activeMenu  = 'reports';
$breadcrumbs = [['label'=>'Reports','url'=>'/reports'],['label'=>'Form 12-A']];
include CLMS_ROOT . '/templates/base.html.php';
