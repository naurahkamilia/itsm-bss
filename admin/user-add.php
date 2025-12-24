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

<div class="container-fluid py-4">
    <h2 class="mb-4">Tambah User</h2>

    <?php if (!empty($error)): ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>

    <form method="POST" class="card p-4 shadow-sm">
        <div class="mb-3">
            <label class="form-label">NIK</label>
            <input type="text" id="nik" name="NIK" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Nama</label>
            <input type="text" id="nama" name="name" class="form-control" readonly>
        </div>

        <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="text" name="email" class="form-control" required>
        </div>

        <div class="mb-3">
        <label class="form-label d-block">Role</label>

        <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" name="role" value="admin" required>
            <label class="form-check-label">Admin</label>
        </div>

        <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" name="role" value="customer" required>
            <label class="form-check-label">Customer</label>
        </div>
    </div>

        <button class="btn btn-success">Simpan</button>
    </form>
</div>

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
