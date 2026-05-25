<?php
/**
 * CLMS 2.0 — Generate Bill from Attendance
 */
Auth::requireRole(['hr_admin','finance']);
$companyId = $_SESSION['company_id'];
$errors    = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    Auth::requireCsrf();
    $vendorId   = (int)($_POST['vendor_id']   ?? 0);
    $fromDate   = Helpers::clean($_POST['from_date'] ?? '');
    $toDate     = Helpers::clean($_POST['to_date']   ?? '');
    $periodType = in_array($_POST['period_type']??'', ['monthly','custom']) ? $_POST['period_type'] : 'custom';
    $poNumber   = Helpers::clean($_POST['po_number'] ?? '');
    $woNumber   = Helpers::clean($_POST['wo_number'] ?? '');

    if (!$vendorId) $errors[] = 'Vendor is required.';
    if (!$fromDate) $errors[] = 'From date is required.';
    if (!$toDate)   $errors[] = 'To date is required.';
    if ($fromDate && $toDate && $fromDate > $toDate) $errors[] = 'From date must be before to date.';

    if (!$errors) {
        // Check no overlapping period for this vendor
        $overlap = DB::value(
            "SELECT id FROM billing_periods WHERE vendor_id=? AND NOT (to_date < ? OR from_date > ?)",
            [$vendorId,$fromDate,$toDate]
        );
        if ($overlap) $errors[] = 'A billing period already exists that overlaps this date range.';
    }

    if (!$errors) {
        // Aggregate attendance for this vendor in the period
        // Mandays = count of distinct present employee-days, grouped by section × category × wage_component
        $attendance = DB::rows(
            "SELECT a.section_id, e.category_id, COUNT(*) AS mandays
             FROM attendance a
             JOIN employees e ON e.id=a.employee_id
             WHERE a.vendor_id=? AND a.company_id=? AND a.attendance_date BETWEEN ? AND ? AND a.is_present=1
             GROUP BY a.section_id, e.category_id",
            [$vendorId,$companyId,$fromDate,$toDate]
        );

        if (empty($attendance)) {
            $errors[] = 'No present attendance records found for the selected period.';
        } else {
            // Sum up gross amount using wage rates
            $totalMandays = 0;
            $grossAmount  = 0.00;
            $lineItems    = [];

            foreach ($attendance as $row) {
                $totalMandays += $row['mandays'];
                // Get wage rates for category and components
                $rates = DB::rows(
                    "SELECT cwr.component_id, cwr.amount, cwr.percent, wc.type, wc.calc_method
                     FROM category_wage_rates cwr
                     JOIN wage_components wc ON wc.id=cwr.component_id
                     WHERE cwr.company_id=? AND cwr.category_id=?
                       AND cwr.is_holiday_rate=0 AND cwr.is_ot_rate=0
                       AND (cwr.effective_to IS NULL OR cwr.effective_to >= ?)
                       AND cwr.effective_from <= ?",
                    [$companyId,$row['category_id'],$fromDate,$toDate]
                );
                foreach ($rates as $rate) {
                    $lineAmount = (float)$rate['amount'] * $row['mandays'];
                    $lineItems[] = [
                        'section_id'   => $row['section_id'],
                        'category_id'  => $row['category_id'],
                        'component_id' => $rate['component_id'],
                        'mandays'      => $row['mandays'],
                        'rate'         => $rate['amount'],
                        'amount'       => $lineAmount,
                        'comp_type'    => $rate['type'],
                    ];
                    if ($rate['type'] !== 'deduction') {
                        $grossAmount += $lineAmount;
                    }
                }
            }

            $deductionAmount = array_sum(array_column(
                array_filter($lineItems, fn($l) => $l['comp_type'] === 'deduction'), 'amount'
            ));
            $netAmount = $grossAmount - $deductionAmount;

            DB::execute(
                "INSERT INTO billing_periods (company_id,vendor_id,period_type,from_date,to_date,po_number,wo_number,
                  status,total_mandays,gross_amount,deduction_amount,net_amount,payment_status,generated_by,generated_at,created_at)
                 VALUES (?,?,?,?,?,?,?,'draft',?,?,?,?,'pending',?,NOW(),NOW())",
                [$companyId,$vendorId,$periodType,$fromDate,$toDate,$poNumber,$woNumber,
                 $totalMandays,$grossAmount,$deductionAmount,$netAmount,Auth::user()['id']]
            );
            $billingId = DB::lastInsertId();

            foreach ($lineItems as $li) {
                DB::execute(
                    "INSERT INTO billing_line_items (billing_period_id,section_id,category_id,component_id,mandays,rate,amount)
                     VALUES (?,?,?,?,?,?,?)",
                    [$billingId,$li['section_id'],$li['category_id'],$li['component_id'],$li['mandays'],$li['rate'],$li['amount']]
                );
            }

            AuditLogger::log('CREATE','billing',$billingId);
            Helpers::redirect('/billing/'.$billingId.'/view', 'Bill generated successfully.', 'success');
        }
    }
}

$vendors = DB::rows("SELECT id, name FROM vendors WHERE company_id=? AND status='active' ORDER BY name", [$companyId]);
ob_start();
?>
<div class="page-header">
  <div><h1 class="page-title">Generate Bill</h1></div>
  <div class="page-actions"><a href="<?= APP_BASE ?>/billing" class="btn btn-ghost">Cancel</a></div>
</div>

<?php foreach ($errors as $e): ?>
  <div class="alert alert-danger" style="margin-bottom:var(--space-2)"><?= Helpers::h($e) ?></div>
<?php endforeach; ?>

<div class="card">
  <div class="card-body">
    <form method="POST" action="<?= APP_BASE ?>/billing/generate">
      <input type="hidden" name="csrf_token" value="<?= Helpers::h($_SESSION['csrf_token']) ?>">
      <div class="form-grid-3">
        <div class="form-group">
          <label class="form-label required">Vendor</label>
          <select name="vendor_id" class="form-control" required>
            <option value="">— Select Vendor —</option>
            <?php foreach ($vendors as $v): ?>
              <option value="<?= $v['id'] ?>"><?= Helpers::h($v['name']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="form-group">
          <label class="form-label required">Period Type</label>
          <select name="period_type" class="form-control">
            <option value="monthly">Monthly</option>
            <option value="custom">Custom</option>
          </select>
        </div>
        <div class="form-group">
          <label class="form-label required">From Date</label>
          <input type="date" name="from_date" class="form-control" required>
        </div>
        <div class="form-group">
          <label class="form-label required">To Date</label>
          <input type="date" name="to_date" class="form-control" required>
        </div>
        <div class="form-group">
          <label class="form-label">PO Number</label>
          <input type="text" name="po_number" class="form-control" maxlength="100">
        </div>
        <div class="form-group">
          <label class="form-label">WO Number</label>
          <input type="text" name="wo_number" class="form-control" maxlength="100">
        </div>
      </div>
      <div class="form-actions">
        <button type="submit" class="btn btn-primary">Generate Bill</button>
      </div>
    </form>
  </div>
</div>

<?php
$pageContent = ob_get_clean();
$pageTitle   = 'Generate Bill';
$activeMenu  = 'billing';
$breadcrumbs = [['label'=>'Billing','url'=>'/billing'],['label'=>'Generate']];
include CLMS_ROOT . '/templates/base.html.php';
