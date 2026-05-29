<?php
/**
 * CLMS 2.0 — Indent Vendor Assignment (HR Admin + Section Incharge)
 */
Auth::requireRole(['hr_admin', 'section_incharge']);
$companyId        = $_SESSION['company_id'];
$isSectionIncharge = Auth::hasRole('section_incharge') && !Auth::hasRole('hr_admin');

// Section incharge: only indents from their own sections
if ($isSectionIncharge) {
    $mySections = array_column(
        DB::rows('SELECT section_id FROM user_sections WHERE user_id=?', [$_SESSION['user_id']]),
        'section_id'
    );
    if ($mySections) {
        $placeholders = implode(',', array_fill(0, count($mySections), '?'));
        $indents = DB::rows(
            "SELECT i.*, s.name AS section_name
             FROM indents i JOIN sections s ON s.id=i.section_id
             WHERE i.company_id=? AND i.status IN ('hr_accepted','assigned','partially_confirmed')
               AND i.section_id IN ($placeholders)
             ORDER BY i.is_urgent DESC, i.created_at ASC",
            array_merge([$companyId], $mySections)
        );
    } else {
        $indents = [];
    }
} else {
    // HR Admin sees all
    $indents = DB::rows(
        "SELECT i.*, s.name AS section_name
         FROM indents i JOIN sections s ON s.id=i.section_id
         WHERE i.company_id=? AND i.status IN ('hr_accepted','assigned','partially_confirmed')
         ORDER BY i.is_urgent DESC, i.created_at ASC",
        [$companyId]
    );
}

$selectedId = (int)($_GET['id'] ?? ($indents[0]['id'] ?? 0));
$selectedIndent = $selectedId ? DB::row("SELECT * FROM indents WHERE id=? AND company_id=?", [$selectedId, $companyId]) : null;

$lines = [];
$vendors = DB::rows("SELECT id, vendor_code, name FROM vendors WHERE company_id=? AND status='active' ORDER BY name", [$companyId]);

