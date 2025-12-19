<?php
/**
 * Configuration File - Toko Online Hijau
 * PHP 8+ E-Commerce Application
 */

// Prevent direct access
defined('APP_ACCESS') or die('Direct access not permitted');

// Application Settings
define('APP_NAME', 'ITSM');
define('APP_VERSION', '1.0.0');
define('APP_ENV', 'development'); // development, production

// Base URL Configuration
define('BASE_URL', 'http://localhost/itsm-reqs/');
define('ADMIN_URL', BASE_URL . 'admin/');
define('USER_URL', BASE_URL . 'user/');

// Database Configuration
define('DB_HOST', '127.0.0.1');
define('DB_NAME', 'toko_online');
define('DB_PORT', 3307);
define('DB_USER', 'root');
define('DB_PASS', ''); // Default XAMPP password kosong
define('DB_CHARSET', 'utf8mb4');

// WhatsApp Configuration
define('WHATSAPP_NUMBER', '6281234567890');
define('WHATSAPP_MESSAGE_PREFIX', "Halo *Toko Online Hijau*,\n\nSaya ingin order:\n\n");

// Security Settings
define('SESSION_LIFETIME', 3600); // 1 hour in seconds
define('PASSWORD_MIN_LENGTH', 6);
define('MAX_LOGIN_ATTEMPTS', 5);
define('LOGIN_TIMEOUT', 300); // 5 minutes

// Pagination Settings
define('ITEMS_PER_PAGE', 12);

// Upload Settings
define('UPLOAD_PATH', __DIR__ . '/../public/uploads/');
define('UPLOAD_URL', BASE_URL . 'public/uploads/');
define('MAX_FILE_SIZE', 5242880); // 5MB
define('ALLOWED_EXTENSIONS', ['jpg', 'jpeg', 'png', 'gif', 'webp']);

// SMTP Mailtrap
define('SMTP_HOST', 'sandbox.smtp.mailtrap.io');
define('SMTP_PORT', 2525);
define('SMTP_USER', 'a72c426d281f3d'); // ini sesuai Mailtrap
define('SMTP_PASS', '****6b47');       // ini sesuai Mailtrap
define('SMTP_FROM', 'noreply@tokoonline.com');
define('SMTP_FROM_NAME', 'ITSM');


// Timezone
date_default_timezone_set('Asia/Jakarta');

// Error Reporting
if (APP_ENV === 'development') {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

/**
 * SESSION CONFIGURATION (SAFE)
 *
 * Important: some other file may have started the session already.
 * We set session-related ini settings only when the session has NOT been started yet.
 */
if (session_status() === PHP_SESSION_NONE) {
    // secure cookie when using HTTPS
    $secure_cookie = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');

    // set session ini options before starting session
    ini_set('session.use_strict_mode', 1);
    ini_set('session.cookie_httponly', 1);
    ini_set('session.use_only_cookies', 1);
    ini_set('session.cookie_secure', $secure_cookie ? 1 : 0); // set to 1 if HTTPS
    // PHP 7.3+ supports samesite via ini; if not available, it's ignored
    if (PHP_VERSION_ID >= 70300) {
        ini_set('session.cookie_samesite', 'Strict');
    }

    // recommended lifetime (you may tune)
    ini_set('session.gc_maxlifetime', (string)SESSION_LIFETIME);

    // start the session
    session_start();
} else {
    // session already active: don't try to change ini settings
    // optionally you may log or handle this situation if needed
}

/**
 * CSRF Token Generation
 * Keep it stable across requests. If already set, do nothing.
 */
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Auto-load classes
spl_autoload_register(function ($class) {
    $file = __DIR__ . '/../classes/' . $class . '.php';
    if (file_exists($file)) {
        require_once $file;
    }
});
