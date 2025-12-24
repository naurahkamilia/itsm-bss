<?php
define('APP_ACCESS', true);
require_once '../config/config.php';
require_once '../config/database.php';
require_once '../models/User.php';
require_once '../helpers/slug.php';

Security::requireAdmin();

$pageTitle = "Tambah User";
$currentPage = "user";

$userModel = new User();

// Jika form disubmit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nik        = trim($_POST['NIK']);
    $nama       = trim($_POST['name']);
    $email        = trim($_POST['email']);
    $role       = trim($_POST['role']);
    
    // VALIDASI WAJIB
    if ($nik === '' || $nama === '' || $email === '' || $role === '') {
        $error = "Semua data wajib diisi!";
    } else {

        $userModel->create([
            'NIK' => $nik,
            'name' => $nama,
            'email' => $email,
            'role' => $role,
            'password' => 'password'
            ]);

        header("Location: user-list.php?success=1");
        exit;
    }
}

require_once __DIR__ . '/includes/header.php';
?>

<div class="container-fluid py-5">
    <h2 class="mb-4 fw-semibold text-center">Add User</h2>

    <?php if (!empty($error)): ?>
        <div class="alert alert-danger shadow-sm"><?= $error ?></div>
    <?php endif; ?>

    <form method="POST" class="card p-5 shadow-lg border-0 mx-auto" style="max-width: 600px;">
        <div class="mb-4">
            <label class="form-label fw-medium">NIK</label>
            <input type="text" id="nik" name="NIK" class="form-control rounded-3 py-2" required>
        </div>

        <div class="mb-4">
            <label class="form-label fw-medium">User Name</label>
            <input type="text" id="nama" name="name" class="form-control rounded-3 py-2" readonly>
        </div>

        <div class="mb-4">
            <label class="form-label fw-medium">Email</label>
            <input type="email" name="email" class="form-control rounded-3 py-2" required>
        </div>

        <div class="mb-4">
            <label class="form-label fw-medium d-block">Role</label>

            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="role" value="admin" required>
                <label class="form-check-label">Admin</label>
            </div>

            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="role" value="customer" required>
                <label class="form-check-label">User</label>
            </div>
        </div>

        <div class="text-end">
            <a href="user-list.php" class="btn btn-secondary rounded-3 px-4 py-2 shadow-sm me-2">
                Back
            </a>
            <button class="btn btn-primary rounded-3 px-5 py-2 shadow-sm" type="submit">
                Simpan
            </button>
        </div>
    </form>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>


<script>
    document.getElementById('nik').addEventListener('keyup', function(){
    const nik = this.value;
    if (nik.length < 3) return;

    fetch('../ajax/get-karyawan.php?NIK=' + nik) 
        .then(res => res.json())
        .then(data => {
            if (data.status) {
                document.getElementById('nama').value = data.nama;
            } else {
                document.getElementById('nama').value = '';
            }
        });
});

</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