if ($selectedIndent) {
    $lines = DB::rows(
        "SELECT il.*, sh.name AS shift_name, sh.code AS shift_code, lc.name AS category_name,
                COALESCE((SELECT SUM(iva.assigned_count) FROM indent_vendor_assignments iva
                          WHERE iva.indent_line_id=il.id AND iva.status='assigned'), 0) AS pending_assigned,
                COALESCE((SELECT SUM(iva.confirmed_count) FROM indent_vendor_assignments iva
                          WHERE iva.indent_line_id=il.id AND iva.status IN ('confirmed','partially_confirmed')), 0) AS total_confirmed
         FROM indent_lines il
         JOIN shifts sh ON sh.id=il.shift_id
         JOIN labour_categories lc ON lc.id=il.category_id
         WHERE il.indent_id=?",
        [$selectedId]
    );

    // Load all vendor assignments, grouped by line_id
    $rawAssignments = DB::rows(
        "SELECT iva.*, v.name AS vendor_name
         FROM indent_vendor_assignments iva
         JOIN vendors v ON v.id = iva.vendor_id
         WHERE iva.indent_id=? ORDER BY iva.id",
        [$selectedId]
    );
    $assignmentsByLine = [];
    foreach ($rawAssignments as $a) {
        $assignmentsByLine[$a['indent_line_id']][] = $a;
    }
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
      <a href="<?= APP_BASE ?>/indent/assign?id=<?= $ind['id'] ?>"
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
          &mdash; <a href="<?= APP_BASE ?>/indent/<?= $selectedId ?>/view">View Detail</a>
        </h3>
      </div>
      <div class="card-body" style="padding:0">
        <?php foreach ($lines as $ln):
            $lineAssignments = $assignmentsByLine[$ln['id']] ?? [];
            $gap = max(0, $ln['required_count'] - $ln['pending_assigned'] - $ln['total_confirmed']);
            $fullyMet = $gap === 0 && ($ln['pending_assigned'] + $ln['total_confirmed']) >= $ln['required_count'];
        ?>
        <div style="padding:var(--space-4);border-bottom:var(--border-base)">

          <!-- Line header -->
          <div style="display:flex;align-items:baseline;gap:var(--space-4);margin-bottom:var(--space-3)">
            <strong><?= Helpers::h($ln['shift_code'].' — '.$ln['shift_name']) ?></strong>
            <span style="color:var(--clr-text-muted)"><?= Helpers::h($ln['category_name']) ?></span>
            <span>Required: <strong><?= $ln['required_count'] ?></strong></span>
            <span>Confirmed: <strong style="color:<?= $ln['total_confirmed']>0?'var(--clr-success)':'inherit' ?>"><?= $ln['total_confirmed'] ?></strong></span>
            <?php if ($gap > 0): ?>
              <span class="badge badge-urgent">Gap: <?= $gap ?></span>
            <?php elseif ($fullyMet): ?>
              <span class="badge badge-active">Fully Covered</span>
            <?php endif; ?>
          </div>

          <!-- Existing assignments for this line -->
          <?php if ($lineAssignments): ?>
          <table class="data-table" style="margin-bottom:var(--space-3);font-size:var(--text-sm)">
            <thead>
              <tr><th>Vendor</th><th>Assigned</th><th>Confirmed</th><th>Status</th><th>Action</th></tr>
            </thead>
            <tbody>
              <?php foreach ($lineAssignments as $asgn):
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
              <tr>
                <td><?= Helpers::h($asgn['vendor_name']) ?></td>
                <td><?= $asgn['assigned_count'] ?></td>
                <td><?= $asgn['confirmed_count'] ?? '—' ?></td>
                <td><span class="badge <?= $aClass ?>"><?= $aLabel ?></span></td>
                <td>
                  <?php if (in_array($asgn['status'], ['partially_confirmed','rejected','assigned'])): ?>
                  <form method="POST" action="<?= APP_BASE ?>/indent/action" style="display:inline"
                        onsubmit="return confirm('Remove this vendor assignment? This cannot be undone.')">
                    <input type="hidden" name="<?= CSRF_KEY ?>"     value="<?= Helpers::h(Auth::csrfToken()) ?>">
                    <input type="hidden" name="action"         value="remove_assignment">
                    <input type="hidden" name="indent_id"      value="<?= $selectedId ?>">
                    <input type="hidden" name="assignment_id"  value="<?= $asgn['id'] ?>">
                    <button type="submit" class="btn btn-danger btn-sm">
                      <?= $asgn['status'] === 'partially_confirmed' ? 'Remove &amp; Reassign All' : 'Remove' ?>
                    </button>
                  </form>
                  <?php endif; ?>
                  <?php if ($asgn['status'] === 'partially_confirmed'): ?>
                    <span style="font-size:var(--text-xs);color:var(--clr-text-muted);display:block;margin-top:2px">
                      <?= ($asgn['assigned_count'] - ($asgn['confirmed_count'] ?? 0)) ?> will be freed up
                    </span>
                  <?php endif; ?>
                </td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
          <?php endif; ?>

          <!-- Assign new/additional vendor (only when gap > 0) -->
          <?php if ($gap > 0): ?>
          <div style="background:var(--clr-bg-subtle);border:var(--border-base);border-radius:var(--radius-sm);padding:var(--space-3)">
            <p style="margin:0 0 var(--space-2);font-size:var(--text-sm);font-weight:600;color:var(--clr-text-muted)">
              <?= $lineAssignments ? 'Assign vendor for remaining gap ('.$gap.' workers):' : 'Assign vendor:' ?>
            </p>
            <form method="POST" action="<?= APP_BASE ?>/indent/action" style="display:flex;gap:var(--space-2);align-items:center;flex-wrap:wrap">
              <input type="hidden" name="<?= CSRF_KEY ?>"     value="<?= Helpers::h(Auth::csrfToken()) ?>">
              <input type="hidden" name="action"         value="assign_vendor">
              <input type="hidden" name="indent_id"      value="<?= $selectedId ?>">
              <input type="hidden" name="indent_line_id" value="<?= $ln['id'] ?>">
              <select name="vendor_id" class="form-control" style="width:200px" required>
                <option value="">— Select Vendor —</option>
                <?php foreach ($vendors as $v): ?>
                  <option value="<?= $v['id'] ?>"><?= Helpers::h($v['name']) ?></option>
                <?php endforeach; ?>
              </select>
              <input type="number" name="assigned_count" class="form-control"
                     min="1" max="<?= $gap ?>" value="<?= $gap ?>" style="width:70px">
              <button type="submit" class="btn btn-primary btn-sm">Assign</button>
            </form>
          </div>
          <?php elseif (!$lineAssignments): ?>
          <p style="color:var(--clr-text-muted);font-size:var(--text-sm);margin:0">No vendors assigned yet.</p>
          <?php endif; ?>

        </div>
        <?php endforeach; ?>
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
