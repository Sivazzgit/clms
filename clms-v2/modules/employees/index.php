<?php
/**
 * CLMS 2.0 — Employee Master List
 * HR Admin: all employees  |  Contractor: own employees only
 */
Auth::requireRole(['hr_admin', 'contractor', 'section_incharge', 'hod', 'plant_head']);

$user     = Auth::user();
$isAdmin  = Auth::hasRole('hr_admin');
$isContr  = Auth::hasRole('contractor');

// Contractor role: pre-filter to their own vendor
$vendorFilter = (int)($_GET['vendor_id'] ?? ($isContr ? $user['vendor_id'] : 0));

// Build filters
$search  = Helpers::clean($_GET['q']       ?? '');
$status  = Helpers::clean($_GET['status']  ?? ($isContr ? 'active' : ''));
$catId   = (int)($_GET['category_id'] ?? 0);
$bind    = [];
$where   = ['e.company_id = ?'];
$bind[]  = $_SESSION['company_id'];

if ($isContr) {
    $where[] = 'e.vendor_id = ?';
    $bind[]  = $user['vendor_id'];
} elseif ($vendorFilter) {
    $where[] = 'e.vendor_id = ?';
    $bind[]  = $vendorFilter;
}

if ($search !== '') {
    $where[] = '(e.employee_code LIKE ? OR e.first_name LIKE ? OR e.last_name LIKE ? OR e.aadhaar_no LIKE ? OR e.mobile LIKE ?)';
    $s = "%$search%";
    array_push($bind, $s, $s, $s, $s, $s);
}
if ($status !== '') {
    $where[] = 'e.status = ?';
    $bind[]  = $status;
}
if ($catId) {
    $where[] = 'e.category_id = ?';
    $bind[]  = $catId;
}

$whereStr = implode(' AND ', $where);
$total    = (int) DB::value("SELECT COUNT(*) FROM employees e WHERE $whereStr", $bind);
$pager    = Helpers::paginate($total);

$employees = DB::rows(
    "SELECT e.id, e.employee_code, e.first_name, e.middle_name, e.last_name,
            e.gender, e.mobile, e.status, e.doj_plant, e.biometric_id,
            e.photo_path, e.aadhaar_no,
            v.name AS vendor_name, v.vendor_code,
            lc.name AS category_name
     FROM employees e
     LEFT JOIN vendors v ON v.id = e.vendor_id
     LEFT JOIN labour_categories lc ON lc.id = e.category_id
     WHERE $whereStr
     ORDER BY e.first_name, e.last_name
     LIMIT ? OFFSET ?",
    [...$bind, $pager['perPage'], $pager['offset']]
);

// Sidebar filter options
$vendors    = $isAdmin ? DB::rows("SELECT id, vendor_code, name FROM vendors WHERE company_id=? AND status='active' ORDER BY name", [$_SESSION['company_id']]) : [];
$categories = DB::rows("SELECT id, name FROM labour_categories WHERE company_id=? AND is_active=1 ORDER BY name", [$_SESSION['company_id']]);

// Pending approval count (for admin alert)
$pendingCount = $isAdmin ? (int) DB::value("SELECT COUNT(*) FROM employees WHERE company_id=? AND status='pending_approval'", [$_SESSION['company_id']]) : 0;

ob_start();
?>

<div class="page-header">
  <div>
    <h1 class="page-title">Employee Master</h1>
    <p class="page-sub" style="color:var(--clr-text-muted);margin:0;"><?= number_format($total) ?> employee(s)</p>
  </div>
  <div class="page-actions">
    <?php if ($isAdmin || $isContr): ?>
      <a href="<?= APP_BASE ?>/employees/create" class="btn btn-primary">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
        Add Employee
      </a>
    <?php endif; ?>
    <?php if ($isAdmin): ?>
      <a href="<?= APP_BASE ?>/employees/upload" class="btn btn-ghost">Bulk Upload</a>
    <?php endif; ?>
  </div>
</div>

