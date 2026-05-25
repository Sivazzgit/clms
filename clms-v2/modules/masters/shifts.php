<?php
/**
 * CLMS 2.0 — Shifts Master (HR Admin only)
 */
Auth::requireRole('hr_admin');

$companyId = $_SESSION['company_id'];
$errors    = [];
$success   = '';

// ----------------------------------------------------------------
// POST: add / edit / toggle
// ----------------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    Auth::requireCsrf();
    $action = Helpers::clean($_POST['action'] ?? '');

    if ($action === 'save') {
        $id         = (int) ($_POST['id'] ?? 0);
        $code       = Helpers::clean($_POST['code']       ?? '');
        $name       = Helpers::clean($_POST['name']       ?? '');
        $startTime  = Helpers::clean($_POST['start_time'] ?? '');
        $endTime    = Helpers::clean($_POST['end_time']   ?? '');
        $isNight    = isset($_POST['is_night_shift']) ? 1 : 0;
        $crossesMid = isset($_POST['crosses_midnight']) ? 1 : 0;

        if ($code === '') $errors[] = 'Shift code is required.';
        if ($name === '') $errors[] = 'Shift name is required.';
        if ($startTime === '') $errors[] = 'Start time is required.';
        if ($endTime === '')   $errors[] = 'End time is required.';

        // Calculate duration
        $dur = null;
        if ($startTime && $endTime) {
            [$sh, $sm] = explode(':', $startTime);
            [$eh, $em] = explode(':', $endTime);
            $mins = ($eh * 60 + $em) - ($sh * 60 + $sm);
            if ($mins < 0) $mins += 1440; // overnight
            $dur = round($mins / 60, 2);
        }

        if (!$errors) {
            if ($id > 0) {
                DB::execute(
                    "UPDATE shifts SET code=?, name=?, start_time=?, end_time=?, crosses_midnight=?, duration_hours=?, is_night_shift=? WHERE id=? AND company_id=?",
                    [$code, $name, $startTime, $endTime, $crossesMid, $dur, $isNight, $id, $companyId]
                );
                AuditLogger::log('UPDATE', 'shifts', $id);
            } else {
                DB::execute(
                    "INSERT INTO shifts (company_id,code,name,start_time,end_time,crosses_midnight,duration_hours,is_night_shift) VALUES (?,?,?,?,?,?,?,?)",
                    [$companyId, $code, $name, $startTime, $endTime, $crossesMid, $dur, $isNight]
                );
                AuditLogger::log('CREATE', 'shifts', DB::lastInsertId());
            }
            $success = 'Shift saved.';
        }
    } elseif ($action === 'toggle') {
        $id = (int) ($_POST['id'] ?? 0);
        DB::execute("UPDATE shifts SET is_active = NOT is_active WHERE id=? AND company_id=?", [$id, $companyId]);
        AuditLogger::log('UPDATE', 'shifts', $id, null, ['toggled' => 'is_active']);
        $success = 'Shift status updated.';
    } elseif ($action === 'delete') {
        $id = (int) ($_POST['id'] ?? 0);
        // Check if in use
        $inUse = (int) DB::value("SELECT COUNT(*) FROM employees WHERE shift_id=?", [$id]);
        if ($inUse > 0) {
            $errors[] = 'Cannot delete: shift is assigned to ' . $inUse . ' employee(s).';
        } else {
            DB::execute("DELETE FROM shifts WHERE id=? AND company_id=?", [$id, $companyId]);
            AuditLogger::log('DELETE', 'shifts', $id);
            $success = 'Shift deleted.';
        }
    }
}

$editId = (int) ($_GET['edit'] ?? 0);
$editRow = $editId ? DB::row("SELECT * FROM shifts WHERE id=? AND company_id=?", [$editId, $companyId]) : null;
$shifts  = DB::rows("SELECT * FROM shifts WHERE company_id=? ORDER BY code", [$companyId]);

