<?php
/**
 * CLMS 2.0 — Labour Categories Master (HR Admin only)
 */
Auth::requireRole('hr_admin');

$companyId = $_SESSION['company_id'];
$errors    = [];
$success   = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    Auth::requireCsrf();
    $action = Helpers::clean($_POST['action'] ?? '');

    if ($action === 'save') {
        $id          = (int) ($_POST['id'] ?? 0);
        $code        = Helpers::clean($_POST['code']        ?? '');
        $name        = Helpers::clean($_POST['name']        ?? '');
        $description = Helpers::clean($_POST['description'] ?? '');

        if ($code === '') $errors[] = 'Category code is required.';
        if ($name === '') $errors[] = 'Category name is required.';

        if (!$errors) {
            if ($id > 0) {
                DB::execute(
                    "UPDATE labour_categories SET code=?, name=?, description=? WHERE id=? AND company_id=?",
                    [$code, $name, $description ?: null, $id, $companyId]
                );
                AuditLogger::log('UPDATE', 'labour_categories', $id);
            } else {
                DB::execute(
                    "INSERT INTO labour_categories (company_id,code,name,description) VALUES (?,?,?,?)",
                    [$companyId, $code, $name, $description ?: null]
                );
                AuditLogger::log('CREATE', 'labour_categories', DB::lastInsertId());
            }
            $success = 'Category saved.';
        }
    } elseif ($action === 'toggle') {
        $id = (int) ($_POST['id'] ?? 0);
        DB::execute("UPDATE labour_categories SET is_active = NOT is_active WHERE id=? AND company_id=?", [$id, $companyId]);
        AuditLogger::log('UPDATE', 'labour_categories', $id, null, ['toggled' => 'is_active']);
        $success = 'Category status updated.';
    } elseif ($action === 'delete') {
        $id = (int) ($_POST['id'] ?? 0);
        $inUse = (int) DB::value("SELECT COUNT(*) FROM employees WHERE category_id=?", [$id]);
        if ($inUse > 0) {
            $errors[] = 'Cannot delete: category is linked to ' . $inUse . ' employee(s).';
        } else {
            DB::execute("DELETE FROM labour_categories WHERE id=? AND company_id=?", [$id, $companyId]);
            AuditLogger::log('DELETE', 'labour_categories', $id);
            $success = 'Category deleted.';
        }
    }
}

$editId     = (int) ($_GET['edit'] ?? 0);
$editRow    = $editId ? DB::row("SELECT * FROM labour_categories WHERE id=? AND company_id=?", [$editId, $companyId]) : null;
$categories = DB::rows(
    "SELECT lc.*, (SELECT COUNT(*) FROM employees e WHERE e.category_id=lc.id AND e.status='active') AS emp_count
     FROM labour_categories lc WHERE lc.company_id=? ORDER BY lc.code",
    [$companyId]
);

ob_start();
?>
<div class="page-header">
  <div><h1 class="page-title">Labour Categories</h1></div>
  <div class="page-actions">
    <a href="?add=1" class="btn btn-primary">
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
      Add Category
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
  <div class="card-header"><h3 class="card-title"><?= $editRow ? 'Edit Category' : 'Add Category' ?></h3></div>
  <div class="card-body">
    <form method="POST" action="/masters/categories">
      <input type="hidden" name="csrf_token" value="<?= Helpers::h($_SESSION['csrf_token']) ?>">
      <input type="hidden" name="action" value="save">
      <input type="hidden" name="id" value="<?= $editRow ? $editRow['id'] : 0 ?>">
      <div class="form-grid-3">
        <div class="form-group">
          <label class="form-label required">Code</label>
          <input type="text" name="code" class="form-control" maxlength="20" required
                 value="<?= Helpers::h($editRow['code'] ?? '') ?>">
        </div>
        <div class="form-group">
          <label class="form-label required">Name</label>
          <input type="text" name="name" class="form-control" maxlength="100" required
                 value="<?= Helpers::h($editRow['name'] ?? '') ?>">
        </div>
      </div>
      <div class="form-group">
        <label class="form-label">Description</label>
        <textarea name="description" class="form-control" rows="2"><?= Helpers::h($editRow['description'] ?? '') ?></textarea>
      </div>
      <div style="display:flex;gap:var(--space-3);margin-top:var(--space-4)">
        <button type="submit" class="btn btn-primary">Save Category</button>
        <a href="/masters/categories" class="btn btn-secondary">Cancel</a>
      </div>
    </form>
  </div>
</div>
<?php endif; ?>

<div class="card">
  <div class="table-wrapper">
    <table class="data-table">
      <thead>
        <tr><th>Code</th><th>Name</th><th>Description</th><th>Active Employees</th><th>Status</th><th>Actions</th></tr>
      </thead>
      <tbody>
        <?php foreach ($categories as $cat): ?>
        <tr>
          <td><code><?= Helpers::h($cat['code']) ?></code></td>
          <td><?= Helpers::h($cat['name']) ?></td>
          <td><?= Helpers::h($cat['description'] ?? '—') ?></td>
          <td><?= $cat['emp_count'] ?></td>
          <td><?= $cat['is_active'] ? '<span class="badge badge-active">Active</span>' : '<span class="badge badge-inactive">Inactive</span>' ?></td>
          <td>
            <div style="display:flex;gap:var(--space-2)">
              <a href="?edit=<?= $cat['id'] ?>" class="btn btn-secondary btn-sm">Edit</a>
              <form method="POST" action="/masters/categories" style="display:inline">
                <input type="hidden" name="csrf_token" value="<?= Helpers::h($_SESSION['csrf_token']) ?>">
                <input type="hidden" name="action" value="toggle">
                <input type="hidden" name="id" value="<?= $cat['id'] ?>">
                <button type="submit" class="btn btn-ghost btn-sm"><?= $cat['is_active'] ? 'Disable' : 'Enable' ?></button>
              </form>
              <form method="POST" action="/masters/categories" style="display:inline"
                    onsubmit="return confirm('Delete this category?')">
                <input type="hidden" name="csrf_token" value="<?= Helpers::h($_SESSION['csrf_token']) ?>">
                <input type="hidden" name="action" value="delete">
                <input type="hidden" name="id" value="<?= $cat['id'] ?>">
                <button type="submit" class="btn btn-danger btn-sm">Delete</button>
              </form>
            </div>
          </td>
        </tr>
        <?php endforeach; ?>
        <?php if (!$categories): ?>
          <tr><td colspan="6" style="text-align:center;color:var(--clr-text-muted)">No categories defined yet.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?php
$pageContent = ob_get_clean();
$pageTitle   = 'Labour Categories';
$activeMenu  = 'masters';
$breadcrumbs = [['label' => 'Masters'], ['label' => 'Labour Categories']];
include CLMS_ROOT . '/templates/base.html.php';
