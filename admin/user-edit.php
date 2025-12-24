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

<div class="container-fluid py-5">
    <div class="mx-auto" style="max-width: 700px;">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-semibold">Edit User Data</h2>
            <a href="user-list.php" class="btn btn-outline-secondary rounded-3 px-3 py-2">
                <i class="bi bi-arrow-left"></i> Back
            </a>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-danger shadow-sm mb-3"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <div class="card shadow-sm border-0">
            <div class="card-body p-4">
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label fw-medium">Employee ID</label>
                        <input type="text" class="form-control rounded-3 py-2" 
                               value="<?= htmlspecialchars($user['NIK']) ?>" readonly>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-medium">Name</label>
                        <input type="text" name="name" class="form-control rounded-3 py-2"
                               value="<?= htmlspecialchars($user['name']) ?>" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-medium">Email</label>
                        <input type="email" name="email" class="form-control rounded-3 py-2"
                               value="<?= htmlspecialchars($user['email']) ?>" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-medium d-block">Role</label>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="role" value="admin"
                                   <?= $user['role'] === 'admin' ? 'checked' : '' ?>>
                            <label class="form-check-label">Admin</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="role" value="customer"
                                   <?= $user['role'] === 'customer' ? 'checked' : '' ?>>
                            <label class="form-check-label">User</label>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-medium">Status</label>
                        <select name="status" class="form-control rounded-3 py-2" required>
                            <option value="aktif" <?= $user['status'] === 'aktif' ? 'selected' : '' ?>>Active</option>
                            <option value="nonaktif" <?= $user['status'] === 'nonaktif' ? 'selected' : '' ?>>Inactive</option>
                        </select>
                    </div>

                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary rounded-3 px-5 py-2">
                            <i class="bi bi-pencil-square"></i> Update User
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>


<?php require_once __DIR__ . '/includes/footer.php'; ?>
