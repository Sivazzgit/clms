<?php
/**
 * CLMS 2.0 — Wage Rates Master (HR Admin only)
 * category_wage_rates: category × wage_component × effective_from → amount
 */
Auth::requireRole('hr_admin');

$companyId = $_SESSION['company_id'];
$errors    = [];
$success   = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    Auth::requireCsrf();
    $action = Helpers::clean($_POST['action'] ?? '');

    if ($action === 'save') {
        $id            = (int) ($_POST['id'] ?? 0);
        $categoryId    = (int) ($_POST['category_id']   ?? 0);
        $componentId   = (int) ($_POST['component_id']  ?? 0);
        $effectiveFrom = Helpers::clean($_POST['effective_from'] ?? '');
        $effectiveTo   = Helpers::clean($_POST['effective_to']   ?? '') ?: null;
        $amount        = (float) ($_POST['amount'] ?? 0);
        $isOtRate      = isset($_POST['is_ot_rate'])      ? 1 : 0;
        $isHolidayRate = isset($_POST['is_holiday_rate']) ? 1 : 0;

        if (!$categoryId)    $errors[] = 'Category is required.';
        if (!$componentId)   $errors[] = 'Wage component is required.';
        if (!$effectiveFrom) $errors[] = 'Effective from date is required.';
        if ($amount <= 0)    $errors[] = 'Amount must be greater than 0.';

        if (!$errors) {
            if ($id > 0) {
                DB::execute(
                    "UPDATE category_wage_rates SET category_id=?, component_id=?, effective_from=?, effective_to=?, amount=?, is_ot_rate=?, is_holiday_rate=? WHERE id=? AND company_id=?",
                    [$categoryId, $componentId, $effectiveFrom, $effectiveTo, $amount, $isOtRate, $isHolidayRate, $id, $companyId]
                );
                AuditLogger::log('UPDATE', 'category_wage_rates', $id);
            } else {
                DB::execute(
                    "INSERT INTO category_wage_rates (company_id,category_id,component_id,effective_from,effective_to,amount,is_ot_rate,is_holiday_rate,created_by) VALUES (?,?,?,?,?,?,?,?,?)",
                    [$companyId, $categoryId, $componentId, $effectiveFrom, $effectiveTo, $amount, $isOtRate, $isHolidayRate, $_SESSION['user_id']]
                );
                AuditLogger::log('CREATE', 'category_wage_rates', DB::lastInsertId());
            }
            $success = 'Wage rate saved.';
        }
    } elseif ($action === 'delete') {
        $id = (int) ($_POST['id'] ?? 0);
        DB::execute("DELETE FROM category_wage_rates WHERE id=? AND company_id=?", [$id, $companyId]);
        AuditLogger::log('DELETE', 'category_wage_rates', $id);
        $success = 'Wage rate deleted.';
    }
}

// Filter
$filterCat = (int) ($_GET['category_id'] ?? 0);

$editId  = (int) ($_GET['edit'] ?? 0);
$editRow = $editId ? DB::row("SELECT * FROM category_wage_rates WHERE id=? AND company_id=?", [$editId, $companyId]) : null;

$categories = DB::rows("SELECT id, code, name FROM labour_categories WHERE company_id=? AND is_active=1 ORDER BY name", [$companyId]);
$components = DB::rows("SELECT id, code, name, type FROM wage_components WHERE company_id=? AND is_active=1 ORDER BY sort_order, name", [$companyId]);

$bindR = [$companyId];
$whereR = 'cwr.company_id=?';
if ($filterCat) { $whereR .= ' AND cwr.category_id=?'; $bindR[] = $filterCat; }

$rates = DB::rows(
    "SELECT cwr.*, lc.name AS category_name, wc.name AS component_name, wc.type AS component_type
     FROM category_wage_rates cwr
     JOIN labour_categories lc ON lc.id = cwr.category_id
     JOIN wage_components wc    ON wc.id = cwr.component_id
     WHERE $whereR
     ORDER BY lc.name, cwr.effective_from DESC, wc.sort_order",
    $bindR
);

