<?php
/**
 * CLMS 2.0 — Vendor Add / Edit form
 * GET  /vendors/create     → blank
 * GET  /vendors/{id}/edit  → populated
 * POST → save
 */
Auth::requireRole('hr_admin');

$id     = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$isEdit = $id > 0;
$errors = [];

$record = $isEdit ? DB::row('SELECT * FROM vendors WHERE id = ? AND company_id = ?', [$id, $_SESSION['company_id']]) : null;
if ($isEdit && !$record) Helpers::redirect('/vendors', 'Contractor not found.', 'error');

// ----------------------------------------------------------------
// POST
// ----------------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    Auth::requireCsrf();

    $data = [
        'company_id'            => (int)$_SESSION['company_id'],
        'name'                  => Helpers::clean($_POST['name'] ?? ''),
        'trade_name'            => Helpers::clean($_POST['trade_name'] ?? ''),
        'address'               => Helpers::clean($_POST['address'] ?? ''),
        'city'                  => Helpers::clean($_POST['city'] ?? ''),
        'state'                 => Helpers::clean($_POST['state'] ?? ''),
        'pincode'               => Helpers::clean($_POST['pincode'] ?? ''),
        'contact_person'        => Helpers::clean($_POST['contact_person'] ?? ''),
        'mobile'                => Helpers::clean($_POST['mobile'] ?? ''),
        'email'                 => Helpers::clean($_POST['email'] ?? ''),
        'gstin'                 => strtoupper(Helpers::clean($_POST['gstin'] ?? '')),
        'pan'                   => strtoupper(Helpers::clean($_POST['pan'] ?? '')),
        'esi_code'              => Helpers::clean($_POST['esi_code'] ?? ''),
        'pf_code'               => Helpers::clean($_POST['pf_code'] ?? ''),
        'labour_licence_no'     => Helpers::clean($_POST['labour_licence_no'] ?? ''),
        'labour_licence_expiry' => Helpers::dateSql($_POST['labour_licence_expiry'] ?? ''),
        'max_employee_limit'    => (int)($_POST['max_employee_limit'] ?? 0),
        'contract_start_date'   => Helpers::dateSql($_POST['contract_start_date'] ?? ''),
        'contract_end_date'     => Helpers::dateSql($_POST['contract_end_date'] ?? ''),
        'bank_name'             => Helpers::clean($_POST['bank_name'] ?? ''),
        'bank_account_no'       => Helpers::clean($_POST['bank_account_no'] ?? ''),
        'bank_ifsc'             => strtoupper(Helpers::clean($_POST['bank_ifsc'] ?? '')),
        'bank_branch'           => Helpers::clean($_POST['bank_branch'] ?? ''),
        'status'                => Helpers::clean($_POST['status'] ?? 'pending'),
    ];

    // Validate required fields
    if (empty($data['name']))           $errors['name']           = 'Contractor name is required.';
    if (empty($data['contact_person'])) $errors['contact_person'] = 'Contact person is required.';
    if (empty($data['mobile']))         $errors['mobile']         = 'Mobile number is required.';

    if (!empty($data['email']) && !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Enter a valid email address.';
    }
    if (!empty($data['gstin']) && !preg_match('/^[0-9]{2}[A-Z]{5}[0-9]{4}[A-Z]{1}[1-9A-Z]{1}Z[0-9A-Z]{1}$/', $data['gstin'])) {
        $errors['gstin'] = 'GSTIN format is invalid.';
    }
    if (!empty($data['pan']) && !preg_match('/^[A-Z]{5}[0-9]{4}[A-Z]{1}$/', $data['pan'])) {
        $errors['pan'] = 'PAN format is invalid.';
    }

    if (empty($errors)) {
        try {
            DB::transaction(function () use ($data, $id, $isEdit) {
                $data['updated_at'] = date('Y-m-d H:i:s');

                if ($isEdit) {
                    $old = DB::row('SELECT * FROM vendors WHERE id = ?', [$id]);
                    DB::update('vendors', $data, ['id' => $id]);
                    AuditLogger::log('UPDATE', 'vendors', null, (string)$id, AuditLogger::sanitize($old), AuditLogger::sanitize($data));
                } else {
                    $data['created_at']   = date('Y-m-d H:i:s');
                    $data['created_by']   = Auth::user()['id'];
                    $data['vendor_code']  = Helpers::generateCode('VND', 'vendors', 'vendor_code');
                    $newId = DB::insert('vendors', $data);
                    AuditLogger::log('INSERT', 'vendors', null, (string)$newId, null, AuditLogger::sanitize($data));
                }
            });

            Helpers::redirect('/vendors', ($isEdit ? 'Contractor updated.' : 'Contractor added.'));
        } catch (Throwable $e) {
            $errors['_general'] = 'Save failed: ' . $e->getMessage();
        }
    }
}

