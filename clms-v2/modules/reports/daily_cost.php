<?php
/**
 * CLMS 2.0 — Daily Cost Report
 */
Auth::requireRole(['hr_admin','finance']);
$companyId = $_SESSION['company_id'];

$fromDate = Helpers::clean($_GET['from_date'] ?? date('Y-m-01'));
$toDate   = Helpers::clean($_GET['to_date']   ?? date('Y-m-d'));
$vendorId = (int)($_GET['vendor_id'] ?? 0);

$where = ['a.company_id=?','a.attendance_date BETWEEN ? AND ?','a.is_present=1'];
$bind  = [$companyId,$fromDate,$toDate];
if ($vendorId) { $where[] = 'a.vendor_id=?'; $bind[] = $vendorId; }
$whereStr = implode(' AND ', $where);

// Aggregate by date: present count and estimate from daily rate
$rows = DB::rows(
    "SELECT a.attendance_date, e.category_id,
            COUNT(*) AS present_count
     FROM attendance a
     JOIN employees e ON e.id=a.employee_id
     WHERE $whereStr
     GROUP BY a.attendance_date, e.category_id
     ORDER BY a.attendance_date",
    $bind
);

// Daily rate per category (sum of all earning components)
$rates = DB::rows(
    "SELECT cwr.category_id, SUM(cwr.amount) AS daily_rate
     FROM category_wage_rates cwr
     JOIN wage_components wc ON wc.id=cwr.component_id
     WHERE cwr.company_id=? AND wc.type='earning' AND is_holiday_rate=0 AND is_ot_rate=0
       AND (effective_to IS NULL OR effective_to >= ?) AND effective_from <= ?
     GROUP BY cwr.category_id",
    [$companyId,$fromDate,$toDate]
);
$rateMap = [];
foreach ($rates as $r) { $rateMap[$r['category_id']] = (float)$r['daily_rate']; }

// Aggregate by date
$byDate = [];
foreach ($rows as $r) {
    $d    = $r['attendance_date'];
    $cost = ($rateMap[$r['category_id']] ?? 0) * $r['present_count'];
    if (!isset($byDate[$d])) $byDate[$d] = ['date'=>$d,'headcount'=>0,'cost'=>0];
    $byDate[$d]['headcount'] += $r['present_count'];
    $byDate[$d]['cost']      += $cost;
}

$vendors = DB::rows("SELECT id,name FROM vendors WHERE company_id=? AND status='active' ORDER BY name", [$companyId]);

ob_start();
?>
<div class="page-header">
  <div><h1 class="page-title">Daily Cost</h1></div>
  <div class="page-actions"><a href="/reports" class="btn btn-ghost">← Reports</a></div>
</div>

<div class="card">
  <div class="table-toolbar">
    <form method="GET" action="/reports/daily-cost" style="display:flex;gap:var(--space-3)">
      <input type="date" name="from_date" class="form-control" value="<?= $fromDate ?>">
      <input type="date" name="to_date"   class="form-control" value="<?= $toDate ?>">
      <select name="vendor_id" class="form-control" style="width:auto">
        <option value="">All Vendors</option>
        <?php foreach ($vendors as $v): ?><option value="<?= $v['id'] ?>" <?= $vendorId==$v['id']?'selected':'' ?>><?= Helpers::h($v['name']) ?></option><?php endforeach; ?>
      </select>
      <button type="submit" class="btn btn-secondary">Apply</button>
    </form>
  </div>
  <div class="table-wrapper">
    <table class="data-table">
      <thead><tr><th>Date</th><th>Headcount (Present)</th><th>Estimated Cost</th></tr></thead>
      <tbody>
        <?php
        $totalCost = 0; $totalHC = 0;
        foreach ($byDate as $d):
            $totalCost += $d['cost']; $totalHC += $d['headcount'];
        ?>
        <tr>
          <td><?= Helpers::dateDisplay($d['date']) ?></td>
          <td><?= $d['headcount'] ?></td>
          <td>₹<?= number_format($d['cost'],2) ?></td>
        </tr>
        <?php endforeach; ?>
        <?php if ($byDate): ?>
          <tr style="font-weight:600;background:var(--clr-bg-subtle)">
            <td>Total</td><td><?= $totalHC ?></td><td>₹<?= number_format($totalCost,2) ?></td>
          </tr>
        <?php else: ?>
          <tr><td colspan="3" style="text-align:center;color:var(--clr-text-muted)">No data.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?php
$pageContent = ob_get_clean();
$pageTitle   = 'Daily Cost';
$activeMenu  = 'reports';
$breadcrumbs = [['label'=>'Reports','url'=>'/reports'],['label'=>'Daily Cost']];
include CLMS_ROOT . '/templates/base.html.php';
