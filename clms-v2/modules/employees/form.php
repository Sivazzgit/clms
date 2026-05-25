<?php
/**
 * CLMS 2.0 — Employee Add / Edit form
 * HR Admin: all vendors  |  Contractor: own vendor only
 */
Auth::requireRole('hr_admin', 'contractor');

$user    = Auth::user();
$isAdmin = Auth::hasRole('hr_admin');
$isContr = Auth::hasRole('contractor');

$id     = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$isEdit = $id > 0;
$errors = [];

// Load record
$record = null;
if ($isEdit) {
    $record = DB::row('SELECT * FROM employees WHERE id = ? AND company_id = ?', [$id, $_SESSION['company_id']]);
    if (!$record) Helpers::redirect('/employees', 'Employee not found.', 'error');
    // Contractor can only edit their own employees
    if ($isContr && $record['vendor_id'] != $user['vendor_id']) {
        Helpers::redirect('/employees', 'Access denied.', 'error');
    }
}

// Load form options
if ($isAdmin) {
    $vendors = DB::rows(
        "SELECT id, vendor_code, name FROM vendors WHERE company_id=? AND status='active' ORDER BY name",
        [$_SESSION['company_id']]
    );
} else {
    $vendors = DB::rows(
        "SELECT id, vendor_code, name FROM vendors WHERE id=?",
        [$user['vendor_id']]
    );
}
$categories = DB::rows("SELECT id, code, name FROM labour_categories WHERE company_id=? AND is_active=1 ORDER BY name", [$_SESSION['company_id']]);

