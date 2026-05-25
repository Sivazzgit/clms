<?php
/**
 * CLMS 2.0 — Audit Log Viewer
 */
Auth::requireRole(['hr_admin','super_admin']);
$companyId = $_SESSION['company_id'];

$filterModule = Helpers::clean($_GET['module'] ?? '');
$filterAction = Helpers::clean($_GET['action'] ?? '');
$filterUser   = (int)($_GET['user_id'] ?? 0);
$fromDate     = Helpers::clean($_GET['from_date'] ?? date('Y-m-01'));
$toDate       = Helpers::clean($_GET['to_date']   ?? date('Y-m-d'));

$where = ['al.company_id=?'];
$bind  = [$companyId];
if ($filterModule) { $where[] = 'al.module=?';  $bind[] = $filterModule; }
if ($filterAction) { $where[] = 'al.action=?';  $bind[] = $filterAction; }
if ($filterUser)   { $where[] = 'al.user_id=?'; $bind[] = $filterUser; }
if ($fromDate)     { $where[] = 'DATE(al.created_at)>=?'; $bind[] = $fromDate; }
if ($toDate)       { $where[] = 'DATE(al.created_at)<=?'; $bind[] = $toDate; }
$whereStr = implode(' AND ', $where);

$total = (int) DB::value("SELECT COUNT(*) FROM audit_log al WHERE $whereStr", $bind);
$pager = Helpers::paginate($total);

$logs = DB::rows(
    "SELECT al.* FROM audit_log al WHERE $whereStr ORDER BY al.created_at DESC LIMIT ? OFFSET ?",
    [...$bind, $pager['perPage'], $pager['offset']]
);

// Distinct modules and actions for filter dropdowns
$modules = DB::rows("SELECT DISTINCT module FROM audit_log WHERE company_id=? ORDER BY module", [$companyId]);
$actions = DB::rows("SELECT DISTINCT action  FROM audit_log WHERE company_id=? ORDER BY action",  [$companyId]);
$users   = DB::rows("SELECT id, full_name, username FROM users WHERE company_id=? ORDER BY full_name", [$companyId]);

ob_start();
?>
<div class="page-header">
  <div>
    <h1 class="page-title">Audit Log</h1>
    <p style="color:var(--clr-text-muted);margin:0"><?= number_format($total) ?> entries</p>
  </div>
</div>

<div class="card">
  <div class="table-toolbar">
    <form method="GET" action="/audit" style="display:flex;gap:var(--space-3);flex-wrap:wrap">
      <input type="date" name="from_date" class="form-control" value="<?= $fromDate ?>">
      <input type="date" name="to_date"   class="form-control" value="<?= $toDate ?>">
      <select name="module" class="form-control" style="width:auto">
        <option value="">All Modules</option>
        <?php foreach ($modules as $m): ?>
          <option value="<?= Helpers::h($m['module']) ?>" <?= $filterModule===$m['module']?'selected':'' ?>><?= Helpers::h(ucfirst($m['module'])) ?></option>
        <?php endforeach; ?>
      </select>
      <select name="action" class="form-control" style="width:auto">
        <option value="">All Actions</option>
        <?php foreach ($actions as $a): ?>
          <option value="<?= Helpers::h($a['action']) ?>" <?= $filterAction===$a['action']?'selected':'' ?>><?= Helpers::h($a['action']) ?></option>
        <?php endforeach; ?>
      </select>
      <select name="user_id" class="form-control" style="width:auto">
        <option value="">All Users</option>
        <?php foreach ($users as $u): ?>
          <option value="<?= $u['id'] ?>" <?= $filterUser==$u['id']?'selected':'' ?>><?= Helpers::h($u['full_name'].' ('.$u['username'].')') ?></option>
        <?php endforeach; ?>
      </select>
      <button type="submit" class="btn btn-secondary">Filter</button>
      <a href="/audit" class="btn btn-ghost">Reset</a>
    </form>
  </div>
  <div class="table-wrapper">
    <table class="data-table">
      <thead>
        <tr><th>Time</th><th>User</th><th>Action</th><th>Module</th><th>Record</th><th>IP</th><th>Changes</th></tr>
      </thead>
      <tbody>
        <?php foreach ($logs as $log): ?>
        <tr>
          <td style="white-space:nowrap"><?= date('d-M-y H:i', strtotime($log['created_at'])) ?></td>
          <td><?= Helpers::h($log['username']) ?></td>
          <td><span class="badge badge-pending"><?= Helpers::h($log['action']) ?></span></td>
          <td><?= Helpers::h(ucfirst($log['module'])) ?></td>
          <td><?= $log['record_id'] ? '#'.Helpers::h($log['record_id']) : '—' ?></td>
          <td style="font-size:var(--text-sm)"><?= Helpers::h($log['ip_address'] ?? '—') ?></td>
          <td>
            <?php if ($log['old_values'] || $log['new_values']): ?>
            <details style="cursor:pointer">
              <summary style="font-size:var(--text-sm)">View diff</summary>
              <?php if ($log['old_values']): ?>
                <pre style="font-size:11px;margin:4px 0 0;max-width:400px;overflow:auto">OLD: <?= Helpers::h($log['old_values']) ?></pre>
              <?php endif; ?>
              <?php if ($log['new_values']): ?>
                <pre style="font-size:11px;margin:2px 0 0;max-width:400px;overflow:auto">NEW: <?= Helpers::h($log['new_values']) ?></pre>
              <?php endif; ?>
            </details>
            <?php else: ?>—<?php endif; ?>
          </td>
        </tr>
        <?php endforeach; ?>
        <?php if (!$logs): ?>
          <tr><td colspan="7" style="text-align:center;color:var(--clr-text-muted)">No audit records found.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?php
$pageContent = ob_get_clean();
$pageTitle   = 'Audit Log';
$activeMenu  = 'audit';
$breadcrumbs = [['label'=>'Audit Log']];
include CLMS_ROOT . '/templates/base.html.php';
