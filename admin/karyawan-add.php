<?php
define('APP_ACCESS', true);
require_once '../config/config.php';
require_once '../config/database.php';
require_once '../models/Karyawan.php';
require_once '../helpers/slug.php';

Security::requireAdmin();

$pageTitle = "Add Employee";
$currentPage = "employees";

$karyawanModel = new Karyawan();

// If form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nik        = trim($_POST['NIK']);
    $name       = trim($_POST['Nama']);
    $position   = trim($_POST['Jabatan']);
    $department = trim($_POST['Departement']);
    $dob        = trim($_POST['TTL']);
    $address    = trim($_POST['Alamat']);
    $dateJoined = $_POST['Tgl_masuk_kerja'];
    $plant      = trim($_POST['Plant']);

    // REQUIRED VALIDATION
    if ($nik === '' || $name === '' || $position === '' || $dob === '' || $department === '' || $address === '' || $dateJoined === '' || $plant === '') {
        $error = "All fields are required!";
    } else {

        $karyawanModel->create([
            'NIK' => $nik,
            'Nama' => $name,
            'Jabatan' => $position,
            'Departemen' => $department,
            'TTL' => $dob,
            'Alamat' => $address,
            'Tgl_masuk_kerja' => $dateJoined,
            'Plant' => $plant
        ]);

        header("Location: karyawan-list.php?success=1");
        exit;
    }
}

require_once __DIR__ . '/includes/header.php';
?>

<div class="container-fluid py-4">
    <h2 class="mb-4">Add Employee</h2>

    <?php if (!empty($error)): ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>

    <form method="POST" class="card p-4 shadow-sm">
        <div class="mb-3">
            <label class="form-label">Employee ID</label>
            <input type="text" name="NIK" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Employee Name</label>
            <input type="text" name="Nama" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Position</label>
            <input type="text" name="Jabatan" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Department</label>
            <input type="text" name="Departement" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Date of Birth</label>
            <input type="date" name="TTL" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Address</label>
            <input type="text" name="Alamat" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Date Joined</label>
            <input type="date" name="Tgl_masuk_kerja" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Plant</label>
            <input type="text" name="Plant" class="form-control" required>
        </div>

        <button class="btn btn-success">Save</button>
    </form>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