// ----------------------------------------------------------------
// POST
// ----------------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    Auth::requireCsrf();

    $data = [
        'company_id'               => (int)$_SESSION['company_id'],
        'vendor_id'                => (int)$_POST['vendor_id'],
        'category_id'              => $_POST['category_id'] ? (int)$_POST['category_id'] : null,
        'first_name'               => Helpers::clean($_POST['first_name'] ?? ''),
        'middle_name'              => Helpers::clean($_POST['middle_name'] ?? '') ?: null,
        'last_name'                => Helpers::clean($_POST['last_name'] ?? ''),
        'gender'                   => Helpers::clean($_POST['gender'] ?? '') ?: null,
        'dob'                      => Helpers::dateSql($_POST['dob'] ?? ''),
        'father_name'              => Helpers::clean($_POST['father_name'] ?? '') ?: null,
        'permanent_address'        => Helpers::clean($_POST['permanent_address'] ?? '') ?: null,
        'current_address'          => Helpers::clean($_POST['current_address'] ?? '') ?: null,
        'mobile'                   => Helpers::clean($_POST['mobile'] ?? '') ?: null,
        'emergency_contact_name'   => Helpers::clean($_POST['emergency_contact_name'] ?? '') ?: null,
        'emergency_contact_mobile' => Helpers::clean($_POST['emergency_contact_mobile'] ?? '') ?: null,
        'aadhaar_no'               => preg_replace('/\D/', '', $_POST['aadhaar_no'] ?? '') ?: null,
        'pan_no'                   => strtoupper(Helpers::clean($_POST['pan_no'] ?? '')) ?: null,
        'esi_no'                   => Helpers::clean($_POST['esi_no'] ?? '') ?: null,
        'pf_uan'                   => Helpers::clean($_POST['pf_uan'] ?? '') ?: null,
        'bank_name'                => Helpers::clean($_POST['bank_name'] ?? '') ?: null,
        'bank_account_no'          => Helpers::clean($_POST['bank_account_no'] ?? '') ?: null,
        'bank_ifsc'                => strtoupper(Helpers::clean($_POST['bank_ifsc'] ?? '')) ?: null,
        'bank_branch'              => Helpers::clean($_POST['bank_branch'] ?? '') ?: null,
        'qualification'            => Helpers::clean($_POST['qualification'] ?? '') ?: null,
        'trade'                    => Helpers::clean($_POST['trade'] ?? '') ?: null,
        'doj_vendor'               => Helpers::dateSql($_POST['doj_vendor'] ?? ''),
        'doj_plant'                => Helpers::dateSql($_POST['doj_plant'] ?? ''),
        'biometric_id'             => Helpers::clean($_POST['biometric_id'] ?? '') ?: null,
        'status'                   => 'pending_approval',
    ];

    // Contractor cannot change status — stays pending_approval until HR approves
    if ($isAdmin && $isEdit) {
        $allowedStatuses = ['pending_approval','active','inactive'];
        $ps = Helpers::clean($_POST['status'] ?? '');
        if (in_array($ps, $allowedStatuses)) $data['status'] = $ps;
    }

    // Contractor isolation
    if ($isContr) {
        $data['vendor_id'] = (int)$user['vendor_id'];
    }

    // Validate
    if (empty($data['first_name']))  $errors['first_name']  = 'First name is required.';
    if (empty($data['last_name']))   $errors['last_name']   = 'Last name is required.';
    if (empty($data['vendor_id']))   $errors['vendor_id']   = 'Contractor is required.';

    if ($data['aadhaar_no'] && strlen($data['aadhaar_no']) !== 12) {
        $errors['aadhaar_no'] = 'Aadhaar must be 12 digits.';
    }
    if ($data['pan_no'] && !preg_match('/^[A-Z]{5}[0-9]{4}[A-Z]{1}$/', $data['pan_no'])) {
        $errors['pan_no'] = 'PAN format is invalid.';
    }
    if ($data['bank_ifsc'] && !preg_match('/^[A-Z]{4}0[A-Z0-9]{6}$/', $data['bank_ifsc'])) {
        $errors['bank_ifsc'] = 'IFSC code format is invalid.';
    }

    // Check Aadhaar uniqueness
    if ($data['aadhaar_no']) {
        $dupQuery = 'SELECT id FROM employees WHERE aadhaar_no = ? AND company_id = ?';
        $dupBind  = [$data['aadhaar_no'], $_SESSION['company_id']];
        if ($isEdit) { $dupQuery .= ' AND id != ?'; $dupBind[] = $id; }
        if (DB::value($dupQuery, $dupBind)) $errors['aadhaar_no'] = 'This Aadhaar number is already registered.';
    }

    if (empty($errors)) {
        try {
            DB::transaction(function () use ($data, $id, $isEdit) {
                // Handle photo upload
                if (!empty($_FILES['photo']['name']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
                    $vendorCode = DB::value('SELECT vendor_code FROM vendors WHERE id=?', [$data['vendor_id']]);
                    $sub  = 'uploads/employees/' . $vendorCode;
                    try {
                        $path = Helpers::saveUpload($_FILES['photo'], $sub, ['image/jpeg', 'image/png']);
                        if ($path) $data['photo_path'] = $path;
                    } catch (Throwable $e) {
                        // Non-fatal: photo upload failure doesn't block employee save
                    }
                }

                if ($isEdit) {
                    $old = DB::row('SELECT * FROM employees WHERE id = ?', [$id]);
                    DB::update('employees', $data, ['id' => $id]);
                    AuditLogger::log('UPDATE', 'employees', null, (string)$id, AuditLogger::sanitize($old), AuditLogger::sanitize($data));
                } else {
                    $data['added_by']       = Auth::user()['id'];
                    $data['created_at']     = date('Y-m-d H:i:s');
                    // Generate code: EMP-VND001-0001
                    $vendorCode = DB::value('SELECT vendor_code FROM vendors WHERE id=?', [$data['vendor_id']]);
                    $prefix     = 'EMP-' . $vendorCode . '-';
                    $maxCode    = DB::value(
                        "SELECT MAX(CAST(SUBSTRING(employee_code,?) AS UNSIGNED)) FROM employees WHERE employee_code LIKE ? AND company_id=?",
                        [strlen($prefix) + 1, $prefix . '%', $data['company_id']]
                    );
                    $data['employee_code'] = $prefix . str_pad((int)$maxCode + 1, 4, '0', STR_PAD_LEFT);

                    $newId = DB::insert('employees', $data);
                    AuditLogger::log('INSERT', 'employees', null, (string)$newId, null, AuditLogger::sanitize($data));
                }
            });

            $msg = $isEdit
                ? 'Employee record updated.'
                : 'Employee added. Pending HR approval before activation.';
            Helpers::redirect('/employees', $msg);

        } catch (Throwable $e) {
            $errors['_general'] = 'Save failed: ' . $e->getMessage();
        }
    }
}

$title = $isEdit ? 'Edit Employee' : 'Add Employee';

ob_start();
?>

