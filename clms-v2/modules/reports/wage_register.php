<?php
/**
 * CLMS 2.0 — Wage Register
 */
Auth::requireRole(['hr_admin','finance']);
$companyId = $_SESSION['company_id'];

$month    = Helpers::clean($_GET['month'] ?? date('Y-m'));
$vendorId = (int)($_GET['vendor_id'] ?? 0);

[$year, $mon] = explode('-', $month);
$fromDate = "$year-$mon-01";
$toDate   = date('Y-m-t', strtotime($fromDate));

$where = ['a.company_id=?','a.attendance_date BETWEEN ? AND ?','a.is_present=1'];
$bind  = [$companyId,$fromDate,$toDate];
if ($vendorId) { $where[] = 'a.vendor_id=?'; $bind[] = $vendorId; }
$whereStr = implode(' AND ', $where);

// Employee-level summary: total present days, total worked hours
$empSummary = DB::rows(
    "SELECT a.employee_id, e.employee_code, e.first_name, e.last_name,
            v.name AS vendor_name, lc.name AS category_name,
            COUNT(*) AS present_days, SUM(a.worked_hours) AS total_hours
     FROM attendance a
     JOIN employees e      ON e.id=a.employee_id
     JOIN vendors v        ON v.id=a.vendor_id
     JOIN labour_categories lc ON lc.id=e.category_id
     WHERE $whereStr
     GROUP BY a.employee_id, e.employee_code, e.first_name, e.last_name, v.name, lc.name
     ORDER BY v.name, e.first_name",
    $bind
);

// Wage components for column headers
$components = DB::rows(
    "SELECT id, name, type FROM wage_components WHERE company_id=? AND is_active=1 AND type IN ('earning','deduction') ORDER BY sort_order",
    [$companyId]
);

// Rate map: [category_id][component_id] = daily_rate
$rates = DB::rows(
    "SELECT cwr.category_id, cwr.component_id, cwr.amount
     FROM category_wage_rates cwr
     WHERE cwr.company_id=? AND is_holiday_rate=0 AND is_ot_rate=0
       AND (effective_to IS NULL OR effective_to >= ?) AND effective_from <= ?",
    [$companyId,$fromDate,$toDate]
);
$rateMap = [];
foreach ($rates as $r) { $rateMap[$r['category_id']][$r['component_id']] = (float)$r['amount']; }

// Get category_id for each employee
$catIds = [];
$catRows = DB::rows("SELECT id, category_id FROM employees WHERE company_id=?", [$companyId]);
foreach ($catRows as $c) { $catIds[$c['id']] = $c['category_id']; }

$vendors = DB::rows("SELECT id,name FROM vendors WHERE company_id=? AND status='active' ORDER BY name", [$companyId]);

ob_start();
?>
<div class="page-header">
  <div><h1 class="page-title">Wage Register</h1></div>
  <div class="page-actions"><a href="<?= APP_BASE ?>/reports" class="btn btn-ghost">← Reports</a></div>
</div>

<div class="card">
  <div class="table-toolbar">
    <form method="GET" action="<?= APP_BASE ?>/reports/wage-register" style="display:flex;gap:var(--space-3)">
      <input type="month" name="month" class="form-control" value="<?= $month ?>">
      <select name="vendor_id" class="form-control" style="width:auto">
        <option value="">All Vendors</option>
        <?php foreach ($vendors as $v): ?><option value="<?= $v['id'] ?>" <?= $vendorId==$v['id']?'selected':'' ?>><?= Helpers::h($v['name']) ?></option><?php endforeach; ?>
      </select>
      <button type="submit" class="btn btn-secondary">Apply</button>
    </form>
  </div>
  <div class="table-wrapper" style="overflow-x:auto">
    <table class="data-table" style="min-width:900px">
      <thead>
        <tr>
          <th>Code</th><th>Name</th><th>Category</th><th>Days</th>
          <?php foreach ($components as $c): ?><th><?= Helpers::h($c['name']) ?></th><?php endforeach; ?>
          <th>Gross</th><th>Deductions</th><th>Net</th>
        </tr>
      </thead>
      <tbody>
        <?php
        $grandGross = $grandDed = $grandNet = 0;
        foreach ($empSummary as $emp):
            $catId   = $catIds[$emp['employee_id']] ?? 0;
            $days    = $emp['present_days'];
            $earning = $deducting = 0;
            $compAmts = [];
            foreach ($components as $c) {
                $rate     = $rateMap[$catId][$c['id']] ?? 0;
                $amt      = $rate * $days;
                $compAmts[$c['id']] = $amt;
                if ($c['type']==='earning')   $earning   += $amt;
                if ($c['type']==='deduction') $deducting += $amt;
            }
            $net = $earning - $deducting;
            $grandGross += $earning; $grandDed += $deducting; $grandNet += $net;
        ?>
        <tr>
          <td><?= Helpers::h($emp['employee_code']) ?></td>
          <td><?= Helpers::h($emp['first_name'].' '.$emp['last_name']) ?></td>
          <td><?= Helpers::h($emp['category_name']) ?></td>
          <td><?= $days ?></td>
          <?php foreach ($components as $c): ?>
            <td>₹<?= number_format($compAmts[$c['id']],2) ?></td>
          <?php endforeach; ?>
          <td>₹<?= number_format($earning,2) ?></td>
          <td>₹<?= number_format($deducting,2) ?></td>
          <td>₹<?= number_format($net,2) ?></td>
        </tr>
        <?php endforeach; ?>
        <?php if ($empSummary): ?>
        <tr style="font-weight:600;background:var(--clr-bg-subtle)">
          <td colspan="4">Total</td>
          <?php foreach ($components as $c): ?><td></td><?php endforeach; ?>
          <td>₹<?= number_format($grandGross,2) ?></td>
          <td>₹<?= number_format($grandDed,2) ?></td>
          <td>₹<?= number_format($grandNet,2) ?></td>
        </tr>
        <?php else: ?>
          <tr><td colspan="<?= 4+count($components)+3 ?>" style="text-align:center;color:var(--clr-text-muted)">No data for selected period.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?php
$pageContent = ob_get_clean();
$pageTitle   = 'Wage Register';
$activeMenu  = 'reports';
$breadcrumbs = [['label'=>'Reports','url'=>'/reports'],['label'=>'Wage Register']];
include CLMS_ROOT . '/templates/base.html.php';
