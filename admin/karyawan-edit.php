<?php
define('APP_ACCESS', true);
require_once '../config/config.php';
require_once '../config/database.php';
require_once '../models/Karyawan.php';

Security::requireAdmin();

$pageTitle = "Edit Employee";
$currentPage = "employees";

$karyawanModel = new Karyawan();
$error = '';
$success = '';

// Get employee data
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$employee = $karyawanModel->getById($id);

if (!$employee) {
    header('Location: karyawan-list.php?error=notfound');
    exit;
}

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nik        = trim($_POST['NIK']);
    $name       = trim($_POST['Name']);
    $position   = trim($_POST['Position']);
    $department = trim($_POST['Department']);
    $dob        = trim($_POST['DOB']);
    $address    = trim($_POST['Address']);
    $dateJoined = $_POST['Date_Joined'];
    $plant      = trim($_POST['Plant']);

    // VALIDATION
    if ($nik === '' || $name === '' || $position === '' || $department === '' || 
        $address === '' || $dateJoined === '' || $plant === '') {

        $error = "All fields are required!";

    } else {

        $data = [
            'Nama' => $name,
            'Jabatan' => $position,
            'Departemen' => $department,
            'TTL' => $dob,
            'Alamat' => $address,
            'Tgl_masuk_kerja' => $dateJoined,
            'Plant' => $plant
        ];

        if ($karyawanModel->update($nik, $data)) {
            header("Location: karyawan-list.php?updated=1");
            exit;
        } else {
            $error = "Failed to update employee data!";
        }
    }
}

require_once __DIR__ . '/includes/header.php';
?>
<div class="container-fluid py-5">
    <h2 class="mb-4 fw-semibold text-center">Edit Employee</h2>

    <?php if (!empty($error)): ?>
        <div class="alert alert-danger shadow-sm"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST" class="card p-5 shadow-lg border-0 mx-auto" style="max-width: 900px;">
        <input type="hidden" name="NIK" value="<?= htmlspecialchars($employee['NIK']) ?>">

        <div class="row g-4">
            <div class="col-md-6">
                <label class="form-label fw-medium">Employee ID</label>
                <input type="text" name="NIK" class="form-control rounded-3 py-2" 
                       value="<?= htmlspecialchars($employee['NIK']) ?>" readonly>
            </div>

            <div class="col-md-6">
                <label class="form-label fw-medium">Employee Name</label>
                <input type="text" name="Nama" class="form-control rounded-3 py-2" 
                       value="<?= htmlspecialchars($employee['Nama']) ?>" required>
            </div>

            <div class="col-md-6">
                <label class="form-label fw-medium">Position</label>
                <input type="text" name="Jabatan" class="form-control rounded-3 py-2" 
                       value="<?= htmlspecialchars($employee['Jabatan']) ?>" required>
            </div>

            <div class="col-md-6">
                <label class="form-label fw-medium">Department</label>
                <input type="text" name="Departement" class="form-control rounded-3 py-2" 
                       value="<?= htmlspecialchars($employee['Departemen']) ?>" required>
            </div>

            <div class="col-md-6">
                <label class="form-label fw-medium">Date of Birth</label>
                <input type="date" name="TTL" class="form-control rounded-3 py-2" 
                       value="<?= htmlspecialchars($employee['TTL'] ?? ''); ?>" required>
            </div>

            <div class="col-md-6">
                <label class="form-label fw-medium">Date Joined</label>
                <input type="date" name="Tgl_masuk_kerja" class="form-control rounded-3 py-2" 
                       value="<?= htmlspecialchars($employee['Tgl_masuk_kerja']) ?>" required>
            </div>

            <div class="col-12">
                <label class="form-label fw-medium">Address</label>
                <input type="text" name="Alamat" class="form-control rounded-3 py-2" 
                       value="<?= htmlspecialchars($employee['Alamat']) ?>" required>
            </div>

            <div class="col-md-6">
                <label class="form-label fw-medium">Plant</label>
                <input type="text" name="Plant" class="form-control rounded-3 py-2" 
                       value="<?= htmlspecialchars($employee['Plant']) ?>" required>
            </div>
        </div>

        <div class="text-end mt-4">
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

