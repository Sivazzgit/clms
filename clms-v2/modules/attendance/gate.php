<?php
/**
 * CLMS 2.0 — Gate Entry / Manual Attendance
 */
Auth::requireRole(['gate_staff','hr_admin','section_incharge']);
$companyId = $_SESSION['company_id'];
$errors    = [];
$success   = '';

$today = date('Y-m-d');
$date  = Helpers::clean($_GET['date'] ?? $today);
if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) $date = $today;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    Auth::requireCsrf();

    $employeeId = (int)($_POST['employee_id'] ?? 0);
    $shiftId    = (int)($_POST['shift_id']    ?? 0);
    $entryDate  = Helpers::clean($_POST['attendance_date'] ?? $today);
    $inTime     = Helpers::clean($_POST['in_time']  ?? '');
    $outTime    = Helpers::clean($_POST['out_time'] ?? '') ?: null;
    $isPresent  = 1;

    if (!$employeeId) $errors[] = 'Employee is required.';
    if (!$shiftId)    $errors[] = 'Shift is required.';
    if (!$entryDate)  $errors[] = 'Date is required.';

    if (!$errors) {
        // Get employee's section & vendor
        $emp = DB::row("SELECT section_id, vendor_id FROM employees WHERE id=? AND company_id=?", [$employeeId, $companyId]);
        if (!$emp) { $errors[] = 'Employee not found.'; }
        else {
            // Check for duplicate
            $dup = DB::value("SELECT id FROM attendance WHERE employee_id=? AND shift_id=? AND attendance_date=?", [$employeeId, $shiftId, $entryDate]);
            if ($dup) {
                $errors[] = 'Attendance already recorded for this employee/shift/date.';
            } else {
                $worked = null;
                if ($inTime && $outTime) {
                    [$ih, $im] = explode(':', $inTime);
                    [$oh, $om] = explode(':', $outTime);
                    $mins = ($oh * 60 + $om) - ($ih * 60 + $im);
                    if ($mins < 0) $mins += 1440;
                    $worked = round($mins / 60, 2);
                }
                DB::execute(
                    "INSERT INTO attendance (company_id,employee_id,vendor_id,section_id,attendance_date,shift_id,source,in_time,out_time,worked_hours,is_present)
                     VALUES (?,?,?,?,?,?,'gate_manual',?,?,?,1)",
                    [$companyId,$employeeId,$emp['vendor_id'],$emp['section_id'],$entryDate,$shiftId,
                     $inTime ? $entryDate.' '.$inTime.':00' : null,
                     $outTime ? $entryDate.' '.$outTime.':00' : null,
                     $worked]
                );
                AuditLogger::log('CREATE', 'attendance', DB::lastInsertId());
                $success = 'Attendance recorded.';
            }
        }
    }
}

// Today's entries for the selected date
$entries = DB::rows(
    "SELECT a.*, e.employee_code, e.first_name, e.last_name, v.name AS vendor_name,
            s.name AS section_name, sh.code AS shift_code
     FROM attendance a
     JOIN employees e ON e.id = a.employee_id
     JOIN vendors v   ON v.id = a.vendor_id
     JOIN sections s  ON s.id = a.section_id
     JOIN shifts sh   ON sh.id = a.shift_id
     WHERE a.company_id=? AND a.attendance_date=? AND a.source='gate_manual'
     ORDER BY a.created_at DESC",
    [$companyId, $date]
);

$shifts    = DB::rows("SELECT id, code, name FROM shifts WHERE company_id=? AND is_active=1 ORDER BY code", [$companyId]);
$employees = DB::rows(
    "SELECT e.id, e.employee_code, e.first_name, e.last_name, v.name AS vendor_name
     FROM employees e JOIN vendors v ON v.id=e.vendor_id
     WHERE e.company_id=? AND e.status='active'
     ORDER BY e.first_name, e.last_name",
    [$companyId]
);

