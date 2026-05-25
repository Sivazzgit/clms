<?php
/**
 * CLMS 2.0 — Employee Bulk Upload (CSV)
 * HR Admin only.
 * GET  → show upload form + template download
 * POST → process CSV, insert/update employees
 */
Auth::requireRole('hr_admin');

$companyId = (int)$_SESSION['company_id'];
$errors    = [];
$results   = null;   // summary after upload

// ---- CSV template download -----------------------------------------------
if (isset($_GET['template'])) {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="employee_upload_template.csv"');
    echo implode(',', [
        'vendor_code','employee_code','first_name','middle_name','last_name',
        'gender','dob','father_name','mobile','aadhaar_no','pan_no',
        'esi_no','pf_uan','bank_name','bank_account_no','bank_ifsc',
        'qualification','trade','doj_vendor','doj_plant','biometric_id',
        'category_code',
    ]) . "\n";
    echo implode(',', [
        'VEN001','EMP001','John','Kumar','Doe',
        'male','1990-01-15','Ram Doe','9876543210','123456789012','ABCDE1234F',
        'ESI001','UAN001','SBI','00001234567','SBIN0001234',
        'ITI','Welder','2023-01-01','2023-01-15','BIO001',
        'HELPER',
    ]) . "\n";
    exit;
}

// ---- POST: process upload --------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    Auth::requireCsrf();

    if (empty($_FILES['csv_file']['tmp_name']) || $_FILES['csv_file']['error'] !== UPLOAD_ERR_OK) {
        $errors[] = 'Please select a valid CSV file.';
    } else {
        $mimeOk = in_array($_FILES['csv_file']['type'], ['text/csv','text/plain','application/csv','application/vnd.ms-excel'], true);
        $extOk  = strtolower(pathinfo($_FILES['csv_file']['name'], PATHINFO_EXTENSION)) === 'csv';
        if (!$mimeOk && !$extOk) {
            $errors[] = 'File must be a .csv file.';
        }
    }

    if (!$errors) {
        // Load lookup maps
        $vendors = [];
        foreach (DB::rows("SELECT id, vendor_code FROM vendors WHERE company_id=? AND status='active'", [$companyId]) as $v) {
            $vendors[strtoupper($v['vendor_code'])] = $v['id'];
        }
        $categories = [];
        foreach (DB::rows("SELECT id, code FROM labour_categories WHERE company_id=? AND is_active=1", [$companyId]) as $c) {
            $categories[strtoupper($c['code'])] = $c['id'];
        }
        // Existing employee_codes (for upsert detection)
        $existing = [];
        foreach (DB::rows("SELECT id, employee_code, vendor_id FROM employees WHERE company_id=?", [$companyId]) as $e) {
            $existing[$e['vendor_id'] . '_' . strtoupper($e['employee_code'])] = $e['id'];
        }

        $handle  = fopen($_FILES['csv_file']['tmp_name'], 'r');
        $header  = fgetcsv($handle);   // skip header row
        // Normalise header to lowercase, trimmed
        $header  = array_map(fn($h) => trim(strtolower(str_replace(' ', '_', $h))), $header);

        $inserted = $updated = $skipped = 0;
        $rowErrors = [];
        $rowNum = 1;

        while (($raw = fgetcsv($handle)) !== false) {
            $rowNum++;
            if (count($raw) < 3) { $skipped++; continue; }

            // Map row to columns
            $row = [];
            foreach ($header as $i => $col) {
                $row[$col] = trim($raw[$i] ?? '');
            }

            // Required: vendor_code, first_name, last_name
            $vendorCode = strtoupper($row['vendor_code'] ?? '');
            if (!$vendorCode || !isset($vendors[$vendorCode])) {
                $rowErrors[] = "Row $rowNum: vendor_code '$vendorCode' not found — skipped.";
                $skipped++;
                continue;
            }
            $vendorId = $vendors[$vendorCode];

            $firstName = Helpers::clean($row['first_name'] ?? '');
            $lastName  = Helpers::clean($row['last_name']  ?? '');
            if (!$firstName || !$lastName) {
                $rowErrors[] = "Row $rowNum: first_name/last_name required — skipped.";
                $skipped++;
                continue;
            }

            $empCode   = strtoupper(Helpers::clean($row['employee_code'] ?? ''));
            $catCode   = strtoupper($row['category_code'] ?? '');
            $catId     = $categories[$catCode] ?? null;

            $data = [
                'company_id'               => $companyId,
                'vendor_id'                => $vendorId,
                'employee_code'            => $empCode ?: strtoupper(substr($vendorCode, 0, 3) . str_pad($rowNum, 4, '0', STR_PAD_LEFT)),
                'first_name'               => $firstName,
                'middle_name'              => Helpers::clean($row['middle_name'] ?? '') ?: null,
                'last_name'                => $lastName,
                'gender'                   => in_array($row['gender'] ?? '', ['male','female','other']) ? $row['gender'] : null,
                'dob'                      => ($row['dob'] ?? '') ?: null,
                'father_name'              => Helpers::clean($row['father_name'] ?? '') ?: null,
                'mobile'                   => preg_replace('/\D/', '', $row['mobile'] ?? '') ?: null,
                'aadhaar_no'               => preg_replace('/\D/', '', $row['aadhaar_no'] ?? '') ?: null,
                'pan_no'                   => strtoupper(Helpers::clean($row['pan_no'] ?? '')) ?: null,
                'esi_no'                   => Helpers::clean($row['esi_no'] ?? '') ?: null,
                'pf_uan'                   => Helpers::clean($row['pf_uan'] ?? '') ?: null,
                'bank_name'                => Helpers::clean($row['bank_name'] ?? '') ?: null,
                'bank_account_no'          => Helpers::clean($row['bank_account_no'] ?? '') ?: null,
                'bank_ifsc'                => strtoupper(Helpers::clean($row['bank_ifsc'] ?? '')) ?: null,
                'qualification'            => Helpers::clean($row['qualification'] ?? '') ?: null,
                'trade'                    => Helpers::clean($row['trade'] ?? '') ?: null,
                'doj_vendor'               => ($row['doj_vendor'] ?? '') ?: null,
                'doj_plant'                => ($row['doj_plant'] ?? '') ?: null,
                'biometric_id'             => Helpers::clean($row['biometric_id'] ?? '') ?: null,
                'category_id'              => $catId,
                'status'                   => 'pending_approval',
                'added_by'                 => $_SESSION['user_id'],
            ];

            $key = $vendorId . '_' . $data['employee_code'];
            if (isset($existing[$key])) {
                // Update existing (don't reset status)
                unset($data['status'], $data['added_by'], $data['company_id'], $data['vendor_id']);
                DB::update('employees', $data, ['id' => $existing[$key]]);
                $updated++;
            } else {
                DB::insert('employees', $data);
                $existing[$key] = DB::lastInsertId();
                $inserted++;
            }
        }
        fclose($handle);

        AuditLogger::log('BULK_UPLOAD', 'employees', null, null, null, [
            'inserted' => $inserted, 'updated' => $updated, 'skipped' => $skipped,
        ]);

        $results = compact('inserted', 'updated', 'skipped', 'rowErrors');
    }
}

