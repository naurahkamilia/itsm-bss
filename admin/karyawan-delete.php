<?php
// karyawan-delete.php
define('APP_ACCESS', true);
require_once '../config/config.php';
require_once '../config/database.php';
require_once '../models/Karyawan.php';

// Hanya admin yang boleh delete
Security::requireAdmin();

// Pastikan ada parameter NIK
if (!isset($_GET['id']) || empty($_GET['id'])) {
    // jika tidak ada NIK, kembalikan ke daftar karyawan
    header('Location: karyawan-list.php');
    exit;
}

$nik = $_GET['id'];

$karyawanModel = new Karyawan();

// Hapus data karyawan berdasarkan NIK
$deleted = $karyawanModel->delete($nik);

if ($deleted) {
    // jika sukses hapus, redirect dengan indikator success
    header('Location: karyawan-list.php?deleted=1');
} else {
    // jika gagal hapus, redirect dengan indikator error
    header('Location: karyawan-list.php?error=1');
}
exit;
