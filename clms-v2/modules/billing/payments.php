<?php
/**
 * CLMS 2.0 — Record Payment
 */
Auth::requireRole(['hr_admin','finance']);
$companyId = $_SESSION['company_id'];
$errors    = [];

$billingId = (int)($_GET['id'] ?? 0);
$period    = DB::row(
    "SELECT bp.*, v.name AS vendor_name FROM billing_periods bp
     JOIN vendors v ON v.id=bp.vendor_id
     WHERE bp.id=? AND bp.company_id=?",
    [$billingId,$companyId]
);
if (!$period) Helpers::redirect('/billing');
if (!in_array($period['status'], ['approved','paid','partially_paid'])) {
    Helpers::redirect("/billing/$billingId/view", 'Bill must be approved before recording payment.', 'error');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    Auth::requireCsrf();
    $amount      = (float)($_POST['amount']      ?? 0);
    $payDate     = Helpers::clean($_POST['payment_date'] ?? '');
    $mode        = Helpers::clean($_POST['payment_mode']  ?? 'neft');
    $refNo       = Helpers::clean($_POST['reference_no']  ?? '');
    $remarks     = Helpers::clean($_POST['remarks']       ?? '');

    if ($amount <= 0) $errors[] = 'Amount must be positive.';
    if (!$payDate)    $errors[] = 'Payment date is required.';
    $validModes = ['neft','rtgs','cheque','cash','other'];
    if (!in_array($mode,$validModes)) $mode = 'neft';

    if (!$errors) {
        DB::execute(
            "INSERT INTO payments (company_id,vendor_id,billing_period_id,payment_date,amount,payment_mode,reference_no,remarks,created_by,created_at)
             VALUES (?,?,?,?,?,?,?,?,?,NOW())",
            [$companyId,$period['vendor_id'],$billingId,$payDate,$amount,$mode,$refNo,$remarks,Auth::user()['id']]
        );
        // Update payment_status
        $totalPaid = (float) DB::value("SELECT SUM(amount) FROM payments WHERE billing_period_id=?", [$billingId]);
        if ($totalPaid >= $period['net_amount']) {
            DB::execute("UPDATE billing_periods SET payment_status='paid',status='paid',updated_at=NOW() WHERE id=?", [$billingId]);
        } else {
            DB::execute("UPDATE billing_periods SET payment_status='partially_paid',status='partially_paid',updated_at=NOW() WHERE id=?", [$billingId]);
        }
        AuditLogger::log('CREATE','payment',DB::lastInsertId());
        Helpers::redirect("/billing/$billingId/view", 'Payment recorded.', 'success');
    }
}

ob_start();
?>
<div class="page-header">
  <div><h1 class="page-title">Record Payment — <?= Helpers::h($period['vendor_name']) ?></h1></div>
  <div class="page-actions"><a href="<?= APP_BASE ?>/billing/<?= $billingId ?>/view" class="btn btn-ghost">Cancel</a></div>
</div>

<?php foreach ($errors as $e): ?>
  <div class="alert alert-danger" style="margin-bottom:var(--space-2)"><?= Helpers::h($e) ?></div>
<?php endforeach; ?>

<div class="card">
  <div class="card-body">
    <p>Net Payable: <strong>₹<?= number_format((float)$period['net_amount'],2) ?></strong></p>
    <form method="POST" action="<?= APP_BASE ?>/billing/<?= $billingId ?>/payment">
      <input type="hidden" name="csrf_token" value="<?= Helpers::h($_SESSION['csrf_token']) ?>">
      <div class="form-grid-3">
        <div class="form-group">
          <label class="form-label required">Amount</label>
          <input type="number" name="amount" class="form-control" step="0.01" min="0.01" value="<?= $period['net_amount'] ?>" required>
        </div>
        <div class="form-group">
          <label class="form-label required">Payment Date</label>
          <input type="date" name="payment_date" class="form-control" value="<?= date('Y-m-d') ?>" required>
        </div>
        <div class="form-group">
          <label class="form-label required">Payment Mode</label>
          <select name="payment_mode" class="form-control">
            <?php foreach (['neft','rtgs','cheque','cash','other'] as $m): ?>
              <option value="<?= $m ?>"><?= strtoupper($m) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="form-group">
          <label class="form-label">Reference No.</label>
          <input type="text" name="reference_no" class="form-control" maxlength="100">
        </div>
        <div class="form-group" style="grid-column:span 2">
          <label class="form-label">Remarks</label>
          <input type="text" name="remarks" class="form-control" maxlength="255">
        </div>
      </div>
      <button type="submit" class="btn btn-primary">Record Payment</button>
    </form>
  </div>
</div>

<?php
$pageContent = ob_get_clean();
$pageTitle   = 'Record Payment';
$activeMenu  = 'billing';
$breadcrumbs = [['label'=>'Billing','url'=>'/billing'],['label'=>'Payment']];
include CLMS_ROOT . '/templates/base.html.php';
