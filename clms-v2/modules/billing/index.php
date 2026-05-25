<?php
/**
 * CLMS 2.0 — Billing Periods List
 */
Auth::requireRole(['hr_admin','finance']);
$companyId = $_SESSION['company_id'];

$filterVendor = (int)($_GET['vendor_id'] ?? 0);
$where = ['bp.company_id=?'];
$bind  = [$companyId];
if ($filterVendor) { $where[] = 'bp.vendor_id=?'; $bind[] = $filterVendor; }
$whereStr = implode(' AND ', $where);

$total = (int) DB::value("SELECT COUNT(*) FROM billing_periods bp WHERE $whereStr", $bind);
$pager = Helpers::paginate($total);

$periods = DB::rows(
    "SELECT bp.*, v.name AS vendor_name FROM billing_periods bp
     JOIN vendors v ON v.id=bp.vendor_id
     WHERE $whereStr ORDER BY bp.from_date DESC LIMIT ? OFFSET ?",
    [...$bind, $pager['perPage'], $pager['offset']]
);

$vendors = DB::rows("SELECT id, name FROM vendors WHERE company_id=? AND status='active' ORDER BY name", [$companyId]);
$flash = $_SESSION['flash'] ?? null; unset($_SESSION['flash']);
ob_start();
?>
<?php if ($flash): ?>
  <div class="alert alert-<?= $flash['type']==='success'?'success':'danger' ?>" style="margin-bottom:var(--space-4)"><?= Helpers::h($flash['msg']) ?></div>
<?php endif; ?>

<div class="page-header">
  <div>
    <h1 class="page-title">Billing</h1>
    <p class="page-sub" style="color:var(--clr-text-muted);margin:0"><?= number_format($total) ?> period(s)</p>
  </div>
  <div class="page-actions">
    <a href="/billing/generate" class="btn btn-primary">Generate Bill</a>
  </div>
</div>

<div class="card">
  <div class="table-toolbar">
    <form method="GET" action="/billing" style="display:flex;gap:var(--space-3)">
      <select name="vendor_id" class="form-control" style="width:auto" onchange="this.form.submit()">
        <option value="">All Vendors</option>
        <?php foreach ($vendors as $v): ?>
          <option value="<?= $v['id'] ?>" <?= $filterVendor==$v['id']?'selected':'' ?>><?= Helpers::h($v['name']) ?></option>
        <?php endforeach; ?>
      </select>
      <?php if ($filterVendor): ?><a href="/billing" class="btn btn-ghost">Clear</a><?php endif; ?>
    </form>
  </div>
  <div class="table-wrapper">
    <table class="data-table">
      <thead><tr><th>Vendor</th><th>Period</th><th>PO/WO</th><th>Mandays</th><th>Gross</th><th>Deductions</th><th>Net</th><th>Status</th><th></th></tr></thead>
      <tbody>
        <?php foreach ($periods as $p): ?>
        <tr>
          <td><?= Helpers::h($p['vendor_name']) ?></td>
          <td><?= Helpers::dateDisplay($p['from_date']).' — '.Helpers::dateDisplay($p['to_date']) ?></td>
          <td><?= Helpers::h(($p['po_number'] ?? '').' / '.($p['wo_number'] ?? '')) ?></td>
          <td><?= number_format((float)$p['total_mandays'], 2) ?></td>
          <td>₹<?= number_format((float)$p['gross_amount'], 2) ?></td>
          <td>₹<?= number_format((float)$p['deduction_amount'], 2) ?></td>
          <td>₹<?= number_format((float)$p['net_amount'], 2) ?></td>
          <td><span class="badge badge-pending"><?= ucwords($p['status']) ?></span></td>
          <td><a href="/billing/<?= $p['id'] ?>/view" class="btn btn-secondary btn-sm">View</a></td>
        </tr>
        <?php endforeach; ?>
        <?php if (!$periods): ?>
          <tr><td colspan="9" style="text-align:center;color:var(--clr-text-muted)">No billing periods found.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?php
$pageContent = ob_get_clean();
$pageTitle   = 'Billing';
$activeMenu  = 'billing';
$breadcrumbs = [['label'=>'Billing']];
include CLMS_ROOT . '/templates/base.html.php';
