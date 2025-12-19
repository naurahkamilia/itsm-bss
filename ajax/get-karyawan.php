<?php
define('APP_ACCESS', true); 
require_once '../config/config.php';
require_once '../config/database.php';

header('Content-Type: application/json');

$nik = $_GET['NIK'] ?? '';

if ($nik === '') {
    echo json_encode(['status' => false]);
    exit;
}

$db = Database::getInstance()->getConnection();

$stmt = $db->prepare("SELECT Nama FROM karyawan WHERE NIK = ?");
$stmt->execute([$nik]);
$data = $stmt->fetch(PDO::FETCH_ASSOC);

if ($data) {
    echo json_encode([
        'status' => true,
        'nama' => $data['Nama']
    ]);
} else {
    echo json_encode(['status' => false]);
}
