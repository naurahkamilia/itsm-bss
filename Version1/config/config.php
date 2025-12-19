<?php
/**
 * Configuration File - Toko Online Hijau
 * PHP 8+ E-Commerce Application
 */

// Prevent direct access
defined('APP_ACCESS') or die('Direct access not permitted');

// Application Settings
define('APP_NAME', 'Toko Online Hijau');
define('APP_VERSION', '1.0.0');
define('APP_ENV', 'development'); // development, production

// Base URL Configuration
// Sesuaikan dengan lokasi XAMPP Anda
define('BASE_URL', 'http://localhost/toko-online/');
define('ADMIN_URL', BASE_URL . 'admin/');

// Database Configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'toko_online');
define('DB_USER', 'root');
define('DB_PASS', ''); // Default XAMPP password kosong
define('DB_CHARSET', 'utf8mb4');

// WhatsApp Configuration
define('WHATSAPP_NUMBER', '6281234567890'); // Format: 62xxx (tanpa +)
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

// Email Configuration (untuk reset password)
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USER', 'your-email@gmail.com');
define('SMTP_PASS', 'your-app-password');
define('SMTP_FROM', 'noreply@tokoonline.com');
define('SMTP_FROM_NAME', APP_NAME);

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

// Session Configuration
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_secure', 0); // Set to 1 if using HTTPS
ini_set('session.cookie_samesite', 'Strict');

// Start session if not started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// CSRF Token Generation
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
