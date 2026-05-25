<?php
/**
 * CLMS 2.0 — User List (HR Admin only)
 */
Auth::requireRole('hr_admin');

// Handle search / filter
$search  = Helpers::clean($_GET['q'] ?? '');
$status  = Helpers::clean($_GET['status'] ?? '');
$bind    = [];
$where   = ['1=1'];

if ($search !== '') {
    $where[] = '(u.username LIKE ? OR u.full_name LIKE ? OR u.email LIKE ?)';
    $s = "%$search%";
    $bind = array_merge($bind, [$s, $s, $s]);
}
if ($status === 'active') {
    $where[] = 'u.is_active = 1 AND u.is_blocked = 0';
} elseif ($status === 'blocked') {
    $where[] = 'u.is_blocked = 1';
} elseif ($status === 'inactive') {
    $where[] = 'u.is_active = 0';
}

$whereStr = implode(' AND ', $where);

$total = (int) DB::value("SELECT COUNT(*) FROM users u WHERE $whereStr", $bind);
$pager = Helpers::paginate($total);

$users = DB::rows(
    "SELECT u.id, u.username, u.full_name, u.email, u.mobile, u.is_active, u.is_blocked, u.last_login_at,
            GROUP_CONCAT(r.name ORDER BY r.name SEPARATOR ', ') AS roles
     FROM users u
     LEFT JOIN user_roles ur ON ur.user_id = u.id
     LEFT JOIN roles r ON r.id = ur.role_id
     WHERE $whereStr
     GROUP BY u.id
     ORDER BY u.full_name
     LIMIT ? OFFSET ?",
    [...$bind, $pager['perPage'], $pager['offset']]
);

ob_start();
?>
<div class="page-header">
  <div>
    <h1 class="page-title">Users</h1>
    <p class="page-sub" style="color:var(--clr-text-muted);margin:0;"><?= number_format($total) ?> user(s)</p>
  </div>
  <div class="page-actions">
    <a href="<?= APP_BASE ?>/users/create" class="btn btn-primary">
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
      Add User
    </a>
  </div>
</div>

<div class="card">
  <!-- Toolbar -->
  <div class="table-toolbar">
    <form method="GET" action="<?= APP_BASE ?>/users" style="display:flex;gap:var(--space-3);flex-wrap:wrap;">
      <input type="search" name="q" class="table-search" placeholder="Search name, username, email…" value="<?= Helpers::h($search) ?>">
      <select name="status" class="form-control" style="width:auto;">
        <option value="">All Status</option>
        <option value="active"   <?= $status === 'active'   ? 'selected' : '' ?>>Active</option>
        <option value="blocked"  <?= $status === 'blocked'  ? 'selected' : '' ?>>Blocked</option>
        <option value="inactive" <?= $status === 'inactive' ? 'selected' : '' ?>>Inactive</option>
      </select>
      <button type="submit" class="btn btn-secondary">Filter</button>
      <?php if ($search || $status): ?>
        <a href="<?= APP_BASE ?>/users" class="btn btn-ghost">Clear</a>
      <?php endif; ?>
    </form>
    <button type="button" class="btn btn-ghost btn-sm" onclick="CLMS.export.csv('usersTable','users-export')">
      Export CSV
    </button>
  </div>

  <!-- Table -->
  <div class="table-wrapper">
    <table class="data-table" id="usersTable">
      <thead>
        <tr>
          <th>Username</th>
          <th>Full Name</th>
          <th>Email</th>
          <th>Mobile</th>
          <th>Roles</th>
          <th>Status</th>
          <th>Last Login</th>
          <th style="width:120px">Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($users as $u): ?>
        <tr>
          <td><?= Helpers::h($u['username']) ?></td>
          <td><?= Helpers::h($u['full_name']) ?></td>
          <td><?= Helpers::h($u['email'] ?? '—') ?></td>
          <td><?= Helpers::h($u['mobile'] ?? '—') ?></td>
          <td>
            <?php foreach (array_filter(explode(', ', $u['roles'] ?? '')) as $role): ?>
              <span class="badge badge-pending" style="margin:1px"><?= Helpers::h($role) ?></span>
            <?php endforeach; ?>
          </td>
          <td>
            <?php if ($u['is_blocked']): ?>
              <span class="badge badge-rejected">Blocked</span>
            <?php elseif (!$u['is_active']): ?>
              <span class="badge badge-inactive">Inactive</span>
            <?php else: ?>
              <span class="badge badge-active">Active</span>
            <?php endif; ?>
          </td>
          <td><?= $u['last_login_at'] ? Helpers::dateDisplay($u['last_login_at']) : '—' ?></td>
          <td>
            <div style="display:flex;gap:var(--space-2)">
              <a href="<?= APP_BASE ?>/users/<?= $u['id'] ?>/edit" class="btn btn-secondary btn-sm" title="Edit">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
              </a>
              <form method="POST" action="<?= APP_BASE ?>/users/<?= $u['id'] ?>/toggle-block" style="display:inline">
                <input type="hidden" name="<?= CSRF_KEY ?>" value="<?= Helpers::h(Auth::csrfToken()) ?>">
                <button type="submit" class="btn btn-ghost btn-sm" title="<?= $u['is_blocked'] ? 'Unblock' : 'Block' ?>">
                  <?php if ($u['is_blocked']): ?>
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 8h1a4 4 0 0 1 0 8h-1"/><path d="M2 8h16v9a4 4 0 0 1-4 4H6a4 4 0 0 1-4-4V8z"/><line x1="6" y1="1" x2="6" y2="4"/><line x1="10" y1="1" x2="10" y2="4"/><line x1="14" y1="1" x2="14" y2="4"/></svg>
                  <?php else: ?>
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="var(--clr-warning)" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="4.93" y1="4.93" x2="19.07" y2="19.07"/></svg>
                  <?php endif; ?>
                </button>
              </form>
              <?php if (Auth::realHasRole(['super_admin', 'admin']) && !Auth::isImpersonating() && $u['id'] !== Auth::user()['id']): ?>
              <a href="<?= APP_BASE ?>/impersonate?prefill=<?= $u['id'] ?>" class="btn btn-ghost btn-sm" title="Switch to this user"
                 style="color:var(--clr-warning)">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                  <circle cx="9" cy="7" r="4"/>
                  <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                  <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                </svg>
              </a>
              <?php endif; ?>
              <?php if ($u['id'] !== Auth::user()['id']): ?>
              <button
                class="btn btn-danger btn-sm btn-delete"
                data-url="/users/<?= $u['id'] ?>/delete"
                data-confirm="Delete user &quot;<?= Helpers::h($u['username']) ?>&quot;? This cannot be undone."
                title="Delete"
              >
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/><path d="M10 11v6M14 11v6"/><path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/></svg>
              </button>
              <?php endif; ?>
            </div>
          </td>
        </tr>
        <?php endforeach; ?>
        <?php if (empty($users)): ?>
        <tr><td colspan="8" style="text-align:center;padding:var(--space-8);color:var(--clr-text-muted)">No users found.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>

  <!-- Pagination -->
  <?php if ($pager['totalPages'] > 1): ?>
  <div class="card-footer">
    <nav class="pagination" aria-label="Pagination">
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
$pageTitle   = 'Users';
$activeMenu  = 'users';
$breadcrumbs = [['label' => 'Users']];
include CLMS_ROOT . '/templates/base.html.php';
