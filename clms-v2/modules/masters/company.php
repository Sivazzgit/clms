<?php
/**
 * CLMS 2.0 — Company Settings (HR Admin only)
 */
Auth::requireRole('hr_admin');

$companyId = $_SESSION['company_id'];
$errors    = [];
$success   = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    Auth::requireCsrf();

    $data = [
        'name'                        => Helpers::clean($_POST['name']                        ?? ''),
        'address'                     => Helpers::clean($_POST['address']                     ?? ''),
        'city'                        => Helpers::clean($_POST['city']                        ?? ''),
        'state'                       => Helpers::clean($_POST['state']                       ?? ''),
        'country'                     => Helpers::clean($_POST['country']                     ?? 'India'),
        'gstin'                       => Helpers::clean($_POST['gstin']                       ?? ''),
        'pan'                         => Helpers::clean($_POST['pan']                         ?? ''),
        'financial_year_start_month'  => (int) ($_POST['financial_year_start_month']          ?? 4),
    ];

    if ($data['name'] === '') $errors[] = 'Company name is required.';

    if (!$errors) {
        DB::execute(
            "UPDATE companies SET name=?, address=?, city=?, state=?, country=?, gstin=?, pan=?,
             financial_year_start_month=?, updated_at=NOW()
             WHERE id=?",
            [...array_values($data), $companyId]
        );
        AuditLogger::log('UPDATE', 'companies', $companyId, null, $data);
        $success = true;
    }
}

$company = DB::row("SELECT * FROM companies WHERE id=?", [$companyId]);

ob_start();
?>
<div class="page-header">
  <div>
    <h1 class="page-title">Company Settings</h1>
    <p class="page-sub" style="color:var(--clr-text-muted);margin:0;">Update your organisation's profile</p>
  </div>
</div>

<?php if ($success): ?>
  <div class="alert alert-success" style="margin-bottom:var(--space-4)">Company settings saved successfully.</div>
<?php endif; ?>
<?php foreach ($errors as $e): ?>
  <div class="alert alert-danger" style="margin-bottom:var(--space-2)"><?= Helpers::h($e) ?></div>
<?php endforeach; ?>

<form method="POST" action="<?= APP_BASE ?>/masters/company">
  <input type="hidden" name="csrf_token" value="<?= Helpers::h($_SESSION['csrf_token']) ?>">

  <div class="card" style="margin-bottom:var(--space-4)">
    <div class="card-header"><h3 class="card-title">Basic Details</h3></div>
    <div class="card-body">
      <div class="form-grid-2">
        <div class="form-group">
          <label class="form-label required">Company Code</label>
          <input type="text" class="form-control" value="<?= Helpers::h($company['code']) ?>" disabled>
          <small class="form-hint">Code cannot be changed.</small>
        </div>
        <div class="form-group">
          <label class="form-label required">Company Name</label>
          <input type="text" name="name" class="form-control" required
                 value="<?= Helpers::h($company['name']) ?>">
        </div>
        <div class="form-group">
          <label class="form-label">GSTIN</label>
          <input type="text" name="gstin" class="form-control" maxlength="20"
                 value="<?= Helpers::h($company['gstin'] ?? '') ?>">
        </div>
        <div class="form-group">
          <label class="form-label">PAN</label>
          <input type="text" name="pan" class="form-control" maxlength="15"
                 value="<?= Helpers::h($company['pan'] ?? '') ?>">
        </div>
        <div class="form-group">
          <label class="form-label">Financial Year Start Month</label>
          <select name="financial_year_start_month" class="form-control">
            <?php
            $months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
            for ($m = 1; $m <= 12; $m++):
            ?>
              <option value="<?= $m ?>" <?= (int)$company['financial_year_start_month'] === $m ? 'selected' : '' ?>>
                <?= $months[$m - 1] ?>
              </option>
            <?php endfor; ?>
          </select>
        </div>
      </div>
    </div>
  </div>

  <div class="card" style="margin-bottom:var(--space-4)">
    <div class="card-header"><h3 class="card-title">Address</h3></div>
    <div class="card-body">
      <div class="form-group">
        <label class="form-label">Address</label>
        <textarea name="address" class="form-control" rows="3"><?= Helpers::h($company['address'] ?? '') ?></textarea>
      </div>
      <div class="form-grid-3">
        <div class="form-group">
          <label class="form-label">City</label>
          <input type="text" name="city" class="form-control" value="<?= Helpers::h($company['city'] ?? '') ?>">
        </div>
        <div class="form-group">
          <label class="form-label">State</label>
          <input type="text" name="state" class="form-control" value="<?= Helpers::h($company['state'] ?? '') ?>">
        </div>
        <div class="form-group">
          <label class="form-label">Country</label>
          <input type="text" name="country" class="form-control" value="<?= Helpers::h($company['country'] ?? 'India') ?>">
        </div>
      </div>
    </div>
  </div>

  <div style="display:flex;gap:var(--space-3)">
    <button type="submit" class="btn btn-primary">Save Changes</button>
  </div>
</form>

<?php
$pageContent = ob_get_clean();
$pageTitle   = 'Company Settings';
$activeMenu  = 'masters';
$breadcrumbs = [['label' => 'Masters'], ['label' => 'Company Settings']];
include CLMS_ROOT . '/templates/base.html.php';
