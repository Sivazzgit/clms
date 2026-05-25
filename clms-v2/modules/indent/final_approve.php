<?php
/**
 * CLMS 2.0 — Indent Final Approval (Plant Head)
 */
Auth::requireRole(['plant_head','hr_admin']);
$companyId = $_SESSION['company_id'];

$pending = DB::rows(
    "SELECT i.*, s.name AS section_name, u.full_name AS created_by_name
     FROM indents i JOIN sections s ON s.id=i.section_id JOIN users u ON u.id=i.created_by
     WHERE i.company_id=? AND i.status='hod_reviewed'
     ORDER BY i.is_urgent DESC, i.created_at ASC",
    [$companyId]
);

$flash = $_SESSION['flash'] ?? null; unset($_SESSION['flash']);
ob_start();
?>
<?php if ($flash): ?>
  <div class="alert alert-<?= $flash['type']==='success'?'success':'danger' ?>" style="margin-bottom:var(--space-4)"><?= Helpers::h($flash['msg']) ?></div>
<?php endif; ?>
<div class="page-header">
  <div>
    <h1 class="page-title">Final Indent Approval</h1>
    <p class="page-sub" style="color:var(--clr-text-muted);margin:0;"><?= count($pending) ?> pending Plant Head approval</p>
  </div>
</div>
<div class="card">
  <div class="table-wrapper">
    <table class="data-table">
      <thead><tr><th>Indent No</th><th>Section</th><th>Period</th><th>Urgent</th><th>Actions</th></tr></thead>
      <tbody>
        <?php foreach ($pending as $ind): ?>
        <tr>
          <td><a href="<?= APP_BASE ?>/indent/<?= $ind['id'] ?>/view"><?= Helpers::h($ind['indent_no']) ?></a></td>
          <td><?= Helpers::h($ind['section_name']) ?></td>
          <td><?= Helpers::dateDisplay($ind['start_date']) ?> – <?= Helpers::dateDisplay($ind['end_date']) ?></td>
          <td><?= $ind['is_urgent'] ? '<span class="badge badge-rejected">Yes</span>' : '—' ?></td>
          <td>
            <div style="display:flex;gap:var(--space-2)">
              <form method="POST" action="<?= APP_BASE ?>/indent/action" style="display:inline">
                <input type="hidden" name="csrf_token" value="<?= Helpers::h($_SESSION['csrf_token']) ?>">
                <input type="hidden" name="indent_id"  value="<?= $ind['id'] ?>">
                <input type="hidden" name="action"     value="plant_approve">
                <button type="submit" class="btn btn-primary btn-sm">Approve</button>
              </form>
              <form method="POST" action="<?= APP_BASE ?>/indent/action" style="display:inline"
                    onsubmit="return confirm('Reject this indent?')">
                <input type="hidden" name="csrf_token" value="<?= Helpers::h($_SESSION['csrf_token']) ?>">
                <input type="hidden" name="indent_id"  value="<?= $ind['id'] ?>">
                <input type="hidden" name="action"     value="reject">
                <button type="submit" class="btn btn-danger btn-sm">Reject</button>
              </form>
            </div>
          </td>
        </tr>
        <?php endforeach; ?>
        <?php if (!$pending): ?>
          <tr><td colspan="5" style="text-align:center;color:var(--clr-text-muted)">No indents pending Plant Head approval.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
<?php
$pageContent = ob_get_clean();
$pageTitle   = 'Final Indent Approval';
$activeMenu  = 'indent';
$breadcrumbs = [['label'=>'Indents','url'=>'/indent'],['label'=>'Plant Head Approval']];
include CLMS_ROOT . '/templates/base.html.php';
