<?php
define('APP_ACCESS', true);
require_once '../config/config.php';
require_once '../config/database.php';
require_once '../models/User.php';

Security::requireAdmin();

$userModel = new User();

$nik = $_GET['nik'] ?? null;

if (!$nik) {
    die("NIK not found");
}

$user = $userModel->getByNik($nik);

if (!$user) {
    die("User not found");
}

$userModel->delete($nik);
header("Location: user-list.php?deleted=1");
exit;