ob_start();
?>
<div class="page-header">
  <div><h1 class="page-title">Wage Rates</h1></div>
  <div class="page-actions">
    <a href="?add=1<?= $filterCat ? '&category_id='.$filterCat : '' ?>" class="btn btn-primary">
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
      Add Rate
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
  <div class="card-header"><h3 class="card-title"><?= $editRow ? 'Edit Wage Rate' : 'Add Wage Rate' ?></h3></div>
  <div class="card-body">
    <form method="POST" action="/masters/wage-rates">
      <input type="hidden" name="csrf_token" value="<?= Helpers::h($_SESSION['csrf_token']) ?>">
      <input type="hidden" name="action" value="save">
      <input type="hidden" name="id" value="<?= $editRow ? $editRow['id'] : 0 ?>">
      <div class="form-grid-3">
        <div class="form-group">
          <label class="form-label required">Labour Category</label>
          <select name="category_id" class="form-control" required>
            <option value="">— Select —</option>
            <?php foreach ($categories as $cat): ?>
              <option value="<?= $cat['id'] ?>"
                <?= ($editRow['category_id'] ?? $filterCat) == $cat['id'] ? 'selected' : '' ?>>
                <?= Helpers::h($cat['code'] . ' — ' . $cat['name']) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="form-group">
          <label class="form-label required">Wage Component</label>
          <select name="component_id" class="form-control" required>
            <option value="">— Select —</option>
            <?php foreach ($components as $wc): ?>
              <option value="<?= $wc['id'] ?>"
                <?= ($editRow['component_id'] ?? 0) == $wc['id'] ? 'selected' : '' ?>>
                <?= Helpers::h($wc['name']) ?> (<?= $wc['type'] ?>)
              </option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="form-group">
          <label class="form-label required">Amount (₹)</label>
          <input type="number" name="amount" class="form-control" min="0" step="0.01" required
                 value="<?= $editRow ? $editRow['amount'] : '' ?>">
        </div>
        <div class="form-group">
          <label class="form-label required">Effective From</label>
          <input type="date" name="effective_from" class="form-control" required
                 value="<?= Helpers::h($editRow['effective_from'] ?? date('Y-m-01')) ?>">
        </div>
        <div class="form-group">
          <label class="form-label">Effective To</label>
          <input type="date" name="effective_to" class="form-control"
                 value="<?= Helpers::h($editRow['effective_to'] ?? '') ?>">
          <small class="form-hint">Leave blank for open-ended.</small>
        </div>
        <div class="form-group" style="display:flex;flex-direction:column;justify-content:flex-end">
          <div style="display:flex;gap:var(--space-4)">
            <label style="display:flex;align-items:center;gap:var(--space-2);cursor:pointer">
              <input type="checkbox" name="is_ot_rate" value="1"
                     <?= ($editRow['is_ot_rate'] ?? 0) ? 'checked' : '' ?>>
              OT Rate
            </label>
            <label style="display:flex;align-items:center;gap:var(--space-2);cursor:pointer">
              <input type="checkbox" name="is_holiday_rate" value="1"
                     <?= ($editRow['is_holiday_rate'] ?? 0) ? 'checked' : '' ?>>
              Holiday Rate
            </label>
          </div>
        </div>
      </div>
      <div style="display:flex;gap:var(--space-3);margin-top:var(--space-4)">
        <button type="submit" class="btn btn-primary">Save Rate</button>
        <a href="/masters/wage-rates" class="btn btn-secondary">Cancel</a>
      </div>
    </form>
  </div>
</div>
<?php endif; ?>

<!-- Filter bar -->
<div class="card">
  <div class="table-toolbar">
    <form method="GET" action="/masters/wage-rates" style="display:flex;gap:var(--space-3)">
      <select name="category_id" class="form-control" style="width:auto;" onchange="this.form.submit()">
        <option value="">All Categories</option>
        <?php foreach ($categories as $cat): ?>
          <option value="<?= $cat['id'] ?>" <?= $filterCat == $cat['id'] ? 'selected' : '' ?>>
            <?= Helpers::h($cat['name']) ?>
          </option>
        <?php endforeach; ?>
      </select>
      <?php if ($filterCat): ?>
        <a href="/masters/wage-rates" class="btn btn-ghost">Clear</a>
      <?php endif; ?>
    </form>
  </div>
  <div class="table-wrapper">
    <table class="data-table">
      <thead>
        <tr><th>Category</th><th>Component</th><th>Amount (₹)</th><th>Effective From</th><th>Effective To</th><th>Flags</th><th>Actions</th></tr>
      </thead>
      <tbody>
        <?php foreach ($rates as $r): ?>
        <tr>
          <td><?= Helpers::h($r['category_name']) ?></td>
          <td><?= Helpers::h($r['component_name']) ?> <small style="color:var(--clr-text-muted)">(<?= $r['component_type'] ?>)</small></td>
          <td style="text-align:right">₹<?= number_format($r['amount'], 2) ?></td>
          <td><?= Helpers::dateDisplay($r['effective_from']) ?></td>
          <td><?= $r['effective_to'] ? Helpers::dateDisplay($r['effective_to']) : '<span style="color:var(--clr-text-muted)">—</span>' ?></td>
          <td>
            <?= $r['is_ot_rate']      ? '<span class="badge badge-pending">OT</span> '      : '' ?>
            <?= $r['is_holiday_rate'] ? '<span class="badge badge-pending">Holiday</span>' : '' ?>
          </td>
          <td>
            <div style="display:flex;gap:var(--space-2)">
              <a href="?edit=<?= $r['id'] ?>" class="btn btn-secondary btn-sm">Edit</a>
              <form method="POST" action="/masters/wage-rates" style="display:inline"
                    onsubmit="return confirm('Delete this rate?')">
                <input type="hidden" name="csrf_token" value="<?= Helpers::h($_SESSION['csrf_token']) ?>">
                <input type="hidden" name="action" value="delete">
                <input type="hidden" name="id" value="<?= $r['id'] ?>">
                <button type="submit" class="btn btn-danger btn-sm">Delete</button>
              </form>
            </div>
          </td>
        </tr>
        <?php endforeach; ?>
        <?php if (!$rates): ?>
          <tr><td colspan="7" style="text-align:center;color:var(--clr-text-muted)">No wage rates defined yet.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?php
$pageContent = ob_get_clean();
$pageTitle   = 'Wage Rates';
$activeMenu  = 'masters';
$breadcrumbs = [['label' => 'Masters'], ['label' => 'Wage Rates']];
include CLMS_ROOT . '/templates/base.html.php';
