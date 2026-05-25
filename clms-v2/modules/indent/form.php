<?php
/**
 * CLMS 2.0 — Indent Create / Edit
 */
Auth::requireRole(['hr_admin','section_incharge']);

$companyId = $_SESSION['company_id'];
$id        = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$isEdit    = $id > 0;
$errors    = [];

$record = $isEdit ? DB::row("SELECT * FROM indents WHERE id=? AND company_id=?", [$id, $companyId]) : null;
if ($isEdit && !$record) { Helpers::redirect('/indent', 'Indent not found.', 'error'); }
if ($isEdit && !in_array($record['status'], ['draft','submitted'])) {
    Helpers::redirect('/indent/'.$id.'/view', 'Cannot edit an indent in this state.', 'error');
}

$lines = $isEdit ? DB::rows("SELECT * FROM indent_lines WHERE indent_id=?", [$id]) : [];

$sections   = DB::rows("SELECT id, name FROM sections WHERE company_id=? AND is_active=1 ORDER BY name", [$companyId]);
$categories = DB::rows("SELECT id, code, name FROM labour_categories WHERE company_id=? AND is_active=1 ORDER BY name", [$companyId]);
$shifts     = DB::rows("SELECT id, code, name FROM shifts WHERE company_id=? AND is_active=1 ORDER BY code", [$companyId]);
$vendors    = DB::rows("SELECT id, vendor_code, name FROM vendors WHERE company_id=? AND status='active' ORDER BY name", [$companyId]);

// Section incharge: only their sections
if (!Auth::hasRole('hr_admin')) {
    $mySecIds = $_SESSION['section_ids'] ?? [];
    $sections = array_filter($sections, fn($s) => in_array($s['id'], $mySecIds));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    Auth::requireCsrf();

    $sectionId   = (int)($_POST['section_id']   ?? 0);
    $startDate   = Helpers::clean($_POST['start_date']  ?? '');
    $endDate     = Helpers::clean($_POST['end_date']    ?? '');
    $indentType  = Helpers::clean($_POST['indent_type'] ?? 'range');
    $isUrgent    = isset($_POST['is_urgent']) ? 1 : 0;
    $natureWork  = Helpers::clean($_POST['nature_of_work'] ?? '');
    $remarks     = Helpers::clean($_POST['remarks']        ?? '');
    $submitAction = Helpers::clean($_POST['submit_action'] ?? 'draft');

    if (!$sectionId) $errors[] = 'Section is required.';
    if (!$startDate) $errors[] = 'Start date is required.';
    if (!$endDate)   $errors[] = 'End date is required.';
    if ($startDate && $endDate && $startDate > $endDate) $errors[] = 'End date must be after start date.';

    // Lines
    $lineData = [];
    $lineShifts     = (array)($_POST['line_shift_id']    ?? []);
    $lineCategories = (array)($_POST['line_category_id'] ?? []);
    $lineCounts     = (array)($_POST['line_count']        ?? []);
    for ($i = 0; $i < count($lineShifts); $i++) {
        if (!$lineShifts[$i] || !$lineCategories[$i]) continue;
        $lineData[] = [
            'shift_id'    => (int)$lineShifts[$i],
            'category_id' => (int)$lineCategories[$i],
            'count'       => max(1, (int)$lineCounts[$i]),
        ];
    }
    if (!$lineData) $errors[] = 'At least one indent line (shift + category + count) is required.';

    if (!$errors) {
        $newStatus = ($submitAction === 'submit') ? 'submitted' : 'draft';

        // Generate indent_no if new
        $indentNo = $record['indent_no'] ?? null;
        if (!$indentNo) {
            $seq      = (int) DB::value("SELECT COUNT(*) FROM indents WHERE company_id=?", [$companyId]) + 1;
            $indentNo = 'IND-' . date('Ym') . '-' . str_pad($seq, 4, '0', STR_PAD_LEFT);
        }

        if ($isEdit) {
            DB::execute(
                "UPDATE indents SET section_id=?,start_date=?,end_date=?,indent_type=?,is_urgent=?,nature_of_work=?,remarks=?,status=?,updated_at=NOW() WHERE id=?",
                [$sectionId,$startDate,$endDate,$indentType,$isUrgent,$natureWork,$remarks,$newStatus,$id]
            );
            DB::execute("DELETE FROM indent_lines WHERE indent_id=?", [$id]);
            AuditLogger::log('UPDATE', 'indents', $id);
        } else {
            DB::execute(
                "INSERT INTO indents (company_id,indent_no,section_id,start_date,end_date,indent_type,is_urgent,nature_of_work,remarks,status,created_by) VALUES (?,?,?,?,?,?,?,?,?,?,?)",
                [$companyId,$indentNo,$sectionId,$startDate,$endDate,$indentType,$isUrgent,$natureWork,$remarks,$newStatus,$_SESSION['user_id']]
            );
            $id = DB::lastInsertId();
            AuditLogger::log('CREATE', 'indents', $id);
        }

        foreach ($lineData as $line) {
            DB::execute(
                "INSERT INTO indent_lines (indent_id,shift_id,category_id,required_count) VALUES (?,?,?,?)",
                [$id, $line['shift_id'], $line['category_id'], $line['count']]
            );
        }

        $msg = $newStatus === 'submitted' ? 'Indent submitted for approval.' : 'Indent saved as draft.';
        Helpers::redirect('/indent/'.$id.'/view', $msg, 'success');
    }
}

