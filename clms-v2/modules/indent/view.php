<?php
/**
 * CLMS 2.0 — Indent View
 */
Auth::requireAuth();
$companyId = $_SESSION['company_id'];
$id = (int)($_GET['id'] ?? 0);

$indent = DB::row(
    "SELECT i.*, s.name AS section_name, u.full_name AS created_by_name,
            hod.full_name AS hod_name, hod.email AS hod_email,
            ph.full_name  AS plant_head_name, ph.email AS plant_head_email
     FROM indents i
     JOIN sections s   ON s.id = i.section_id
     JOIN users u      ON u.id = i.created_by
     LEFT JOIN users hod ON hod.id = s.hod_user_id
     LEFT JOIN plants pl ON pl.id  = s.plant_id
     LEFT JOIN users ph  ON ph.id  = pl.plant_head_user_id
     WHERE i.id=? AND i.company_id=?",
    [$id, $companyId]
);
if (!$indent) { http_response_code(404); include CLMS_ROOT.'/modules/errors/404.php'; exit; }

$lines = DB::rows(
    "SELECT il.*, sh.name AS shift_name, sh.code AS shift_code, lc.name AS category_name, lc.code AS category_code,
            v.name AS preferred_vendor
     FROM indent_lines il
     JOIN shifts sh ON sh.id = il.shift_id
     JOIN labour_categories lc ON lc.id = il.category_id
     LEFT JOIN vendors v ON v.id = il.preferred_vendor_id
     WHERE il.indent_id=?",
    [$id]
);

$approvals = DB::rows(
    "SELECT ia.*, u.full_name AS actor_name
     FROM indent_approvals ia
     JOIN users u ON u.id = ia.actor_id
     WHERE ia.indent_id=? ORDER BY ia.step, ia.acted_at",
    [$id]
);

$assignments = DB::rows(
    "SELECT iva.*, v.name AS vendor_name, lc.name AS category_name, sh.code AS shift_code
     FROM indent_vendor_assignments iva
     JOIN vendors v ON v.id = iva.vendor_id
     JOIN indent_lines il ON il.id = iva.indent_line_id
     JOIN labour_categories lc ON lc.id = il.category_id
     JOIN shifts sh ON sh.id = il.shift_id
     WHERE iva.indent_id=?",
    [$id]
);

$flash = $_SESSION['flash'] ?? null; unset($_SESSION['flash']);

// Contractor's own assignments (for response form)
$myAssignments = [];
$canRespond    = false;
if (Auth::hasRole('contractor') && !empty($_SESSION['vendor_id'])) {
    $myAssignments = DB::rows(
        "SELECT iva.*, lc.name AS category_name, sh.code AS shift_code
         FROM indent_vendor_assignments iva
         JOIN indent_lines il  ON il.id  = iva.indent_line_id
         JOIN labour_categories lc ON lc.id = il.category_id
         JOIN shifts sh ON sh.id = il.shift_id
         WHERE iva.indent_id=? AND iva.vendor_id=?",
        [$id, $_SESSION['vendor_id']]
    );
    $canRespond = in_array($indent['status'], ['assigned','partially_confirmed'])
        && (bool) array_filter($myAssignments, fn($a) => $a['status'] === 'assigned');
}

// Determine reviewer action available to current user
$reviewAction = null;
if ($indent['status'] === 'submitted'   && (Auth::hasRole('hod')        || Auth::hasRole('hr_admin'))) $reviewAction = 'hod_review';
if ($indent['status'] === 'hod_reviewed' && (Auth::hasRole('plant_head') || Auth::hasRole('hr_admin'))) $reviewAction = 'plant_approve';
$canRejectNow = ($reviewAction !== null)
    || ($indent['status'] === 'plant_approved' && Auth::hasRole('hr_admin'));

ob_start();
?>
<?php if ($flash): ?>
  <div class="alert alert-<?= $flash['type'] === 'success' ? 'success' : 'danger' ?>" style="margin-bottom:var(--space-4)"><?= Helpers::h($flash['msg']) ?></div>
<?php endif; ?>

