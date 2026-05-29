<?php
/**
 * CLMS 2.0 — User Add / Edit form (HR Admin only)
 * GET  /users/create       → blank form
 * GET  /users/{id}/edit    → populated form
 * POST either URL          → save
 */
Auth::requireRole('hr_admin');

$id      = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$isEdit  = $id > 0;
$errors  = [];

// Load record for edit
$record = $isEdit
    ? DB::row('SELECT * FROM users WHERE id = ?', [$id])
    : null;

if ($isEdit && !$record) {
    Helpers::redirect('/users', 'User not found.', 'error');
}

// All roles
$allRoles = DB::rows('SELECT id, code, name FROM roles ORDER BY name');

// Sections (scoped to current company)
$allSections = DB::rows('SELECT id, name FROM sections WHERE company_id = ? AND is_active = 1 ORDER BY name', [$_SESSION['company_id']]);

// All vendors (for contractor role assignment)
$allVendors = DB::rows("SELECT id, vendor_code, name FROM vendors WHERE status='active' ORDER BY name");

// Current role & section assignments
$currentRoles    = $isEdit ? array_column(DB::rows('SELECT role_id, vendor_id FROM user_roles WHERE user_id = ?', [$id]), null, 'role_id') : [];
$currentSections = $isEdit ? array_column(DB::rows('SELECT section_id FROM user_sections WHERE user_id = ?', [$id]), 'section_id') : [];
$currentPlants   = $isEdit ? array_column(DB::rows('SELECT plant_id FROM user_plants WHERE user_id = ?', [$id]), 'plant_id') : [];

// Resolve contractor's pre-assigned vendor_id from user_roles
$contractorRoleId   = null;
foreach ($allRoles as $r) { if ($r['code'] === 'contractor') { $contractorRoleId = $r['id']; break; } }
$contractorVendorId = ($contractorRoleId && isset($currentRoles[$contractorRoleId]))
    ? (int)($currentRoles[$contractorRoleId]['vendor_id'] ?? 0) : 0;

// All plants for this company
$allPlants = DB::rows('SELECT id, code, name FROM plants WHERE company_id = ? AND is_active = 1 ORDER BY code', [$_SESSION['company_id']]);

// ----------------------------------------------------------------
// POST — Save
// ----------------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    Auth::requireCsrf();

    $data = [
        'username'         => Helpers::clean($_POST['username'] ?? ''),
        'full_name'        => Helpers::clean($_POST['full_name'] ?? ''),
        'email'            => Helpers::clean($_POST['email'] ?? ''),
        'mobile'           => Helpers::clean($_POST['mobile'] ?? ''),
        'employee_code'    => Helpers::clean($_POST['employee_code'] ?? ''),
        'is_active'        => isset($_POST['is_active']) ? 1 : 0,
        'force_pwd_change' => isset($_POST['force_pwd_change']) ? 1 : 0,
    ];

    $selectedRoles    = array_map('intval', (array)($_POST['roles']    ?? []));
    $selectedSections = array_map('intval', (array)($_POST['sections'] ?? []));
    $selectedPlants   = array_map('intval', (array)($_POST['plants']   ?? []));
    $vendorId         = !empty($_POST['vendor_id']) ? (int)$_POST['vendor_id'] : null;
    $newPassword     = $_POST['password'] ?? '';

    // Validate
    if (empty($data['username']))  $errors['username']  = 'Username is required.';
    if (empty($data['full_name'])) $errors['full_name'] = 'Full name is required.';
    if (!$isEdit && empty($newPassword)) $errors['password'] = 'Password is required for new users.';
    if (!empty($newPassword) && strlen($newPassword) < 8) $errors['password'] = 'Password must be at least 8 characters.';
    if (!empty($data['email']) && !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) $errors['email'] = 'Enter a valid email address.';
    if (empty($selectedRoles)) $errors['roles'] = 'Assign at least one role.';

    // Unique username check
    $dupCheck = DB::value(
        'SELECT id FROM users WHERE username = ? AND id != ?',
        [$data['username'], $id]
    );
    if ($dupCheck) $errors['username'] = 'This username is already taken.';

    if (empty($errors)) {
        try {
            DB::transaction(function () use ($data, $id, $isEdit, $newPassword, $selectedRoles, $selectedSections, $selectedPlants, $vendorId) {
                if (!empty($newPassword)) {
                    $data['password_hash'] = password_hash($newPassword, PASSWORD_BCRYPT, ['cost' => BCRYPT_COST]);
                }
                $data['updated_at'] = date('Y-m-d H:i:s');

                if ($isEdit) {
                    $old = DB::row('SELECT * FROM users WHERE id = ?', [$id]);
                    DB::update('users', $data, ['id' => $id]);
                    AuditLogger::log('UPDATE', 'users', $id, AuditLogger::sanitize($old), AuditLogger::sanitize($data));
                } else {
                    $data['created_at'] = date('Y-m-d H:i:s');
                    $data['company_id']  = $_SESSION['company_id'];
                    $id = DB::insert('users', $data);
                    AuditLogger::log('INSERT', 'users', $id, null, AuditLogger::sanitize($data));
                }

                // Sync roles
                DB::execute('DELETE FROM user_roles WHERE user_id = ?', [$id]);
                foreach ($selectedRoles as $roleId) {
                    $vid = null;
                    // Check if this is the contractor role
                    $roleCode = DB::value('SELECT code FROM roles WHERE id = ?', [$roleId]);
                    if ($roleCode === 'contractor') $vid = $vendorId;
                    DB::insert('user_roles', ['user_id' => $id, 'role_id' => $roleId, 'vendor_id' => $vid]);
                }

                // Sync sections
                DB::execute('DELETE FROM user_sections WHERE user_id = ?', [$id]);
                foreach ($selectedSections as $sectionId) {
                    DB::insert('user_sections', ['user_id' => $id, 'section_id' => $sectionId]);
                }

                // Sync plants
                DB::execute('DELETE FROM user_plants WHERE user_id = ?', [$id]);
                foreach ($selectedPlants as $plantId) {
                    DB::insert('user_plants', ['user_id' => $id, 'plant_id' => $plantId, 'assigned_by' => $_SESSION['user_id']]);
                }
            });

            Helpers::redirect('/users', ($isEdit ? 'User updated.' : 'User created.'));
        } catch (Throwable $e) {
            $errors['_general'] = 'Save failed: ' . $e->getMessage();
        }
    }
}

