<?php
/**
 * CLMS 2.0 — Plants / Locations Master (HR Admin only)
 * GET  /masters/plants                 → plant list
 * GET  /masters/plants?add=1           → add plant form
 * GET  /masters/plants?edit=ID         → edit plant form
 * GET  /masters/plants?plant_id=ID     → plant list + cost centres panel for that plant
 * POST /masters/plants                 → save / toggle / delete plant or cost centre
 */
Auth::requireRole('hr_admin');

$companyId = $_SESSION['company_id'];
$errors    = [];
$success   = '';

// ============================================================
// POST handlers
// ============================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    Auth::requireCsrf();
    $action = Helpers::clean($_POST['action'] ?? '');

    // --------------------------------------------------------
    // Plants: save
    // --------------------------------------------------------
    if ($action === 'save_plant') {
        $id              = (int) ($_POST['id'] ?? 0);
        $code            = Helpers::clean($_POST['code']            ?? '');
        $name            = Helpers::clean($_POST['name']            ?? '');
        $address         = Helpers::clean($_POST['address']         ?? '');
        $city            = Helpers::clean($_POST['city']            ?? '');
        $state           = Helpers::clean($_POST['state']           ?? '');
        $country         = Helpers::clean($_POST['country']         ?? 'India');
        $plantHeadUserId = (int) ($_POST['plant_head_user_id'] ?? 0) ?: null;

        if ($code === '') $errors[] = 'Plant code is required.';
        if ($name === '') $errors[] = 'Plant name is required.';

        if (!$errors) {
            if ($id > 0) {
                DB::execute(
                    "UPDATE plants SET code=?, name=?, address=?, city=?, state=?, country=?, plant_head_user_id=?, updated_at=NOW()
                     WHERE id=? AND company_id=?",
                    [$code, $name, $address ?: null, $city ?: null, $state ?: null, $country ?: 'India',
                     $plantHeadUserId, $id, $companyId]
                );
                AuditLogger::log('UPDATE', 'plants', $id);
                $success = 'Plant updated.';
            } else {
                DB::execute(
                    "INSERT INTO plants (company_id, code, name, address, city, state, country, plant_head_user_id, created_by)
                     VALUES (?,?,?,?,?,?,?,?,?)",
                    [$companyId, $code, $name, $address ?: null, $city ?: null, $state ?: null,
                     $country ?: 'India', $plantHeadUserId, $_SESSION['user_id']]
                );
                AuditLogger::log('CREATE', 'plants', DB::lastInsertId());
                $success = 'Plant added.';
            }
        }

    // --------------------------------------------------------
    // Plants: toggle active
    // --------------------------------------------------------
    } elseif ($action === 'toggle_plant') {
        $id = (int) ($_POST['id'] ?? 0);
        DB::execute("UPDATE plants SET is_active = NOT is_active WHERE id=? AND company_id=?", [$id, $companyId]);
        AuditLogger::log('UPDATE', 'plants', $id, null, ['toggled' => 'is_active']);
        $success = 'Plant status updated.';

    // --------------------------------------------------------
    // Plants: delete
    // --------------------------------------------------------
    } elseif ($action === 'delete_plant') {
        $id = (int) ($_POST['id'] ?? 0);
        // Check if sections, shifts, holidays, cost centres reference this plant
        $secCount = (int) DB::value("SELECT COUNT(*) FROM sections WHERE plant_id=?", [$id]);
        $ccCount  = (int) DB::value("SELECT COUNT(*) FROM cost_centers WHERE plant_id=?", [$id]);
        if ($secCount + $ccCount > 0) {
            $errors[] = "Cannot delete: plant has $secCount section(s) and $ccCount cost centre(s) linked to it. Reassign or delete them first.";
        } else {
            DB::execute("DELETE FROM plants WHERE id=? AND company_id=?", [$id, $companyId]);
            AuditLogger::log('DELETE', 'plants', $id);
            $success = 'Plant deleted.';
        }

    // --------------------------------------------------------
    // Cost Centres: save
    // --------------------------------------------------------
    } elseif ($action === 'save_cc') {
        $ccId    = (int) ($_POST['cc_id']    ?? 0);
        $plantId = (int) ($_POST['plant_id'] ?? 0);
        $code    = Helpers::clean($_POST['cc_code'] ?? '');
        $name    = Helpers::clean($_POST['cc_name'] ?? '');

        // Verify plant belongs to this company
        $plantOwner = DB::value("SELECT company_id FROM plants WHERE id=?", [$plantId]);
        if ((int) $plantOwner !== $companyId) {
            $errors[] = 'Invalid plant.';
        }
        if ($code === '') $errors[] = 'Cost centre code is required.';
        if ($name === '') $errors[] = 'Cost centre name is required.';

        if (!$errors) {
            if ($ccId > 0) {
                DB::execute(
                    "UPDATE cost_centers SET code=?, name=?, updated_at=NOW() WHERE id=? AND company_id=? AND plant_id=?",
                    [$code, $name, $ccId, $companyId, $plantId]
                );
                AuditLogger::log('UPDATE', 'cost_centers', $ccId);
                $success = 'Cost centre updated.';
            } else {
                DB::execute(
                    "INSERT INTO cost_centers (company_id, plant_id, code, name) VALUES (?,?,?,?)",
                    [$companyId, $plantId, $code, $name]
                );
                AuditLogger::log('CREATE', 'cost_centers', DB::lastInsertId());
                $success = 'Cost centre added.';
            }
        }

    // --------------------------------------------------------
    // Cost Centres: toggle active
    // --------------------------------------------------------
    } elseif ($action === 'toggle_cc') {
        $ccId    = (int) ($_POST['cc_id']    ?? 0);
        $plantId = (int) ($_POST['plant_id'] ?? 0);
        DB::execute(
            "UPDATE cost_centers SET is_active = NOT is_active WHERE id=? AND company_id=? AND plant_id=?",
            [$ccId, $companyId, $plantId]
        );
        AuditLogger::log('UPDATE', 'cost_centers', $ccId, null, ['toggled' => 'is_active']);
        $success = 'Cost centre status updated.';

    // --------------------------------------------------------
    // Cost Centres: delete
    // --------------------------------------------------------
    } elseif ($action === 'delete_cc') {
        $ccId    = (int) ($_POST['cc_id']    ?? 0);
        $plantId = (int) ($_POST['plant_id'] ?? 0);
        $inUse   = (int) DB::value("SELECT COUNT(*) FROM sections WHERE cost_center_id=?", [$ccId]);
        if ($inUse > 0) {
            $errors[] = "Cannot delete: cost centre is used by $inUse section(s).";
        } else {
            DB::execute("DELETE FROM cost_centers WHERE id=? AND company_id=? AND plant_id=?", [$ccId, $companyId, $plantId]);
            AuditLogger::log('DELETE', 'cost_centers', $ccId);
            $success = 'Cost centre deleted.';
        }
    }

    // Redirect to prevent double-submit (keep plant_id in URL if working with cost centres)
    if (!$errors && $success) {
        $ccPlantIdRedir = (int) ($_POST['plant_id'] ?? 0);
        $redirectUrl = '/masters/plants' . ($ccPlantIdRedir > 0 && in_array($action, ['save_cc','toggle_cc','delete_cc']) ? '?plant_id=' . $ccPlantIdRedir : '');
        Helpers::redirect($redirectUrl, $success);
    }
}

