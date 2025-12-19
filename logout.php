<?php
define('APP_ACCESS', true);
require_once 'config/config.php';

session_start();

$_SESSION = [];
session_unset();
session_destroy();

if (isset($_COOKIE[session_name()])) {
    setcookie(
        session_name(),
        '',
        time() - 3600,
        '/'  
    );
}

/* Redirect ke login */
header('Location: ' . BASE_URL . 'index.php');
exit;
