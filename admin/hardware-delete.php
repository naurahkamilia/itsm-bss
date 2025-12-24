<?php
define('APP_ACCESS', true);
require_once '../config/config.php';
require_once '../config/database.php';
require_once '../models/Hardware.php';

Security::requireAdmin();

if (!isset($_GET['HwID']) || empty($_GET['HwID'])) {
    header('Location: hardware-list.php');
    exit;
}

$hwid = $_GET['HwID'];

$hwModel = new Hardware();

// Hapus hardware
$deleted = $hwModel->delete($hwid);

if ($deleted) {
    header('Location: hardware-list.php?deleted=1');
} else {
    header('Location: hardware-list.php?error=1');
}
exit;
