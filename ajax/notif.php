<?php
require_once '../config/database.php';
require_once '../models/Notification.php';

$notif = new Notification($db);
$notif->markAsRead($_POST['id']);
?>