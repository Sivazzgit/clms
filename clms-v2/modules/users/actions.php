<?php
/**
 * CLMS 2.0 — User actions (POST only, JSON responses)
 * POST /users/{id}/delete
 * POST /users/{id}/toggle-block
 */
Auth::requireRole('hr_admin');
Auth::requireCsrf();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit;
}

$id     = (int)($_GET['id'] ?? 0);
$action = basename($_SERVER['REQUEST_URI']);   // last segment: 'delete' or 'toggle-block'

$user = DB::row('SELECT id, username, is_blocked, is_active FROM users WHERE id = ?', [$id]);
if (!$user) {
    Helpers::redirect('/users', 'User not found.', 'error');
}

// Prevent self-harm
if ($id === Auth::user()['id']) {
    Helpers::redirect('/users', 'You cannot modify your own account here.', 'error');
}

if ($action === 'delete') {
    DB::transaction(function () use ($id, $user) {
        DB::execute('DELETE FROM user_roles   WHERE user_id = ?', [$id]);
        DB::execute('DELETE FROM user_sections WHERE user_id = ?', [$id]);
        DB::delete('users', ['id' => $id]);
        AuditLogger::log('DELETE', 'users', $id, $user);
    });
    Helpers::redirect('/users', 'User "' . $user['username'] . '" deleted.');
}

if ($action === 'toggle-block') {
    $newBlocked = $user['is_blocked'] ? 0 : 1;
    DB::update('users', ['is_blocked' => $newBlocked, 'updated_at' => date('Y-m-d H:i:s')], ['id' => $id]);
    AuditLogger::log($newBlocked ? 'BLOCK' : 'UNBLOCK', 'users', $id, ['is_blocked' => $user['is_blocked']], ['is_blocked' => $newBlocked]);
    $msg = $newBlocked ? 'User blocked.' : 'User unblocked.';
    Helpers::redirect('/users', $msg);
}

Helpers::redirect('/users');
