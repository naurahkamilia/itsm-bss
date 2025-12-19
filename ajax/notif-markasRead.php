<?php
session_start(); // WAJIB
defined('APP_ACCESS') or die('Direct access not permitted');
require_once '../config/config.php';
require_once '../config/database.php';
require_once '../models/Notification.php';

$db = Database::getInstance()->getConnection();
$notifModel = new Notification($db);

$adminNik = (int)($_SESSION['user_id'] ?? 0);

// Debug: pastikan session ada dan sama dengan DB
// var_dump($adminNik); die();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['mark_all_read'])) {
    $notifModel->markAllAsRead($adminNik);
    echo "OK";
}