// ============================================================
// GET: load data
// ============================================================
$editPlantId = (int) ($_GET['edit'] ?? 0);
$editPlant   = $editPlantId
    ? DB::row("SELECT * FROM plants WHERE id=? AND company_id=?", [$editPlantId, $companyId])
    : null;

$plants = DB::rows(
    "SELECT p.*, u.full_name AS plant_head_name
     FROM plants p
     LEFT JOIN users u ON u.id = p.plant_head_user_id
     WHERE p.company_id = ?
     ORDER BY p.code",
    [$companyId]
);

// Users with plant_head role (for the Plant Head dropdown)
$plantHeadUsers = DB::rows(
    "SELECT u.id, u.full_name
     FROM users u
     JOIN user_roles ur ON ur.user_id = u.id
     JOIN roles r ON r.id = ur.role_id
     WHERE r.code = 'plant_head' AND u.company_id = ? AND u.is_active = 1
     ORDER BY u.full_name",
    [$companyId]
);

// ---- Cost Centres panel ----
$ccPlantId  = (int) ($_GET['plant_id'] ?? 0);
$ccPlant    = null;
$costCentres = [];
$editCcId   = 0;
$editCc     = null;

if ($ccPlantId > 0) {
    $ccPlant = DB::row("SELECT * FROM plants WHERE id=? AND company_id=?", [$ccPlantId, $companyId]);
    if ($ccPlant) {
        $editCcId = (int) ($_GET['editcc'] ?? 0);
        $editCc   = $editCcId
            ? DB::row("SELECT * FROM cost_centers WHERE id=? AND plant_id=?", [$editCcId, $ccPlantId])
            : null;
        $costCentres = DB::rows(
            "SELECT * FROM cost_centers WHERE company_id=? AND plant_id=? ORDER BY code",
            [$companyId, $ccPlantId]
        );
    }
}

