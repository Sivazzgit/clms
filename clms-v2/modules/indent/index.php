<?php
/**
 * CLMS 2.0 — Indent List
 * - section_incharge: sees their section's indents
 * - hod / plant_head / hr_admin: sees all
 * - contractor: not applicable (redirected)
 */
Auth::requireAuth();
$user  = Auth::user();
$roles = $user['roles'];

if (Auth::hasRole('contractor')) {
    Helpers::redirect('/dashboard');
}

$companyId  = $_SESSION['company_id'];
$errors     = [];
$filterStatus = Helpers::clean($_GET['status'] ?? '');
$search       = Helpers::clean($_GET['q']      ?? '');

$where = ['i.company_id = ?'];
$bind  = [$companyId];

if (Auth::hasRole('section_incharge') && !Auth::hasRole('hr_admin') && !Auth::hasRole('hod') && !Auth::hasRole('plant_head')) {
    $secIds = $user['section_ids'] ?? [];
    if ($secIds) {
        $ph = implode(',', array_fill(0, count($secIds), '?'));
        $where[] = "i.section_id IN ($ph)";
        $bind    = array_merge($bind, $secIds);
    }
}

if ($filterStatus !== '') { $where[] = 'i.status = ?'; $bind[] = $filterStatus; }
if ($search !== '') {
    $where[] = '(i.indent_no LIKE ? OR s.name LIKE ? OR i.nature_of_work LIKE ?)';
    $s = "%$search%";
    array_push($bind, $s, $s, $s);
}

$whereStr = implode(' AND ', $where);
$total    = (int) DB::value("SELECT COUNT(*) FROM indents i JOIN sections s ON s.id=i.section_id WHERE $whereStr", $bind);
$pager    = Helpers::paginate($total);

$indents = DB::rows(
    "SELECT i.*, s.name AS section_name, u.full_name AS created_by_name
     FROM indents i
     JOIN sections s ON s.id = i.section_id
     JOIN users u    ON u.id = i.created_by
     WHERE $whereStr
     ORDER BY i.created_at DESC
     LIMIT ? OFFSET ?",
    [...$bind, $pager['perPage'], $pager['offset']]
);

$statuses = ['draft','submitted','hod_reviewed','plant_approved','hr_accepted','assigned','contractor_confirmed','rejected','cancelled'];

ob_start();
?>
<div class="page-header">
  <div>
    <h1 class="page-title">Indents / Manpower Requests</h1>
    <p class="page-sub" style="color:var(--clr-text-muted);margin:0;"><?= number_format($total) ?> indent(s)</p>
  </div>
  <?php if (Auth::hasRole('section_incharge') || Auth::hasRole('hr_admin')): ?>
  <div class="page-actions">
    <a href="<?= APP_BASE ?>/indent/create" class="btn btn-primary">
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
      New Indent
    </a>
  </div>
  <?php endif; ?>
</div>

<div class="card">
  <div class="table-toolbar">
    <form method="GET" action="<?= APP_BASE ?>/indent" style="display:flex;gap:var(--space-3);flex-wrap:wrap;">
      <input type="search" name="q" class="table-search" placeholder="Search indent no, section…" value="<?= Helpers::h($search) ?>">
      <select name="status" class="form-control" style="width:auto;" onchange="this.form.submit()">
        <option value="">All Status</option>
        <?php foreach ($statuses as $st): ?>
          <option value="<?= $st ?>" <?= $filterStatus === $st ? 'selected' : '' ?>><?= ucwords(str_replace('_', ' ', $st)) ?></option>
        <?php endforeach; ?>
      </select>
      <button type="submit" class="btn btn-secondary">Filter</button>
      <?php if ($search || $filterStatus): ?><a href="<?= APP_BASE ?>/indent" class="btn btn-ghost">Clear</a><?php endif; ?>
    </form>
  </div>
  <div class="table-wrapper">
    <table class="data-table">
      <thead>
        <tr>
          <th>Indent No</th><th>Section</th><th>Period</th><th>Type</th>
          <th>Urgent</th><th>Status</th><th>Created By</th><th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($indents as $ind): ?>
        <tr>
          <td><a href="<?= APP_BASE ?>/indent/<?= $ind['id'] ?>/view"><?= Helpers::h($ind['indent_no']) ?></a></td>
          <td><?= Helpers::h($ind['section_name']) ?></td>
          <td><?= Helpers::dateDisplay($ind['start_date']) ?> – <?= Helpers::dateDisplay($ind['end_date']) ?></td>
          <td><?= ucfirst($ind['indent_type'] ?? 'range') ?></td>
          <td><?= $ind['is_urgent'] ? '<span class="badge badge-rejected">Urgent</span>' : '—' ?></td>
          <td><span class="badge badge-pending"><?= ucwords(str_replace('_',' ',$ind['status'])) ?></span></td>
          <td><?= Helpers::h($ind['created_by_name']) ?></td>
          <td>
            <div style="display:flex;gap:var(--space-2)">
              <a href="<?= APP_BASE ?>/indent/<?= $ind['id'] ?>/view" class="btn btn-secondary btn-sm">View</a>
              <?php if (in_array($ind['status'], ['draft','submitted']) && (Auth::hasRole('section_incharge') || Auth::hasRole('hr_admin'))): ?>
                <a href="<?= APP_BASE ?>/indent/<?= $ind['id'] ?>/edit" class="btn btn-ghost btn-sm">Edit</a>
              <?php endif; ?>
            </div>
          </td>
        </tr>
        <?php endforeach; ?>
        <?php if (!$indents): ?>
          <tr><td colspan="8" style="text-align:center;color:var(--clr-text-muted)">No indents found.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
  <?php if ($pager['totalPages'] > 1): ?>
  <div style="display:flex;justify-content:space-between;align-items:center;padding:var(--space-4);border-top:var(--border-base)">
    <nav class="pagination">
      <?php foreach ($pager['pages'] as $p): ?>
        <a href="?page=<?= $p ?>&status=<?= urlencode($filterStatus) ?>&q=<?= urlencode($search) ?>"
           class="page-link <?= $p === $pager['current'] ? 'active' : '' ?>"><?= $p ?></a>
      <?php endforeach; ?>
    </nav>
    <span style="font-size:var(--text-sm);color:var(--clr-text-muted)">
      <?= ($pager['offset']+1) ?>–<?= min($pager['offset']+$pager['perPage'],$total) ?> of <?= $total ?>
    </span>
  </div>
  <?php endif; ?>
</div>

<?php
$pageContent = ob_get_clean();
$pageTitle   = 'Indents';
$activeMenu  = 'indent';
$breadcrumbs = [['label' => 'Indents']];
include CLMS_ROOT . '/templates/base.html.php';