<?php
// Show revision note when indent is sent back for corrections
$revNote = null;
if ($indent['status'] === 'needs_revision') {
    foreach (array_reverse($approvals) as $ap) {
        if ($ap['action'] === 'needs_revision') { $revNote = $ap; break; }
    }
}
?>
<?php if ($revNote): ?>
<div class="alert alert-warning" style="margin-bottom:var(--space-4);border-left:4px solid #e67e00">
  <strong>Revision Required</strong> &mdash;
  <?= $revNote['remarks'] ? Helpers::h($revNote['remarks']) : 'Please review and correct the indent before resubmitting.' ?>
  <div style="font-size:var(--text-xs);color:var(--clr-text-muted);margin-top:4px">
    &mdash; <?= Helpers::h($revNote['actor_name']) ?>, <?= Helpers::dateDisplay($revNote['acted_at']) ?>
  </div>
</div>
<?php endif; ?>

<div class="page-header">
  <div>
    <h1 class="page-title"><?= Helpers::h($indent['indent_no']) ?></h1>
    <p class="page-sub" style="color:var(--clr-text-muted);margin:0;">
      <?= Helpers::h($indent['section_name']) ?> &bull;
      <?= Helpers::dateDisplay($indent['start_date']) ?> – <?= Helpers::dateDisplay($indent['end_date']) ?>
    </p>
  </div>
  <div class="page-actions">
    <?php if (in_array($indent['status'],['draft','submitted','needs_revision']) && (Auth::hasRole('section_incharge')||Auth::hasRole('hr_admin'))): ?>
      <a href="<?= APP_BASE ?>/indent/<?= $id ?>/edit" class="btn btn-secondary">Edit</a>
    <?php endif; ?>
    <?php if (in_array($indent['status'],['draft','needs_revision']) && (Auth::hasRole('section_incharge')||Auth::hasRole('hr_admin'))): ?>
      <form method="POST" action="<?= APP_BASE ?>/indent/action" style="display:inline">
        <input type="hidden" name="<?= CSRF_KEY ?>" value="<?= Helpers::h(Auth::csrfToken()) ?>">
        <input type="hidden" name="indent_id" value="<?= $id ?>">
        <input type="hidden" name="action"    value="submit">
        <button type="submit" class="btn btn-primary">
          <?= $indent['status'] === 'needs_revision' ? 'Resubmit for Approval' : 'Submit for Approval' ?>
        </button>
      </form>
    <?php endif; ?>
    <?php if ($indent['status'] === 'plant_approved' && Auth::hasRole('hr_admin')): ?>
      <form method="POST" action="<?= APP_BASE ?>/indent/action" style="display:inline">
        <input type="hidden" name="<?= CSRF_KEY ?>" value="<?= Helpers::h(Auth::csrfToken()) ?>">
        <input type="hidden" name="indent_id" value="<?= $id ?>">
        <input type="hidden" name="action"    value="hr_accept">
        <button type="submit" class="btn btn-primary">Accept &amp; Proceed to Vendor Assignment</button>
      </form>
    <?php endif; ?>
    <?php if (in_array($indent['status'], ['hr_accepted','assigned','partially_confirmed']) && (Auth::hasRole('hr_admin') || Auth::hasRole('section_incharge'))): ?>
      <a href="<?= APP_BASE ?>/indent/assign?id=<?= $id ?>" class="btn btn-primary">
        <?= $indent['status'] === 'partially_confirmed' ? 'Assign Additional Vendors' : 'Assign Vendors' ?>
      </a>
    <?php endif; ?>
  </div>
</div>