ob_start();
$title = $isEdit ? 'Edit Indent' : 'New Indent';
?>
<div class="page-header">
  <div><h1 class="page-title"><?= $title ?></h1></div>
</div>

<?php foreach ($errors as $e): ?>
  <div class="alert alert-danger" style="margin-bottom:var(--space-2)"><?= Helpers::h($e) ?></div>
<?php endforeach; ?>

<form method="POST" action="<?= $isEdit ? '/indent/'.$id.'/edit' : '/indent/create' ?>">
  <input type="hidden" name="csrf_token" value="<?= Helpers::h($_SESSION['csrf_token']) ?>">

  <div class="card" style="margin-bottom:var(--space-4)">
    <div class="card-header"><h3 class="card-title">Indent Details</h3></div>
    <div class="card-body">
      <div class="form-grid-3">
        <div class="form-group">
          <label class="form-label required">Section</label>
          <select name="section_id" class="form-control" required>
            <option value="">— Select —</option>
            <?php foreach ($sections as $sec): ?>
              <option value="<?= $sec['id'] ?>" <?= ($record['section_id'] ?? 0) == $sec['id'] ? 'selected' : '' ?>>
                <?= Helpers::h($sec['name']) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="form-group">
          <label class="form-label required">Start Date</label>
          <input type="date" name="start_date" class="form-control" required value="<?= Helpers::h($record['start_date'] ?? '') ?>">
        </div>
        <div class="form-group">
          <label class="form-label required">End Date</label>
          <input type="date" name="end_date" class="form-control" required value="<?= Helpers::h($record['end_date'] ?? '') ?>">
        </div>
        <div class="form-group">
          <label class="form-label">Type</label>
          <select name="indent_type" class="form-control">
            <option value="range"   <?= ($record['indent_type']??'range') === 'range'   ? 'selected':'' ?>>Date Range</option>
            <option value="monthly" <?= ($record['indent_type']??'')      === 'monthly' ? 'selected':'' ?>>Monthly</option>
            <option value="daily"   <?= ($record['indent_type']??'')      === 'daily'   ? 'selected':'' ?>>Daily</option>
          </select>
        </div>
        <div class="form-group" style="display:flex;align-items:flex-end">
          <label style="display:flex;align-items:center;gap:var(--space-2);cursor:pointer">
            <input type="checkbox" name="is_urgent" value="1" <?= ($record['is_urgent'] ?? 0) ? 'checked':'' ?>>
            Mark as Urgent
          </label>
        </div>
      </div>
      <div class="form-group">
        <label class="form-label">Nature of Work</label>
        <textarea name="nature_of_work" class="form-control" rows="2"><?= Helpers::h($record['nature_of_work'] ?? '') ?></textarea>
      </div>
      <div class="form-group">
        <label class="form-label">Remarks</label>
        <textarea name="remarks" class="form-control" rows="2"><?= Helpers::h($record['remarks'] ?? '') ?></textarea>
      </div>
    </div>
  </div>

  <!-- Indent Lines -->
  <div class="card" style="margin-bottom:var(--space-4)">
    <div class="card-header" style="display:flex;justify-content:space-between;align-items:center">
      <h3 class="card-title">Manpower Requirements</h3>
      <button type="button" class="btn btn-secondary btn-sm" onclick="addLine()">+ Add Line</button>
    </div>
    <div class="card-body" style="padding:0">
      <table class="data-table" id="linesTable">
        <thead>
          <tr><th>Shift</th><th>Category</th><th>Required Count</th><th></th></tr>
        </thead>
        <tbody id="linesBody">
          <?php if ($lines): foreach ($lines as $ln): ?>
          <tr>
            <td>
              <select name="line_shift_id[]" class="form-control" required>
                <option value="">— Shift —</option>
                <?php foreach ($shifts as $sh): ?>
                  <option value="<?= $sh['id'] ?>" <?= $ln['shift_id'] == $sh['id'] ? 'selected':'' ?>><?= Helpers::h($sh['code'].' — '.$sh['name']) ?></option>
                <?php endforeach; ?>
              </select>
            </td>
            <td>
              <select name="line_category_id[]" class="form-control" required>
                <option value="">— Category —</option>
                <?php foreach ($categories as $cat): ?>
                  <option value="<?= $cat['id'] ?>" <?= $ln['category_id'] == $cat['id'] ? 'selected':'' ?>><?= Helpers::h($cat['code'].' — '.$cat['name']) ?></option>
                <?php endforeach; ?>
              </select>
            </td>
            <td><input type="number" name="line_count[]" class="form-control" min="1" value="<?= $ln['required_count'] ?>"></td>
            <td><button type="button" class="btn btn-danger btn-sm" onclick="this.closest('tr').remove()">×</button></td>
          </tr>
          <?php endforeach; else: ?>
          <tr id="emptyRow"><td colspan="4" style="text-align:center;color:var(--clr-text-muted);padding:var(--space-4)">Click "Add Line" to add manpower requirements.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>

  <div style="display:flex;gap:var(--space-3)">
    <button type="submit" name="submit_action" value="draft" class="btn btn-secondary">Save as Draft</button>
    <button type="submit" name="submit_action" value="submit" class="btn btn-primary">Submit for Approval</button>
    <a href="/indent" class="btn btn-ghost">Cancel</a>
  </div>
