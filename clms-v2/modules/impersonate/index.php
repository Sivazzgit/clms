<?php
/**
 * CLMS 2.0 — Impersonate: User Picker
 * GET  /impersonate        → show user list to switch to
 * POST /impersonate        → start impersonation
 */
Auth::requireRole(['super_admin', 'admin']);

$companyId   = $_SESSION['company_id'];
$isSuperAdmin = Auth::realHasRole('super_admin');
$errors       = [];

// Handle POST — start impersonation
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    Auth::requireCsrf();
    $targetId = (int)($_POST['target_user_id'] ?? 0);
    $reason   = Helpers::clean($_POST['reason'] ?? '');

    if (!$targetId) {
        $errors[] = 'Please select a user to impersonate.';
    } else {
        $result = Auth::startImpersonation($targetId, $reason);
        if ($result === true) {
            $_SESSION['flash_success'] = 'Now acting as ' . Helpers::h($_SESSION['full_name']) . '. Use the banner to exit.';
            Helpers::redirect('/dashboard');
        } else {
            $errors[] = $result;
        }
    }
}

// Build user list
// super_admin: all companies' users
// admin: users in own company only, excluding super_admin
$where = ['u.is_active = 1', 'u.id != ?'];
$bind  = [$_SESSION['real_user_id'] ?? $_SESSION['user_id']];

if (!$isSuperAdmin) {
    $where[] = 'u.company_id = ?';
    $bind[]  = $companyId;
    // exclude super_admin and admin targets
    $where[] = "u.id NOT IN (
        SELECT ur2.user_id FROM user_roles ur2
        JOIN roles r2 ON r2.id=ur2.role_id
        WHERE r2.code IN ('super_admin','admin')
    )";
}

$whereStr = implode(' AND ', $where);

$users = DB::rows(
    "SELECT u.id, u.username, u.full_name, u.email, c.name AS company_name,
            GROUP_CONCAT(r.name ORDER BY r.name SEPARATOR ', ') AS roles
     FROM users u
     JOIN companies c ON c.id = u.company_id
     LEFT JOIN user_roles ur ON ur.user_id = u.id
     LEFT JOIN roles r ON r.id = ur.role_id
     WHERE $whereStr
     GROUP BY u.id
     ORDER BY c.name, u.full_name",
    $bind
);

ob_start();
?>
<div class="page-header">
  <div>
    <h1 class="page-title">Switch User Identity</h1>
    <p class="page-sub" style="color:var(--clr-text-muted);margin:0">
      Select a user to impersonate. All actions will be performed on their behalf and logged.
    </p>
  </div>
</div>

<?php foreach ($errors as $e): ?>
  <div class="alert alert-danger" style="margin-bottom:var(--space-3)"><?= Helpers::h($e) ?></div>
<?php endforeach; ?>

<?php if (Auth::isImpersonating()): ?>
  <div class="alert alert-warning" style="margin-bottom:var(--space-4)">
    You are already impersonating <strong><?= Helpers::h($_SESSION['full_name']) ?></strong>.
    <a href="/impersonate/exit" style="margin-left:var(--space-3)">Exit impersonation first</a>.
  </div>
<?php endif; ?>

<form method="POST" action="/impersonate" id="impersonateForm">
  <input type="hidden" name="<?= CSRF_KEY ?>" value="<?= Helpers::h(Auth::csrfToken()) ?>">
  <input type="hidden" name="target_user_id" id="targetUserId" value="">

  <div class="card" style="margin-bottom:var(--space-4)">
    <div class="card-header"><h3 class="card-title">Select User</h3></div>
    <div class="table-toolbar">
      <input type="search" class="table-search" placeholder="Filter by name, username, role…"
             id="userFilter" style="max-width:360px">
    </div>
    <div class="table-wrapper">
      <table class="data-table" id="usersTable">
        <thead>
          <tr>
            <?php if ($isSuperAdmin): ?><th>Company</th><?php endif; ?>
            <th>Username</th>
            <th>Full Name</th>
            <th>Roles</th>
            <th>Email</th>
            <th></th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($users as $u): ?>
          <tr>
            <?php if ($isSuperAdmin): ?><td><?= Helpers::h($u['company_name']) ?></td><?php endif; ?>
            <td><?= Helpers::h($u['username']) ?></td>
            <td><?= Helpers::h($u['full_name']) ?></td>
            <td>
              <?php foreach (array_filter(explode(', ', $u['roles'] ?? '')) as $r): ?>
                <span class="badge badge-pending" style="margin:1px"><?= Helpers::h($r) ?></span>
              <?php endforeach; ?>
            </td>
            <td><?= Helpers::h($u['email'] ?? '—') ?></td>
            <td>
              <button type="button" class="btn btn-secondary btn-sm btn-pick-user"
                      data-id="<?= $u['id'] ?>"
                      data-name="<?= Helpers::h($u['full_name']) ?>">
                Switch to this user
              </button>
            </td>
          </tr>
          <?php endforeach; ?>
          <?php if (!$users): ?>
            <tr><td colspan="6" style="text-align:center;color:var(--clr-text-muted)">No users available.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>

  <!-- Confirm modal (hidden until user picks) -->
  <div id="confirmPanel" style="display:none" class="card">
    <div class="card-body">
      <h3 style="margin:0 0 var(--space-3)">Confirm Impersonation</h3>
      <p>You are about to act as <strong id="confirmName"></strong>.</p>
      <div class="form-group" style="max-width:420px">
        <label class="form-label">Reason <span style="color:var(--clr-text-muted);font-size:var(--text-sm)">(optional — shown in log)</span></label>
        <input type="text" name="reason" class="form-control" maxlength="255"
               placeholder="e.g. Support ticket #123">
      </div>
      <div style="display:flex;gap:var(--space-3)">
        <button type="submit" class="btn btn-danger">Yes, switch identity</button>
        <button type="button" class="btn btn-ghost" id="cancelPick">Cancel</button>
      </div>
    </div>
  </div>
</form>

<script>
(function () {
  const rows    = document.querySelectorAll('.btn-pick-user');
  const panel   = document.getElementById('confirmPanel');
  const nameEl  = document.getElementById('confirmName');
  const idInput = document.getElementById('targetUserId');
  const cancel  = document.getElementById('cancelPick');
  const filter  = document.getElementById('userFilter');

  rows.forEach(btn => btn.addEventListener('click', () => {
    idInput.value  = btn.dataset.id;
    nameEl.textContent = btn.dataset.name;
    panel.style.display = '';
    panel.scrollIntoView({ behavior: 'smooth', block: 'start' });
  }));

  cancel.addEventListener('click', () => {
    panel.style.display = 'none';
    idInput.value = '';
  });

  // Live filter
  filter.addEventListener('input', () => {
    const q = filter.value.toLowerCase();
    document.querySelectorAll('#usersTable tbody tr').forEach(row => {
      row.style.display = row.textContent.toLowerCase().includes(q) ? '' : 'none';
    });
  });

  // Pre-select user if ?prefill=ID is in URL
  const prefillId = new URLSearchParams(window.location.search).get('prefill');
  if (prefillId) {
    const btn = document.querySelector(`.btn-pick-user[data-id="${prefillId}"]`);
    if (btn) btn.click();
  }
})();
</script>

<?php
$pageContent = ob_get_clean();
$pageTitle   = 'Switch User';
$activeMenu  = 'impersonate';
$breadcrumbs = [['label' => 'Switch User']];
include CLMS_ROOT . '/templates/base.html.php';
