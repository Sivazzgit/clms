<?php
/**
 * CLMS 2.0 — app.php
 * Application constants and configuration
 */

defined('CLMS_ROOT') || define('CLMS_ROOT', dirname(__DIR__));

// ----------------------------------------------------------------
// Environment — set to 'production' on live server
// ----------------------------------------------------------------
define('APP_ENV',     getenv('APP_ENV')     ?: 'development');
define('APP_VERSION', '2.0.0');
define('APP_NAME',    'CLMS');

// ----------------------------------------------------------------
// Base URL path — set this when deploying into a subfolder.
// Root deployment (domain.com/):        APP_BASE = ''
// Subfolder deployment (domain.com/clms): APP_BASE = '/clms'
// No trailing slash.
// To set on shared hosting (when SetEnv doesn't work), create config/local.php:
//   <?php return ['APP_BASE' => '/clms', 'DB_PASS' => 'secret'];
// ----------------------------------------------------------------
$_localCfg = is_file(__DIR__ . '/local.php') ? (include __DIR__ . '/local.php') : [];
define('APP_BASE', getenv('APP_BASE') ?: ($_SERVER['APP_BASE'] ?? ($_localCfg['APP_BASE'] ?? '')));

// ----------------------------------------------------------------
// Database — reads Docker env vars (CLMS_V2_*) with fallbacks
// ----------------------------------------------------------------
define('DB_HOST',    getenv('CLMS_V2_DB_HOST') ?: (getenv('MYSQL_HOST')     ?: ($_localCfg['DB_HOST'] ?? 'localhost')));
define('DB_PORT',    getenv('MYSQL_PORT')       ?: ($_localCfg['DB_PORT'] ?? '3306'));
define('DB_NAME',    getenv('CLMS_V2_DB_NAME')  ?: ($_localCfg['DB_NAME'] ?? 'clms_v2'));
define('DB_USER',    getenv('CLMS_V2_DB_USER')  ?: (getenv('MYSQL_USER')    ?: ($_localCfg['DB_USER'] ?? 'clmsuser')));
define('DB_PASS',    getenv('CLMS_V2_DB_PASS')  ?: (getenv('MYSQL_PASSWORD')?: ($_localCfg['DB_PASS'] ?? '')));
define('DB_CHARSET', 'utf8mb4');

// ----------------------------------------------------------------
// Session
// ----------------------------------------------------------------
define('SESSION_LIFETIME',   3600);       // 1 hour idle timeout (seconds)
define('SESSION_COOKIE_NAME','clms_sess');

// ----------------------------------------------------------------
// Security
// ----------------------------------------------------------------
define('BCRYPT_COST',  12);              // password_hash cost factor
define('CSRF_KEY',     'csrf_token');

// ----------------------------------------------------------------
// Uploads
// ----------------------------------------------------------------
define('UPLOAD_ROOT',    CLMS_ROOT . '/uploads');
define('UPLOAD_MAX_MB',  10);
define('ALLOWED_IMG_TYPES', ['image/jpeg', 'image/png', 'image/webp']);
define('ALLOWED_DOC_TYPES', ['application/pdf', 'image/jpeg', 'image/png', 'application/vnd.ms-excel',
                              'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet']);

// ----------------------------------------------------------------
// Pagination
// ----------------------------------------------------------------
define('PAGE_SIZE', 25);

// ----------------------------------------------------------------
// Error reporting
// ----------------------------------------------------------------
if (APP_ENV === 'development') {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
} else {
    error_reporting(0);
    ini_set('display_errors', '0');
}

// ----------------------------------------------------------------
// Timezone
// ----------------------------------------------------------------
date_default_timezone_set('Asia/Kolkata');