ob_start();
?>
<div class="page-header">
  <div>
    <h1 class="page-title">Bulk Employee Upload</h1>
    <p class="page-sub" style="color:var(--clr-text-muted);margin:0">
      Upload a CSV to add or update employees in bulk.
    </p>
  </div>
  <div class="page-actions">
    <a href="<?= APP_BASE ?>/employees" class="btn btn-ghost">← Back to Employees</a>
    <a href="<?= APP_BASE ?>/employees/upload?template=1" class="btn btn-secondary">
      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
      Download Template
    </a>
  </div>
</div>

<?php foreach ($errors as $e): ?>
  <div class="alert alert-danger" style="margin-bottom:var(--space-3)"><?= Helpers::h($e) ?></div>
<?php endforeach; ?>

<?php if ($results !== null): ?>
<div class="card" style="margin-bottom:var(--space-4);border-left:4px solid var(--clr-success)">
  <div class="card-body">
    <h3 style="margin:0 0 var(--space-3)">Upload Complete</h3>
    <div style="display:flex;gap:var(--space-6)">
      <div><strong style="font-size:var(--text-2xl)"><?= $results['inserted'] ?></strong><br><small>Inserted</small></div>
      <div><strong style="font-size:var(--text-2xl)"><?= $results['updated'] ?></strong><br><small>Updated</small></div>
      <div><strong style="font-size:var(--text-2xl)"><?= $results['skipped'] ?></strong><br><small>Skipped</small></div>
    </div>
    <?php if ($results['rowErrors']): ?>
      <details style="margin-top:var(--space-3)">
        <summary style="cursor:pointer;font-weight:var(--fw-semi)">
          <?= count($results['rowErrors']) ?> row error(s)
        </summary>
        <ul style="margin:var(--space-2) 0 0 var(--space-4);font-size:var(--text-sm);color:var(--clr-danger)">
          <?php foreach ($results['rowErrors'] as $re): ?>
            <li><?= Helpers::h($re) ?></li>
          <?php endforeach; ?>
        </ul>
      </details>
    <?php endif; ?>
    <div style="margin-top:var(--space-4)">
      <a href="<?= APP_BASE ?>/employees/pending" class="btn btn-primary">Review Pending Approvals</a>
      <a href="<?= APP_BASE ?>/employees/upload" class="btn btn-ghost" style="margin-left:var(--space-2)">Upload Another</a>
    </div>
  </div>
