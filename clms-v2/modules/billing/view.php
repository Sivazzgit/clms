<?php
/**
 * CLMS 2.0 — Billing Period Detail View
 */
Auth::requireRole(['hr_admin','finance']);
$companyId = $_SESSION['company_id'];

$billingId = (int)($_GET['id'] ?? 0);
$period = DB::row(
    "SELECT bp.*, v.name AS vendor_name FROM billing_periods bp
     JOIN vendors v ON v.id=bp.vendor_id
     WHERE bp.id=? AND bp.company_id=?",
    [$billingId,$companyId]
);
if (!$period) Helpers::redirect('/billing');

$lineItems = DB::rows(
    "SELECT bli.*, s.name AS section_name, lc.name AS category_name, wc.name AS component_name, wc.type AS comp_type
     FROM billing_line_items bli
     JOIN sections s ON s.id=bli.section_id
     JOIN labour_categories lc ON lc.id=bli.category_id
     JOIN wage_components wc ON wc.id=bli.component_id
     WHERE bli.billing_period_id=? ORDER BY s.name, lc.name, wc.sort_order",
    [$billingId]
);

$payments = DB::rows(
    "SELECT p.*, u.full_name AS recorded_by_name FROM payments p
     JOIN users u ON u.id=p.created_by WHERE p.billing_period_id=? ORDER BY p.payment_date",
    [$billingId]
);

$flash = $_SESSION['flash'] ?? null; unset($_SESSION['flash']);
ob_start();
?>
<?php if ($flash): ?>
  <div class="alert alert-<?= $flash['type']==='success'?'success':'danger' ?>" style="margin-bottom:var(--space-4)"><?= Helpers::h($flash['msg']) ?></div>
<?php endif; ?>

<div class="page-header">
  <div>
    <h1 class="page-title"><?= Helpers::h($period['vendor_name']) ?> — <?= Helpers::dateDisplay($period['from_date']) ?> to <?= Helpers::dateDisplay($period['to_date']) ?></h1>
    <span class="badge badge-pending"><?= ucwords($period['status']) ?></span>
  </div>
  <div class="page-actions">
    <?php if ($period['status']==='draft'): ?>
    <form method="POST" action="<?= APP_BASE ?>/billing/action" style="display:inline">
      <input type="hidden" name="csrf_token"      value="<?= Helpers::h($_SESSION['csrf_token']) ?>">
      <input type="hidden" name="billing_id"      value="<?= $billingId ?>">
      <input type="hidden" name="action"          value="submit">
      <button type="submit" class="btn btn-primary">Submit for Approval</button>
    </form>
    <?php endif; ?>
    <a href="<?= APP_BASE ?>/billing" class="btn btn-ghost">Back</a>
  </div>
</div>

<!-- Summary Cards -->
<div style="display:grid;grid-template-columns:repeat(4,1fr);gap:var(--space-4);margin-bottom:var(--space-4)">
  <div class="stat-card"><div class="stat-value"><?= number_format((float)$period['total_mandays'],2) ?></div><div class="stat-label">Mandays</div></div>
  <div class="stat-card"><div class="stat-value">₹<?= number_format((float)$period['gross_amount'],2) ?></div><div class="stat-label">Gross</div></div>
  <div class="stat-card"><div class="stat-value">₹<?= number_format((float)$period['deduction_amount'],2) ?></div><div class="stat-label">Deductions</div></div>
  <div class="stat-card"><div class="stat-value">₹<?= number_format((float)$period['net_amount'],2) ?></div><div class="stat-label">Net Payable</div></div>
</div>

<!-- Line Items -->
<div class="card" style="margin-bottom:var(--space-4)">
  <div class="card-header"><h3 class="card-title">Line Items</h3></div>
  <div class="table-wrapper">
    <table class="data-table">
      <thead><tr><th>Section</th><th>Category</th><th>Component</th><th>Type</th><th>Mandays</th><th>Rate</th><th>Amount</th></tr></thead>
      <tbody>
        <?php foreach ($lineItems as $li): ?>
        <tr>
          <td><?= Helpers::h($li['section_name']) ?></td>
          <td><?= Helpers::h($li['category_name']) ?></td>
          <td><?= Helpers::h($li['component_name']) ?></td>
          <td><?= ucwords(str_replace('_',' ',$li['comp_type'])) ?></td>
          <td><?= number_format((float)$li['mandays'],2) ?></td>
          <td>₹<?= number_format((float)$li['rate'],2) ?></td>
          <td>₹<?= number_format((float)$li['amount'],2) ?></td>
        </tr>
        <?php endforeach; ?>
        <?php if (!$lineItems): ?><tr><td colspan="7" style="color:var(--clr-text-muted)">No line items.</td></tr><?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<!-- Payments -->
<div class="card" style="margin-bottom:var(--space-4)">
  <div class="card-header">
    <h3 class="card-title">Payments</h3>
    <?php if (in_array($period['status'],['approved','paid','partially_paid'])): ?>
    <a href="<?= APP_BASE ?>/billing/<?= $billingId ?>/payment" class="btn btn-primary btn-sm">Record Payment</a>
    <?php endif; ?>
  </div>
  <div class="table-wrapper">
    <table class="data-table">
      <thead><tr><th>Date</th><th>Amount</th><th>Mode</th><th>Reference</th><th>Remarks</th><th>By</th></tr></thead>
      <tbody>
        <?php foreach ($payments as $p): ?>
        <tr>
          <td><?= Helpers::dateDisplay($p['payment_date']) ?></td>
          <td>₹<?= number_format((float)$p['amount'],2) ?></td>
          <td><?= strtoupper($p['payment_mode']) ?></td>
          <td><?= Helpers::h($p['reference_no'] ?? '—') ?></td>
          <td><?= Helpers::h($p['remarks'] ?? '—') ?></td>
          <td><?= Helpers::h($p['recorded_by_name']) ?></td>
        </tr>
        <?php endforeach; ?>
        <?php if (!$payments): ?><tr><td colspan="6" style="color:var(--clr-text-muted)">No payments recorded.</td></tr><?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?php
$pageContent = ob_get_clean();
$pageTitle   = 'Billing Detail';
$activeMenu  = 'billing';
$breadcrumbs = [['label'=>'Billing','url'=>'/billing'],['label'=>'Detail']];
include CLMS_ROOT . '/templates/base.html.php';
