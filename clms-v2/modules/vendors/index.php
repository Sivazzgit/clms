<?php
/**
 * CLMS 2.0 — Vendor List (HR Admin only)
 */
Auth::requireRole('hr_admin');

$search    = Helpers::clean($_GET['q']      ?? '');
$status    = Helpers::clean($_GET['status'] ?? '');
$bind      = [];
$where     = ['v.company_id = ?'];
$bind[]    = $_SESSION['company_id'];

if ($search !== '') {
    $where[] = '(v.vendor_code LIKE ? OR v.name LIKE ? OR v.contact_person LIKE ? OR v.mobile LIKE ?)';
    $s = "%$search%";
    array_push($bind, $s, $s, $s, $s);
}
if ($status !== '') {
    $where[] = 'v.status = ?';
    $bind[]  = $status;
}

$whereStr = implode(' AND ', $where);
$total    = (int) DB::value("SELECT COUNT(*) FROM vendors v WHERE $whereStr", $bind);
$pager    = Helpers::paginate($total);

$vendors = DB::rows(
    "SELECT v.id, v.vendor_code, v.name, v.contact_person, v.mobile, v.email,
            v.labour_licence_no, v.labour_licence_expiry, v.status,
            v.contract_start_date, v.contract_end_date,
            (SELECT COUNT(*) FROM employees e WHERE e.vendor_id = v.id AND e.status='active') AS active_employees
     FROM vendors v
     WHERE $whereStr
     ORDER BY v.name
     LIMIT ? OFFSET ?",
    [...$bind, $pager['perPage'], $pager['offset']]
);

// Count expiring licences in next 30 days (for alert)
$expiringCount = (int) DB::value(
    "SELECT COUNT(*) FROM vendors WHERE company_id=? AND labour_licence_expiry BETWEEN CURDATE() AND DATE_ADD(CURDATE(),INTERVAL 30 DAY)",
    [$_SESSION['company_id']]
);

ob_start();
?>
<div class="page-header">
  <div>
    <h1 class="page-title">Contractors</h1>
    <p class="page-sub" style="color:var(--clr-text-muted);margin:0;"><?= number_format($total) ?> contractor(s)</p>
  </div>
  <div class="page-actions">
    <a href="/vendors/create" class="btn btn-primary">
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
      Add Contractor
    </a>
  </div>
</div>

<?php if ($expiringCount > 0): ?>
<div class="alert alert-warning" style="margin-bottom:var(--space-4)">
  <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
  <?= $expiringCount ?> contractor labour licence(s) expiring within 30 days.
</div>
<?php endif; ?>

