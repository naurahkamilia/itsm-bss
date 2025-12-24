<?php
define('APP_ACCESS', true);
require_once 'config/config.php';
require_once 'config/database.php';
require_once 'models/User.php';
require_once __DIR__ . '/helpers/Mailers.php';

$db = Database::getInstance()->getConnection();

$error = '';
$success = '';

$userModel = new User();

if (!isset($_SESSION['captcha'])) {
    Security::generateCaptcha();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email     = Security::clean($_POST['email'] ?? '');
    $captcha   = $_POST['captcha'] ?? '';
    $csrfToken = $_POST['csrf_token'] ?? '';

    if (!Security::verifyCsrfToken($csrfToken)) {
        $error = 'Invalid request.';
    }
    elseif (!Security::verifyCaptcha($captcha)) {
        $error = 'Captcha code is incorrect.';
        Security::generateCaptcha(); 
    }
    elseif (empty($email)) {
        $error = 'Email is required.';
    }
    else {
        $user = $userModel->getByEmail($email);

        if (!$user) {
            $error = 'Email not found.';
        } 
        else {
            if (!Security::checkForgotPasswordAttempts($email)) {
                $success = 'A password reset link has been sent to your email.';
                sleep(1);
            } 
            else {
                $token   = bin2hex(random_bytes(32));
                $expired = date('Y-m-d H:i:s', strtotime('+30 minutes'));

                $db->prepare("DELETE FROM password_resets WHERE email=?")
                   ->execute([$email]);

                $db->prepare(
                    "INSERT INTO password_resets (email, token, expired_at)
                     VALUES (?, ?, ?)"
                )->execute([
                    $email,
                    password_hash($token, PASSWORD_DEFAULT),
                    $expired
                ]);

                $resetLink = BASE_URL . "reset-password.php?email=$email&token=$token";
                Mailer::sendResetPassword($email, $resetLink);

                $success = 'A password reset link has been sent to your email.';
            }
        }
    }
}

include 'includes/header.php';
?><main class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">

                <div class="card shadow-lg border-0">
                    <div class="card-body p-5">

                        <!-- Header -->
                        <div class="text-center mb-4">
                            <div class="d-inline-flex align-items-center justify-content-center mb-3"
                                style="width:100px;height:100px;border-radius:50%;
                                        background: #89919cff">
                                <i class="bi bi-envelope-paper fs-1 text-white"></i>
                            </div>
                            <h2 class="mb-2">Forgot Password</h2>
                            <p class="text-muted">Enter your registered email to receive a password reset link</p>
                        </div>

                        <!-- Error -->
                        <?php if ($error): ?>
                        <div class="alert alert-danger alert-dismissible fade show">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i>
                            <?= htmlspecialchars($error); ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                        <?php endif; ?>

                        <!-- Success -->
                        <?php if ($success): ?>
                        <div class="alert alert-success">
                            <i class="bi bi-check-circle-fill me-2"></i>
                            <?= htmlspecialchars($success); ?>
                        </div>
                        <?php endif; ?>

                        <?php if (!$success): ?>
                        <form method="POST">
                            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token']; ?>">

                            <!-- Email -->
                            <div class="mb-3">
                                <label class="form-label">Email</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                                    <input type="email" class="form-control" name="email"
                                        placeholder="email@example.com" required autofocus>
                                </div>
                            </div>

                            <!-- Captcha -->
                            <div class="mb-4">
                                <label class="form-label">Security Code</label>
                                <div class="d-flex gap-2 mb-2">
                                    <div class="captcha-box flex-grow-1" id="captcha-display">
                                        <?= $_SESSION['captcha'] ?? ''; ?>
                                    </div>
                                    <button type="button" class="btn btn-outline-secondary" onclick="refreshCaptcha()">
                                        <i class="bi bi-arrow-clockwise"></i>
                                    </button>
                                </div>
                                <input type="text" class="form-control" name="captcha"
                                    placeholder="Enter the code above" required autocomplete="off">
                            </div>

                            <div class="d-grid">
                                <button class="btn btn-dark btn-lg">
                                    <i class="bi bi-envelope-paper me-2"></i>
                                    Send Reset Link
                                </button>
                            </div>
                        </form>
                        <?php endif; ?>

                        <div class="text-center mt-3">
                            <a href="index.php" class="text-decoration-none" style="color:#555;">
                                <u><small>Back to Login</small></u>
                            </a>
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </div>
</main>
