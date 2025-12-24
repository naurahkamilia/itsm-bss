<?php
define('APP_ACCESS', true);
require_once 'config/config.php';
require_once 'config/database.php';
require_once 'models/User.php';

$db = Database::getInstance()->getConnection();

$email = $_GET['email'] ?? '';
$token = $_GET['token'] ?? '';

if (!$email || !$token) {
    die('Link tidak valid');
}

// ambil token
$stmt = $db->prepare(
    "SELECT * FROM password_resets 
     WHERE email=? AND expired_at > NOW()"
);
$stmt->execute([$email]);
$data = $stmt->fetch();

if (!$data || !password_verify($token, $data['token'])) {
    die('Link reset tidak valid atau sudah kadaluarsa');
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $password = $_POST['password'] ?? '';
    $confirm  = $_POST['password_confirm'] ?? '';

    if (strlen($password) < 8) {
        $error = 'Password minimal 8 karakter';
    }
    elseif ($password !== $confirm) {
        $error = 'Konfirmasi password tidak cocok';
    }
    else {
        // update password
        $hash = password_hash($password, PASSWORD_DEFAULT);

        $db->prepare(
            "UPDATE users SET password=? WHERE email=?"
        )->execute([$hash, $email]);

        // hapus token
        $db->prepare(
            "DELETE FROM password_resets WHERE email=?"
        )->execute([$email]);

        $success = 'Password berhasil diubah. Silakan login.';
    }
}

include 'includes/header.php';
?>
<main class="py-5" style="background-color:#f8f9fa; min-height:100vh;">
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">

            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-body p-4">

                    <div class="text-center mb-4">
                        <i class="bi bi-key-fill fs-2 text-dark mb-2"></i>
                        <h4 class="mb-1" style="color:#212529;">Reset Password</h4>
                        <p class="text-muted small mb-0">Enter your new password</p>
                    </div>

                    <!-- Error -->
                    <?php if ($error): ?>
                    <div class="alert alert-danger alert-dismissible fade show">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>
                        <?= htmlspecialchars($error) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    <?php endif; ?>

                    <!-- Success -->
                    <?php if ($success): ?>
                    <div class="alert alert-success">
                        <i class="bi bi-check-circle-fill me-2"></i>
                        <?= htmlspecialchars($success) ?><br>
                        <a href="index.php" class="text-decoration-none">Login</a>
                    </div>
                    <?php else: ?>

                    <form method="POST">

                        <div class="mb-3">
                            <label class="form-label" style="color:#212529;">New Password</label>
                            <input type="password" name="password" class="form-control" placeholder="Enter New Password" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label" style="color:#212529;">Confirm Password</label>
                            <input type="password" name="password_confirm" class="form-control" placeholder="Confirm New Password" required>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-dark rounded-3 py-2">
                                Save New Password
                            </button>
                        </div>
                    </form>
                    <?php endif; ?>

                    <div class="text-center mt-3">
                        <a href="index.php" class="text-decoration-none text-muted">
                            <u><small>Back to Login</small></a></u>
                    </div>

                </div>
            </div>

        </div>
    </div>
</div>
</main>
