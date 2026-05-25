<?php
/**
 * CLMS 2.0 — Auth.php
 * Session management, login, logout, role checks.
 */

class Auth
{
    // ----------------------------------------------------------------
    // Session bootstrap — call once at top of every page
    // ----------------------------------------------------------------
    public static function start(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_name(SESSION_COOKIE_NAME);
            session_set_cookie_params([
                'lifetime' => 0,
                'path'     => '/',
                'secure'   => isset($_SERVER['HTTPS']),
                'httponly' => true,
                'samesite' => 'Lax',
            ]);
            session_start();
        }

        // Idle timeout
        if (isset($_SESSION['last_active'])) {
            if (time() - $_SESSION['last_active'] > SESSION_LIFETIME) {
                self::logout();
                self::redirectToLogin('Session expired. Please log in again.');
            }
        }
        $_SESSION['last_active'] = time();

        // Auto-expire impersonation
        self::checkImpersonationExpiry();
    }

    // ----------------------------------------------------------------
    // Login
    // ----------------------------------------------------------------
    public static function attempt(string $username, string $password): bool
    {
        $user = DB::row(
            'SELECT u.*, GROUP_CONCAT(r.code ORDER BY r.code SEPARATOR ",") AS roles,
                    GROUP_CONCAT(ur.vendor_id ORDER BY r.code SEPARATOR ",") AS vendor_ids
             FROM users u
             JOIN user_roles ur ON ur.user_id = u.id
             JOIN roles r       ON r.id = ur.role_id
             WHERE u.username = ? AND u.is_active = 1 AND u.is_blocked = 0
             GROUP BY u.id',
            [$username]
        );

        if (!$user || !password_verify($password, $user['password_hash'])) {
            AuditLogger::log('LOGIN_FAIL', 'auth', null, null, null, ['username' => $username]);
            return false;
        }

        // Regenerate session ID on login (session fixation prevention)
        session_regenerate_id(true);

        $_SESSION['user_id']      = $user['id'];
        $_SESSION['username']     = $user['username'];
        $_SESSION['full_name']    = $user['full_name'];
        $_SESSION['company_id']   = $user['company_id'];
        $_SESSION['roles']        = explode(',', $user['roles']);
        $_SESSION['force_change'] = (bool) $user['force_pwd_change'];
        $_SESSION['last_active']  = time();
        $_SESSION['csrf_token']   = bin2hex(random_bytes(32));

        // If contractor role, store the vendor_id
        $roleList   = $_SESSION['roles'];
        $vendorIds  = explode(',', $user['vendor_ids'] ?? '');
        $cIdx       = array_search('contractor', $roleList);
        if ($cIdx !== false && !empty($vendorIds[$cIdx])) {
            $_SESSION['vendor_id'] = (int) $vendorIds[$cIdx];
        }

        // Load section assignments for section_incharge
        if (in_array('section_incharge', $roleList, true)) {
            $_SESSION['section_ids'] = array_column(
                DB::rows('SELECT section_id FROM user_sections WHERE user_id = ?', [$user['id']]),
                'section_id'
            );
        }

        // Update last_login
        DB::update('users', ['last_login_at' => date('Y-m-d H:i:s')], ['id' => $user['id']]);

        AuditLogger::log('LOGIN', 'auth', $user['id'], null, null, ['username' => $username]);

        return true;
    }

    // ----------------------------------------------------------------
    // Logout
    // ----------------------------------------------------------------
    public static function logout(): void
    {
        if (isset($_SESSION['user_id'])) {
            AuditLogger::log('LOGOUT', 'auth', $_SESSION['user_id']);
        }
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            setcookie(session_name(), '', time() - 3600, '/');
        }
        session_destroy();
    }

    // ----------------------------------------------------------------
    // Checks
    // ----------------------------------------------------------------
    public static function check(): bool
    {
        return !empty($_SESSION['user_id']);
    }

    public static function user(): ?array
    {
        if (!self::check()) return null;
        return [
            'id'          => $_SESSION['user_id'],
            'username'    => $_SESSION['username'],
            'full_name'   => $_SESSION['full_name'],
            'company_id'  => $_SESSION['company_id'],
            'roles'       => $_SESSION['roles'],
            'vendor_id'   => $_SESSION['vendor_id']   ?? null,
            'section_ids' => $_SESSION['section_ids'] ?? [],
        ];
    }

    /** Returns the REAL authenticated user (ignores impersonation) */
    public static function realUser(): ?array
    {
        if (!self::check()) return null;
        if (!empty($_SESSION['impersonating_as'])) {
            return [
                'id'        => $_SESSION['real_user_id'],
                'username'  => $_SESSION['real_username'],
                'full_name' => $_SESSION['real_full_name'],
                'company_id'=> $_SESSION['real_company_id'],
                'roles'     => $_SESSION['real_roles'],
            ];
        }
        return self::user();
    }

    public static function isImpersonating(): bool
    {
        return !empty($_SESSION['impersonating_as']);
    }

    /** Primary role for UI (first in list) */
    public static function primaryRole(): string
    {
        return $_SESSION['roles'][0] ?? 'guest';
    }

    public static function hasRole(string|array $roles): bool
    {
        $required = is_array($roles) ? $roles : [$roles];
        foreach ($required as $r) {
            if (in_array($r, $_SESSION['roles'] ?? [], true)) return true;
        }
        return false;
    }

    /** Check role against the REAL user (not the impersonated identity) */
    public static function realHasRole(string|array $roles): bool
    {
        $required = is_array($roles) ? $roles : [$roles];
        $realRoles = self::isImpersonating() ? ($_SESSION['real_roles'] ?? []) : ($_SESSION['roles'] ?? []);
        foreach ($required as $r) {
            if (in_array($r, $realRoles, true)) return true;
        }
        return false;
    }

    // ----------------------------------------------------------------
    // Impersonation
    // ----------------------------------------------------------------

    /**
     * Start impersonating $targetUserId.
     * Returns true on success, string error message on failure.
     */
    public static function startImpersonation(int $targetId, string $reason = ''): string|true
    {
        if (self::isImpersonating()) {
            return 'Already impersonating a user. Exit first.';
        }

        $rolesConfig   = require CLMS_ROOT . '/config/roles.php';
        $callerRoles   = $_SESSION['roles'] ?? [];
        $callerCompany = $_SESSION['company_id'];

        // Only super_admin and admin can impersonate
        $canImpersonate = false;
        foreach ($callerRoles as $r) {
            if (isset($rolesConfig['impersonation'][$r])) {
                $canImpersonate = true;
                break;
            }
        }
        if (!$canImpersonate) {
            return 'You do not have permission to impersonate users.';
        }

        // Load target user
        $target = DB::row(
            'SELECT u.*, GROUP_CONCAT(r.code ORDER BY r.code SEPARATOR ",") AS role_codes,
                    GROUP_CONCAT(ur.vendor_id ORDER BY r.code SEPARATOR ",") AS vendor_ids
             FROM users u
             JOIN user_roles ur ON ur.user_id = u.id
             JOIN roles r ON r.id = ur.role_id
             WHERE u.id = ? AND u.is_active = 1 AND u.is_blocked = 0
             GROUP BY u.id',
            [$targetId]
        );

        if (!$target) {
            return 'Target user not found or is inactive.';
        }

        // Cannot impersonate yourself
        if ($target['id'] === $_SESSION['user_id']) {
            return 'You cannot impersonate yourself.';
        }

        $targetRoles = explode(',', $target['role_codes']);

        // super_admin: can impersonate anyone except another super_admin
        // admin: can only impersonate allowed roles within own company
        $isSuperAdmin = in_array('super_admin', $callerRoles, true);

        if (!$isSuperAdmin) {
            // Must be same company
            if ((int)$target['company_id'] !== (int)$callerCompany) {
                return 'You can only impersonate users within your own company.';
            }
            // Check that target role is in allowed list for caller
            $allowedTargetRoles = [];
            foreach ($callerRoles as $cr) {
                if (isset($rolesConfig['impersonation'][$cr])) {
                    $allowedTargetRoles = array_merge($allowedTargetRoles, $rolesConfig['impersonation'][$cr]);
                }
            }
            foreach ($targetRoles as $tr) {
                if (!in_array($tr, $allowedTargetRoles, true)) {
                    return 'You cannot impersonate a user with role: ' . $tr;
                }
            }
        } else {
            // super_admin: block impersonating another super_admin
            if (in_array('super_admin', $targetRoles, true)) {
                return 'Super admin accounts cannot be impersonated.';
            }
        }

        // Save real identity
        $_SESSION['real_user_id']    = $_SESSION['user_id'];
        $_SESSION['real_username']   = $_SESSION['username'];
        $_SESSION['real_full_name']  = $_SESSION['full_name'];
        $_SESSION['real_company_id'] = $_SESSION['company_id'];
        $_SESSION['real_roles']      = $_SESSION['roles'];
        $_SESSION['real_vendor_id']  = $_SESSION['vendor_id'] ?? null;

        // Switch to target identity
        $_SESSION['user_id']      = $target['id'];
        $_SESSION['username']     = $target['username'];
        $_SESSION['full_name']    = $target['full_name'];
        $_SESSION['company_id']   = $target['company_id'];
        $_SESSION['roles']        = $targetRoles;
        $_SESSION['vendor_id']    = null;

        $vendorIds = explode(',', $target['vendor_ids'] ?? '');
        $cIdx = array_search('contractor', $targetRoles);
        if ($cIdx !== false && !empty($vendorIds[$cIdx])) {
            $_SESSION['vendor_id'] = (int) $vendorIds[$cIdx];
        }

        // Load section assignments for section_incharge
        if (in_array('section_incharge', $targetRoles, true)) {
            $_SESSION['section_ids'] = array_column(
                DB::rows('SELECT section_id FROM user_sections WHERE user_id = ?', [$target['id']]),
                'section_id'
            );
        } else {
            $_SESSION['section_ids'] = [];
        }

        $_SESSION['impersonating_as'] = [
            'user_id'    => $target['id'],
            'username'   => $target['username'],
            'full_name'  => $target['full_name'],
            'company_id' => $target['company_id'],
        ];
        $_SESSION['impersonation_started_at'] = time();

        // Log it
        DB::execute(
            "INSERT INTO impersonation_log (actor_id,actor_username,target_id,target_username,target_company,reason,ip_address,started_at)
             VALUES (?,?,?,?,?,?,?,NOW())",
            [$_SESSION['real_user_id'], $_SESSION['real_username'],
             $target['id'], $target['username'], $target['company_id'],
             $reason ?: null, self::getClientIp()]
        );

        return true;
    }

    /**
     * End the current impersonation and restore real identity.
     */
    public static function endImpersonation(): void
    {
        if (!self::isImpersonating()) return;

        // Update log
        DB::execute(
            "UPDATE impersonation_log SET ended_at=NOW()
             WHERE actor_id=? AND target_id=? AND ended_at IS NULL
             ORDER BY started_at DESC LIMIT 1",
            [$_SESSION['real_user_id'], $_SESSION['impersonating_as']['user_id']]
        );

        // Restore real identity
        $_SESSION['user_id']      = $_SESSION['real_user_id'];
        $_SESSION['username']     = $_SESSION['real_username'];
        $_SESSION['full_name']    = $_SESSION['real_full_name'];
        $_SESSION['company_id']   = $_SESSION['real_company_id'];
        $_SESSION['roles']        = $_SESSION['real_roles'];
        $_SESSION['vendor_id']    = $_SESSION['real_vendor_id'] ?? null;
        $_SESSION['section_ids']  = [];

        // Clear impersonation keys
        unset(
            $_SESSION['impersonating_as'],
            $_SESSION['impersonation_started_at'],
            $_SESSION['real_user_id'],
            $_SESSION['real_username'],
            $_SESSION['real_full_name'],
            $_SESSION['real_company_id'],
            $_SESSION['real_roles'],
            $_SESSION['real_vendor_id']
        );
    }

    /**
     * Auto-expire impersonation if idle > IMPERSONATION_TIMEOUT seconds.
     * Called from session bootstrap.
     */
    public static function checkImpersonationExpiry(): void
    {
        if (!self::isImpersonating()) return;
        $timeout = defined('IMPERSONATION_TIMEOUT') ? IMPERSONATION_TIMEOUT : 3600;
        $started = $_SESSION['impersonation_started_at'] ?? 0;
        if (time() - $started > $timeout) {
            self::endImpersonation();
            $_SESSION['flash_error'] = 'Impersonation session expired after ' . ($timeout / 60) . ' minutes.';
        }
    }

    private static function getClientIp(): string
    {
        foreach (['HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'REMOTE_ADDR'] as $k) {
            if (!empty($_SERVER[$k])) return trim(explode(',', $_SERVER[$k])[0]);
        }
        return '0.0.0.0';
    }

    // ----------------------------------------------------------------
    // Guards — call at top of each protected module
    // ----------------------------------------------------------------

    /** Redirect to login if not authenticated */
    public static function requireAuth(): void
    {
        if (!self::check()) {
            self::redirectToLogin();
        }
    }

    /** Redirect to 403 if user doesn't have required role */
    public static function requireRole(string|array $roles): void
    {
        self::requireAuth();
        if (!self::hasRole($roles)) {
            http_response_code(403);
            include CLMS_ROOT . '/modules/errors/403.php';
            exit;
        }
    }

    // ----------------------------------------------------------------
    // CSRF
    // ----------------------------------------------------------------
    public static function csrfToken(): string
    {
        return $_SESSION['csrf_token'] ?? '';
    }

    public static function verifyCsrf(): bool
    {
        $token = $_POST[CSRF_KEY] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
        return hash_equals($_SESSION['csrf_token'] ?? '', $token);
    }

    public static function requireCsrf(): void
    {
        if (!self::verifyCsrf()) {
            http_response_code(403);
            self::jsonError('Invalid security token. Please reload the page.');
        }
    }

    // ----------------------------------------------------------------
    // Helpers
    // ----------------------------------------------------------------
    private static function redirectToLogin(string $message = ''): void
    {
        if ($message) $_SESSION['flash_error'] = $message;
        header('Location: ' . APP_BASE . '/login');
        exit;
    }

    public static function jsonError(string $message, int $code = 403): void
    {
        http_response_code($code);
        header('Content-Type: application/json');
        echo json_encode(['ok' => false, 'message' => $message]);
        exit;
    }

    public static function json(mixed $data, int $code = 200): void
    {
        http_response_code($code);
        header('Content-Type: application/json');
        echo json_encode(['ok' => true, 'data' => $data]);
        exit;
    }
}