<!-- Review Action Card: for HOD / Plant Head / HR to approve, reject, or send back -->
<?php if ($reviewAction || $canRejectNow): ?>
<div class="card" style="margin-bottom:var(--space-4);border-left:4px solid var(--clr-primary)">
  <div class="card-header"><h3 class="card-title">Your Action Required</h3></div>
  <div class="card-body">
    <form method="POST" action="<?= APP_BASE ?>/indent/action" id="reviewActionForm">
      <input type="hidden" name="<?= CSRF_KEY ?>" value="<?= Helpers::h(Auth::csrfToken()) ?>">
      <input type="hidden" name="indent_id" value="<?= $id ?>">
      <div style="margin-bottom:var(--space-3)">
        <label style="font-weight:600;display:block;margin-bottom:4px">
          Remarks
          <span style="font-weight:400;color:var(--clr-text-muted);font-size:var(--text-sm)">(optional for approval; required for rejection or revision)</span>
        </label>
        <textarea name="remarks" id="reviewRemarks" rows="3"
          style="width:100%;padding:8px;border:var(--border-base);border-radius:var(--radius-sm);font-size:var(--text-sm);resize:vertical;box-sizing:border-box"
          placeholder="Enter your comments, suggested changes, or reason..."></textarea>
      </div>
      <div style="display:flex;gap:var(--space-2);flex-wrap:wrap">
        <?php if ($reviewAction === 'hod_review'): ?>
          <button type="submit" name="action" value="hod_review"   class="btn btn-primary">Approve &amp; Forward to Plant Head</button>
        <?php elseif ($reviewAction === 'plant_approve'): ?>
          <button type="submit" name="action" value="plant_approve" class="btn btn-primary">Approve &amp; Forward to HR</button>
        <?php endif; ?>
        <?php if ($reviewAction): ?>
          <button type="submit" name="action" value="send_back" class="btn btn-warning"
                  onclick="return clmsRequireRemarks('Please enter revision comments before sending back.')">Send Back for Revision</button>
        <?php endif; ?>
        <?php if ($canRejectNow): ?>
          <button type="submit" name="action" value="reject" class="btn btn-danger"
                  onclick="return clmsRequireRemarks('Please provide a reason for rejection.')">Reject Indent</button>
        <?php endif; ?>
      </div>
    </form>
  </div>
</div>
<?php endif; ?>

<!-- Status Badge -->
<div style="margin-bottom:var(--space-4)">
  <?php $sb = Helpers::indentStatusBadge($indent['status']); ?>
  <span class="badge <?= $sb['class'] ?>" style="font-size:1rem;padding:.4rem .8rem">
    <?= $sb['label'] ?>
  </span>
  <?php if ($indent['status'] === 'submitted' && !empty($indent['hod_name'])): ?>
    <span style="font-size:var(--text-sm);color:var(--clr-text-muted);margin-left:var(--space-3)">
      Pending with HOD: <strong><?= Helpers::h($indent['hod_name']) ?></strong>
      <?php if ($indent['hod_email']): ?>
        &nbsp;<a href="mailto:<?= Helpers::h($indent['hod_email']) ?>" title="Send reminder" style="font-size:var(--text-xs)">✉ Remind</a>
      <?php endif; ?>
    </span>
  <?php elseif ($indent['status'] === 'hod_reviewed' && !empty($indent['plant_head_name'])): ?>
    <span style="font-size:var(--text-sm);color:var(--clr-text-muted);margin-left:var(--space-3)">
      Pending with Plant Head: <strong><?= Helpers::h($indent['plant_head_name']) ?></strong>
      <?php if ($indent['plant_head_email']): ?>
        &nbsp;<a href="mailto:<?= Helpers::h($indent['plant_head_email']) ?>" title="Send reminder" style="font-size:var(--text-xs)">✉ Remind</a>
      <?php endif; ?>
    </span>
  <?php elseif ($indent['status'] === 'plant_approved'): ?>
    <span style="font-size:var(--text-sm);color:var(--clr-text-muted);margin-left:var(--space-3)">Pending with HR Admin</span>
  <?php endif; ?>
  <?php if ($indent['is_urgent']): ?>
    <span class="badge badge-rejected" style="margin-left:var(--space-2)">Urgent</span>
  <?php endif; ?>
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:var(--space-4);margin-bottom:var(--space-4)">
  <div class="card">
    <div class="card-header"><h3 class="card-title">Details</h3></div>
    <div class="card-body">
      <table style="width:100%;border-collapse:collapse;font-size:var(--text-sm)">
        <tr><th style="padding:4px 8px;text-align:left;color:var(--clr-text-muted)">Section</th><td><?= Helpers::h($indent['section_name']) ?></td></tr>
        <tr><th style="padding:4px 8px;text-align:left;color:var(--clr-text-muted)">Period</th><td><?= Helpers::dateDisplay($indent['start_date']) ?> – <?= Helpers::dateDisplay($indent['end_date']) ?></td></tr>
        <tr><th style="padding:4px 8px;text-align:left;color:var(--clr-text-muted)">Type</th><td><?= ucfirst($indent['indent_type'] ?? 'range') ?></td></tr>
        <tr><th style="padding:4px 8px;text-align:left;color:var(--clr-text-muted)">Nature of Work</th><td><?= Helpers::h($indent['nature_of_work'] ?? '—') ?></td></tr>
        <tr><th style="padding:4px 8px;text-align:left;color:var(--clr-text-muted)">Remarks</th><td><?= Helpers::h($indent['remarks'] ?? '—') ?></td></tr>
        <tr><th style="padding:4px 8px;text-align:left;color:var(--clr-text-muted)">Created By</th><td><?= Helpers::h($indent['created_by_name']) ?></td></tr>
        <tr><th style="padding:4px 8px;text-align:left;color:var(--clr-text-muted)">Created At</th><td><?= Helpers::dateDisplay($indent['created_at']) ?></td></tr>
      </table>
    </div>
  </div>

  <!-- Approval Flow -->
  <div class="card">
    <div class="card-header"><h3 class="card-title">Approval History</h3></div>
    <div class="card-body">
      <?php if ($approvals): ?>
        <?php foreach ($approvals as $ap): ?>
        <div style="padding:var(--space-2) 0;border-bottom:var(--border-base)">
          <div style="display:flex;justify-content:space-between">
            <strong><?= ucwords(str_replace('_',' ',$ap['action'])) ?></strong>
            <span style="font-size:var(--text-xs);color:var(--clr-text-muted)"><?= Helpers::dateDisplay($ap['acted_at']) ?></span>
          </div>
          <div style="font-size:var(--text-sm);color:var(--clr-text-muted)"><?= Helpers::h($ap['actor_name']) ?></div>
          <?php if ($ap['remarks']): ?>
            <div style="font-size:var(--text-sm);font-style:italic"><?= Helpers::h($ap['remarks']) ?></div>
          <?php endif; ?>
        </div>
        <?php endforeach; ?>
      <?php else: ?>
        <p style="color:var(--clr-text-muted)">No approval actions yet.</p>
      <?php endif; ?>
    </div>
  </div>