// ============================================================
// VIEW
// ============================================================
ob_start();
?>
<div class="page-header">
  <div>
    <h1 class="page-title">Plants &amp; Locations</h1>
    <p style="margin:0;color:var(--clr-text-muted);font-size:var(--text-sm)">Manage physical plant locations and their cost centres.</p>
  </div>
  <div class="page-actions">
    <?php if (!$ccPlantId): ?>
      <a href="?add=1" class="btn btn-primary">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
        Add Plant
      </a>
    <?php else: ?>
      <a href="<?= APP_BASE ?>/masters/plants" class="btn btn-secondary">← Back to Plants</a>
    <?php endif; ?>
  </div>
</div>

<?php if ($success): ?>
  <div class="alert alert-success" style="margin-bottom:var(--space-4)"><?= Helpers::h($success) ?></div>
<?php endif; ?>
<?php foreach ($errors as $e): ?>
  <div class="alert alert-danger" style="margin-bottom:var(--space-2)"><?= Helpers::h($e) ?></div>
<?php endforeach; ?>

<?php if (!$ccPlantId): ?>
<!-- ============================================================
     PLANT ADD / EDIT FORM
     ============================================================ -->
<?php if (isset($_GET['add']) || $editPlant): ?>
<div class="card" style="margin-bottom:var(--space-4)">
  <div class="card-header">
    <h3 class="card-title"><?= $editPlant ? 'Edit Plant' : 'Add Plant' ?></h3>
  </div>
  <div class="card-body">
    <form method="POST" action="<?= APP_BASE ?>/masters/plants">
      <input type="hidden" name="<?= CSRF_KEY ?>" value="<?= Helpers::h(Auth::csrfToken()) ?>">
      <input type="hidden" name="action" value="save_plant">
      <input type="hidden" name="id" value="<?= $editPlant ? $editPlant['id'] : 0 ?>">
      <div class="form-grid-3">
        <div class="form-group">
          <label class="form-label required">Plant Code</label>
          <input type="text" name="code" class="form-control" maxlength="20" required
                 value="<?= Helpers::h($editPlant['code'] ?? '') ?>"
                 placeholder="e.g. PLT-KNC">
        </div>
        <div class="form-group" style="grid-column:span 2">
          <label class="form-label required">Plant Name</label>
          <input type="text" name="name" class="form-control" maxlength="150" required
                 value="<?= Helpers::h($editPlant['name'] ?? '') ?>"
                 placeholder="e.g. Kancor Flavours — Angamaly Plant">
        </div>
        <div class="form-group" style="grid-column:span 3">
          <label class="form-label">Address</label>
          <input type="text" name="address" class="form-control" maxlength="255"
                 value="<?= Helpers::h($editPlant['address'] ?? '') ?>">
        </div>
        <div class="form-group">
          <label class="form-label">City</label>
          <input type="text" name="city" class="form-control" maxlength="100"
                 value="<?= Helpers::h($editPlant['city'] ?? '') ?>">
        </div>
        <div class="form-group">
          <label class="form-label">State</label>
          <input type="text" name="state" class="form-control" maxlength="100"
                 value="<?= Helpers::h($editPlant['state'] ?? '') ?>">
        </div>
        <div class="form-group">
          <label class="form-label">Country</label>
          <input type="text" name="country" class="form-control" maxlength="100"
                 value="<?= Helpers::h($editPlant['country'] ?? 'India') ?>">
        </div>
        <div class="form-group" style="grid-column:span 2">
          <label class="form-label">Plant Head</label>
          <select name="plant_head_user_id" class="form-control">
            <option value="">— None —</option>
            <?php foreach ($plantHeadUsers as $u): ?>
              <option value="<?= $u['id'] ?>"
                <?= ($editPlant['plant_head_user_id'] ?? 0) == $u['id'] ? 'selected' : '' ?>>
                <?= Helpers::h($u['full_name']) ?>
              </option>
            <?php endforeach; ?>
          </select>
          <?php if (!$plantHeadUsers): ?>
            <small style="color:var(--clr-text-muted)">No users with Plant Head role. Assign it via User Management first.</small>
          <?php endif; ?>
        </div>
      </div>
      <div style="display:flex;gap:var(--space-3);margin-top:var(--space-4)">
        <button type="submit" class="btn btn-primary">Save Plant</button>
        <a href="<?= APP_BASE ?>/masters/plants" class="btn btn-secondary">Cancel</a>
      </div>
    </form>
  </div>
