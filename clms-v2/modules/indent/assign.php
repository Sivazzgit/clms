<?php
/**
 * CLMS 2.0 — Indent Vendor Assignment (HR Admin only)
 */
Auth::requireRole('hr_admin');
$companyId = $_SESSION['company_id'];

// HR-accepted indents ready for vendor assignment
$indents = DB::rows(
    "SELECT i.*, s.name AS section_name
     FROM indents i JOIN sections s ON s.id=i.section_id
     WHERE i.company_id=? AND i.status IN ('hr_accepted','assigned')
     ORDER BY i.is_urgent DESC, i.created_at ASC",
    [$companyId]
);

$selectedId = (int)($_GET['id'] ?? ($indents[0]['id'] ?? 0));
$selectedIndent = $selectedId ? DB::row("SELECT * FROM indents WHERE id=? AND company_id=?", [$selectedId, $companyId]) : null;

$lines = [];
$vendors = DB::rows("SELECT id, vendor_code, name FROM vendors WHERE company_id=? AND status='active' ORDER BY name", [$companyId]);

if ($selectedIndent) {
    $lines = DB::rows(
        "SELECT il.*, sh.name AS shift_name, sh.code AS shift_code, lc.name AS category_name,
                COALESCE((SELECT SUM(iva.assigned_count) FROM indent_vendor_assignments iva WHERE iva.indent_line_id=il.id),'0') AS total_assigned
         FROM indent_lines il
         JOIN shifts sh ON sh.id=il.shift_id
         JOIN labour_categories lc ON lc.id=il.category_id
         WHERE il.indent_id=?",
        [$selectedId]
    );
}

$flash = $_SESSION['flash'] ?? null; unset($_SESSION['flash']);
ob_start();
?>
<?php if ($flash): ?>
  <div class="alert alert-<?= $flash['type']==='success'?'success':'danger' ?>" style="margin-bottom:var(--space-4)"><?= Helpers::h($flash['msg']) ?></div>
<?php endif; ?>
<div class="page-header">
  <div><h1 class="page-title">Assign Vendors to Indent</h1></div>
</div>

<div style="display:grid;grid-template-columns:280px 1fr;gap:var(--space-4)">
  <!-- Left: indent list -->
  <div class="card" style="height:fit-content">
    <div class="card-header"><h3 class="card-title">HR-Accepted Indents</h3></div>
    <div style="padding:0">
      <?php foreach ($indents as $ind): ?>
      <a href="/indent/assign?id=<?= $ind['id'] ?>"
         style="display:block;padding:var(--space-3) var(--space-4);border-bottom:var(--border-base);
                text-decoration:none;color:inherit;<?= $ind['id']==$selectedId ? 'background:var(--clr-bg-subtle);font-weight:600' : '' ?>">
        <div><?= Helpers::h($ind['indent_no']) ?></div>
        <div style="font-size:var(--text-sm);color:var(--clr-text-muted)"><?= Helpers::h($ind['section_name'] ?? '') ?></div>
      </a>
      <?php endforeach; ?>
      <?php if (!$indents): ?>
        <p style="padding:var(--space-4);color:var(--clr-text-muted)">No indents ready for assignment.</p>
      <?php endif; ?>
    </div>
  </div>

  <!-- Right: assignment form -->
  <div>
    <?php if ($selectedIndent && $lines): ?>
    <div class="card">
      <div class="card-header">
        <h3 class="card-title">
          <?= Helpers::h($selectedIndent['indent_no']) ?>
          &mdash; <a href="/indent/<?= $selectedId ?>/view">View Detail</a>
        </h3>
      </div>
      <div class="table-wrapper">
        <table class="data-table">
          <thead><tr><th>Shift</th><th>Category</th><th>Required</th><th>Assigned</th><th>Assign Vendor</th></tr></thead>
          <tbody>
            <?php foreach ($lines as $ln): ?>
            <tr>
              <td><?= Helpers::h($ln['shift_code'].' — '.$ln['shift_name']) ?></td>
              <td><?= Helpers::h($ln['category_name']) ?></td>
              <td><?= $ln['required_count'] ?></td>
              <td><?= $ln['total_assigned'] ?></td>
              <td>
                <form method="POST" action="/indent/action" style="display:flex;gap:var(--space-2);align-items:center">
                  <input type="hidden" name="csrf_token"      value="<?= Helpers::h($_SESSION['csrf_token']) ?>">
                  <input type="hidden" name="action"          value="assign_vendor">
                  <input type="hidden" name="indent_id"       value="<?= $selectedId ?>">
                  <input type="hidden" name="indent_line_id"  value="<?= $ln['id'] ?>">
                  <select name="vendor_id" class="form-control" style="width:180px" required>
                    <option value="">— Vendor —</option>
                    <?php foreach ($vendors as $v): ?>
                      <option value="<?= $v['id'] ?>"><?= Helpers::h($v['name']) ?></option>
                    <?php endforeach; ?>
                  </select>
                  <input type="number" name="assigned_count" class="form-control" min="1"
                         max="<?= $ln['required_count'] ?>" value="<?= $ln['required_count'] - $ln['total_assigned'] ?>"
                         style="width:70px">
                  <button type="submit" class="btn btn-primary btn-sm">Assign</button>
                </form>
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
    <?php elseif ($indents): ?>
      <div class="card"><div class="card-body"><p style="color:var(--clr-text-muted)">Select an indent from the left to assign vendors.</p></div></div>
    <?php endif; ?>
  </div>
</div>

<?php
$pageContent = ob_get_clean();
$pageTitle   = 'Vendor Assignment';
$activeMenu  = 'indent';
$breadcrumbs = [['label'=>'Indents','url'=>'/indent'],['label'=>'Vendor Assignment']];
include CLMS_ROOT . '/templates/base.html.php';
