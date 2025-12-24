<?php
define('APP_ACCESS', true);
require_once '../config/config.php';
require_once '../config/database.php';
require_once '../models/Aplikasi.php';

Security::requireAdmin();

if (!isset($_GET['ApkID'])) {
    header("Location: aplikasi-list.php");
    exit;
}

$ApkID = $_GET['ApkID'];

$aplikasiModel = new Aplikasi();

try {
    $deleted = $aplikasiModel->delete($ApkID);

    if ($deleted) {
        // Redirect dengan pesan sukses
        header("Location: aplikasi.php?deleted=1");
        exit;
    } else {
        // Redirect dengan pesan error umum
        header("Location: aplikasi.php?error=1");
        exit;
    }
} catch (PDOException $e) {
    // Jika error foreign key
    if ($e->getCode() == '23000') {
        // Bisa redirect dengan pesan khusus
        header("Location: aplikasi.php?fk_error=1");
        exit;
    } else {
        // Error lain
        die("Error: " . $e->getMessage());
    }
}