<div class="page-header">
  <div>
    <h1 class="page-title"><?= $title ?></h1>
    <?php if ($isEdit): ?>
      <p class="page-sub" style="color:var(--clr-text-muted);margin:0;">
        Code: <strong><?= Helpers::h($record['employee_code']) ?></strong>
        &nbsp;|&nbsp;
        <span class="badge badge-<?= $record['status'] === 'active' ? 'active' : ($record['status'] === 'pending_approval' ? 'pending' : 'inactive') ?>">
          <?= ucwords(str_replace('_', ' ', $record['status'])) ?>
        </span>
      </p>
    <?php endif; ?>
  </div>
  <div class="page-actions">
    <?php if ($isEdit && $isAdmin && $record['status'] === 'active'): ?>
      <a href="/employees/<?= $id ?>/separate" class="btn btn-ghost">Record Separation</a>
    <?php endif; ?>
    <a href="/employees" class="btn btn-ghost">Cancel</a>
  </div>
</div>

<?php if (!empty($errors['_general'])): ?>
  <div class="alert alert-danger"><?= Helpers::h($errors['_general']) ?></div>
<?php endif; ?>

<?php if ($isEdit && $isAdmin && $record['status'] === 'pending_approval'): ?>
<div class="alert alert-warning" style="display:flex;align-items:center;justify-content:space-between;margin-bottom:var(--space-4)">
  <span>This employee is <strong>pending approval</strong>. Review and activate when ready.</span>
  <form method="POST" action="/employees/<?= $id ?>/approve">
    <input type="hidden" name="<?= CSRF_KEY ?>" value="<?= Helpers::h(Auth::csrfToken()) ?>">
    <button type="submit" class="btn btn-success btn-sm">Approve &amp; Activate</button>
  </form>
</div>
<?php endif; ?>

