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

<div class="container-fluid py-5">
    <h2 class="mb-4 fw-semibold text-center">Add Employee</h2>

    <?php if (!empty($error)): ?>
        <div class="alert alert-danger shadow-sm"><?= $error ?></div>
    <?php endif; ?>

    <form method="POST" class="card p-5 shadow-lg border-0 mx-auto" style="max-width: 900px;">
        <div class="row g-4">
            <div class="col-md-6">
                <label class="form-label fw-medium">Employee ID</label>
                <input type="text" name="NIK" class="form-control rounded-3 py-2" required>
            </div>

            <div class="col-md-6">
                <label class="form-label fw-medium">Employee Name</label>
                <input type="text" name="Nama" class="form-control rounded-3 py-2" required>
            </div>

            <div class="col-md-6">
                <label class="form-label fw-medium">Position</label>
                <input type="text" name="Jabatan" class="form-control rounded-3 py-2" required>
            </div>

            <div class="col-md-6">
                <label class="form-label fw-medium">Department</label>
                <input type="text" name="Departement" class="form-control rounded-3 py-2" required>
            </div>

            <div class="col-md-6">
                <label class="form-label fw-medium">Date of Birth</label>
                <input type="date" name="TTL" class="form-control rounded-3 py-2" required>
            </div>

            <div class="col-md-6">
                <label class="form-label fw-medium">Date Joined</label>
                <input type="date" name="Tgl_masuk_kerja" class="form-control rounded-3 py-2" required>
            </div>

            <div class="col-12">
                <label class="form-label fw-medium">Address</label>
                <input type="text" name="Alamat" class="form-control rounded-3 py-2" required>
            </div>

            <div class="col-md-6">
                <label class="form-label fw-medium">Plant</label>
                <input type="text" name="Plant" class="form-control rounded-3 py-2" required>
            </div>
        </div>

        <div class="text-end">
            <a href="karyawan-list.php" class="btn btn-secondary rounded-3 px-4 py-2 shadow-sm me-2">
                Cancel
            </a>
            <button class="btn btn-primary rounded-3 px-5 py-2 shadow-sm" type="submit">
                Save
            </button>
        </div>
    </form>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>