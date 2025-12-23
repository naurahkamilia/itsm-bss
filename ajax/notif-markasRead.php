<?php
session_start(); 
defined('APP_ACCESS') or die('Direct access not permitted');

require_once '../config/config.php';
require_once '../config/database.php';
require_once '../models/Notification.php';

$db = Database::getInstance()->getConnection();
$notifModel = new Notification($db);

$receiver = $_SESSION['user_id'] ?? null;

if ($receiver) {
    $success = $notifModel->markAllAsRead($receiver);

    if ($success) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to update database']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'User not logged in']);
}