</div>

<!-- Lines -->
<div class="card" style="margin-bottom:var(--space-4)">
  <div class="card-header"><h3 class="card-title">Manpower Requirements</h3></div>
  <div class="table-wrapper">
    <table class="data-table">
      <thead><tr><th>Shift</th><th>Category</th><th>Required</th><th>Preferred Vendor</th></tr></thead>
      <tbody>
        <?php foreach ($lines as $ln): ?>
        <tr>
          <td><?= Helpers::h($ln['shift_code'].' — '.$ln['shift_name']) ?></td>
          <td><?= Helpers::h($ln['category_code'].' — '.$ln['category_name']) ?></td>
          <td><?= $ln['required_count'] ?></td>
          <td><?= Helpers::h($ln['preferred_vendor'] ?? '—') ?></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<?php if ($assignments): ?>
<div class="card">
  <div class="card-header"><h3 class="card-title">Vendor Assignments</h3></div>
  <div class="table-wrapper">
    <table class="data-table">
      <thead><tr><th>Vendor</th><th>Shift</th><th>Category</th><th>Assigned</th><th>Confirmed</th><th>Status</th></tr></thead>
      <tbody>
        <?php foreach ($assignments as $asgn): ?>
        <tr>
          <td><?= Helpers::h($asgn['vendor_name']) ?></td>
          <td><?= Helpers::h($asgn['shift_code']) ?></td>
          <td><?= Helpers::h($asgn['category_name']) ?></td>
          <td><?= $asgn['assigned_count'] ?></td>
          <td><?= $asgn['confirmed_count'] ?? '—' ?></td>
          <?php
          $aClass = match($asgn['status']) {
              'confirmed'           => 'badge-active',
              'partially_confirmed' => 'badge-urgent',
              'rejected'            => 'badge-rejected',
              default               => 'badge-pending',
          };
          $aLabel = match($asgn['status']) {
              'assigned'            => 'Awaiting Response',
              'confirmed'           => 'Confirmed',
              'partially_confirmed' => 'Partially Confirmed',
              'rejected'            => 'Rejected',
              default               => ucfirst($asgn['status']),
          };
          ?>
          <td><span class="badge <?= $aClass ?>"><?= $aLabel ?></span></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>
<?php endif; ?>

