<?php
/**
 * CLMS 2.0 — Sections Master (HR Admin only)
 */
Auth::requireRole('hr_admin');

$companyId = $_SESSION['company_id'];
$errors    = [];
$success   = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    Auth::requireCsrf();
    $action = Helpers::clean($_POST['action'] ?? '');

    if ($action === 'save') {
        $id           = (int) ($_POST['id']             ?? 0);
        $code         = Helpers::clean($_POST['code']   ?? '');
        $name         = Helpers::clean($_POST['name']   ?? '');
        $area         = Helpers::clean($_POST['area']   ?? '');
        $plantId      = (int) ($_POST['plant_id']       ?? 0) ?: null;
        $costCenterId = (int) ($_POST['cost_center_id'] ?? 0) ?: null;
        $hodUserId    = (int) ($_POST['hod_user_id']    ?? 0) ?: null;

        if ($code === '') $errors[] = 'Section code is required.';
        if ($name === '') $errors[] = 'Section name is required.';

        if (!$errors) {
            if ($id > 0) {
                DB::execute(
                    "UPDATE sections SET code=?, name=?, area=?, plant_id=?, cost_center_id=?, hod_user_id=?, updated_at=NOW() WHERE id=? AND company_id=?",
                    [$code, $name, $area ?: null, $plantId, $costCenterId, $hodUserId, $id, $companyId]
                );
                AuditLogger::log('UPDATE', 'sections', $id);
            } else {
                DB::execute(
                    "INSERT INTO sections (company_id,plant_id,code,name,area,cost_center_id,hod_user_id,created_by) VALUES (?,?,?,?,?,?,?,?)",
                    [$companyId, $plantId, $code, $name, $area ?: null, $costCenterId, $hodUserId, $_SESSION['user_id']]
                );
                AuditLogger::log('CREATE', 'sections', DB::lastInsertId());
            }
            $success = 'Section saved.';
        }
    } elseif ($action === 'toggle') {
        $id = (int) ($_POST['id'] ?? 0);
        DB::execute("UPDATE sections SET is_active = NOT is_active, updated_at=NOW() WHERE id=? AND company_id=?", [$id, $companyId]);
        AuditLogger::log('UPDATE', 'sections', $id, null, ['toggled' => 'is_active']);
        $success = 'Section status updated.';
    } elseif ($action === 'delete') {
        $id = (int) ($_POST['id'] ?? 0);
        $inUse = (int) DB::value("SELECT COUNT(*) FROM employees WHERE section_id=?", [$id]);
        if ($inUse > 0) {
            $errors[] = 'Cannot delete: section is linked to ' . $inUse . ' employee(s).';
        } else {
            DB::execute("DELETE FROM sections WHERE id=? AND company_id=?", [$id, $companyId]);
            AuditLogger::log('DELETE', 'sections', $id);
            $success = 'Section deleted.';
        }
    }
}

$editId   = (int) ($_GET['edit'] ?? 0);
$editRow  = $editId ? DB::row("SELECT * FROM sections WHERE id=? AND company_id=?", [$editId, $companyId]) : null;
$sections = DB::rows(
    "SELECT s.*,
            pl.name AS plant_name,
            pl.code AS plant_code,
            cc.name AS cost_center_name,
            hod.full_name AS hod_name
     FROM sections s
     LEFT JOIN plants pl ON pl.id = s.plant_id
     LEFT JOIN cost_centers cc ON cc.id = s.cost_center_id
     LEFT JOIN users hod ON hod.id = s.hod_user_id
     WHERE s.company_id=? ORDER BY pl.code, s.code",
    [$companyId]
);

// Plants for dropdown
$plants = DB::rows("SELECT id, code, name FROM plants WHERE company_id=? AND is_active=1 ORDER BY code", [$companyId]);

// Cost centres with plant_id for JS-based filtering
$costCenters = DB::rows(
    "SELECT id, code, name, plant_id FROM cost_centers WHERE company_id=? AND is_active=1 ORDER BY name",
    [$companyId]
);

