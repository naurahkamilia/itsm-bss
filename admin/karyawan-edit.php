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

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Edit Employee</h2>
        <a href="karyawan-list.php" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Back
        </a>
    </div>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>

    <div class="card shadow-sm">
        <div class="card-body">
            <form method="POST">
                <input type="hidden" name="NIK" value="<?= htmlspecialchars($employee['NIK']) ?>">

                <div class="mb-3">
                    <label class="form-label">Employee Name</label>
                    <input type="text" class="form-control" name="Name"
                        value="<?= htmlspecialchars($employee['Nama']) ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Position</label>
                    <input type="text" class="form-control" name="Position"
                        value="<?= htmlspecialchars($employee['Jabatan']) ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Department</label>
                    <input type="text" class="form-control" name="Department"
                        value="<?= htmlspecialchars($employee['Departemen']) ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Date of Birth</label>
                    <input type="date" class="form-control" name="DOB"
                        value="<?= htmlspecialchars($employee['TTL'] ?? ''); ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Address</label>
                    <input type="text" class="form-control" name="Address"
                        value="<?= htmlspecialchars($employee['Alamat']) ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Date Joined</label>
                    <input type="date" class="form-control" name="Date_Joined"
                        value="<?= htmlspecialchars($employee['Tgl_masuk_kerja']) ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Plant</label>
                    <input type="text" class="form-control" name="Plant"
                        value="<?= htmlspecialchars($employee['Plant']) ?>" required>
                </div>

                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-pencil-square"></i> Update Employee
                </button>
            </form>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
