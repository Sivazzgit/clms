<?php
/**
 * CLMS 2.0 — Vendor Documents (HR Admin can upload; others can view)
 */
Auth::requireRole('hr_admin', 'section_incharge', 'hod', 'plant_head');

$vendorId = (int)($_GET['vendor_id'] ?? 0);
if (!$vendorId) Helpers::redirect('/vendors', 'Vendor not found.', 'error');

$vendor = DB::row('SELECT id, vendor_code, name FROM vendors WHERE id = ? AND company_id = ?', [$vendorId, $_SESSION['company_id']]);
if (!$vendor) Helpers::redirect('/vendors', 'Vendor not found.', 'error');

$isAdmin = Auth::hasRole('hr_admin');
$errors  = [];

// ----------------------------------------------------------------
// POST: Upload
// ----------------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $isAdmin) {
    Auth::requireCsrf();

    $action = Helpers::clean($_POST['action'] ?? 'upload');

    if ($action === 'delete') {
        $docId = (int)$_POST['doc_id'];
        $doc   = DB::row('SELECT * FROM vendor_documents WHERE id = ? AND vendor_id = ?', [$docId, $vendorId]);
        if ($doc) {
            $path = CLMS_ROOT . '/' . ltrim($doc['file_path'], '/');
            if (file_exists($path)) @unlink($path);
            DB::delete('vendor_documents', ['id' => $docId]);
            AuditLogger::log('DELETE', 'vendor_documents', null, (string)$docId, ['doc_name' => $doc['doc_name']], null);
        }
        Helpers::redirect("/vendors/documents?vendor_id=$vendorId", 'Document deleted.');
    }

    // Upload
    $docType  = Helpers::clean($_POST['doc_type'] ?? '');
    $docName  = Helpers::clean($_POST['doc_name'] ?? '');
    $expiry   = Helpers::dateSql($_POST['expiry_date'] ?? '');

    if (empty($docName)) $errors['doc_name'] = 'Document name is required.';

    if (empty($_FILES['doc_file']['name'])) {
        $errors['doc_file'] = 'Please select a file to upload.';
    } elseif (!in_array(strtolower(pathinfo($_FILES['doc_file']['name'], PATHINFO_EXTENSION)), ['pdf','jpg','jpeg','png'])) {
        $errors['doc_file'] = 'Only PDF, JPG, PNG files are allowed.';
    }

    if (empty($errors)) {
        $sub = 'uploads/vendors/' . $vendor['vendor_code'];
        try {
            $filePath = Helpers::saveUpload($_FILES['doc_file'], $sub, [
                'application/pdf', 'image/jpeg', 'image/jpg', 'image/png'
            ]);
        } catch (Throwable $e) {
            $filePath = null;
            $errors['doc_file'] = $e->getMessage();
        }
        if ($filePath) {
            DB::insert('vendor_documents', [
                'vendor_id'   => $vendorId,
                'doc_type'    => $docType,
                'doc_name'    => $docName,
                'file_path'   => $filePath,
                'expiry_date' => $expiry ?: null,
                'uploaded_by' => Auth::user()['id'],
                'uploaded_at' => date('Y-m-d H:i:s'),
            ]);
            AuditLogger::log('UPLOAD', 'vendor_documents', null, null, null, ['vendor_id' => $vendorId, 'doc_name' => $docName]);
            Helpers::redirect("/vendors/documents?vendor_id=$vendorId", 'Document uploaded.');
        } else {
            $errors['doc_file'] = 'File upload failed. Check permissions.';
        }
    }
}

$documents = DB::rows(
    "SELECT d.*, u.full_name AS uploaded_by_name
     FROM vendor_documents d
     LEFT JOIN users u ON u.id = d.uploaded_by
     WHERE d.vendor_id = ?
     ORDER BY d.uploaded_at DESC",
    [$vendorId]
);

$docTypes = ['labour_licence','pf_challan','esi_challan','contract_agreement','gstin_certificate','pan_card','bank_details','other'];

ob_start();
?>

<div class="page-header">
  <div>
    <h1 class="page-title">Documents</h1>
    <p class="page-sub" style="color:var(--clr-text-muted);margin:0;">
      <?= Helpers::h($vendor['vendor_code']) ?> — <?= Helpers::h($vendor['name']) ?>
    </p>
  </div>
  <div class="page-actions">
    <a href="/vendors/<?= $vendorId ?>/edit" class="btn btn-ghost">Back to Contractor</a>
  </div>
</div>

