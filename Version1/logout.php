<?php
/**
 * Logout Handler
 */

define('APP_ACCESS', true);
require_once 'config/config.php';

// Clear all session data
$_SESSION = [];

// Destroy session cookie
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time() - 3600, '/');
}

// Destroy session
session_destroy();

// Redirect to home with message
session_start();
$_SESSION['success_message'] = 'Anda telah berhasil logout.';

header('Location: ' . BASE_URL);
exit;
