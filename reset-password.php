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

<main class="py-5">
<div class="container">
<div class="row justify-content-center">
<div class="col-md-6 col-lg-5">

<div class="card shadow-lg border-0">
<div class="card-body p-5">

<h3 class="text-center mb-3">Reset Password</h3>

<?php if ($error): ?>
<div class="alert alert-danger"><?= $error ?></div>
<?php endif; ?>

<?php if ($success): ?>
<div class="alert alert-success">
    <?= $success ?><br>
    <a href="index.php">Login</a>
</div>
<?php else: ?>

<form method="POST">

<div class="mb-3">
    <label>Password Baru</label>
    <input type="password" name="password" class="form-control" required>
</div>

<div class="mb-3">
    <label>Konfirmasi Password</label>
    <input type="password" name="password_confirm" class="form-control" required>
</div>

<button class="btn btn-gradient w-100">
    Simpan Password Baru
</button>

</form>
<?php endif; ?>

</div>
</div>

</div>
</div>
</div>
</main>