$title = $isEdit ? 'Edit Contractor' : 'Add Contractor';

// Approval action (HR Admin can approve/suspend directly from edit form)
if ($isEdit && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['quick_status'])) {
    Auth::requireCsrf();
    $qs = in_array($_POST['quick_status'], ['active','pending','suspended','expired']) ? $_POST['quick_status'] : null;
    if ($qs) {
        DB::update('vendors', [
            'status'      => $qs,
            'approved_by' => Auth::user()['id'],
            'approved_at' => date('Y-m-d H:i:s'),
        ], ['id' => $id]);
        AuditLogger::log('STATUS_CHANGE', 'vendors', null, (string)$id, null, ['status' => $qs]);
        Helpers::redirect("/vendors/$id/edit", 'Status updated.');
    }
}

ob_start();
?>

<div class="page-header">
  <div>
    <h1 class="page-title"><?= $title ?></h1>
    <?php if ($isEdit): ?>
      <p class="page-sub" style="color:var(--clr-text-muted);margin:0;">
        Code: <strong><?= Helpers::h($record['vendor_code']) ?></strong>
      </p>
    <?php endif; ?>
  </div>
  <div class="page-actions">
    <?php if ($isEdit): ?>
      <a href="/employees?vendor_id=<?= $id ?>" class="btn btn-ghost">View Employees</a>
      <a href="/vendors/documents?vendor_id=<?= $id ?>" class="btn btn-ghost">Documents</a>
    <?php endif; ?>
    <a href="/vendors" class="btn btn-ghost">Cancel</a>
  </div>
</div>

<?php if (!empty($errors['_general'])): ?>
  <div class="alert alert-danger"><?= Helpers::h($errors['_general']) ?></div>
<?php endif; ?>

<?php if ($isEdit && $record['status'] === 'pending'): ?>
<div class="alert alert-info" style="display:flex;align-items:center;justify-content:space-between;margin-bottom:var(--space-4)">
  <span>This contractor is <strong>Pending Approval</strong>. Review details and approve to activate.</span>
  <form method="POST" action="/vendors/<?= $id ?>/edit" style="display:inline">
    <input type="hidden" name="<?= CSRF_KEY ?>" value="<?= Helpers::h(Auth::csrfToken()) ?>">
    <input type="hidden" name="quick_status" value="active">
    <button type="submit" class="btn btn-success btn-sm">Approve &amp; Activate</button>
  </form>
</div>
<?php endif; ?>

