<?php
define('APP_ACCESS', true);
require_once '../config/config.php';
require_once '../config/database.php';
require_once '../models/User.php';

Security::requireAdmin();

$pageTitle   = "Edit User";
$currentPage = "user";

$userModel = new User();
$error = '';

$nik = $_GET['NIK'] ?? null;

if (!$nik) {
    header('Location: user-list.php?error=notfound');
    exit;
}

$user = $userModel->getByNik($nik);

if (!$user) {
    header('Location: user-list.php?error=notfound');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $name     = trim($_POST['name']);
    $email    = trim($_POST['email']);
    $password = trim($_POST['password']); 
    $role     = trim($_POST['role']);
    $status   = trim($_POST['status']);

    // VALIDATION
    if ($name === '' || $email === '' || $role === '' || $status === '') {
        $error = "All fields are required except password!";
    } else {

        $data = [
            'name'   => $name,
            'email'  => $email,
            'role'   => $role,
            'status' => $status
        ];

        if ($password !== '') {
            $data['password'] = $password;
        }

        if ($userModel->update($nik, $data)) {
            header("Location: user-list.php?updated=1");
            exit;
        } else {
            $error = "Failed to update user data!";
        }
    }
}

require_once __DIR__ . '/includes/header.php';
?>

<div class="container-fluid py-4">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Edit User Data</h2>
        <a href="user-list.php" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Back
        </a>
    </div>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>

    <div class="card shadow-sm">
        <div class="card-body">

        <form method="POST" class="p-4">

            <!-- NIK (READONLY) -->
            <div class="mb-3">
                <label class="form-label">Employee ID</label>
                <input type="text" class="form-control"
                       value="<?= htmlspecialchars($user['NIK']) ?>"
                       readonly>
            </div>

            <div class="mb-3">
                <label class="form-label">Name</label>
                <input type="text" name="name" class="form-control"
                       value="<?= htmlspecialchars($user['name']) ?>" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control"
                       value="<?= htmlspecialchars($user['email']) ?>" required>
            </div>

            <div class="mb-3">
                <label class="form-label">New Password (Optional)</label>
                <input type="password" name="password" class="form-control">
                <small class="text-muted">Leave blank if you do not want to change the password</small>
            </div>

            <div class="mb-3">
                <label class="form-label d-block">Role</label>

                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio"
                           name="role" value="admin"
                           <?= $user['role'] === 'admin' ? 'checked' : '' ?>>
                    <label class="form-check-label">Admin</label>
                </div>

                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio"
                           name="role" value="customer"
                           <?= $user['role'] === 'customer' ? 'checked' : '' ?>>
                    <label class="form-check-label">Customer</label>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Status</label>
                <select name="status" class="form-control" required>
                    <option value="aktif" <?= $user['status'] === 'aktif' ? 'selected' : '' ?>>Active</option>
                    <option value="nonaktif" <?= $user['status'] === 'nonaktif' ? 'selected' : '' ?>>Inactive</option>
                </select>
            </div>

            <button type="submit" class="btn btn-primary">
                <i class="bi bi-pencil-square"></i> Update User
            </button>

        </form>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
