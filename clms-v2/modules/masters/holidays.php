<?php
/**
 * CLMS 2.0 — Holidays Master (HR Admin only)
 */
Auth::requireRole('hr_admin');

$companyId = $_SESSION['company_id'];
$errors    = [];
$success   = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    Auth::requireCsrf();
    $action = Helpers::clean($_POST['action'] ?? '');

    if ($action === 'save') {
        $id           = (int) ($_POST['id'] ?? 0);
        $holidayDate  = Helpers::clean($_POST['holiday_date'] ?? '');
        $name         = Helpers::clean($_POST['name']         ?? '');
        $type         = Helpers::clean($_POST['type']         ?? 'factory');
        $isPaid       = isset($_POST['is_paid']) ? 1 : 0;
        $plantId      = (int) ($_POST['plant_id'] ?? 0) ?: null;

        if (!$holidayDate) $errors[] = 'Date is required.';
        if ($name === '')  $errors[] = 'Holiday name is required.';
        if (!in_array($type, ['national','state','factory','optional'])) $type = 'factory';

        if (!$errors) {
            if ($id > 0) {
                DB::execute(
                    "UPDATE holidays SET holiday_date=?, name=?, type=?, is_paid=?, plant_id=? WHERE id=? AND company_id=?",
                    [$holidayDate, $name, $type, $isPaid, $plantId, $id, $companyId]
                );
                AuditLogger::log('UPDATE', 'holidays', $id);
            } else {
                DB::execute(
                    "INSERT INTO holidays (company_id,plant_id,holiday_date,name,type,is_paid,created_by) VALUES (?,?,?,?,?,?,?)",
                    [$companyId, $plantId, $holidayDate, $name, $type, $isPaid, $_SESSION['user_id']]
                );
                AuditLogger::log('CREATE', 'holidays', DB::lastInsertId());
            }
            $success = 'Holiday saved.';
        }
    } elseif ($action === 'delete') {
        $id = (int) ($_POST['id'] ?? 0);
        DB::execute("DELETE FROM holidays WHERE id=? AND company_id=?", [$id, $companyId]);
        AuditLogger::log('DELETE', 'holidays', $id);
        $success = 'Holiday deleted.';
    }
}

// Filter by year
$filterYear = (int) ($_GET['year'] ?? date('Y'));
$filterPlant = (int) ($_GET['plant_id'] ?? 0);
$editId     = (int) ($_GET['edit'] ?? 0);
$editRow    = $editId ? DB::row("SELECT * FROM holidays WHERE id=? AND company_id=?", [$editId, $companyId]) : null;

$holidayWhere = "h.company_id=? AND YEAR(h.holiday_date)=?";
$holidayParams = [$companyId, $filterYear];
if ($filterPlant > 0) {
    $holidayWhere .= " AND (h.plant_id=? OR h.plant_id IS NULL)";
    $holidayParams[] = $filterPlant;
}

$holidays = DB::rows(
    "SELECT h.*, pl.name AS plant_name
     FROM holidays h
     LEFT JOIN plants pl ON pl.id = h.plant_id
     WHERE $holidayWhere ORDER BY h.holiday_date",
    $holidayParams
);

$plants = DB::rows("SELECT id, code, name FROM plants WHERE company_id=? AND is_active=1 ORDER BY code", [$companyId]);
$years = range(date('Y') - 1, date('Y') + 2);

ob_start();
?>
<div class="page-header">
  <div><h1 class="page-title">Holidays</h1></div>
  <div class="page-actions">
    <a href="?add=1&year=<?= $filterYear ?>" class="btn btn-primary">
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
      Add Holiday
    </a>
  </div>
</div>

<?php if ($success): ?>
  <div class="alert alert-success" style="margin-bottom:var(--space-4)"><?= Helpers::h($success) ?></div>
<?php endif; ?>
<?php foreach ($errors as $e): ?>
  <div class="alert alert-danger" style="margin-bottom:var(--space-2)"><?= Helpers::h($e) ?></div>
<?php endforeach; ?>