<?php if ($isAdmin): ?>
<div class="card" style="margin-bottom:var(--space-6)">
  <div class="card-header"><h2 class="card-title">Upload Document</h2></div>
  <div class="card-body">
    <?php if (!empty($errors)): ?>
      <div class="alert alert-danger" style="margin-bottom:var(--space-4)">
        <?= implode('<br>', array_map('Helpers::h', $errors)) ?>
      </div>
    <?php endif; ?>
    <form method="POST" action="/vendors/documents?vendor_id=<?= $vendorId ?>" enctype="multipart/form-data" id="uploadForm">
      <input type="hidden" name="<?= CSRF_KEY ?>" value="<?= Helpers::h(Auth::csrfToken()) ?>">
      <input type="hidden" name="action" value="upload">
      <div class="form-grid" style="grid-template-columns:repeat(2,1fr)">

        <div class="form-group">
          <label class="form-label required" for="doc_name">Document Name</label>
          <input type="text" id="doc_name" name="doc_name" class="form-control"
            value="<?= Helpers::h($_POST['doc_name'] ?? '') ?>" data-validate="required">
          <span class="form-error"><?= $errors['doc_name'] ?? '' ?></span>
        </div>

        <div class="form-group">
          <label class="form-label" for="doc_type">Document Type</label>
          <select id="doc_type" name="doc_type" class="form-control">
            <option value="">— Select type —</option>
            <?php foreach ($docTypes as $dt): ?>
              <option value="<?= $dt ?>"><?= ucwords(str_replace('_',' ',$dt)) ?></option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="form-group">
          <label class="form-label" for="doc_file">File (PDF, JPG, PNG — max 5 MB)</label>
          <input type="file" id="doc_file" name="doc_file" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
          <span class="form-error"><?= $errors['doc_file'] ?? '' ?></span>
        </div>

        <div class="form-group">
          <label class="form-label" for="expiry_date">Expiry Date (optional)</label>
          <input type="date" id="expiry_date" name="expiry_date" class="form-control"
            value="<?= Helpers::h($_POST['expiry_date'] ?? '') ?>">
        </div>

      </div>
      <button type="submit" class="btn btn-primary">Upload</button>
    </form>
  </div>
</div>
<?php endif; ?>

<div class="card">
  <div class="card-header"><h2 class="card-title">Uploaded Documents (<?= count($documents) ?>)</h2></div>
  <div class="table-wrapper">
    <table class="data-table">
      <thead>
        <tr>
          <th>Document Name</th>
          <th>Type</th>
          <th>Expiry</th>
          <th>Uploaded By</th>
          <th>Uploaded On</th>
          <?php if ($isAdmin): ?><th style="width:80px">Actions</th><?php endif; ?>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($documents as $doc): ?>
        <?php $expired = $doc['expiry_date'] && $doc['expiry_date'] < date('Y-m-d'); ?>
        <tr>
          <td>
            <a href="/<?= Helpers::h(ltrim($doc['file_path'],'/')) ?>" target="_blank" rel="noopener" style="font-weight:500">
              <?= Helpers::h($doc['doc_name']) ?>
            </a>
          </td>
          <td><?= Helpers::h(ucwords(str_replace('_',' ',$doc['doc_type'] ?? ''))) ?></td>
          <td>
            <?php if ($doc['expiry_date']): ?>
              <span class="<?= $expired ? 'text-danger' : '' ?>">
                <?= Helpers::dateDisplay($doc['expiry_date']) ?>
                <?= $expired ? '⚠' : '' ?>
              </span>
            <?php else: ?>—<?php endif; ?>
          </td>
          <td><?= Helpers::h($doc['uploaded_by_name'] ?? '—') ?></td>
          <td><span style="font-size:var(--text-sm);color:var(--clr-text-muted)"><?= Helpers::dateDisplay($doc['uploaded_at']) ?></span></td>
          <?php if ($isAdmin): ?>
          <td>
            <form method="POST" action="/vendors/documents?vendor_id=<?= $vendorId ?>" style="display:inline"
              onsubmit="return confirm('Delete this document?')">
              <input type="hidden" name="<?= CSRF_KEY ?>" value="<?= Helpers::h(Auth::csrfToken()) ?>">
              <input type="hidden" name="action" value="delete">
              <input type="hidden" name="doc_id" value="<?= $doc['id'] ?>">
              <button type="submit" class="btn btn-danger btn-sm">Delete</button>
            </form>
          </td>
          <?php endif; ?>
        </tr>
        <?php endforeach; ?>
        <?php if (empty($documents)): ?>
          <tr><td colspan="<?= $isAdmin ? 6 : 5 ?>" style="text-align:center;padding:var(--space-8);color:var(--clr-text-muted)">No documents uploaded yet.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?php
$pageContent = ob_get_clean();
$pageTitle   = 'Vendor Documents';
$activeMenu  = 'vendors';
$breadcrumbs = [
    ['label' => 'Contractors', 'url' => '/vendors'],
    ['label' => Helpers::h($vendor['name']), 'url' => "/vendors/$vendorId/edit"],
    ['label' => 'Documents'],
];
include CLMS_ROOT . '/templates/base.html.php';