<form method="POST"
      action="<?= $isEdit ? "/employees/$id/edit" : '/employees/create' ?>"
      enctype="multipart/form-data"
      id="empForm" novalidate>
  <input type="hidden" name="<?= CSRF_KEY ?>" value="<?= Helpers::h(Auth::csrfToken()) ?>">

  <!-- SECTION 1: Employment Details -->
  <div class="card" style="margin-bottom:var(--space-6)">
    <div class="card-header"><h2 class="card-title">Employment Details</h2></div>
    <div class="card-body">
      <div class="form-grid">

        <div class="form-group">
          <label class="form-label required" for="vendor_id">Contractor</label>
          <select id="vendor_id" name="vendor_id" class="form-control" <?= $isContr ? 'disabled' : '' ?>>
            <option value="">— Select Contractor —</option>
            <?php foreach ($vendors as $v): ?>
              <option value="<?= $v['id'] ?>" <?= ((int)($record['vendor_id'] ?? ($isContr ? $user['vendor_id'] : 0))) === (int)$v['id'] ? 'selected' : '' ?>>
                <?= Helpers::h($v['vendor_code']) ?> — <?= Helpers::h($v['name']) ?>
              </option>
            <?php endforeach; ?>
          </select>
          <?php if ($isContr): ?>
            <input type="hidden" name="vendor_id" value="<?= (int)$user['vendor_id'] ?>">
          <?php endif; ?>
          <span class="form-error"><?= $errors['vendor_id'] ?? '' ?></span>
        </div>

        <div class="form-group">
          <label class="form-label" for="category_id">Labour Category</label>
          <select id="category_id" name="category_id" class="form-control">
            <option value="">— Select Category —</option>
            <?php foreach ($categories as $cat): ?>
              <option value="<?= $cat['id'] ?>" <?= ((int)($record['category_id'] ?? 0)) === (int)$cat['id'] ? 'selected' : '' ?>>
                <?= Helpers::h($cat['name']) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="form-group">
          <label class="form-label" for="biometric_id">Biometric ID</label>
          <input type="text" id="biometric_id" name="biometric_id" class="form-control"
            value="<?= Helpers::h($record['biometric_id'] ?? '') ?>">
        </div>

        <div class="form-group">
          <label class="form-label" for="doj_vendor">Date of Joining (Contractor)</label>
          <input type="date" id="doj_vendor" name="doj_vendor" class="form-control"
            value="<?= Helpers::h($record['doj_vendor'] ?? '') ?>">
        </div>

        <div class="form-group">
          <label class="form-label" for="doj_plant">Date of Joining (Plant)</label>
          <input type="date" id="doj_plant" name="doj_plant" class="form-control"
            value="<?= Helpers::h($record['doj_plant'] ?? '') ?>">
        </div>

        <div class="form-group">
          <label class="form-label" for="qualification">Qualification</label>
          <input type="text" id="qualification" name="qualification" class="form-control"
            value="<?= Helpers::h($record['qualification'] ?? '') ?>">
        </div>

        <div class="form-group">
          <label class="form-label" for="trade">Trade</label>
          <input type="text" id="trade" name="trade" class="form-control"
            value="<?= Helpers::h($record['trade'] ?? '') ?>">
        </div>

        <?php if ($isAdmin && $isEdit): ?>
        <div class="form-group">
          <label class="form-label" for="status">Status</label>
          <select id="status" name="status" class="form-control">
            <?php foreach (['pending_approval','active','inactive'] as $s): ?>
              <option value="<?= $s ?>" <?= ($record['status'] ?? '') === $s ? 'selected' : '' ?>><?= ucwords(str_replace('_',' ',$s)) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <?php endif; ?>

      </div>
    </div>
  </div>

  <!-- SECTION 2: Personal Details -->
  <div class="card" style="margin-bottom:var(--space-6)">
    <div class="card-header"><h2 class="card-title">Personal Details</h2></div>
    <div class="card-body">
      <div class="form-grid">

        <div class="form-group">
          <label class="form-label required" for="first_name">First Name</label>
          <input type="text" id="first_name" name="first_name" class="form-control"
            value="<?= Helpers::h($record['first_name'] ?? '') ?>"
            data-validate="required" data-format="uppercase">
          <span class="form-error"><?= $errors['first_name'] ?? '' ?></span>
        </div>

        <div class="form-group">
          <label class="form-label" for="middle_name">Middle Name</label>
          <input type="text" id="middle_name" name="middle_name" class="form-control"
            value="<?= Helpers::h($record['middle_name'] ?? '') ?>" data-format="uppercase">
        </div>

        <div class="form-group">
          <label class="form-label required" for="last_name">Last Name</label>
          <input type="text" id="last_name" name="last_name" class="form-control"
            value="<?= Helpers::h($record['last_name'] ?? '') ?>"
            data-validate="required" data-format="uppercase">
          <span class="form-error"><?= $errors['last_name'] ?? '' ?></span>
        </div>

        <div class="form-group">
          <label class="form-label" for="gender">Gender</label>
          <select id="gender" name="gender" class="form-control">
            <option value="">— Select —</option>
            <option value="male"   <?= ($record['gender'] ?? '') === 'male'   ? 'selected' : '' ?>>Male</option>
            <option value="female" <?= ($record['gender'] ?? '') === 'female' ? 'selected' : '' ?>>Female</option>
            <option value="other"  <?= ($record['gender'] ?? '') === 'other'  ? 'selected' : '' ?>>Other</option>
          </select>
        </div>

        <div class="form-group">
          <label class="form-label" for="dob">Date of Birth</label>
          <input type="date" id="dob" name="dob" class="form-control"
            value="<?= Helpers::h($record['dob'] ?? '') ?>">
        </div>

        <div class="form-group">
          <label class="form-label" for="father_name">Father's Name</label>
          <input type="text" id="father_name" name="father_name" class="form-control"
            value="<?= Helpers::h($record['father_name'] ?? '') ?>" data-format="uppercase">
        </div>

        <div class="form-group">
          <label class="form-label" for="mobile">Mobile</label>
          <input type="tel" id="mobile" name="mobile" class="form-control"
            value="<?= Helpers::h($record['mobile'] ?? '') ?>"
            data-validate="mobile" data-format="digits" maxlength="10">
        </div>

        <div class="form-group" style="grid-column:span 2">
          <label class="form-label" for="permanent_address">Permanent Address</label>
          <textarea id="permanent_address" name="permanent_address" class="form-control" rows="2"><?= Helpers::h($record['permanent_address'] ?? '') ?></textarea>
        </div>

        <div class="form-group" style="grid-column:span 2">
          <label class="form-label" for="current_address">Current Address</label>
          <textarea id="current_address" name="current_address" class="form-control" rows="2"><?= Helpers::h($record['current_address'] ?? '') ?></textarea>
          <small class="form-hint">Leave blank if same as permanent address</small>
        </div>

        <div class="form-group">
          <label class="form-label" for="emergency_contact_name">Emergency Contact Name</label>
          <input type="text" id="emergency_contact_name" name="emergency_contact_name" class="form-control"
            value="<?= Helpers::h($record['emergency_contact_name'] ?? '') ?>">
        </div>

        <div class="form-group">
          <label class="form-label" for="emergency_contact_mobile">Emergency Contact Mobile</label>
          <input type="tel" id="emergency_contact_mobile" name="emergency_contact_mobile" class="form-control"
            value="<?= Helpers::h($record['emergency_contact_mobile'] ?? '') ?>"
            data-format="digits" maxlength="10">
        </div>

        <!-- Photo -->
        <div class="form-group">
          <label class="form-label" for="photo">Photo (JPG/PNG, max 2 MB)</label>
          <?php if ($isEdit && $record['photo_path']): ?>
            <div style="margin-bottom:var(--space-2)">
              <img src="/<?= Helpers::h(ltrim($record['photo_path'],'/')) ?>" alt="Employee Photo"
                style="width:80px;height:80px;object-fit:cover;border-radius:8px;border:1px solid var(--clr-border)">
            </div>
          <?php endif; ?>
          <input type="file" id="photo" name="photo" class="form-control" accept=".jpg,.jpeg,.png">
        </div>

      </div>
    </div>
  </div>

  <!-- SECTION 3: Identity Documents -->
  <div class="card" style="margin-bottom:var(--space-6)">
    <div class="card-header"><h2 class="card-title">Identity &amp; Statutory</h2></div>
    <div class="card-body">
      <div class="form-grid">

        <div class="form-group">
          <label class="form-label" for="aadhaar_no">Aadhaar Number</label>
          <input type="text" id="aadhaar_no" name="aadhaar_no" class="form-control"
            value="<?= Helpers::h($record['aadhaar_no'] ?? '') ?>"
            data-format="digits" maxlength="12" placeholder="12 digits">
          <span class="form-error"><?= $errors['aadhaar_no'] ?? '' ?></span>
        </div>

        <div class="form-group">
          <label class="form-label" for="pan_no">PAN Number</label>
          <input type="text" id="pan_no" name="pan_no" class="form-control"
            value="<?= Helpers::h($record['pan_no'] ?? '') ?>"
            data-format="uppercase" maxlength="10" placeholder="AAAAA0000A">
          <span class="form-error"><?= $errors['pan_no'] ?? '' ?></span>
        </div>

        <div class="form-group">
          <label class="form-label" for="esi_no">ESI Number</label>
          <input type="text" id="esi_no" name="esi_no" class="form-control"
            value="<?= Helpers::h($record['esi_no'] ?? '') ?>">
        </div>

        <div class="form-group">
          <label class="form-label" for="pf_uan">PF / UAN Number</label>
          <input type="text" id="pf_uan" name="pf_uan" class="form-control"
            value="<?= Helpers::h($record['pf_uan'] ?? '') ?>" data-format="digits" maxlength="12">
        </div>

      </div>
    </div>
  </div>

  <!-- SECTION 4: Bank Details -->
  <div class="card" style="margin-bottom:var(--space-6)">
    <div class="card-header"><h2 class="card-title">Bank Details</h2></div>
    <div class="card-body">
      <div class="form-grid">

        <div class="form-group">
          <label class="form-label" for="bank_name">Bank Name</label>
          <input type="text" id="bank_name" name="bank_name" class="form-control"
            value="<?= Helpers::h($record['bank_name'] ?? '') ?>">
        </div>

        <div class="form-group">
          <label class="form-label" for="bank_account_no">Account Number</label>
          <input type="text" id="bank_account_no" name="bank_account_no" class="form-control"
            value="<?= Helpers::h($record['bank_account_no'] ?? '') ?>"
            data-format="digits" maxlength="20">
        </div>

        <div class="form-group">
          <label class="form-label" for="bank_ifsc">IFSC Code</label>
          <input type="text" id="bank_ifsc" name="bank_ifsc" class="form-control"
            value="<?= Helpers::h($record['bank_ifsc'] ?? '') ?>"
            data-format="uppercase" maxlength="11" placeholder="SBIN0001234">
          <span class="form-error"><?= $errors['bank_ifsc'] ?? '' ?></span>
        </div>

        <div class="form-group">
          <label class="form-label" for="bank_branch">Branch</label>
          <input type="text" id="bank_branch" name="bank_branch" class="form-control"
            value="<?= Helpers::h($record['bank_branch'] ?? '') ?>">
        </div>

      </div>
    </div>
  </div>

  <!-- Submit -->
  <div style="display:flex;gap:var(--space-3)">
    <button type="submit" class="btn btn-primary">
      <?= $isEdit ? 'Update Employee' : 'Add Employee' ?>
    </button>
    <a href="/employees" class="btn btn-ghost">Cancel</a>
  </div>

</form>

<?php
$pageContent = ob_get_clean();
$pageTitle   = $title;
$activeMenu  = 'employees';
$breadcrumbs = [['label' => 'Employee Master', 'url' => '/employees'], ['label' => $title]];
include CLMS_ROOT . '/templates/base.html.php';
