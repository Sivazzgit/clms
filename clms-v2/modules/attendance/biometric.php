<?php
/**
 * CLMS 2.0 — Biometric Data Upload
 * Accepts CSV: employee_code, attendance_date, in_time, out_time
 */
Auth::requireRole('hr_admin');
$companyId = $_SESSION['company_id'];
$errors    = [];
$results   = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    Auth::requireCsrf();

    if (empty($_FILES['csv_file']['tmp_name'])) {
        $errors[] = 'Please select a CSV file to upload.';
    } else {
        $fh = fopen($_FILES['csv_file']['tmp_name'], 'r');
        $header = fgetcsv($fh); // skip header row
        $inserted = 0; $skipped = 0; $errs = 0;

        // Build employee_code → id map
        $empMap = [];
        $rows = DB::rows("SELECT id, employee_code, section_id, vendor_id FROM employees WHERE company_id=? AND status='active'", [$companyId]);
        foreach ($rows as $r) { $empMap[$r['employee_code']] = $r; }

        $shiftMap = [];
        $shifts = DB::rows("SELECT id, code FROM shifts WHERE company_id=?", [$companyId]);
        foreach ($shifts as $s) { $shiftMap[$s['code']] = $s['id']; }

        $lineNo = 1;
        while (($row = fgetcsv($fh)) !== false) {
            $lineNo++;
            if (count($row) < 3) { $errs++; continue; }
            [$empCode, $attDate, $inTime] = array_map('trim', $row);
            $outTime   = trim($row[3] ?? '');
            $shiftCode = trim($row[4] ?? '');

            if (!isset($empMap[$empCode])) {
                $results[] = "Line $lineNo: employee code '$empCode' not found — skipped.";
                $skipped++; continue;
            }
            if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $attDate)) {
                $results[] = "Line $lineNo: invalid date '$attDate' — skipped.";
                $skipped++; continue;
            }

            $emp      = $empMap[$empCode];
            $shiftId  = $shiftMap[$shiftCode] ?? null;

            // Look for an existing biometric record for this employee/date
            $existing = DB::value("SELECT id FROM attendance WHERE employee_id=? AND attendance_date=? AND source='biometric'", [$emp['id'], $attDate]);
            if ($existing) { $skipped++; continue; }

            $worked = null;
            if ($inTime && $outTime) {
                [$ih, $im] = explode(':', $inTime);
                [$oh, $om] = explode(':', $outTime);
                $mins = ($oh*60+$om) - ($ih*60+$im);
                if ($mins < 0) $mins += 1440;
                $worked = round($mins/60, 2);
            }

            DB::execute(
                "INSERT INTO attendance (company_id,employee_id,vendor_id,section_id,attendance_date,shift_id,source,in_time,out_time,worked_hours,is_present)
                 VALUES (?,?,?,?,?,?,'biometric',?,?,?,1)",
                [$companyId,$emp['id'],$emp['vendor_id'],$emp['section_id'],$attDate,$shiftId,
                 $inTime  ? $attDate.' '.$inTime.':00'  : null,
                 $outTime ? $attDate.' '.$outTime.':00' : null,
                 $worked]
            );
            $inserted++;
        }
        fclose($fh);
        AuditLogger::log('UPLOAD', 'attendance', null, null, ['inserted'=>$inserted,'skipped'=>$skipped]);
        $results[] = "Upload complete: $inserted inserted, $skipped skipped, $errs invalid rows.";
    }
}

ob_start();
?>
<div class="page-header">
  <div><h1 class="page-title">Biometric Data Upload</h1></div>
</div>

<?php foreach ($results as $r): ?>
  <div class="alert alert-info" style="margin-bottom:var(--space-2)"><?= Helpers::h($r) ?></div>
<?php endforeach; ?>
<?php foreach ($errors as $e): ?>
  <div class="alert alert-danger" style="margin-bottom:var(--space-2)"><?= Helpers::h($e) ?></div>
<?php endforeach; ?>

<div class="card" style="margin-bottom:var(--space-4)">
  <div class="card-header"><h3 class="card-title">Upload CSV</h3></div>
  <div class="card-body">
    <p style="color:var(--clr-text-muted);font-size:var(--text-sm)">
      CSV columns: <code>employee_code, attendance_date (YYYY-MM-DD), in_time (HH:MM), out_time (HH:MM), shift_code</code><br>
      Header row is skipped. Duplicate biometric records for same employee+date are skipped automatically.
    </p>
    <form method="POST" action="/attendance/biometric" enctype="multipart/form-data">
      <input type="hidden" name="csrf_token" value="<?= Helpers::h($_SESSION['csrf_token']) ?>">
      <div class="form-group" style="max-width:400px">
        <label class="form-label required">CSV File</label>
        <input type="file" name="csv_file" class="form-control" accept=".csv,text/csv" required>
      </div>
      <button type="submit" class="btn btn-primary">Upload</button>
    </form>
  </div>
</div>

<div class="card">
  <div class="card-header"><h3 class="card-title">Sample CSV Format</h3></div>
  <div class="card-body">
    <pre style="background:var(--clr-bg-subtle);padding:var(--space-4);border-radius:var(--radius-base);font-size:var(--text-sm);overflow:auto">employee_code,attendance_date,in_time,out_time,shift_code
EMP001,2026-05-25,08:00,17:00,GEN
EMP002,2026-05-25,08:15,17:30,GEN
EMP003,2026-05-25,20:00,,NIGHT</pre>
  </div>
</div>

<?php
$pageContent = ob_get_clean();
$pageTitle   = 'Biometric Upload';
$activeMenu  = 'attendance';
$breadcrumbs = [['label'=>'Attendance','url'=>'/attendance'],['label'=>'Biometric Upload']];
include CLMS_ROOT . '/templates/base.html.php';