$title = $isEdit ? 'Edit User' : 'Add User';

ob_start();
?>

<div class="page-header">
  <div>
    <h1 class="page-title"><?= $title ?></h1>
  </div>
  <div class="page-actions">
    <a href="<?= APP_BASE ?>/users" class="btn btn-ghost">Cancel</a>
  </div>
</div>

<form method="POST" action="<?= $isEdit ? "/users/$id/edit" : '/users/create' ?>" id="userForm" novalidate>
  <input type="hidden" name="<?= CSRF_KEY ?>" value="<?= Helpers::h(Auth::csrfToken()) ?>">

  <?php if (!empty($errors['_general'])): ?>
    <div class="alert alert-danger"><?= Helpers::h($errors['_general']) ?></div>
  <?php endif; ?>

  <!-- Basic Info -->
  <div class="card" style="margin-bottom:var(--space-6)">
    <div class="card-header"><h2 class="card-title">Basic Information</h2></div>
    <div class="card-body">
      <div class="form-grid">

        <div class="form-group">
          <label class="form-label required" for="username">Username</label>
          <input type="text" id="username" name="username" class="form-control <?= isset($errors['username']) ? 'is-invalid' : '' ?>"
            value="<?= Helpers::h($record['username'] ?? $_POST['username'] ?? '') ?>"
            data-validate="required|minlen:3|maxlen:50"
            autocomplete="off" <?= $isEdit ? 'readonly' : '' ?>>
          <span class="form-error"><?= $errors['username'] ?? '' ?></span>
        </div>

        <div class="form-group">
          <label class="form-label required" for="full_name">Full Name</label>
          <input type="text" id="full_name" name="full_name" class="form-control <?= isset($errors['full_name']) ? 'is-invalid' : '' ?>"
            value="<?= Helpers::h($record['full_name'] ?? $_POST['full_name'] ?? '') ?>"
            data-validate="required|minlen:2">
          <span class="form-error"><?= $errors['full_name'] ?? '' ?></span>
        </div>

        <div class="form-group">
          <label class="form-label" for="email">Email</label>
          <input type="email" id="email" name="email" class="form-control <?= isset($errors['email']) ? 'is-invalid' : '' ?>"
            value="<?= Helpers::h($record['email'] ?? $_POST['email'] ?? '') ?>"
            data-validate="email">
          <span class="form-error"><?= $errors['email'] ?? '' ?></span>
        </div>

        <div class="form-group">
          <label class="form-label" for="mobile">Mobile</label>
          <input type="tel" id="mobile" name="mobile" class="form-control"
            value="<?= Helpers::h($record['mobile'] ?? $_POST['mobile'] ?? '') ?>"
            data-validate="mobile" maxlength="10" data-format="digits">
          <span class="form-error"></span>
        </div>

        <div class="form-group">
          <label class="form-label" for="employee_code">Employee Code</label>
          <input type="text" id="employee_code" name="employee_code" class="form-control"
            value="<?= Helpers::h($record['employee_code'] ?? $_POST['employee_code'] ?? '') ?>"
            data-format="uppercase">
        </div>

        <div class="form-group" style="align-self:end">
          <label style="display:flex;align-items:center;gap:var(--space-2);cursor:pointer">
            <input type="checkbox" name="is_active" value="1" <?= ($record['is_active'] ?? 1) ? 'checked' : '' ?>>
            <span>Active account</span>
          </label>
          <label style="display:flex;align-items:center;gap:var(--space-2);cursor:pointer;margin-top:var(--space-2)">
            <input type="checkbox" name="force_pwd_change" value="1" <?= ($record['force_pwd_change'] ?? 0) ? 'checked' : '' ?>>
            <span>Force password change on next login</span>
          </label>
        </div>

      </div>
    </div>
  </div>

  <!-- Password -->
  <div class="card" style="margin-bottom:var(--space-6)">
    <div class="card-header">
      <h2 class="card-title"><?= $isEdit ? 'Change Password' : 'Set Password' ?></h2>
      <?php if ($isEdit): ?><p class="card-subtitle" style="font-size:var(--text-sm);color:var(--clr-text-muted)">Leave blank to keep current password.</p><?php endif; ?>
    </div>
    <div class="card-body">
      <div class="form-grid" style="grid-template-columns:1fr 1fr">
        <div class="form-group">
          <label class="form-label <?= !$isEdit ? 'required' : '' ?>" for="password">Password</label>
          <input type="password" id="password" name="password" class="form-control <?= isset($errors['password']) ? 'is-invalid' : '' ?>"
            data-validate="<?= !$isEdit ? 'required|minlen:8' : 'minlen:8' ?>"
            autocomplete="new-password">
          <span class="form-error"><?= $errors['password'] ?? '' ?></span>
        </div>
        <div class="form-group">
          <label class="form-label" for="password_confirm">Confirm Password</label>
          <input type="password" id="password_confirm" name="password_confirm" class="form-control"
            autocomplete="new-password">
          <span class="form-error"></span>
        </div>
      </div>
    </div>
  </div>

  <!-- Role Assignment -->
  <div class="card" style="margin-bottom:var(--space-6)">
    <div class="card-header">
      <h2 class="card-title">Role Assignment</h2>
    </div>
    <div class="card-body">
      <?php if (isset($errors['roles'])): ?>
        <div class="alert alert-danger" style="margin-bottom:var(--space-4)"><?= Helpers::h($errors['roles']) ?></div>
      <?php endif; ?>
      <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:var(--space-4);margin-bottom:var(--space-5)">
        <?php foreach ($allRoles as $role): ?>
          <?php $checked = isset($currentRoles[$role['id']]) || in_array($role['id'], array_map('intval', (array)($_POST['roles'] ?? []))); ?>
          <label style="display:flex;align-items:center;gap:var(--space-2);cursor:pointer;padding:var(--space-3);border:var(--border-base);border-radius:var(--radius-base)">
            <input type="checkbox" name="roles[]" value="<?= $role['id'] ?>" <?= $checked ? 'checked' : '' ?> data-role-code="<?= Helpers::h($role['code']) ?>">
            <span><?= Helpers::h($role['name']) ?></span>
          </label>
        <?php endforeach; ?>
      </div>

      <!-- Vendor assignment (shown when contractor role is checked) -->
      <div id="vendorRow" style="display:none">
        <div class="form-group" style="max-width:400px">
          <label class="form-label required" for="vendor_id">Assign Contractor</label>
          <select id="vendor_id" name="vendor_id" class="form-control">
            <option value="">— Select contractor —</option>
            <?php foreach ($allVendors as $v): ?>
              <option value="<?= $v['id'] ?>" <?= ($contractorVendorId == $v['id'] || ($_POST['vendor_id'] ?? '') == $v['id']) ? 'selected' : '' ?>>
                <?= Helpers::h($v['vendor_code'] . ' — ' . $v['name']) ?>
              </option>
            <?php endforeach; ?>
          </select>
          <span class="form-error"></span>
        </div>
      </div>
    </div>
  </div>

  <!-- Section Assignment -->
  <div class="card" style="margin-bottom:var(--space-6)" id="sectionCard">
    <div class="card-header">
      <h2 class="card-title">Section Assignment</h2>
      <p class="card-subtitle" style="font-size:var(--text-sm);color:var(--clr-text-muted)">Required when the user has the Section In-charge role.</p>
    </div>
    <div class="card-body">
      <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:var(--space-3)">
        <?php foreach ($allSections as $sec): ?>
          <?php $checked = in_array($sec['id'], $currentSections) || in_array($sec['id'], array_map('intval', (array)($_POST['sections'] ?? []))); ?>
          <label style="display:flex;align-items:center;gap:var(--space-2);cursor:pointer">
            <input type="checkbox" name="sections[]" value="<?= $sec['id'] ?>" <?= $checked ? 'checked' : '' ?>>
            <span><?= Helpers::h($sec['name']) ?></span>
          </label>
        <?php endforeach; ?>
      </div>
    </div>
  </div>

  <!-- Plant Assignment -->
  <?php if ($allPlants): ?>
  <div class="card" style="margin-bottom:var(--space-6)" id="plantCard">
    <div class="card-header">
      <h2 class="card-title">Plant Assignment</h2>
      <p class="card-subtitle" style="font-size:var(--text-sm);color:var(--clr-text-muted)">Assign the plant(s) this user belongs to. Relevant for Plant Head, HOD, Section In-charge, and Group Head roles.</p>
    </div>
    <div class="card-body">
      <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(220px,1fr));gap:var(--space-3)">
        <?php foreach ($allPlants as $pl): ?>
          <?php $checked = in_array($pl['id'], $currentPlants) || in_array($pl['id'], array_map('intval', (array)($_POST['plants'] ?? []))); ?>
          <label style="display:flex;align-items:center;gap:var(--space-2);cursor:pointer;padding:var(--space-3);border:var(--border-base);border-radius:var(--radius-base)">
            <input type="checkbox" name="plants[]" value="<?= $pl['id'] ?>" <?= $checked ? 'checked' : '' ?>>
            <span><strong><?= Helpers::h($pl['code']) ?></strong> — <?= Helpers::h($pl['name']) ?></span>
          </label>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
  <?php endif; ?>

  <!-- Submit -->
  <div style="display:flex;gap:var(--space-3)">
    <button type="submit" class="btn btn-primary">
      <?= $isEdit ? 'Update User' : 'Create User' ?>
    </button>
    <a href="<?= APP_BASE ?>/users" class="btn btn-ghost">Cancel</a>
  </div>

</form>

<script>
// Show vendor row when contractor role selected
function toggleVendorRow() {
  const contractorChecked = document.querySelector('[data-role-code="contractor"]')?.checked;
  document.getElementById('vendorRow').style.display = contractorChecked ? '' : 'none';
  if (contractorChecked) {
    document.getElementById('vendor_id').setAttribute('data-validate', 'required');
  } else {
    document.getElementById('vendor_id').removeAttribute('data-validate');
  }
}

document.querySelectorAll('input[name="roles[]"]').forEach(cb => {
  cb.addEventListener('change', toggleVendorRow);
});
toggleVendorRow();

// Password match validation
document.getElementById('userForm').addEventListener('submit', function(e) {
  const pwd  = document.getElementById('password').value;
  const conf = document.getElementById('password_confirm').value;
  if (pwd && pwd !== conf) {
    e.preventDefault();
    CLMS.toast.error('Passwords do not match.');
    document.getElementById('password_confirm').classList.add('is-invalid');
  }
});
</script>

<?php
$pageContent = ob_get_clean();
$pageTitle   = $title;
$activeMenu  = 'users';
$breadcrumbs = [['label' => 'Users', 'url' => '/users'], ['label' => $title]];
include CLMS_ROOT . '/templates/base.html.php';
