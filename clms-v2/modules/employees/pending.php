<?php
/**
 * CLMS 2.0 — Employee Pending Approval Queue (HR Admin only)
 */
Auth::requireRole('hr_admin');

$pending = DB::rows(
    "SELECT e.id, e.employee_code, e.first_name, e.middle_name, e.last_name,
            e.aadhaar_no, e.mobile, e.doj_plant, e.created_at,
            v.vendor_code, v.name AS vendor_name,
            lc.name AS category_name,
            u.full_name AS added_by_name
     FROM employees e
     LEFT JOIN vendors v ON v.id = e.vendor_id
     LEFT JOIN labour_categories lc ON lc.id = e.category_id
     LEFT JOIN users u ON u.id = e.added_by
     WHERE e.company_id = ? AND e.status = 'pending_approval'
     ORDER BY e.created_at ASC",
    [$_SESSION['company_id']]
);

// Bulk approve via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    Auth::requireCsrf();
    $ids = array_filter(array_map('intval', (array)($_POST['emp_ids'] ?? [])));
    if ($ids) {
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        DB::execute(
            "UPDATE employees SET status='active', approved_by=?, approved_at=NOW() WHERE id IN ($placeholders) AND company_id=?",
            array_merge([Auth::user()['id']], $ids, [$_SESSION['company_id']])
        );
        foreach ($ids as $eid) {
            AuditLogger::log('APPROVE', 'employees', null, (string)$eid, ['status' => 'pending_approval'], ['status' => 'active']);
        }
        Helpers::redirect('/employees/pending', count($ids) . ' employee(s) approved and activated.');
    }
}

ob_start();
?>

<div class="page-header">
  <div>
    <h1 class="page-title">Pending Approval</h1>
    <p class="page-sub" style="color:var(--clr-text-muted);margin:0;"><?= count($pending) ?> employee(s) awaiting approval</p>
  </div>
  <div class="page-actions">
    <a href="<?= APP_BASE ?>/employees" class="btn btn-ghost">Back to Employee Master</a>
  </div>
</div>

<?php if (empty($pending)): ?>
  <div class="alert alert-info">No employees pending approval. All caught up!</div>
<?php else: ?>

<form method="POST" action="<?= APP_BASE ?>/employees/pending">
  <input type="hidden" name="<?= CSRF_KEY ?>" value="<?= Helpers::h(Auth::csrfToken()) ?>">

  <div class="card">
    <div class="table-toolbar">
      <div style="display:flex;gap:var(--space-3);align-items:center">
        <label>
          <input type="checkbox" id="selectAll" style="margin-right:var(--space-2)">
          Select All
        </label>
        <button type="submit" class="btn btn-success btn-sm">
          Approve Selected
        </button>
      </div>
    </div>

    <div class="table-wrapper">
      <table class="data-table">
        <thead>
          <tr>
            <th style="width:40px"><input type="checkbox" id="selectAllHeader"></th>
            <th>Code</th>
            <th>Name</th>
            <th>Contractor</th>
            <th>Category</th>
            <th>Mobile</th>
            <th>DOJ Plant</th>
            <th>Added By</th>
            <th>Added On</th>
            <th style="width:100px">Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($pending as $emp): ?>
          <?php $fullName = trim($emp['first_name'] . ' ' . ($emp['middle_name'] ? $emp['middle_name'] . ' ' : '') . $emp['last_name']); ?>
          <tr>
            <td><input type="checkbox" name="emp_ids[]" value="<?= $emp['id'] ?>" class="emp-check"></td>
            <td><code><?= Helpers::h($emp['employee_code']) ?></code></td>
            <td>
              <a href="<?= APP_BASE ?>/employees/<?= $emp['id'] ?>/edit" style="font-weight:500"><?= Helpers::h($fullName) ?></a>
              <?php if ($emp['aadhaar_no']): ?>
                <small style="color:var(--clr-text-muted);display:block">Aadhaar: <?= substr($emp['aadhaar_no'], 0, 4) ?>XXXX<?= substr($emp['aadhaar_no'], -4) ?></small>
              <?php endif; ?>
            </td>
            <td>
              <div style="font-size:var(--text-sm)"><?= Helpers::h($emp['vendor_code']) ?></div>
              <div style="font-size:var(--text-xs);color:var(--clr-text-muted)"><?= Helpers::h($emp['vendor_name']) ?></div>
            </td>
            <td><?= Helpers::h($emp['category_name'] ?? '—') ?></td>
            <td><?= Helpers::h($emp['mobile'] ?? '—') ?></td>
            <td><?= Helpers::dateDisplay($emp['doj_plant']) ?></td>
            <td><?= Helpers::h($emp['added_by_name'] ?? '—') ?></td>
            <td><span style="font-size:var(--text-xs);color:var(--clr-text-muted)"><?= Helpers::dateDisplay($emp['created_at']) ?></span></td>
            <td>
              <div style="display:flex;gap:var(--space-2)">
                <a href="<?= APP_BASE ?>/employees/<?= $emp['id'] ?>/edit" class="btn btn-secondary btn-sm" title="View/Edit">
                  <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                </a>
                <button type="button"
                  class="btn btn-success btn-sm"
                  onclick="approveOne(<?= $emp['id'] ?>)"
                  title="Approve">✓</button>
              </div>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>

</form>

<?php endif; ?>

<?php
$pageContent  = ob_get_clean();
$pageTitle    = 'Pending Employee Approval';
$activeMenu   = 'employees';
$breadcrumbs  = [['label' => 'Employee Master', 'url' => '/employees'], ['label' => 'Pending Approval']];
$inlineScript = <<<'JS'
// Select all checkboxes
document.getElementById('selectAll')?.addEventListener('change', function() {
  document.querySelectorAll('.emp-check').forEach(cb => cb.checked = this.checked);
});
document.getElementById('selectAllHeader')?.addEventListener('change', function() {
  document.querySelectorAll('.emp-check').forEach(cb => cb.checked = this.checked);
});

// Approve single employee
function approveOne(id) {
  if (!confirm('Approve and activate this employee?')) return;
  CLMS.api.post('/employees/actions', {
    action: 'approve',
    id: id
  }).then(r => {
    if (r.ok) { CLMS.toast.success(r.message); setTimeout(() => location.reload(), 1000); }
    else CLMS.toast.error(r.message);
  });
}
JS;
include CLMS_ROOT . '/templates/base.html.php';
