<?php
/**
 * CLMS 2.0 — Cost by Contractor Report
 */
Auth::requireRole(['hr_admin','finance']);
$companyId = $_SESSION['company_id'];

$month    = Helpers::clean($_GET['month'] ?? date('Y-m'));
[$year,$mon] = explode('-',$month);
$fromDate = "$year-$mon-01";
$toDate   = date('Y-m-t', strtotime($fromDate));

$rows = DB::rows(
    "SELECT v.name AS vendor_name, v.vendor_code,
            COUNT(DISTINCT a.employee_id) AS unique_workers,
            SUM(a.is_present) AS total_mandays,
            SUM(a.worked_hours) AS total_hours
     FROM attendance a
     JOIN vendors v ON v.id=a.vendor_id
     WHERE a.company_id=? AND a.attendance_date BETWEEN ? AND ?
     GROUP BY a.vendor_id ORDER BY total_mandays DESC",
    [$companyId,$fromDate,$toDate]
);

// Get billing amounts for the same period
$billing = DB::rows(
    "SELECT vendor_id, SUM(gross_amount) AS gross, SUM(net_amount) AS net
     FROM billing_periods WHERE company_id=? AND from_date=? AND to_date=?
     GROUP BY vendor_id",
    [$companyId,$fromDate,$toDate]
);
$billingMap = [];
foreach ($billing as $b) { $billingMap[$b['vendor_id']] = $b; }

$vendors = DB::rows("SELECT id,vendor_code FROM vendors WHERE company_id=? AND status='active'", [$companyId]);
$vendorCodeMap = [];
foreach ($vendors as $v) { $vendorCodeMap[$v['id']] = $v['vendor_code']; }

ob_start();
?>
<div class="page-header">
  <div><h1 class="page-title">Cost by Contractor</h1></div>
  <div class="page-actions"><a href="<?= APP_BASE ?>/reports" class="btn btn-ghost">← Reports</a></div>
</div>

<div class="card">
  <div class="table-toolbar">
    <form method="GET" action="<?= APP_BASE ?>/reports/cost-contractor" style="display:flex;gap:var(--space-3)">
      <input type="month" name="month" class="form-control" value="<?= $month ?>">
      <button type="submit" class="btn btn-secondary">Apply</button>
    </form>
  </div>
  <div class="table-wrapper">
    <table class="data-table">
      <thead><tr><th>Contractor</th><th>Code</th><th>Workers</th><th>Mandays</th><th>Hours</th><th>Gross Bill</th><th>Net Bill</th></tr></thead>
      <tbody>
        <?php foreach ($rows as $r): ?>
        <tr>
          <td><?= Helpers::h($r['vendor_name']) ?></td>
          <td><?= Helpers::h($r['vendor_code']) ?></td>
          <td><?= $r['unique_workers'] ?></td>
          <td><?= number_format((float)$r['total_mandays'],0) ?></td>
          <td><?= number_format((float)$r['total_hours'],1) ?></td>
          <td>₹<?= number_format((float)($billingMap[$r['vendor_id']]['gross'] ?? 0),2) ?></td>
          <td>₹<?= number_format((float)($billingMap[$r['vendor_id']]['net'] ?? 0),2) ?></td>
        </tr>
        <?php endforeach; ?>
        <?php if (!$rows): ?><tr><td colspan="7" style="text-align:center;color:var(--clr-text-muted)">No data.</td></tr><?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?php
$pageContent = ob_get_clean();
$pageTitle   = 'Cost by Contractor';
$activeMenu  = 'reports';
$breadcrumbs = [['label'=>'Reports','url'=>'/reports'],['label'=>'Cost by Contractor']];
include CLMS_ROOT . '/templates/base.html.php';