ob_start();
?>
<div class="page-header">
  <div><h1 class="page-title">Shifts</h1></div>
  <div class="page-actions">
    <a href="?add=1" class="btn btn-primary">
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
      Add Shift
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
  <div class="card-header"><h3 class="card-title"><?= $editRow ? 'Edit Shift' : 'Add Shift' ?></h3></div>
  <div class="card-body">
    <form method="POST" action="<?= APP_BASE ?>/masters/shifts">
      <input type="hidden" name="csrf_token" value="<?= Helpers::h($_SESSION['csrf_token']) ?>">
      <input type="hidden" name="action" value="save">
      <input type="hidden" name="id" value="<?= $editRow ? $editRow['id'] : 0 ?>">
      <div class="form-grid-3">
        <div class="form-group">
          <label class="form-label required">Code</label>
          <input type="text" name="code" class="form-control" maxlength="10" required
                 value="<?= Helpers::h($editRow['code'] ?? '') ?>">
        </div>
        <div class="form-group">
          <label class="form-label required">Name</label>
          <input type="text" name="name" class="form-control" maxlength="50" required
                 value="<?= Helpers::h($editRow['name'] ?? '') ?>">
        </div>
        <div class="form-group">
          <label class="form-label required">Start Time</label>
          <input type="time" name="start_time" class="form-control" required
                 value="<?= Helpers::h($editRow['start_time'] ?? '') ?>">
        </div>
        <div class="form-group">
          <label class="form-label required">End Time</label>
          <input type="time" name="end_time" class="form-control" required
                 value="<?= Helpers::h($editRow['end_time'] ?? '') ?>">
        </div>
        <div class="form-group" style="display:flex;flex-direction:column;justify-content:flex-end">
          <label class="form-label" style="visibility:hidden">Options</label>
          <div style="display:flex;gap:var(--space-4)">
            <label style="display:flex;align-items:center;gap:var(--space-2);cursor:pointer">
              <input type="checkbox" name="crosses_midnight" value="1"
                     <?= ($editRow['crosses_midnight'] ?? 0) ? 'checked' : '' ?>>
              Crosses midnight
            </label>
            <label style="display:flex;align-items:center;gap:var(--space-2);cursor:pointer">
              <input type="checkbox" name="is_night_shift" value="1"
                     <?= ($editRow['is_night_shift'] ?? 0) ? 'checked' : '' ?>>
              Night shift
            </label>
          </div>
        </div>
      </div>
      <div style="display:flex;gap:var(--space-3);margin-top:var(--space-4)">
        <button type="submit" class="btn btn-primary">Save Shift</button>
        <a href="<?= APP_BASE ?>/masters/shifts" class="btn btn-secondary">Cancel</a>
      </div>
    </form>
  </div>
</div>
<?php endif; ?>

<div class="card">
  <div class="table-wrapper">
    <table class="data-table">
      <thead>
        <tr>
          <th>Code</th><th>Name</th><th>Start</th><th>End</th>
          <th>Duration (hrs)</th><th>Night</th><th>Status</th><th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($shifts as $s): ?>
        <tr>
          <td><code><?= Helpers::h($s['code']) ?></code></td>
          <td><?= Helpers::h($s['name']) ?></td>
          <td><?= substr($s['start_time'], 0, 5) ?></td>
          <td><?= substr($s['end_time'], 0, 5) ?><?= $s['crosses_midnight'] ? ' (+1)' : '' ?></td>
          <td><?= number_format($s['duration_hours'] ?? 0, 2) ?></td>
          <td><?= $s['is_night_shift'] ? '<span class="badge badge-pending">Yes</span>' : '—' ?></td>
          <td><?= $s['is_active'] ? '<span class="badge badge-active">Active</span>' : '<span class="badge badge-inactive">Inactive</span>' ?></td>
          <td>
            <div style="display:flex;gap:var(--space-2)">
              <a href="?edit=<?= $s['id'] ?>" class="btn btn-secondary btn-sm">Edit</a>
              <form method="POST" action="<?= APP_BASE ?>/masters/shifts" style="display:inline">
                <input type="hidden" name="csrf_token" value="<?= Helpers::h($_SESSION['csrf_token']) ?>">
                <input type="hidden" name="action" value="toggle">
                <input type="hidden" name="id" value="<?= $s['id'] ?>">
                <button type="submit" class="btn btn-ghost btn-sm"><?= $s['is_active'] ? 'Disable' : 'Enable' ?></button>
              </form>
              <form method="POST" action="<?= APP_BASE ?>/masters/shifts" style="display:inline"
                    onsubmit="return confirm('Delete this shift?')">
                <input type="hidden" name="csrf_token" value="<?= Helpers::h($_SESSION['csrf_token']) ?>">
                <input type="hidden" name="action" value="delete">
                <input type="hidden" name="id" value="<?= $s['id'] ?>">
                <button type="submit" class="btn btn-danger btn-sm">Delete</button>
              </form>
            </div>
          </td>
        </tr>
        <?php endforeach; ?>
        <?php if (!$shifts): ?>
          <tr><td colspan="8" style="text-align:center;color:var(--clr-text-muted)">No shifts defined yet.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?php
$pageContent = ob_get_clean();
$pageTitle   = 'Shifts';
$activeMenu  = 'masters';
$breadcrumbs = [['label' => 'Masters'], ['label' => 'Shifts']];
include CLMS_ROOT . '/templates/base.html.php';