</div>
<?php endif; ?>

<!-- ============================================================
     PLANTS LIST TABLE
     ============================================================ -->
<div class="card">
  <div class="card-header"><h3 class="card-title">All Plants</h3></div>
  <div class="table-wrapper">
    <table class="data-table">
      <thead>
        <tr>
          <th>Code</th>
          <th>Plant Name</th>
          <th>Location</th>
          <th>Plant Head</th>
          <th>Status</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($plants as $p): ?>
        <tr>
          <td><code><?= Helpers::h($p['code']) ?></code></td>
          <td>
            <strong><?= Helpers::h($p['name']) ?></strong>
            <?php if ($p['address']): ?>
              <div style="font-size:var(--text-xs);color:var(--clr-text-muted)"><?= Helpers::h($p['address']) ?></div>
            <?php endif; ?>
          </td>
          <td>
            <?= Helpers::h(implode(', ', array_filter([$p['city'] ?? '', $p['state'] ?? '']))) ?: '—' ?>
          </td>
          <td><?= Helpers::h($p['plant_head_name'] ?? '—') ?></td>
          <td><?= $p['is_active']
                ? '<span class="badge badge-active">Active</span>'
                : '<span class="badge badge-inactive">Inactive</span>' ?></td>
          <td>
            <div style="display:flex;gap:var(--space-2);flex-wrap:wrap">
              <a href="?edit=<?= $p['id'] ?>" class="btn btn-secondary btn-sm">Edit</a>
              <a href="?plant_id=<?= $p['id'] ?>" class="btn btn-ghost btn-sm">Cost Centres</a>
              <form method="POST" action="<?= APP_BASE ?>/masters/plants" style="display:inline">
                <input type="hidden" name="<?= CSRF_KEY ?>" value="<?= Helpers::h(Auth::csrfToken()) ?>">
                <input type="hidden" name="action" value="toggle_plant">
                <input type="hidden" name="id" value="<?= $p['id'] ?>">
                <button type="submit" class="btn btn-ghost btn-sm"><?= $p['is_active'] ? 'Disable' : 'Enable' ?></button>
              </form>
              <form method="POST" action="<?= APP_BASE ?>/masters/plants" style="display:inline"
                    onsubmit="return confirm('Delete plant <?= Helpers::h(addslashes($p['name'])) ?>?')">
                <input type="hidden" name="<?= CSRF_KEY ?>" value="<?= Helpers::h(Auth::csrfToken()) ?>">
                <input type="hidden" name="action" value="delete_plant">
                <input type="hidden" name="id" value="<?= $p['id'] ?>">
                <button type="submit" class="btn btn-danger btn-sm">Delete</button>
              </form>
            </div>
          </td>
        </tr>
        <?php endforeach; ?>
        <?php if (!$plants): ?>
          <tr><td colspan="6" style="text-align:center;color:var(--clr-text-muted)">No plants defined yet.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?php else: ?>
<!-- ============================================================
     COST CENTRES PANEL  (when ?plant_id=X is present)
     ============================================================ -->
<?php if (!$ccPlant): ?>
  <div class="alert alert-danger">Plant not found.</div>
<?php else: ?>

<div class="card" style="margin-bottom:var(--space-4);border-left:4px solid var(--clr-primary)">
  <div class="card-body" style="padding:var(--space-3) var(--space-4)">
    <strong>Plant:</strong> <?= Helpers::h($ccPlant['code'] . ' — ' . $ccPlant['name']) ?>
    <?php if ($ccPlant['city'] || $ccPlant['state']): ?>
      <span style="color:var(--clr-text-muted);margin-left:var(--space-3)">
        <?= Helpers::h(implode(', ', array_filter([$ccPlant['city'] ?? '', $ccPlant['state'] ?? '']))) ?>
      </span>
    <?php endif; ?>
  </div>
</div>