<?php if ($pendingCount > 0): ?>
<div class="alert alert-warning" style="margin-bottom:var(--space-4);display:flex;align-items:center;justify-content:space-between">
  <span><?= $pendingCount ?> employee(s) awaiting approval.</span>
  <a href="<?= APP_BASE ?>/employees/pending" class="btn btn-secondary btn-sm">Review</a>
</div>
<?php endif; ?>

<div class="card">
  <!-- Filters -->
  <div class="table-toolbar">
    <form method="GET" action="<?= APP_BASE ?>/employees" style="display:flex;gap:var(--space-3);flex-wrap:wrap;align-items:flex-end;">
      <input type="search" name="q" class="table-search" placeholder="Name, code, Aadhaar, mobile…" value="<?= Helpers::h($search) ?>">

      <?php if ($isAdmin && $vendors): ?>
      <select name="vendor_id" class="form-control" style="width:auto;">
        <option value="">All Contractors</option>
        <?php foreach ($vendors as $v): ?>
          <option value="<?= $v['id'] ?>" <?= $vendorFilter === (int)$v['id'] ? 'selected' : '' ?>>
            <?= Helpers::h($v['vendor_code']) ?> — <?= Helpers::h($v['name']) ?>
          </option>
        <?php endforeach; ?>
      </select>
      <?php endif; ?>

      <select name="status" class="form-control" style="width:auto;">
        <option value="">All Status</option>
        <option value="pending_approval" <?= $status === 'pending_approval' ? 'selected' : '' ?>>Pending Approval</option>
        <option value="active"           <?= $status === 'active'           ? 'selected' : '' ?>>Active</option>
        <option value="inactive"         <?= $status === 'inactive'         ? 'selected' : '' ?>>Inactive</option>
        <option value="separated"        <?= $status === 'separated'        ? 'selected' : '' ?>>Separated</option>
      </select>

      <select name="category_id" class="form-control" style="width:auto;">
        <option value="">All Categories</option>
        <?php foreach ($categories as $cat): ?>
          <option value="<?= $cat['id'] ?>" <?= $catId === (int)$cat['id'] ? 'selected' : '' ?>><?= Helpers::h($cat['name']) ?></option>
        <?php endforeach; ?>
      </select>

      <button type="submit" class="btn btn-secondary">Filter</button>
      <?php if ($search || $status || $catId || $vendorFilter): ?>
        <a href="<?= APP_BASE ?>/employees" class="btn btn-ghost">Clear</a>
      <?php endif; ?>
    </form>

    <button type="button" class="btn btn-ghost btn-sm" onclick="CLMS.export.csv('empTable','employees-export')">
      Export CSV
    </button>
  </div>

  <div class="table-wrapper">
    <table class="data-table" id="empTable">
      <thead>
        <tr>
          <th style="width:50px">Photo</th>
          <th>Code</th>
          <th>Name</th>
          <th>Contractor</th>
          <th>Category</th>
          <th>Mobile</th>
          <th>DOJ Plant</th>
          <th>Biometric ID</th>
          <th>Status</th>
          <th style="width:120px">Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($employees as $emp): ?>
        <?php
          $fullName  = trim($emp['first_name'] . ' ' . $emp['middle_name'] . ' ' . $emp['last_name']);
          $initials  = Helpers::initials($emp['first_name'] . ' ' . $emp['last_name']);
          $badges    = [
            'pending_approval' => 'pending',
            'active'           => 'active',
            'inactive'         => 'inactive',
            'separated'        => 'rejected',
          ];
        ?>
        <tr>
          <td>
            <?php if ($emp['photo_path']): ?>
              <img src="/<?= Helpers::h(ltrim($emp['photo_path'],'/')) ?>" alt="<?= Helpers::h($fullName) ?>"
                style="width:36px;height:36px;border-radius:50%;object-fit:cover;border:1px solid var(--clr-border)">
            <?php else: ?>
              <div style="width:36px;height:36px;border-radius:50%;background:var(--clr-primary-light);color:var(--clr-primary);display:flex;align-items:center;justify-content:center;font-weight:600;font-size:var(--text-xs)"><?= Helpers::h($initials) ?></div>
            <?php endif; ?>
          </td>
          <td><code><?= Helpers::h($emp['employee_code']) ?></code></td>
          <td>
            <a href="<?= APP_BASE ?>/employees/<?= $emp['id'] ?>/edit" style="font-weight:500"><?= Helpers::h($fullName) ?></a>
            <?php if ($emp['gender']): ?>
              <small style="color:var(--clr-text-muted);display:block"><?= ucfirst($emp['gender']) ?></small>
            <?php endif; ?>
          </td>
          <td>
            <?php if ($emp['vendor_name']): ?>
              <a href="<?= APP_BASE ?>/employees?vendor_id=<?= $vendorFilter ?>" style="font-size:var(--text-sm)">
                <?= Helpers::h($emp['vendor_code']) ?>
              </a>
              <div style="font-size:var(--text-xs);color:var(--clr-text-muted)"><?= Helpers::h($emp['vendor_name']) ?></div>
            <?php else: ?>—<?php endif; ?>
          </td>
          <td><?= Helpers::h($emp['category_name'] ?? '—') ?></td>
          <td><?= Helpers::h($emp['mobile'] ?? '—') ?></td>
          <td><?= Helpers::dateDisplay($emp['doj_plant']) ?></td>
          <td><?= Helpers::h($emp['biometric_id'] ?? '—') ?></td>
          <td>
            <span class="badge badge-<?= $badges[$emp['status']] ?? 'pending' ?>">
              <?= $emp['status'] === 'pending_approval' ? 'Pending' : ucfirst($emp['status']) ?>
            </span>
          </td>
          <td>
            <div style="display:flex;gap:var(--space-2)">
              <?php if ($isAdmin || $isContr): ?>
              <a href="<?= APP_BASE ?>/employees/<?= $emp['id'] ?>/edit" class="btn btn-secondary btn-sm" title="Edit">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
              </a>
              <?php endif; ?>
              <?php if ($isAdmin && $emp['status'] === 'active'): ?>
              <a href="<?= APP_BASE ?>/employees/<?= $emp['id'] ?>/separate" class="btn btn-ghost btn-sm" title="Separate">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><line x1="23" y1="11" x2="17" y2="11"/></svg>
              </a>
              <?php endif; ?>
            </div>
          </td>
        </tr>
        <?php endforeach; ?>
        <?php if (empty($employees)): ?>
          <tr><td colspan="10" style="text-align:center;padding:var(--space-8);color:var(--clr-text-muted)">No employees found.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>

  <?php if ($pager['totalPages'] > 1): ?>
  <div class="card-footer">
    <nav class="pagination">
      <?php if ($pager['current'] > 1): ?>
        <a href="?<?= http_build_query(array_merge($_GET, ['page' => $pager['current'] - 1])) ?>" class="page-btn">&laquo;</a>
      <?php endif; ?>
      <?php foreach ($pager['pages'] as $p): ?>
        <a href="?<?= http_build_query(array_merge($_GET, ['page' => $p])) ?>"
           class="page-btn <?= $p === $pager['current'] ? 'active' : '' ?>"><?= $p ?></a>
      <?php endforeach; ?>
      <?php if ($pager['current'] < $pager['totalPages']): ?>
        <a href="?<?= http_build_query(array_merge($_GET, ['page' => $pager['current'] + 1])) ?>" class="page-btn">&raquo;</a>
      <?php endif; ?>
    </nav>
    <span style="font-size:var(--text-sm);color:var(--clr-text-muted)">
      <?= ($pager['offset'] + 1) ?>–<?= min($pager['offset'] + $pager['perPage'], $total) ?> of <?= $total ?>
    </span>
  </div>
  <?php endif; ?>
</div>

<?php
$pageContent = ob_get_clean();
$pageTitle   = 'Employee Master';
$activeMenu  = 'employees';
$breadcrumbs = [['label' => 'Employee Master']];
include CLMS_ROOT . '/templates/base.html.php';