ob_start();
?>
<div class="page-header">
  <div><h1 class="page-title">Gate / Manual Attendance Entry</h1></div>
  <div class="page-actions">
    <form method="GET" style="display:flex;gap:var(--space-3);align-items:center">
      <label style="font-size:var(--text-sm)">Date:</label>
      <input type="date" name="date" class="form-control" value="<?= $date ?>" onchange="this.form.submit()">
    </form>
  </div>
</div>

<?php if ($success): ?>
  <div class="alert alert-success" style="margin-bottom:var(--space-4)"><?= Helpers::h($success) ?></div>
<?php endif; ?>
<?php foreach ($errors as $e): ?>
  <div class="alert alert-danger" style="margin-bottom:var(--space-2)"><?= Helpers::h($e) ?></div>
<?php endforeach; ?>

<!-- Entry Form -->
<div class="card" style="margin-bottom:var(--space-4)">
  <div class="card-header"><h3 class="card-title">Mark Attendance — <?= $date ?></h3></div>
  <div class="card-body">
    <form method="POST" action="/attendance/gate">
      <input type="hidden" name="csrf_token"       value="<?= Helpers::h($_SESSION['csrf_token']) ?>">
      <input type="hidden" name="attendance_date"  value="<?= $date ?>">
      <div class="form-grid-3">
        <div class="form-group">
          <label class="form-label required">Employee</label>
          <select name="employee_id" class="form-control" required>
            <option value="">— Select Employee —</option>
            <?php foreach ($employees as $emp): ?>
              <option value="<?= $emp['id'] ?>">
                <?= Helpers::h($emp['employee_code'].' — '.$emp['first_name'].' '.$emp['last_name'].' ('.$emp['vendor_name'].')') ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="form-group">
          <label class="form-label required">Shift</label>
          <select name="shift_id" class="form-control" required>
            <option value="">— Select Shift —</option>
            <?php foreach ($shifts as $sh): ?>
              <option value="<?= $sh['id'] ?>"><?= Helpers::h($sh['code'].' — '.$sh['name']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="form-group">
          <label class="form-label">In Time</label>
          <input type="time" name="in_time" class="form-control">
        </div>
        <div class="form-group">
          <label class="form-label">Out Time</label>
          <input type="time" name="out_time" class="form-control">
          <small class="form-hint">Leave blank if not yet out.</small>
        </div>
      </div>
      <button type="submit" class="btn btn-primary">Mark Present</button>
    </form>
  </div>
</div>

<!-- Today's Entries -->
<div class="card">
  <div class="card-header">
    <h3 class="card-title">Entries for <?= $date ?> <span style="font-weight:400;font-size:var(--text-sm)">(<?= count($entries) ?> records)</span></h3>
  </div>
  <div class="table-wrapper">
    <table class="data-table">
      <thead>
        <tr><th>Employee</th><th>Vendor</th><th>Section</th><th>Shift</th><th>In</th><th>Out</th><th>Hours</th></tr>
      </thead>
      <tbody>
        <?php foreach ($entries as $a): ?>
        <tr>
          <td><?= Helpers::h($a['employee_code'].' — '.$a['first_name'].' '.$a['last_name']) ?></td>
          <td><?= Helpers::h($a['vendor_name']) ?></td>
          <td><?= Helpers::h($a['section_name']) ?></td>
          <td><?= Helpers::h($a['shift_code']) ?></td>
          <td><?= $a['in_time'] ? date('H:i', strtotime($a['in_time'])) : '—' ?></td>
          <td><?= $a['out_time'] ? date('H:i', strtotime($a['out_time'])) : '—' ?></td>
          <td><?= $a['worked_hours'] ? number_format($a['worked_hours'], 2) : '—' ?></td>
        </tr>
        <?php endforeach; ?>
        <?php if (!$entries): ?>
          <tr><td colspan="7" style="text-align:center;color:var(--clr-text-muted)">No entries for this date yet.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?php
$pageContent = ob_get_clean();
$pageTitle   = 'Gate Attendance';
$activeMenu  = 'attendance';
$breadcrumbs = [['label'=>'Attendance'],['label'=>'Gate Entry']];
include CLMS_ROOT . '/templates/base.html.php';