<!-- Cost Centre Add / Edit Form -->
<?php if (isset($_GET['addcc']) || $editCc): ?>
<div class="card" style="margin-bottom:var(--space-4)">
  <div class="card-header">
    <h3 class="card-title"><?= $editCc ? 'Edit Cost Centre' : 'Add Cost Centre' ?></h3>
  </div>
  <div class="card-body">
    <form method="POST" action="<?= APP_BASE ?>/masters/plants">
      <input type="hidden" name="<?= CSRF_KEY ?>" value="<?= Helpers::h(Auth::csrfToken()) ?>">
      <input type="hidden" name="action" value="save_cc">
      <input type="hidden" name="plant_id" value="<?= $ccPlantId ?>">
      <input type="hidden" name="cc_id" value="<?= $editCc ? $editCc['id'] : 0 ?>">
      <div class="form-grid-3">
        <div class="form-group">
          <label class="form-label required">Code</label>
          <input type="text" name="cc_code" class="form-control" maxlength="30" required
                 value="<?= Helpers::h($editCc['code'] ?? '') ?>"
                 placeholder="e.g. CC-001">
        </div>
        <div class="form-group" style="grid-column:span 2">
          <label class="form-label required">Name</label>
          <input type="text" name="cc_name" class="form-control" maxlength="100" required
                 value="<?= Helpers::h($editCc['name'] ?? '') ?>"
                 placeholder="e.g. Production — Line A">
        </div>
      </div>
      <div style="display:flex;gap:var(--space-3);margin-top:var(--space-4)">
        <button type="submit" class="btn btn-primary">Save Cost Centre</button>
        <a href="<?= APP_BASE ?>/masters/plants?plant_id=<?= $ccPlantId ?>" class="btn btn-secondary">Cancel</a>
      </div>
    </form>
  </div>
</div>
<?php endif; ?>

<!-- Cost Centres List -->
<div class="card">
  <div class="card-header" style="display:flex;justify-content:space-between;align-items:center">
    <h3 class="card-title">Cost Centres — <?= Helpers::h($ccPlant['name']) ?></h3>
    <a href="?plant_id=<?= $ccPlantId ?>&addcc=1" class="btn btn-primary btn-sm">
      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
      Add Cost Centre
    </a>
  </div>
  <div class="table-wrapper">
    <table class="data-table">
      <thead>
        <tr><th>Code</th><th>Name</th><th>Status</th><th>Actions</th></tr>
      </thead>
      <tbody>
        <?php foreach ($costCentres as $cc): ?>
        <tr>
          <td><code><?= Helpers::h($cc['code']) ?></code></td>
          <td><?= Helpers::h($cc['name']) ?></td>
          <td><?= $cc['is_active']
                ? '<span class="badge badge-active">Active</span>'
                : '<span class="badge badge-inactive">Inactive</span>' ?></td>
          <td>
            <div style="display:flex;gap:var(--space-2)">
              <a href="?plant_id=<?= $ccPlantId ?>&editcc=<?= $cc['id'] ?>" class="btn btn-secondary btn-sm">Edit</a>
              <form method="POST" action="<?= APP_BASE ?>/masters/plants" style="display:inline">
                <input type="hidden" name="<?= CSRF_KEY ?>" value="<?= Helpers::h(Auth::csrfToken()) ?>">
                <input type="hidden" name="action" value="toggle_cc">
                <input type="hidden" name="plant_id" value="<?= $ccPlantId ?>">
                <input type="hidden" name="cc_id" value="<?= $cc['id'] ?>">
                <button type="submit" class="btn btn-ghost btn-sm"><?= $cc['is_active'] ? 'Disable' : 'Enable' ?></button>
              </form>
              <form method="POST" action="<?= APP_BASE ?>/masters/plants" style="display:inline"
                    onsubmit="return confirm('Delete cost centre <?= Helpers::h(addslashes($cc['name'])) ?>?')">
                <input type="hidden" name="<?= CSRF_KEY ?>" value="<?= Helpers::h(Auth::csrfToken()) ?>">
                <input type="hidden" name="action" value="delete_cc">
                <input type="hidden" name="plant_id" value="<?= $ccPlantId ?>">
                <input type="hidden" name="cc_id" value="<?= $cc['id'] ?>">
                <button type="submit" class="btn btn-danger btn-sm">Delete</button>
              </form>
            </div>
          </td>
        </tr>
        <?php endforeach; ?>
        <?php if (!$costCentres): ?>
          <tr><td colspan="4" style="text-align:center;color:var(--clr-text-muted)">No cost centres for this plant yet.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?php endif; // $ccPlant check ?>
<?php endif; // $ccPlantId check ?>

<?php
$pageContent = ob_get_clean();
$pageTitle   = 'Plants & Locations';
$activeMenu  = 'masters';
$breadcrumbs = [['label' => 'Masters'], ['label' => 'Plants & Locations']];
include CLMS_ROOT . '/templates/base.html.php';
