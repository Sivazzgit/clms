<?php
/**
 * CLMS 2.0 — Super Admin: Impersonation Audit Log
 * GET /superadmin/impersonation-log
 */
Auth::requireRole(['super_admin', 'admin']);

$isSuperAdmin = Auth::realHasRole('super_admin');
$companyId    = $_SESSION['company_id'];

// Filters
$filterActor  = (int)($_GET['actor_id']  ?? 0);
$filterTarget = (int)($_GET['target_id'] ?? 0);
$filterFrom   = Helpers::clean($_GET['from'] ?? '');
$filterTo     = Helpers::clean($_GET['to']   ?? '');

$where = ['1=1'];
$bind  = [];

if (!$isSuperAdmin) {
    // Admin: only see their own company
    $where[] = 'il.target_company = ?';
    $bind[]  = $companyId;
}
if ($filterActor) {
    $where[] = 'il.actor_id = ?';
    $bind[]  = $filterActor;
}
if ($filterTarget) {
    $where[] = 'il.target_id = ?';
    $bind[]  = $filterTarget;
}
if ($filterFrom) {
    $where[] = 'il.started_at >= ?';
    $bind[]  = $filterFrom . ' 00:00:00';
}
if ($filterTo) {
    $where[] = 'il.started_at <= ?';
    $bind[]  = $filterTo . ' 23:59:59';
}

$whereStr = implode(' AND ', $where);

$total = (int)DB::value("SELECT COUNT(*) FROM impersonation_log il WHERE $whereStr", $bind);
$pager = Helpers::paginate($total);

$logs = DB::rows(
    "SELECT il.*,
            c.name AS company_name
     FROM impersonation_log il
     JOIN companies c ON c.id = il.target_company
     WHERE $whereStr
     ORDER BY il.started_at DESC
     LIMIT ? OFFSET ?",
    [...$bind, $pager['perPage'], $pager['offset']]
);

ob_start();
?>
<div class="page-header">
  <div>
    <h1 class="page-title">Impersonation Log</h1>
    <p class="page-sub" style="color:var(--clr-text-muted);margin:0"><?= number_format($total) ?> event(s)</p>
  </div>
  <div class="page-actions">
    <a href="<?= APP_BASE ?>/superadmin" class="btn btn-ghost btn-sm">← Super Admin</a>
  </div>
</div>

<div class="card">
  <!-- Filters -->
  <div class="table-toolbar">
    <form method="GET" action="<?= APP_BASE ?>/superadmin/impersonation-log"
          style="display:flex;gap:var(--space-3);flex-wrap:wrap;align-items:flex-end">
      <div>
        <label class="form-label" style="font-size:var(--text-xs)">Actor user ID</label>
        <input type="number" name="actor_id" class="form-control" style="width:120px"
               value="<?= $filterActor ?: '' ?>" placeholder="ID">
      </div>
      <div>
        <label class="form-label" style="font-size:var(--text-xs)">Target user ID</label>
        <input type="number" name="target_id" class="form-control" style="width:120px"
               value="<?= $filterTarget ?: '' ?>" placeholder="ID">
      </div>
      <div>
        <label class="form-label" style="font-size:var(--text-xs)">From</label>
        <input type="date" name="from" class="form-control" value="<?= Helpers::h($filterFrom) ?>">
      </div>
      <div>
        <label class="form-label" style="font-size:var(--text-xs)">To</label>
        <input type="date" name="to" class="form-control" value="<?= Helpers::h($filterTo) ?>">
      </div>
      <button type="submit" class="btn btn-secondary">Filter</button>
      <a href="<?= APP_BASE ?>/superadmin/impersonation-log" class="btn btn-ghost">Clear</a>
    </form>
  </div>

  <div class="table-wrapper">
    <table class="data-table">
      <thead>
        <tr>
          <th>#</th>
          <?php if ($isSuperAdmin): ?><th>Company</th><?php endif; ?>
          <th>Actor</th>
          <th>Target (Impersonated)</th>
          <th>Reason</th>
          <th>Started</th>
          <th>Ended</th>
          <th>Duration</th>
          <th>IP</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($logs as $log):
          $duration = '—';
          if ($log['ended_at']) {
              $secs  = strtotime($log['ended_at']) - strtotime($log['started_at']);
              $mins  = (int)($secs / 60);
              $duration = $mins > 0 ? "{$mins}m " . ($secs % 60) . "s" : "{$secs}s";
          } elseif (strtotime($log['started_at']) > time() - 3700) {
              $duration = '<span class="badge badge-pending">Active</span>';
          } else {
              $duration = '<span style="color:var(--clr-text-muted)">Expired</span>';
          }
        ?>
        <tr>
          <td><?= $log['id'] ?></td>
          <?php if ($isSuperAdmin): ?><td><?= Helpers::h($log['company_name']) ?></td><?php endif; ?>
          <td>
            <strong><?= Helpers::h($log['actor_username']) ?></strong>
            <small style="color:var(--clr-text-muted)">#<?= $log['actor_id'] ?></small>
          </td>
          <td>
            <strong><?= Helpers::h($log['target_username']) ?></strong>
            <small style="color:var(--clr-text-muted)">#<?= $log['target_id'] ?></small>
          </td>
          <td><?= $log['reason'] ? Helpers::h($log['reason']) : '<span style="color:var(--clr-text-muted)">—</span>' ?></td>
          <td><?= Helpers::dateDisplay($log['started_at']) ?></td>
          <td><?= $log['ended_at'] ? Helpers::dateDisplay($log['ended_at']) : '<span style="color:var(--clr-text-muted)">—</span>' ?></td>
          <td><?= $duration ?></td>
          <td><code style="font-size:var(--text-xs)"><?= Helpers::h($log['ip_address']) ?></code></td>
        </tr>
        <?php endforeach; ?>
        <?php if (!$logs): ?>
        <tr><td colspan="<?= $isSuperAdmin ? 9 : 8 ?>" style="text-align:center;color:var(--clr-text-muted)">
          No impersonation events found.
        </td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>

  <?php if ($pager['totalPages'] > 1): ?>
  <div class="pagination-bar">
    <?= $pager['html'] ?>
  </div>
  <?php endif; ?>
</div>

<?php
$pageContent = ob_get_clean();
$pageTitle   = 'Impersonation Log';
$activeMenu  = 'superadmin';
$breadcrumbs = [['label' => 'Super Admin', 'url' => '/superadmin'], ['label' => 'Impersonation Log']];
include CLMS_ROOT . '/templates/base.html.php';
