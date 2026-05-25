<?php
/**
 * CLMS 2.0 — Cost by Section Report
 */
Auth::requireRole(['hr_admin','finance','hod','plant_head']);
$companyId = $_SESSION['company_id'];

$month    = Helpers::clean($_GET['month'] ?? date('Y-m'));
[$year,$mon] = explode('-',$month);
$fromDate = "$year-$mon-01";
$toDate   = date('Y-m-t', strtotime($fromDate));

$rows = DB::rows(
    "SELECT s.name AS section_name,
            COUNT(DISTINCT a.employee_id) AS unique_workers,
            SUM(a.is_present) AS total_mandays
     FROM attendance a
     JOIN sections s ON s.id=a.section_id
     WHERE a.company_id=? AND a.attendance_date BETWEEN ? AND ?
     GROUP BY a.section_id ORDER BY total_mandays DESC",
    [$companyId,$fromDate,$toDate]
);

// Billing line items aggregated by section for the period
$billingLines = DB::rows(
    "SELECT bli.section_id, SUM(bli.amount) AS total_amount
     FROM billing_line_items bli
     JOIN billing_periods bp ON bp.id=bli.billing_period_id
     WHERE bp.company_id=? AND bp.from_date=? AND bp.to_date=?
     GROUP BY bli.section_id",
    [$companyId,$fromDate,$toDate]
);
$billingBySection = [];
foreach ($billingLines as $b) { $billingBySection[$b['section_id']] = (float)$b['total_amount']; }

// section name to id map for billing
$secIds = DB::rows("SELECT id,name FROM sections WHERE company_id=?", [$companyId]);
$secIdMap = [];
foreach ($secIds as $sec) { $secIdMap[$sec['name']] = $sec['id']; }

ob_start();
?>
<div class="page-header">
  <div><h1 class="page-title">Cost by Section</h1></div>
  <div class="page-actions"><a href="<?= APP_BASE ?>/reports" class="btn btn-ghost">← Reports</a></div>
</div>

<div class="card">
  <div class="table-toolbar">
    <form method="GET" action="<?= APP_BASE ?>/reports/cost-section" style="display:flex;gap:var(--space-3)">
      <input type="month" name="month" class="form-control" value="<?= $month ?>">
      <button type="submit" class="btn btn-secondary">Apply</button>
    </form>
  </div>
  <div class="table-wrapper">
    <table class="data-table">
      <thead><tr><th>Section</th><th>Workers</th><th>Mandays</th><th>Total Cost</th></tr></thead>
      <tbody>
        <?php foreach ($rows as $r):
            $secId = $secIdMap[$r['section_name']] ?? 0;
            $cost  = $billingBySection[$secId] ?? 0;
        ?>
        <tr>
          <td><?= Helpers::h($r['section_name']) ?></td>
          <td><?= $r['unique_workers'] ?></td>
          <td><?= number_format((float)$r['total_mandays'],0) ?></td>
          <td>₹<?= number_format($cost,2) ?></td>
        </tr>
        <?php endforeach; ?>
        <?php if (!$rows): ?><tr><td colspan="4" style="text-align:center;color:var(--clr-text-muted)">No data.</td></tr><?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?php
$pageContent = ob_get_clean();
$pageTitle   = 'Cost by Section';
$activeMenu  = 'reports';
$breadcrumbs = [['label'=>'Reports','url'=>'/reports'],['label'=>'Cost by Section']];
include CLMS_ROOT . '/templates/base.html.php';
