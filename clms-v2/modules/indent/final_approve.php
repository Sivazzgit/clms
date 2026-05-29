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
            <div style="display:flex;gap:var(--space-2);flex-wrap:wrap">
              <form method="POST" action="<?= APP_BASE ?>/indent/action" style="display:inline">
                <input type="hidden" name="<?= CSRF_KEY ?>" value="<?= Helpers::h(Auth::csrfToken()) ?>">
                <input type="hidden" name="indent_id"  value="<?= $ind['id'] ?>">
                <input type="hidden" name="action"     value="plant_approve">
                <button type="submit" class="btn btn-primary btn-sm">Approve</button>
              </form>
              <button type="button" class="btn btn-warning btn-sm"
                      onclick="clmsShowRemarks(this,'send_back','Send Back for Revision')">Send Back</button>
              <button type="button" class="btn btn-danger btn-sm"
                      onclick="clmsShowRemarks(this,'reject','Reject Indent')">Reject</button>
            </div>
            <div class="clms-inline-form" style="display:none;margin-top:8px">
              <form method="POST" action="<?= APP_BASE ?>/indent/action">
                <input type="hidden" name="<?= CSRF_KEY ?>" value="<?= Helpers::h(Auth::csrfToken()) ?>">
                <input type="hidden" name="indent_id" value="<?= $ind['id'] ?>">
                <input type="hidden" name="action"    class="clms-action-val" value="">
                <textarea name="remarks" rows="2" required
                  placeholder="Enter your reason or revision comments (required)..."
                  style="width:100%;padding:6px;border:var(--border-base);border-radius:var(--radius-sm);font-size:var(--text-sm);resize:vertical;box-sizing:border-box;margin-bottom:4px"></textarea>
                <div style="display:flex;gap:6px">
                  <button type="submit" class="btn btn-sm clms-action-btn">Submit</button>
                  <button type="button" class="btn btn-secondary btn-sm"
                          onclick="this.closest('.clms-inline-form').style.display='none'">Cancel</button>
                </div>
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
$inlineScript = <<<'JS'
function clmsShowRemarks(btn, action, label) {
  var cell = btn.closest('td');
  var box  = cell.querySelector('.clms-inline-form');
  cell.querySelector('.clms-action-val').value = action;
  var ab = cell.querySelector('.clms-action-btn');
  ab.textContent = label;
  ab.className   = 'btn btn-sm ' + (action === 'send_back' ? 'btn-warning' : 'btn-danger');
  box.style.display = '';
  box.querySelector('textarea').focus();
}
JS;
include CLMS_ROOT . '/templates/base.html.php';