</form>

<script>
const shiftsOpts = <?= json_encode(array_map(fn($s) => ['id'=>$s['id'],'label'=>$s['code'].' — '.$s['name']], $shifts)) ?>;
const catsOpts   = <?= json_encode(array_map(fn($c) => ['id'=>$c['id'],'label'=>$c['code'].' — '.$c['name']], $categories)) ?>;

function buildSelect(name, opts) {
    let s = `<select name="${name}" class="form-control" required><option value="">— Select —</option>`;
    opts.forEach(o => s += `<option value="${o.id}">${o.label}</option>`);
    return s + '</select>';
}
function addLine() {
    document.getElementById('emptyRow')?.remove();
    const tr = document.createElement('tr');
    tr.innerHTML = `<td>${buildSelect('line_shift_id[]', shiftsOpts)}</td>
        <td>${buildSelect('line_category_id[]', catsOpts)}</td>
        <td><input type="number" name="line_count[]" class="form-control" min="1" value="1"></td>
        <td><button type="button" class="btn btn-danger btn-sm" onclick="this.closest('tr').remove()">×</button></td>`;
    document.getElementById('linesBody').appendChild(tr);
}
</script>

<?php
$pageContent = ob_get_clean();
$pageTitle   = $title;
$activeMenu  = 'indent';
$breadcrumbs = [['label' => 'Indents', 'url' => '/indent'], ['label' => $title]];
include CLMS_ROOT . '/templates/base.html.php';
