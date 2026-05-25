<?php
/**
 * CLMS 2.0 — change-password.php
 * Force-change or voluntary password change.
 */
Auth::requireAuth();

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    Auth::requireCsrf();

    $current  = $_POST['current_password'] ?? '';
    $new      = $_POST['new_password'] ?? '';
    $confirm  = $_POST['confirm_password'] ?? '';

    if (empty($current))           $errors['current_password'] = 'Current password is required.';
    if (strlen($new) < 8)          $errors['new_password']     = 'New password must be at least 8 characters.';
    if ($new !== $confirm)         $errors['confirm_password'] = 'Passwords do not match.';

    if (empty($errors)) {
        $user = DB::row('SELECT password_hash FROM users WHERE id = ?', [Auth::user()['id']]);
        if (!password_verify($current, $user['password_hash'])) {
            $errors['current_password'] = 'Current password is incorrect.';
        }
    }

    if (empty($errors)) {
        DB::update('users', [
            'password_hash'    => password_hash($new, PASSWORD_BCRYPT, ['cost' => BCRYPT_COST]),
            'force_pwd_change' => 0,
            'updated_at'       => date('Y-m-d H:i:s'),
        ], ['id' => Auth::user()['id']]);
        $_SESSION['force_change'] = false;
        AuditLogger::log('PASSWORD_CHANGE', 'auth', Auth::user()['id']);
        Helpers::redirect('/dashboard', 'Password updated successfully.');
    }
}

$token = Auth::csrfToken();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="<?= Helpers::h($token) ?>">
  <title>Change Password — CLMS</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="<?= APP_BASE ?>/assets/css/theme.css">
  <link rel="stylesheet" href="<?= APP_BASE ?>/assets/css/components.css">
  <style>
    *, *::before, *::after { box-sizing: border-box; }
    body { margin: 0; font-family: var(--font-body); background: var(--clr-bg); display: flex; align-items: center; justify-content: center; min-height: 100vh; padding: var(--space-4); }
    .card { max-width: 440px; width: 100%; }
    .card-header { background: var(--clr-primary); color: #fff; padding: var(--space-6); border-radius: var(--radius-lg) var(--radius-lg) 0 0; }
    .card-header h1 { margin: 0; font-size: var(--text-xl); }
    .card-header p  { margin: var(--space-1) 0 0; opacity: .85; font-size: var(--text-sm); }
    .card-body { padding: var(--space-6); background: var(--clr-surface); border: var(--border-base); border-top: 0; border-radius: 0 0 var(--radius-lg) var(--radius-lg); }
  </style>
</head>
<body>
<div class="card">
  <div class="card-header">
    <h1>Change Password</h1>
    <p>Set a new secure password for your account.</p>
  </div>
  <div class="card-body">
    <?php if (!empty($_SESSION['flash_error'])): ?>
      <div class="alert alert-warning"><?= Helpers::h($_SESSION['flash_error']) ?></div>
      <?php unset($_SESSION['flash_error']); ?>
    <?php endif; ?>

    <form method="POST" action="<?= APP_BASE ?>/change-password" novalidate>
      <input type="hidden" name="<?= CSRF_KEY ?>" value="<?= Helpers::h($token) ?>">

      <div class="form-group">
        <label class="form-label required" for="current_password">Current Password</label>
        <input type="password" id="current_password" name="current_password" class="form-control <?= isset($errors['current_password']) ? 'is-invalid' : '' ?>" data-validate="required" autocomplete="current-password">
        <span class="form-error"><?= $errors['current_password'] ?? '' ?></span>
      </div>

      <div class="form-group">
        <label class="form-label required" for="new_password">New Password</label>
        <input type="password" id="new_password" name="new_password" class="form-control <?= isset($errors['new_password']) ? 'is-invalid' : '' ?>" data-validate="required|minlen:8" autocomplete="new-password">
        <span class="form-error"><?= $errors['new_password'] ?? '' ?></span>
      </div>

      <div class="form-group">
        <label class="form-label required" for="confirm_password">Confirm New Password</label>
        <input type="password" id="confirm_password" name="confirm_password" class="form-control <?= isset($errors['confirm_password']) ? 'is-invalid' : '' ?>" data-validate="required" autocomplete="new-password">
        <span class="form-error"><?= $errors['confirm_password'] ?? '' ?></span>
      </div>

      <button type="submit" class="btn btn-primary" style="width:100%;margin-top:var(--space-2)">Update Password</button>
    </form>
  </div>
</div>
<div id="toast-container"></div>
<script src="/assets/js/utils.js"></script>
<script src="/assets/js/forms.js"></script>
</body>
</html>