</div>
<?php endif; ?>

<div class="card">
  <div class="card-header">
    <h3 class="card-title">Upload CSV</h3>
  </div>
  <div class="card-body" style="max-width:540px">
    <form method="POST" action="<?= APP_BASE ?>/employees/upload" enctype="multipart/form-data" novalidate>
      <input type="hidden" name="<?= CSRF_KEY ?>" value="<?= Helpers::h(Auth::csrfToken()) ?>">

      <div class="form-group">
        <label class="form-label">CSV File <span class="req">*</span></label>
        <input type="file" name="csv_file" class="form-control" accept=".csv,text/csv" required>
        <p class="form-hint">Max 2 MB. First row must be the header. UTF-8 encoding.</p>
      </div>

      <div class="form-group">
        <button type="submit" class="btn btn-primary">Upload &amp; Process</button>
      </div>
    </form>
  </div>
</div>

<!-- Column reference -->
<div class="card" style="margin-top:var(--space-4)">
  <div class="card-header"><h3 class="card-title">CSV Column Reference</h3></div>
  <div class="table-wrapper">
    <table class="data-table">
      <thead>
        <tr><th>Column</th><th>Required</th><th>Notes</th></tr>
      </thead>
      <tbody>
        <tr><td><code>vendor_code</code></td><td>Yes</td><td>Must match an active vendor's code</td></tr>
        <tr><td><code>employee_code</code></td><td>No</td><td>Auto-generated if blank. Used to detect duplicates (update vs insert).</td></tr>
        <tr><td><code>first_name</code></td><td>Yes</td><td></td></tr>
        <tr><td><code>middle_name</code></td><td>No</td><td></td></tr>
        <tr><td><code>last_name</code></td><td>Yes</td><td></td></tr>
        <tr><td><code>gender</code></td><td>No</td><td>male / female / other</td></tr>
        <tr><td><code>dob</code></td><td>No</td><td>YYYY-MM-DD</td></tr>
        <tr><td><code>father_name</code></td><td>No</td><td></td></tr>
        <tr><td><code>mobile</code></td><td>No</td><td>Digits only</td></tr>
        <tr><td><code>aadhaar_no</code></td><td>No</td><td>12 digits</td></tr>
        <tr><td><code>pan_no</code></td><td>No</td><td></td></tr>
        <tr><td><code>esi_no</code></td><td>No</td><td></td></tr>
        <tr><td><code>pf_uan</code></td><td>No</td><td></td></tr>
        <tr><td><code>bank_name</code></td><td>No</td><td></td></tr>
        <tr><td><code>bank_account_no</code></td><td>No</td><td></td></tr>
        <tr><td><code>bank_ifsc</code></td><td>No</td><td></td></tr>
        <tr><td><code>qualification</code></td><td>No</td><td></td></tr>
        <tr><td><code>trade</code></td><td>No</td><td></td></tr>
        <tr><td><code>doj_vendor</code></td><td>No</td><td>Date joined contractor (YYYY-MM-DD)</td></tr>
        <tr><td><code>doj_plant</code></td><td>No</td><td>Date joined plant (YYYY-MM-DD)</td></tr>
        <tr><td><code>biometric_id</code></td><td>No</td><td>Links to biometric device ID</td></tr>
        <tr><td><code>category_code</code></td><td>No</td><td>Must match a labour category code</td></tr>
      </tbody>
    </table>
  </div>
</div>

<?php
$pageContent = ob_get_clean();
$pageTitle   = 'Bulk Upload';
$activeMenu  = 'employees';
$breadcrumbs = [
    ['label' => 'Employees', 'url' => '/employees'],
    ['label' => 'Bulk Upload'],
];
include CLMS_ROOT . '/templates/base.html.php';