<div class="card">
  <!-- Toolbar -->
  <div class="table-toolbar">
    <form method="GET" action="/vendors" style="display:flex;gap:var(--space-3);flex-wrap:wrap;">
      <input type="search" name="q" class="table-search" placeholder="Search name, code, contact…" value="<?= Helpers::h($search) ?>">
      <select name="status" class="form-control" style="width:auto;">
        <option value="">All Status</option>
        <option value="pending"   <?= $status === 'pending'   ? 'selected' : '' ?>>Pending</option>
        <option value="active"    <?= $status === 'active'    ? 'selected' : '' ?>>Active</option>
        <option value="suspended" <?= $status === 'suspended' ? 'selected' : '' ?>>Suspended</option>
        <option value="expired"   <?= $status === 'expired'   ? 'selected' : '' ?>>Expired</option>
      </select>
      <button type="submit" class="btn btn-secondary">Filter</button>
      <?php if ($search || $status): ?>
        <a href="/vendors" class="btn btn-ghost">Clear</a>
      <?php endif; ?>
    </form>
    <button type="button" class="btn btn-ghost btn-sm" onclick="CLMS.export.csv('vendorTable','contractors-export')">
      Export CSV
    </button>
  </div>

  <div class="table-wrapper">
    <table class="data-table" id="vendorTable">
      <thead>
        <tr>
          <th>Code</th>
          <th>Name</th>
          <th>Contact Person</th>
          <th>Mobile</th>
          <th>Labour Licence</th>
          <th>Contract Period</th>
          <th>Employees</th>
          <th>Status</th>
          <th style="width:130px">Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($vendors as $v): ?>
        <?php
          $licExpired = $v['labour_licence_expiry'] && $v['labour_licence_expiry'] < date('Y-m-d');
          $licExpiring = $v['labour_licence_expiry'] && !$licExpired
                         && strtotime($v['labour_licence_expiry']) <= strtotime('+30 days');
        ?>
        <tr>
          <td><code><?= Helpers::h($v['vendor_code']) ?></code></td>
          <td>
            <a href="/vendors/<?= $v['id'] ?>/edit" style="font-weight:500"><?= Helpers::h($v['name']) ?></a>
          </td>
          <td><?= Helpers::h($v['contact_person'] ?? '—') ?></td>
          <td><?= Helpers::h($v['mobile'] ?? '—') ?></td>
          <td>
            <?php if ($v['labour_licence_no']): ?>
              <div><?= Helpers::h($v['labour_licence_no']) ?></div>
              <?php if ($v['labour_licence_expiry']): ?>
                <small class="<?= $licExpired ? 'text-danger' : ($licExpiring ? 'text-warning' : 'text-muted') ?>">
                  Exp: <?= Helpers::dateDisplay($v['labour_licence_expiry']) ?>
                  <?= $licExpired ? '⚠ Expired' : ($licExpiring ? '⚠ Expiring soon' : '') ?>
                </small>
              <?php endif; ?>
            <?php else: ?>
              —
            <?php endif; ?>
          </td>
          <td>
            <?= Helpers::dateDisplay($v['contract_start_date']) ?>
            <?php if ($v['contract_end_date']): ?>
              <br><small style="color:var(--clr-text-muted)">to <?= Helpers::dateDisplay($v['contract_end_date']) ?></small>
            <?php endif; ?>
          </td>
          <td><?= (int)$v['active_employees'] ?></td>
          <td>
            <?php
              $badges = [
                'pending'   => 'pending',
                'active'    => 'active',
                'suspended' => 'rejected',
                'expired'   => 'inactive',
              ];
            ?>
            <span class="badge badge-<?= $badges[$v['status']] ?? 'pending' ?>">
              <?= ucfirst($v['status']) ?>
            </span>
          </td>
          <td>
            <div style="display:flex;gap:var(--space-2)">
              <a href="/vendors/<?= $v['id'] ?>/edit" class="btn btn-secondary btn-sm" title="Edit">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
              </a>
              <a href="/vendors/documents?vendor_id=<?= $v['id'] ?>" class="btn btn-ghost btn-sm" title="Documents">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
              </a>
              <?php if ($v['active_employees'] == 0): ?>
              <button
                class="btn btn-danger btn-sm btn-delete"
                data-url="/vendors/<?= $v['id'] ?>/delete"
                data-confirm="Delete contractor &quot;<?= Helpers::h($v['name']) ?>&quot;? This cannot be undone."
                title="Delete"
              >
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/><path d="M10 11v6M14 11v6"/><path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/></svg>
              </button>
              <?php endif; ?>
            </div>
          </td>
        </tr>
        <?php endforeach; ?>
        <?php if (empty($vendors)): ?>
        <tr><td colspan="9" style="text-align:center;padding:var(--space-8);color:var(--clr-text-muted)">No contractors found.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>

  <?php if ($pager['totalPages'] > 1): ?>
  <div class="card-footer">
    <nav class="pagination">
      <?php if ($pager['current'] > 1): ?>
        <a href="?<?= http_build_query(array_merge($_GET, ['page' => $pager['current'] - 1])) ?>" class="page-btn">&laquo; Prev</a>
      <?php endif; ?>
      <?php foreach ($pager['pages'] as $p): ?>
        <a href="?<?= http_build_query(array_merge($_GET, ['page' => $p])) ?>"
           class="page-btn <?= $p === $pager['current'] ? 'active' : '' ?>"><?= $p ?></a>
      <?php endforeach; ?>
      <?php if ($pager['current'] < $pager['totalPages']): ?>
        <a href="?<?= http_build_query(array_merge($_GET, ['page' => $pager['current'] + 1])) ?>" class="page-btn">Next &raquo;</a>
      <?php endif; ?>
    </nav>
    <span style="font-size:var(--text-sm);color:var(--clr-text-muted)">
      Showing <?= ($pager['offset'] + 1) ?>–<?= min($pager['offset'] + $pager['perPage'], $total) ?> of <?= $total ?>
    </span>
  </div>
  <?php endif; ?>
</div>

<?php
$pageContent = ob_get_clean();
$pageTitle   = 'Contractors';
$activeMenu  = 'vendors';
$breadcrumbs = [['label' => 'Contractors']];
include CLMS_ROOT . '/templates/base.html.php';
