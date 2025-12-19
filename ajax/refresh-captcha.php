<?php
/**
 * AJAX endpoint to refresh captcha
 */

define('APP_ACCESS', true);
require_once '../config/config.php';

header('Content-Type: application/json');

// Generate new captcha
$captcha = Security::generateCaptcha();

echo json_encode([
    'success' => true,
    'captcha' => $captcha
]);
