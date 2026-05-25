<?php
/**
 * CLMS 2.0 — login.php
 * Standalone authentication page (no sidebar layout).
 */

if (Auth::check()) {
    header('Location: /dashboard');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    Auth::requireCsrf();

    $username = Helpers::clean($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === '' || $password === '') {
        $error = 'Username and password are required.';
    } elseif (!Auth::attempt($username, $password)) {
        $error = 'Invalid username or password.';
    } else {
        if ($_SESSION['force_change'] ?? false) {
            Helpers::redirect('/change-password', 'You must change your password before continuing.');
        }
        Helpers::redirect('/dashboard');
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
  <title>Login — CLMS</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="<?= APP_BASE ?>/assets/css/theme.css">
  <link rel="stylesheet" href="<?= APP_BASE ?>/assets/css/components.css">
  <style>
    *, *::before, *::after { box-sizing: border-box; }
    body {
      margin: 0;
      font-family: var(--font-body);
      background: var(--clr-bg);
      display: flex;
      align-items: center;
      justify-content: center;
      min-height: 100vh;
      padding: var(--space-4);
    }

    .login-wrapper {
      width: 100%;
      max-width: 420px;
    }

    .login-card {
      background: var(--clr-surface);
      border: var(--border-base);
      border-radius: var(--radius-lg);
      box-shadow: var(--shadow-lg);
      overflow: hidden;
    }

    .login-header {
      background: var(--clr-primary);
      padding: var(--space-8) var(--space-6);
      text-align: center;
      color: #fff;
    }

    .login-logo {
      width: 56px;
      height: 56px;
      background: rgba(255,255,255,.2);
      border-radius: var(--radius-lg);
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 1.75rem;
      font-weight: 700;
      margin: 0 auto var(--space-3);
    }

    .login-title {
      font-size: var(--text-xl);
      font-weight: 700;
      margin: 0 0 var(--space-1);
    }

    .login-subtitle {
      font-size: var(--text-sm);
      opacity: .85;
      margin: 0;
    }

    .login-body {
      padding: var(--space-8) var(--space-6);
    }

    .login-body .form-group { margin-bottom: var(--space-5); }

    .login-footer {
      text-align: center;
      padding: var(--space-4) var(--space-6);
      border-top: var(--border-base);
      font-size: var(--text-xs);
      color: var(--clr-text-muted);
    }

    .password-wrapper {
      position: relative;
    }

    .password-wrapper .form-control {
      padding-right: 2.75rem;
    }

    .password-toggle {
      position: absolute;
      right: 0.75rem;
      top: 50%;
      transform: translateY(-50%);
      background: none;
      border: none;
      cursor: pointer;
      color: var(--clr-text-muted);
      padding: 0;
      line-height: 1;
    }
    .password-toggle:hover { color: var(--clr-text); }
  </style>
</head>
<body>

<div class="login-wrapper">
  <div class="login-card">

    <!-- Header -->
    <div class="login-header">
      <div class="login-logo">C</div>
      <h1 class="login-title">CLMS 2.0</h1>
      <p class="login-subtitle">Contract Labour Management System</p>
    </div>

    <!-- Body -->
    <div class="login-body">

      <?php if (!empty($_SESSION['flash_error'])): ?>
        <div class="alert alert-danger" role="alert">
          <?= Helpers::h($_SESSION['flash_error']) ?>
        </div>
        <?php unset($_SESSION['flash_error']); ?>
      <?php endif; ?>

      <?php if ($error): ?>
        <div class="alert alert-danger" role="alert" id="loginError">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
            <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
          </svg>
          <?= Helpers::h($error) ?>
        </div>
      <?php endif; ?>

      <form method="POST" action="<?= APP_BASE ?>/login" id="loginForm" novalidate>
        <input type="hidden" name="<?= CSRF_KEY ?>" value="<?= Helpers::h($token) ?>">

        <div class="form-group">
          <label class="form-label" for="username">Username</label>
          <input
            type="text"
            id="username"
            name="username"
            class="form-control"
            placeholder="Enter your username"
            value="<?= Helpers::h($_POST['username'] ?? '') ?>"
            autocomplete="username"
            data-validate="required"
            autofocus
          >
          <span class="form-error"></span>
        </div>

        <div class="form-group">
          <label class="form-label" for="password">Password</label>
          <div class="password-wrapper">
            <input
              type="password"
              id="password"
              name="password"
              class="form-control"
              placeholder="Enter your password"
              autocomplete="current-password"
              data-validate="required"
            >
            <button type="button" class="password-toggle" id="togglePwd" aria-label="Show/hide password">
              <svg id="eyeIcon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>
              </svg>
            </button>
          </div>
          <span class="form-error"></span>
        </div>

        <button type="submit" class="btn btn-primary" style="width:100%;margin-top:var(--space-2)" id="submitBtn">
          Sign In
        </button>
      </form>
    </div>

    <!-- Footer -->
    <div class="login-footer">
      Muthiah Beverage and Confectionery (Pvt) Ltd &mdash; RCS &copy; <?= date('Y') ?>
    </div>

  </div><!-- .login-card -->
</div><!-- .login-wrapper -->

<div id="toast-container"></div>

<script src="<?= APP_BASE ?>/assets/js/utils.js"></script>
<script src="<?= APP_BASE ?>/assets/js/forms.js"></script>
<script>
  // Password visibility toggle
  document.getElementById('togglePwd').addEventListener('click', function () {
    const input = document.getElementById('password');
    const isHidden = input.type === 'password';
    input.type = isHidden ? 'text' : 'password';
    document.getElementById('eyeIcon').innerHTML = isHidden
      ? '<path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/>'
      : '<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>';
  });

  // Client-side validation on submit
  document.getElementById('loginForm').addEventListener('submit', function (e) {
    const valid = CLMS.forms.validate(this);
    if (!valid) e.preventDefault();
  });
</script>
</body>
</html>
