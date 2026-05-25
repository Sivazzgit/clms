<?php
/**
 * CLMS 2.0 — PF & ESI Register
 * Shows statutory deductions per employee for selected month.
 */
Auth::requireRole(['hr_admin','finance']);
$companyId = $_SESSION['company_id'];

$month    = Helpers::clean($_GET['month'] ?? date('Y-m'));
$vendorId = (int)($_GET['vendor_id'] ?? 0);
[$year,$mon] = explode('-',$month);
$fromDate = "$year-$mon-01";
$toDate   = date('Y-m-t', strtotime($fromDate));

$where = ['a.company_id=?','a.attendance_date BETWEEN ? AND ?','a.is_present=1'];
$bind  = [$companyId,$fromDate,$toDate];
if ($vendorId) { $where[] = 'a.vendor_id=?'; $bind[] = $vendorId; }
$whereStr = implode(' AND ', $where);

$empSummary = DB::rows(
    "SELECT a.employee_id, e.employee_code, e.first_name, e.last_name,
            v.name AS vendor_name, e.category_id, COUNT(*) AS present_days
     FROM attendance a
     JOIN employees e ON e.id=a.employee_id
     JOIN vendors v   ON v.id=a.vendor_id
     WHERE $whereStr
     GROUP BY a.employee_id ORDER BY v.name, e.first_name",
    $bind
);

// Get statutory components (PF, ESI etc)
$statComponents = DB::rows(
    "SELECT id,name FROM wage_components WHERE company_id=? AND is_statutory=1 AND is_active=1 ORDER BY sort_order",
    [$companyId]
);

// Rate map
$rates = DB::rows(
    "SELECT cwr.category_id, cwr.component_id, cwr.amount
     FROM category_wage_rates cwr
     WHERE cwr.company_id=? AND is_holiday_rate=0 AND is_ot_rate=0
       AND (effective_to IS NULL OR effective_to >= ?) AND effective_from <= ?",
    [$companyId,$fromDate,$toDate]
);
$rateMap = [];
foreach ($rates as $r) { $rateMap[$r['category_id']][$r['component_id']] = (float)$r['amount']; }

$vendors = DB::rows("SELECT id,name FROM vendors WHERE company_id=? AND status='active' ORDER BY name", [$companyId]);

ob_start();
?>
<div class="page-header">
  <div><h1 class="page-title">PF &amp; ESI Register</h1></div>
  <div class="page-actions"><a href="/reports" class="btn btn-ghost">← Reports</a></div>
</div>

<div class="card">
  <div class="table-toolbar">
    <form method="GET" action="/reports/pf-esi" style="display:flex;gap:var(--space-3)">
      <input type="month" name="month" class="form-control" value="<?= $month ?>">
      <select name="vendor_id" class="form-control" style="width:auto">
        <option value="">All Vendors</option>
        <?php foreach ($vendors as $v): ?><option value="<?= $v['id'] ?>" <?= $vendorId==$v['id']?'selected':'' ?>><?= Helpers::h($v['name']) ?></option><?php endforeach; ?>
      </select>
      <button type="submit" class="btn btn-secondary">Apply</button>
    </form>
  </div>
  <div class="table-wrapper" style="overflow-x:auto">
    <table class="data-table">
      <thead>
        <tr>
          <th>Code</th><th>Name</th><th>Vendor</th><th>Days</th>
          <?php foreach ($statComponents as $c): ?><th><?= Helpers::h($c['name']) ?></th><?php endforeach; ?>
          <th>Total Statutory</th>
        </tr>
      </thead>
      <tbody>
        <?php
        $grandTotal = 0;
        foreach ($empSummary as $emp):
            $days = $emp['present_days'];
            $compAmts = []; $total = 0;
            foreach ($statComponents as $c) {
                $amt = ($rateMap[$emp['category_id']][$c['id']] ?? 0) * $days;
                $compAmts[$c['id']] = $amt;
                $total += $amt;
            }
            $grandTotal += $total;
        ?>
        <tr>
          <td><?= Helpers::h($emp['employee_code']) ?></td>
          <td><?= Helpers::h($emp['first_name'].' '.$emp['last_name']) ?></td>
          <td><?= Helpers::h($emp['vendor_name']) ?></td>
          <td><?= $days ?></td>
          <?php foreach ($statComponents as $c): ?><td>₹<?= number_format($compAmts[$c['id']],2) ?></td><?php endforeach; ?>
          <td>₹<?= number_format($total,2) ?></td>
        </tr>
        <?php endforeach; ?>
        <?php if ($empSummary): ?>
          <tr style="font-weight:600;background:var(--clr-bg-subtle)">
            <td colspan="4">Total</td>
            <?php foreach ($statComponents as $c): ?><td></td><?php endforeach; ?>
            <td>₹<?= number_format($grandTotal,2) ?></td>
          </tr>
        <?php else: ?>
          <tr><td colspan="<?= 4+count($statComponents)+1 ?>" style="text-align:center;color:var(--clr-text-muted)">No data.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?php
$pageContent = ob_get_clean();
$pageTitle   = 'PF & ESI Register';
$activeMenu  = 'reports';
$breadcrumbs = [['label'=>'Reports','url'=>'/reports'],['label'=>'PF & ESI']];
include CLMS_ROOT . '/templates/base.html.php';