// Users with hod role for this company
$hodUsers = DB::rows(
    "SELECT DISTINCT u.id, u.full_name FROM users u
     JOIN user_roles ur ON ur.user_id = u.id
     JOIN roles r ON r.id = ur.role_id
     WHERE u.company_id=? AND u.is_active=1 AND r.code='hod'
     ORDER BY u.full_name",
    [$companyId]
);

// Section in-charges per section (from user_sections table)
$sectionInchargeMap = [];
$inchargeRows = DB::rows(
    "SELECT us.section_id, u.full_name FROM user_sections us
     JOIN users u ON u.id = us.user_id
     JOIN user_roles ur ON ur.user_id = us.user_id
     JOIN roles r ON r.id = ur.role_id
     WHERE u.company_id=? AND u.is_active=1 AND r.code='section_incharge'
     ORDER BY us.section_id, u.full_name",
    [$companyId]
);
foreach ($inchargeRows as $row) {
    $sectionInchargeMap[$row['section_id']][] = $row['full_name'];
}

ob_start();
?>
<div class="page-header">
  <div><h1 class="page-title">Sections</h1></div>
  <div class="page-actions">
    <a href="?add=1" class="btn btn-primary">
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
      Add Section
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
  <div class="card-header"><h3 class="card-title"><?= $editRow ? 'Edit Section' : 'Add Section' ?></h3></div>
  <div class="card-body">
    <form method="POST" action="<?= APP_BASE ?>/masters/sections">
      <input type="hidden" name="<?= CSRF_KEY ?>" value="<?= Helpers::h(Auth::csrfToken()) ?>">
      <input type="hidden" name="action" value="save">
      <input type="hidden" name="id" value="<?= $editRow ? $editRow['id'] : 0 ?>">
      <div class="form-grid-3">
        <div class="form-group">
          <label class="form-label required">Code</label>
          <input type="text" name="code" class="form-control" maxlength="30" required
                 value="<?= Helpers::h($editRow['code'] ?? '') ?>">
        </div>
        <div class="form-group">
          <label class="form-label required">Name</label>
          <input type="text" name="name" class="form-control" maxlength="100" required
                 value="<?= Helpers::h($editRow['name'] ?? '') ?>">
        </div>
        <div class="form-group">
          <label class="form-label">Area / Department</label>
          <input type="text" name="area" class="form-control" maxlength="100"
                 value="<?= Helpers::h($editRow['area'] ?? '') ?>">
        </div>
        <div class="form-group">
          <label class="form-label">Plant / Location</label>
          <select name="plant_id" id="sectionPlantSel" class="form-control">
            <option value="">— None / Company-wide —</option>
            <?php foreach ($plants as $pl): ?>
              <option value="<?= $pl['id'] ?>"
                <?= ($editRow['plant_id'] ?? 0) == $pl['id'] ? 'selected' : '' ?>>
                <?= Helpers::h($pl['code'] . ' — ' . $pl['name']) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="form-group">
          <label class="form-label">Cost Centre</label>
          <select name="cost_center_id" id="sectionCcSel" class="form-control">
            <option value="">— None —</option>
            <?php foreach ($costCenters as $cc): ?>
              <option value="<?= $cc['id'] ?>"
                      data-plant="<?= (int)($cc['plant_id'] ?? 0) ?>"
                <?= ($editRow['cost_center_id'] ?? 0) == $cc['id'] ? 'selected' : '' ?>>
                <?= Helpers::h($cc['code'] . ' — ' . $cc['name']) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="form-group">
          <label class="form-label">HOD (Head of Department)</label>
          <select name="hod_user_id" class="form-control">
            <option value="">— None —</option>
            <?php foreach ($hodUsers as $u): ?>
              <option value="<?= $u['id'] ?>"
                <?= ($editRow['hod_user_id'] ?? 0) == $u['id'] ? 'selected' : '' ?>>
                <?= Helpers::h($u['full_name']) ?>
              </option>
            <?php endforeach; ?>
          </select>
          <?php if (!$hodUsers): ?>
            <small style="color:var(--clr-text-muted)">No users with HOD role found. Assign HOD role via User Management.</small>
          <?php endif; ?>
        </div>
      </div>
      <script>
      (function(){
        var plantSel = document.getElementById('sectionPlantSel');
        var ccSel    = document.getElementById('sectionCcSel');
        function filterCC() {
          var plantId = plantSel.value;
          Array.from(ccSel.options).forEach(function(o) {
            if (!o.value) { o.hidden = false; return; }
            o.hidden = !!(plantId && o.dataset.plant !== plantId && o.dataset.plant !== '0');
          });
          if (ccSel.selectedOptions[0] && ccSel.selectedOptions[0].hidden) ccSel.value = '';
        }
        plantSel.addEventListener('change', filterCC);
        filterCC();
      })();
      </script>
      </div>
      <div style="display:flex;gap:var(--space-3);margin-top:var(--space-4)">
        <button type="submit" class="btn btn-primary">Save Section</button>
        <a href="<?= APP_BASE ?>/masters/sections" class="btn btn-secondary">Cancel</a>
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
          <th>Code</th>
          <th>Name</th>
          <th>Area</th>
          <th>Plant</th>
          <th>Cost Centre</th>
          <th>Section In-charge(s)</th>
          <th>HOD</th>
          <th>Status</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($sections as $s): ?>
        <?php $incharges = $sectionInchargeMap[$s['id']] ?? []; ?>
        <tr>
          <td><code><?= Helpers::h($s['code']) ?></code></td>
          <td><?= Helpers::h($s['name']) ?></td>
          <td><?= Helpers::h($s['area'] ?? '—') ?></td>
          <td><?= Helpers::h($s['plant_name'] ?? '—') ?></td>
          <td><?= Helpers::h($s['cost_center_name'] ?? '—') ?></td>
          <td>
            <?php if ($incharges): ?>
              <?php foreach ($incharges as $ic): ?>
                <span class="badge" style="background:var(--clr-primary-light,#e8f0fe);color:var(--clr-primary);margin:1px"><?= Helpers::h($ic) ?></span>
              <?php endforeach; ?>
            <?php else: ?>
              <span style="color:var(--clr-text-muted)">—</span>
            <?php endif; ?>
          </td>
          <td><?= Helpers::h($s['hod_name'] ?? '—') ?></td>
          <td><?= $s['is_active'] ? '<span class="badge badge-active">Active</span>' : '<span class="badge badge-inactive">Inactive</span>' ?></td>
          <td>
            <div style="display:flex;gap:var(--space-2)">
              <a href="?edit=<?= $s['id'] ?>" class="btn btn-secondary btn-sm">Edit</a>
              <form method="POST" action="<?= APP_BASE ?>/masters/sections" style="display:inline">
                <input type="hidden" name="<?= CSRF_KEY ?>" value="<?= Helpers::h(Auth::csrfToken()) ?>">
                <input type="hidden" name="action" value="toggle">
                <input type="hidden" name="id" value="<?= $s['id'] ?>">
                <button type="submit" class="btn btn-ghost btn-sm"><?= $s['is_active'] ? 'Disable' : 'Enable' ?></button>
              </form>
              <form method="POST" action="<?= APP_BASE ?>/masters/sections" style="display:inline"
                    onsubmit="return confirm('Delete this section?')">
                <input type="hidden" name="<?= CSRF_KEY ?>" value="<?= Helpers::h(Auth::csrfToken()) ?>">
                <input type="hidden" name="action" value="delete">
                <input type="hidden" name="id" value="<?= $s['id'] ?>">
                <button type="submit" class="btn btn-danger btn-sm">Delete</button>
              </form>
            </div>
          </td>
        </tr>
        <?php endforeach; ?>
        <?php if (!$sections): ?>
          <tr><td colspan="8" style="text-align:center;color:var(--clr-text-muted)">No sections defined yet.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?php
$pageContent = ob_get_clean();
$pageTitle   = 'Sections';
$activeMenu  = 'masters';
$breadcrumbs = [['label' => 'Masters'], ['label' => 'Sections']];
include CLMS_ROOT . '/templates/base.html.php';
