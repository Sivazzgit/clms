<?php
/**
 * CLMS 2.0 — Employee Separation
 * HR Admin records a separation; employee status → 'separated'
 */
Auth::requireRole('hr_admin');

$id  = (int)($_GET['id'] ?? 0);
if (!$id) Helpers::redirect('/employees', 'Employee not found.', 'error');

$emp = DB::row(
    "SELECT e.*, v.name AS vendor_name, v.vendor_code FROM employees e
     LEFT JOIN vendors v ON v.id = e.vendor_id
     WHERE e.id = ? AND e.company_id = ?",
    [$id, $_SESSION['company_id']]
);
if (!$emp) Helpers::redirect('/employees', 'Employee not found.', 'error');
if ($emp['status'] !== 'active') Helpers::redirect("/employees/$id/edit", 'Only active employees can be separated.', 'error');

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    Auth::requireCsrf();

    $data = [
        'employee_id'       => $id,
        'separation_date'   => Helpers::dateSql($_POST['separation_date'] ?? ''),
        'reason'            => Helpers::clean($_POST['reason'] ?? ''),
        'reason_details'    => Helpers::clean($_POST['reason_details'] ?? '') ?: null,
        'last_working_date' => Helpers::dateSql($_POST['last_working_date'] ?? ''),
        'processed_by'      => Auth::user()['id'],
        'processed_at'      => date('Y-m-d H:i:s'),
    ];

    if (empty($data['separation_date'])) $errors['separation_date'] = 'Separation date is required.';
    if (empty($data['reason']))          $errors['reason']          = 'Reason is required.';

    if (empty($errors)) {
        DB::transaction(function () use ($data, $id) {
            DB::insert('employee_separations', $data);
            DB::update('employees', ['status' => 'separated'], ['id' => $id]);
            AuditLogger::log('SEPARATION', 'employees', null, (string)$id,
                ['status' => 'active'], ['status' => 'separated', 'reason' => $data['reason']]);
        });
        Helpers::redirect('/employees', 'Employee separation recorded.');
    }
}

$fullName = trim($emp['first_name'] . ' ' . ($emp['middle_name'] ? $emp['middle_name'] . ' ' : '') . $emp['last_name']);

ob_start();
?>

<div class="page-header">
  <div>
    <h1 class="page-title">Record Separation</h1>
    <p class="page-sub" style="color:var(--clr-text-muted);margin:0;">
      <?= Helpers::h($emp['employee_code']) ?> — <?= Helpers::h($fullName) ?>
    </p>
  </div>
  <div class="page-actions">
    <a href="/employees/<?= $id ?>/edit" class="btn btn-ghost">Cancel</a>
  </div>
</div>

<div class="alert alert-warning" style="margin-bottom:var(--space-6)">
  <strong>Warning:</strong> Recording a separation will change this employee's status to <strong>Separated</strong>
  and they will no longer be available for deployment or attendance.
</div>

<div class="card" style="max-width:620px">
  <div class="card-header"><h2 class="card-title">Separation Details</h2></div>
  <div class="card-body">
    <form method="POST" action="/employees/<?= $id ?>/separate">
      <input type="hidden" name="<?= CSRF_KEY ?>" value="<?= Helpers::h(Auth::csrfToken()) ?>">

      <!-- Employee summary -->
      <div style="background:var(--clr-bg-light);border-radius:var(--radius-md);padding:var(--space-4);margin-bottom:var(--space-5)">
        <div style="font-weight:600"><?= Helpers::h($fullName) ?></div>
        <div style="font-size:var(--text-sm);color:var(--clr-text-muted)">
          <?= Helpers::h($emp['employee_code']) ?> &nbsp;|&nbsp; <?= Helpers::h($emp['vendor_code']) ?> — <?= Helpers::h($emp['vendor_name']) ?>
        </div>
        <div style="font-size:var(--text-sm);color:var(--clr-text-muted)">DOJ Plant: <?= Helpers::dateDisplay($emp['doj_plant']) ?></div>
      </div>

      <div class="form-group">
        <label class="form-label required" for="separation_date">Separation Date</label>
        <input type="date" id="separation_date" name="separation_date" class="form-control"
          value="<?= Helpers::h($_POST['separation_date'] ?? date('Y-m-d')) ?>"
          data-validate="required">
        <span class="form-error"><?= $errors['separation_date'] ?? '' ?></span>
      </div>

      <div class="form-group">
        <label class="form-label" for="last_working_date">Last Working Date</label>
        <input type="date" id="last_working_date" name="last_working_date" class="form-control"
          value="<?= Helpers::h($_POST['last_working_date'] ?? date('Y-m-d')) ?>">
      </div>

      <div class="form-group">
        <label class="form-label required" for="reason">Reason</label>
        <select id="reason" name="reason" class="form-control" data-validate="required">
          <option value="">— Select —</option>
          <?php $reasons = ['resignation'=>'Resignation','contract_completion'=>'Contract Completion','termination'=>'Termination','absconding'=>'Absconding','death'=>'Death','other'=>'Other']; ?>
          <?php foreach ($reasons as $val => $label): ?>
            <option value="<?= $val ?>" <?= ($_POST['reason'] ?? '') === $val ? 'selected' : '' ?>><?= $label ?></option>
          <?php endforeach; ?>
        </select>
        <span class="form-error"><?= $errors['reason'] ?? '' ?></span>
      </div>

      <div class="form-group">
        <label class="form-label" for="reason_details">Additional Details</label>
        <textarea id="reason_details" name="reason_details" class="form-control" rows="3"><?= Helpers::h($_POST['reason_details'] ?? '') ?></textarea>
      </div>

      <div style="display:flex;gap:var(--space-3);margin-top:var(--space-5)">
        <button type="submit" class="btn btn-danger">Confirm Separation</button>
        <a href="/employees/<?= $id ?>/edit" class="btn btn-ghost">Cancel</a>
      </div>

    </form>
  </div>
</div>

<?php
$pageContent = ob_get_clean();
$pageTitle   = 'Record Separation';
$activeMenu  = 'employees';
$breadcrumbs = [
    ['label' => 'Employee Master', 'url' => '/employees'],
    ['label' => Helpers::h($fullName), 'url' => "/employees/$id/edit"],
    ['label' => 'Separation'],
];
include CLMS_ROOT . '/templates/base.html.php';
