<?php
/**
 * Login Page
 */

define('APP_ACCESS', true);
require_once 'config/config.php';
require_once 'config/database.php';
require_once 'models/User.php';

// Redirect if already logged in
if (Security::isLoggedIn()) {
    if (Security::isAdmin()) {
        header('Location: ' . BASE_URL . 'admin/');
    } else {
        header('Location: ' . BASE_URL);
    }
    exit;
}

$pageTitle = 'Login - ' . APP_NAME;
$currentPage = 'login';
$error = '';

// Generate captcha if not exists
if (!isset($_SESSION['captcha'])) {
    Security::generateCaptcha();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = Security::clean($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $captcha = $_POST['captcha'] ?? '';
    $csrfToken = $_POST['csrf_token'] ?? '';
    
    // Verify CSRF token
    if (!Security::verifyCsrfToken($csrfToken)) {
        $error = 'Invalid request. Please try again.';
    }
    // Verify captcha
    elseif (!Security::verifyCaptcha($captcha)) {
        $error = 'Kode captcha tidak valid.';
        Security::generateCaptcha(); // Generate new captcha
    }
    // Check rate limiting
    elseif (!Security::checkLoginAttempts($email)) {
        $error = 'Terlalu banyak percobaan login. Silakan coba lagi dalam ' . (LOGIN_TIMEOUT / 60) . ' menit.';
    }
    // Validate input
    elseif (empty($email) || empty($password)) {
        $error = 'Email dan password harus diisi.';
        Security::recordLoginAttempt($email);
    }
    // Attempt login
    else {
        $userModel = new User();
        $user = $userModel->verifyLogin($email, $password);
        
        if ($user) {
            // Check if user is admin
            if ($user['role'] !== 'admin') {
                $error = 'Akses ditolak. Hanya admin yang dapat login.';
                Security::recordLoginAttempt($email);
            } else {
                // Clear login attempts
                Security::clearLoginAttempts($email);
                
                // Set session
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['user_role'] = $user['role'];
                $_SESSION['login_time'] = time();
                
                // Regenerate session ID
                session_regenerate_id(true);
                
                // Redirect to admin panel
                $_SESSION['success_message'] = 'Selamat datang, ' . $user['name'] . '!';
                header('Location: ' . BASE_URL . 'admin/');
                exit;
            }
        } else {
            $error = 'Email atau password salah.';
            Security::recordLoginAttempt($email);
        }
        
        // Generate new captcha on failed login
        Security::generateCaptcha();
    }
}

include 'includes/header.php';
?>

<main class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                <!-- Login Card -->
                <div class="card shadow-lg border-0">
                    <div class="card-body p-5">
                        <!-- Header -->
                        <div class="text-center mb-4">
                            <div class="bg-gradient-to-br p-4 rounded-circle d-inline-flex mb-3" 
                                 style="background: linear-gradient(135deg, var(--primary-green) 0%, var(--primary-blue) 100%);">
                                <i class="bi bi-lock-fill fs-1 text-white"></i>
                            </div>
                            <h2 class="mb-2">Login Admin</h2>
                            <p class="text-muted">Masukkan kredensial Anda untuk mengakses panel admin</p>
                        </div>

                        <!-- Error Message -->
                        <?php if ($error): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i>
                            <?php echo htmlspecialchars($error); ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                        <?php endif; ?>

                        <!-- Login Form -->
                        <form method="POST" action="">
                            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                            
                            <!-- Email -->
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="bi bi-envelope"></i>
                                    </span>
                                    <input type="email" 
                                           class="form-control" 
                                           id="email" 
                                           name="email" 
                                           placeholder="email@example.com"
                                           value="<?php echo htmlspecialchars($email ?? ''); ?>"
                                           required>
                                </div>
                            </div>

                            <!-- Password -->
                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="bi bi-key"></i>
                                    </span>
                                    <input type="password" 
                                           class="form-control" 
                                           id="password" 
                                           name="password" 
                                           placeholder="••••••••"
                                           required>
                                    <button class="btn btn-outline-secondary" 
                                            type="button"
                                            onclick="togglePassword('password', 'toggleIcon')">
                                        <i class="bi bi-eye" id="toggleIcon"></i>
                                    </button>
                                </div>
                            </div>

                            <!-- Captcha -->
                            <div class="mb-4">
                                <label for="captcha" class="form-label">Kode Keamanan (Captcha)</label>
                                
                                <!-- Captcha Display -->
                                <div class="d-flex gap-2 mb-2">
                                    <div class="captcha-box flex-grow-1" id="captcha-display">
                                        <?php echo $_SESSION['captcha']; ?>
                                    </div>
                                    <button type="button" 
                                            class="btn btn-outline-secondary"
                                            onclick="refreshCaptcha()"
                                            title="Refresh Captcha">
                                        <i class="bi bi-arrow-clockwise"></i>
                                    </button>
                                </div>
                                
                                <!-- Captcha Input -->
                                <input type="text" 
                                       class="form-control" 
                                       id="captcha" 
                                       name="captcha" 
                                       placeholder="Masukkan kode di atas"
                                       required
                                       autocomplete="off">
                            </div>

                            <!-- Submit Button -->
                            <div class="d-grid mb-3">
                                <button type="submit" class="btn btn-gradient btn-lg">
                                    <i class="bi bi-box-arrow-in-right me-2"></i>
                                    Login
                                </button>
                            </div>

                            <!-- Reset Password Link -->
                            <div class="text-center">
                                <a href="reset-password.php" class="text-decoration-none">
                                    <small>Lupa password? Reset di sini</small>
                                </a>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Demo Credentials -->
                <div class="card mt-3 border-primary">
                    <div class="card-body">
                        <h6 class="card-title mb-2">
                            <i class="bi bi-info-circle me-1"></i>
                            Demo Credentials
                        </h6>
                        <small class="text-muted d-block">Email: <strong>admin@tokoonline.com</strong></small>
                        <small class="text-muted d-block">Password: <strong>admin123</strong></small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<script>
// Refresh captcha via AJAX
function refreshCaptcha() {
    fetch('<?php echo BASE_URL; ?>ajax/refresh-captcha.php')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('captcha-display').textContent = data.captcha;
                document.getElementById('captcha').value = '';
            }
        })
        .catch(error => {
            console.error('Error refreshing captcha:', error);
            location.reload();
        });
}
</script>

<?php include 'includes/footer.php'; ?>