<?php if (isset($_GET['add']) || $editRow): ?>
<div class="card" style="margin-bottom:var(--space-4)">
  <div class="card-header"><h3 class="card-title"><?= $editRow ? 'Edit Holiday' : 'Add Holiday' ?></h3></div>
  <div class="card-body">
    <form method="POST" action="<?= APP_BASE ?>/masters/holidays">
      <input type="hidden" name="<?= CSRF_KEY ?>" value="<?= Helpers::h(Auth::csrfToken()) ?>">
      <input type="hidden" name="action" value="save">
      <input type="hidden" name="id" value="<?= $editRow ? $editRow['id'] : 0 ?>">
      <div class="form-grid-3">
        <div class="form-group">
          <label class="form-label required">Date</label>
          <input type="date" name="holiday_date" class="form-control" required
                 value="<?= Helpers::h($editRow['holiday_date'] ?? date('Y-m-d')) ?>">
        </div>
        <div class="form-group">
          <label class="form-label required">Holiday Name</label>
          <input type="text" name="name" class="form-control" maxlength="150" required
                 value="<?= Helpers::h($editRow['name'] ?? '') ?>">
        </div>
        <div class="form-group">
          <label class="form-label">Plant / Location</label>
          <select name="plant_id" class="form-control">
            <option value="">— All Plants —</option>
            <?php foreach ($plants as $pl): ?>
              <option value="<?= $pl['id'] ?>"
                <?= ($editRow['plant_id'] ?? 0) == $pl['id'] ? 'selected' : '' ?>>
                <?= Helpers::h($pl['code'] . ' — ' . $pl['name']) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="form-group">
          <label class="form-label">Type</label>
          <select name="type" class="form-control">
            <option value="national" <?= ($editRow['type'] ?? '') === 'national' ? 'selected' : '' ?>>National</option>
            <option value="state"    <?= ($editRow['type'] ?? '') === 'state'    ? 'selected' : '' ?>>State</option>
            <option value="factory"  <?= ($editRow['type'] ?? 'factory') === 'factory' ? 'selected' : '' ?>>Factory</option>
            <option value="optional" <?= ($editRow['type'] ?? '') === 'optional' ? 'selected' : '' ?>>Optional</option>
          </select>
        </div>
        <div class="form-group" style="display:flex;align-items:flex-end">
          <label style="display:flex;align-items:center;gap:var(--space-2);cursor:pointer">
            <input type="checkbox" name="is_paid" value="1"
                   <?= ($editRow['is_paid'] ?? 1) ? 'checked' : '' ?>>
            Paid Holiday
          </label>
        </div>
      </div>
      <div style="display:flex;gap:var(--space-3);margin-top:var(--space-4)">
        <button type="submit" class="btn btn-primary">Save Holiday</button>
        <a href="<?= APP_BASE ?>/masters/holidays?year=<?= $filterYear ?>" class="btn btn-secondary">Cancel</a>
      </div>
    </form>
  </div>
</div>
<?php endif; ?>

<div class="card">
  <div class="table-toolbar">
    <form method="GET" action="<?= APP_BASE ?>/masters/holidays" style="display:flex;gap:var(--space-3);align-items:center;flex-wrap:wrap">
      <select name="year" class="form-control" style="width:auto;" onchange="this.form.submit()">
        <?php foreach ($years as $y): ?>
          <option value="<?= $y ?>" <?= $filterYear === $y ? 'selected' : '' ?>><?= $y ?></option>
        <?php endforeach; ?>
      </select>
      <select name="plant_id" class="form-control" style="width:auto;" onchange="this.form.submit()">
        <option value="">All Plants</option>
        <?php foreach ($plants as $pl): ?>
          <option value="<?= $pl['id'] ?>" <?= $filterPlant === $pl['id'] ? 'selected' : '' ?>>
            <?= Helpers::h($pl['code'] . ' — ' . $pl['name']) ?>
          </option>
        <?php endforeach; ?>
      </select>
    </form>
    <span style="font-size:var(--text-sm);color:var(--clr-text-muted)"><?= count($holidays) ?> holiday(s) in <?= $filterYear ?></span>
  </div>
  <div class="table-wrapper">
    <table class="data-table">
      <thead>
        <tr><th>Date</th><th>Day</th><th>Holiday</th><th>Plant</th><th>Type</th><th>Paid</th><th>Actions</th></tr>
      </thead>
      <tbody>
        <?php foreach ($holidays as $h): ?>
        <tr>
          <td><?= Helpers::dateDisplay($h['holiday_date']) ?></td>
          <td><?= date('D', strtotime($h['holiday_date'])) ?></td>
          <td><?= Helpers::h($h['name']) ?></td>
          <td><?= Helpers::h($h['plant_name'] ?? '<em style="color:var(--clr-text-muted)">All Plants</em>') ?></td>
          <td><span class="badge badge-pending"><?= ucfirst($h['type']) ?></span></td>
          <td><?= $h['is_paid'] ? '<span class="badge badge-active">Paid</span>' : '<span class="badge badge-inactive">Unpaid</span>' ?></td>
          <td>
            <div style="display:flex;gap:var(--space-2)">
              <a href="?edit=<?= $h['id'] ?>&year=<?= $filterYear ?>" class="btn btn-secondary btn-sm">Edit</a>
              <form method="POST" action="<?= APP_BASE ?>/masters/holidays" style="display:inline"
                    onsubmit="return confirm('Delete this holiday?')">
                <input type="hidden" name="<?= CSRF_KEY ?>" value="<?= Helpers::h(Auth::csrfToken()) ?>">
                <input type="hidden" name="action" value="delete">
                <input type="hidden" name="id" value="<?= $h['id'] ?>">
                <button type="submit" class="btn btn-danger btn-sm">Delete</button>
              </form>
            </div>
          </td>
        </tr>
        <?php endforeach; ?>
        <?php if (!$holidays): ?>
          <tr><td colspan="7" style="text-align:center;color:var(--clr-text-muted)">No holidays for <?= $filterYear ?>.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?php
$pageContent = ob_get_clean();
$pageTitle   = 'Holidays';
$activeMenu  = 'masters';
$breadcrumbs = [['label' => 'Masters'], ['label' => 'Holidays']];
include CLMS_ROOT . '/templates/base.html.php';