<?php if ($canRespond && $myAssignments): ?>
<div class="card" style="margin-top:var(--space-4);border-left:4px solid var(--clr-primary)">
  <div class="card-header"><h3 class="card-title">Your Response Required</h3></div>
  <div class="card-body">
    <form method="POST" action="<?= APP_BASE ?>/indent/action" id="vendorResponseForm">
      <input type="hidden" name="<?= CSRF_KEY ?>" value="<?= Helpers::h(Auth::csrfToken()) ?>">
      <input type="hidden" name="indent_id" value="<?= $id ?>">
      <input type="hidden" name="action"    value="vendor_respond">

      <div style="margin-bottom:var(--space-3)">
        <label style="font-weight:600;display:block;margin-bottom:8px">Response Type</label>
        <label style="display:inline-flex;align-items:center;gap:6px;margin-right:20px;cursor:pointer">
          <input type="radio" name="response_type" value="confirm" checked onchange="clmsTogglePartial(this)">
          <strong>Confirm All</strong> &mdash; I can provide all requested workers
        </label><br style="margin-bottom:6px">
        <label style="display:inline-flex;align-items:center;gap:6px;margin-right:20px;cursor:pointer">
          <input type="radio" name="response_type" value="partial" onchange="clmsTogglePartial(this)">
          <strong>Partial Confirmation</strong> &mdash; I can provide fewer workers than requested
        </label><br style="margin-bottom:6px">
        <label style="display:inline-flex;align-items:center;gap:6px;cursor:pointer">
          <input type="radio" name="response_type" value="reject" onchange="clmsTogglePartial(this)">
          <strong>Cannot Fulfil</strong> &mdash; I am unable to provide workers for this indent
        </label>
      </div>

      <div id="partialCountBlock" style="display:none;margin-bottom:var(--space-3)">
        <p style="font-size:var(--text-sm);color:var(--clr-text-muted);margin:0 0 8px">Enter how many workers you can provide per assignment:</p>
        <table class="data-table" style="margin-bottom:0">
          <thead><tr><th>Shift</th><th>Category</th><th>Requested</th><th>You Can Provide</th></tr></thead>
          <tbody>
            <?php foreach ($myAssignments as $ma): ?>
              <?php if ($ma['status'] !== 'assigned') continue; ?>
              <tr>
                <td><?= Helpers::h($ma['shift_code']) ?></td>
                <td><?= Helpers::h($ma['category_name']) ?></td>
                <td><?= $ma['assigned_count'] ?></td>
                <td>
                  <input type="number" name="confirmed_counts[<?= $ma['id'] ?>]"
                         min="0" max="<?= $ma['assigned_count'] ?>" value="<?= $ma['assigned_count'] ?>"
                         style="width:80px;padding:4px 6px;border:var(--border-base);border-radius:var(--radius-sm);font-size:var(--text-sm)">
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>

      <div style="margin-bottom:var(--space-3)">
        <label style="font-weight:600;display:block;margin-bottom:4px">
          Remarks <span style="font-weight:400;color:var(--clr-text-muted);font-size:var(--text-sm)">(optional &mdash; availability dates, constraints, etc.)</span>
        </label>
        <textarea name="remarks" rows="2"
          style="width:100%;padding:8px;border:var(--border-base);border-radius:var(--radius-sm);font-size:var(--text-sm);resize:vertical;box-sizing:border-box"
          placeholder="e.g. Only 5 workers available due to prior commitment at another site."></textarea>
      </div>

      <button type="submit" class="btn btn-primary">Submit My Response</button>
    </form>
  </div>
</div>
<?php endif; ?>

<script>
function clmsRequireRemarks(msg) {
  var r = document.getElementById('reviewRemarks');
  if (r && !r.value.trim()) { alert(msg); r.focus(); return false; }
  return true;
}
function clmsTogglePartial(el) {
  document.getElementById('partialCountBlock').style.display = (el.value === 'partial') ? '' : 'none';
}
</script>

<?php
$pageContent = ob_get_clean();
$pageTitle   = $indent['indent_no'];
$activeMenu  = 'indent';
$breadcrumbs = [['label' => 'Indents', 'url' => '/indent'], ['label' => $indent['indent_no']]];
include CLMS_ROOT . '/templates/base.html.php';