<form method="POST" action="<?= $isEdit ? "/vendors/$id/edit" : '/vendors/create' ?>" id="vendorForm" novalidate>
  <input type="hidden" name="<?= CSRF_KEY ?>" value="<?= Helpers::h(Auth::csrfToken()) ?>">

  <!-- Basic Details -->
  <div class="card" style="margin-bottom:var(--space-6)">
    <div class="card-header"><h2 class="card-title">Basic Details</h2></div>
    <div class="card-body">
      <div class="form-grid">

        <div class="form-group" style="grid-column:span 2">
          <label class="form-label required" for="name">Contractor / Firm Name</label>
          <input type="text" id="name" name="name" class="form-control <?= isset($errors['name']) ? 'is-invalid' : '' ?>"
            value="<?= Helpers::h($record['name'] ?? $_POST['name'] ?? '') ?>"
            data-validate="required|minlen:2" data-format="uppercase">
          <span class="form-error"><?= $errors['name'] ?? '' ?></span>
        </div>

        <div class="form-group">
          <label class="form-label" for="trade_name">Trade Name</label>
          <input type="text" id="trade_name" name="trade_name" class="form-control"
            value="<?= Helpers::h($record['trade_name'] ?? $_POST['trade_name'] ?? '') ?>">
        </div>

        <div class="form-group">
          <label class="form-label required" for="contact_person">Contact Person</label>
          <input type="text" id="contact_person" name="contact_person" class="form-control <?= isset($errors['contact_person']) ? 'is-invalid' : '' ?>"
            value="<?= Helpers::h($record['contact_person'] ?? $_POST['contact_person'] ?? '') ?>"
            data-validate="required">
          <span class="form-error"><?= $errors['contact_person'] ?? '' ?></span>
        </div>

        <div class="form-group">
          <label class="form-label required" for="mobile">Mobile</label>
          <input type="tel" id="mobile" name="mobile" class="form-control <?= isset($errors['mobile']) ? 'is-invalid' : '' ?>"
            value="<?= Helpers::h($record['mobile'] ?? $_POST['mobile'] ?? '') ?>"
            data-validate="required|mobile" data-format="digits" maxlength="10">
          <span class="form-error"><?= $errors['mobile'] ?? '' ?></span>
        </div>

        <div class="form-group">
          <label class="form-label" for="email">Email</label>
          <input type="email" id="email" name="email" class="form-control <?= isset($errors['email']) ? 'is-invalid' : '' ?>"
            value="<?= Helpers::h($record['email'] ?? $_POST['email'] ?? '') ?>"
            data-validate="email">
          <span class="form-error"><?= $errors['email'] ?? '' ?></span>
        </div>

        <div class="form-group">
          <label class="form-label" for="status">Status</label>
          <select id="status" name="status" class="form-control">
            <?php foreach (['pending','active','suspended','expired'] as $s): ?>
              <option value="<?= $s ?>" <?= ($record['status'] ?? 'pending') === $s ? 'selected' : '' ?>>
                <?= ucfirst($s) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>

      </div>
    </div>
  </div>

  <!-- Address -->
  <div class="card" style="margin-bottom:var(--space-6)">
    <div class="card-header"><h2 class="card-title">Address</h2></div>
    <div class="card-body">
      <div class="form-grid">

        <div class="form-group" style="grid-column:span 2">
          <label class="form-label" for="address">Address</label>
          <textarea id="address" name="address" class="form-control" rows="2"><?= Helpers::h($record['address'] ?? $_POST['address'] ?? '') ?></textarea>
        </div>

        <div class="form-group">
          <label class="form-label" for="city">City</label>
          <input type="text" id="city" name="city" class="form-control"
            value="<?= Helpers::h($record['city'] ?? $_POST['city'] ?? '') ?>">
        </div>

        <div class="form-group">
          <label class="form-label" for="state">State</label>
          <input type="text" id="state" name="state" class="form-control"
            value="<?= Helpers::h($record['state'] ?? $_POST['state'] ?? '') ?>">
        </div>

        <div class="form-group">
          <label class="form-label" for="pincode">Pincode</label>
          <input type="text" id="pincode" name="pincode" class="form-control"
            value="<?= Helpers::h($record['pincode'] ?? $_POST['pincode'] ?? '') ?>"
            data-format="digits" maxlength="6">
        </div>

      </div>
    </div>
  </div>

  <!-- Registration / Compliance -->
  <div class="card" style="margin-bottom:var(--space-6)">
    <div class="card-header"><h2 class="card-title">Registration &amp; Compliance</h2></div>
    <div class="card-body">
      <div class="form-grid">

        <div class="form-group">
          <label class="form-label" for="gstin">GSTIN</label>
          <input type="text" id="gstin" name="gstin" class="form-control <?= isset($errors['gstin']) ? 'is-invalid' : '' ?>"
            value="<?= Helpers::h($record['gstin'] ?? $_POST['gstin'] ?? '') ?>"
            data-format="uppercase" maxlength="15" placeholder="22AAAAA0000A1Z5">
          <span class="form-error"><?= $errors['gstin'] ?? '' ?></span>
        </div>

        <div class="form-group">
          <label class="form-label" for="pan">PAN</label>
          <input type="text" id="pan" name="pan" class="form-control <?= isset($errors['pan']) ? 'is-invalid' : '' ?>"
            value="<?= Helpers::h($record['pan'] ?? $_POST['pan'] ?? '') ?>"
            data-format="uppercase" maxlength="10" placeholder="AAAAA0000A">
          <span class="form-error"><?= $errors['pan'] ?? '' ?></span>
        </div>

        <div class="form-group">
          <label class="form-label" for="esi_code">ESI Code</label>
          <input type="text" id="esi_code" name="esi_code" class="form-control"
            value="<?= Helpers::h($record['esi_code'] ?? $_POST['esi_code'] ?? '') ?>">
        </div>

        <div class="form-group">
          <label class="form-label" for="pf_code">PF Code</label>
          <input type="text" id="pf_code" name="pf_code" class="form-control"
            value="<?= Helpers::h($record['pf_code'] ?? $_POST['pf_code'] ?? '') ?>">
        </div>

        <div class="form-group">
          <label class="form-label" for="labour_licence_no">Labour Licence No.</label>
          <input type="text" id="labour_licence_no" name="labour_licence_no" class="form-control"
            value="<?= Helpers::h($record['labour_licence_no'] ?? $_POST['labour_licence_no'] ?? '') ?>">
        </div>

        <div class="form-group">
          <label class="form-label" for="labour_licence_expiry">Labour Licence Expiry</label>
          <input type="date" id="labour_licence_expiry" name="labour_licence_expiry" class="form-control"
            value="<?= Helpers::h($record['labour_licence_expiry'] ?? $_POST['labour_licence_expiry'] ?? '') ?>">
        </div>

      </div>
    </div>
  </div>

  <!-- Contract Details -->
  <div class="card" style="margin-bottom:var(--space-6)">
    <div class="card-header"><h2 class="card-title">Contract Details</h2></div>
    <div class="card-body">
      <div class="form-grid" style="grid-template-columns:repeat(3,1fr)">

        <div class="form-group">
          <label class="form-label" for="contract_start_date">Contract Start Date</label>
          <input type="date" id="contract_start_date" name="contract_start_date" class="form-control"
            value="<?= Helpers::h($record['contract_start_date'] ?? $_POST['contract_start_date'] ?? '') ?>">
        </div>

        <div class="form-group">
          <label class="form-label" for="contract_end_date">Contract End Date</label>
          <input type="date" id="contract_end_date" name="contract_end_date" class="form-control"
            value="<?= Helpers::h($record['contract_end_date'] ?? $_POST['contract_end_date'] ?? '') ?>">
        </div>

        <div class="form-group">
          <label class="form-label" for="max_employee_limit">Max Employee Limit</label>
          <input type="number" id="max_employee_limit" name="max_employee_limit" class="form-control"
            value="<?= (int)($record['max_employee_limit'] ?? $_POST['max_employee_limit'] ?? 0) ?>"
            min="0" max="9999">
          <small class="form-hint">Set to 0 for no limit</small>
        </div>

      </div>
    </div>
  </div>

  <!-- Bank Details -->
  <div class="card" style="margin-bottom:var(--space-6)">
    <div class="card-header"><h2 class="card-title">Bank Details</h2></div>
    <div class="card-body">
      <div class="form-grid">

        <div class="form-group">
          <label class="form-label" for="bank_name">Bank Name</label>
          <input type="text" id="bank_name" name="bank_name" class="form-control"
            value="<?= Helpers::h($record['bank_name'] ?? $_POST['bank_name'] ?? '') ?>">
        </div>

        <div class="form-group">
          <label class="form-label" for="bank_account_no">Account Number</label>
          <input type="text" id="bank_account_no" name="bank_account_no" class="form-control"
            value="<?= Helpers::h($record['bank_account_no'] ?? $_POST['bank_account_no'] ?? '') ?>"
            data-format="digits" maxlength="20">
        </div>

        <div class="form-group">
          <label class="form-label" for="bank_ifsc">IFSC Code</label>
          <input type="text" id="bank_ifsc" name="bank_ifsc" class="form-control"
            value="<?= Helpers::h($record['bank_ifsc'] ?? $_POST['bank_ifsc'] ?? '') ?>"
            data-format="uppercase" maxlength="11" placeholder="SBIN0001234">
        </div>

        <div class="form-group">
          <label class="form-label" for="bank_branch">Branch</label>
          <input type="text" id="bank_branch" name="bank_branch" class="form-control"
            value="<?= Helpers::h($record['bank_branch'] ?? $_POST['bank_branch'] ?? '') ?>">
        </div>

      </div>
    </div>
  </div>

  <!-- Submit -->
  <div style="display:flex;gap:var(--space-3)">
    <button type="submit" class="btn btn-primary">
      <?= $isEdit ? 'Update Contractor' : 'Add Contractor' ?>
    </button>
    <a href="/vendors" class="btn btn-ghost">Cancel</a>
  </div>

</form>

<?php
$pageContent = ob_get_clean();
$pageTitle   = $title;
$activeMenu  = 'vendors';
$breadcrumbs = [['label' => 'Contractors', 'url' => '/vendors'], ['label' => $title]];
include CLMS_ROOT . '/templates/base.html.php';
