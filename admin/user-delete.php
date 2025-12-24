<?php
define('APP_ACCESS', true);
require_once '../config/config.php';
require_once '../config/database.php';
require_once '../models/User.php';

Security::requireAdmin();

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: user-list.php');
    exit;
}

$nik = $_GET['id'];

$userModel = new User();

// Hapus user
$deleted = $userModel->delete($nik);

if ($deleted) {
    header('Location: user-list.php?deleted=1');
} else {
    header('Location: user-list.php?error=1');
}
exit;
