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
    if ($_SESSION['user_role'] === 'admin') {
        header('Location: ' . BASE_URL . 'admin/');
    } elseif ($_SESSION['user_role'] === 'customer') {
        header('Location: ' . BASE_URL . 'user/');
    } else {
        session_destroy();
        header('Location: ' . BASE_URL . 'login.php');
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
        $error = 'Captcha code is invalid.';
        Security::generateCaptcha(); // Generate new captcha
    }
    // Check rate limiting
    elseif (!Security::checkLoginAttempts($email)) {
        $error = 'Too many login attempts. Please try again in ' . (LOGIN_TIMEOUT / 60) . ' minutes.';
    }
    // Validate input
    elseif (empty($email) || empty($password)) {
        $error = 'Email and password are required.';
        Security::recordLoginAttempt($email);
    }
    // Attempt login
    else {
        $userModel = new User();
        $user = $userModel->verifyLogin($email, $password);
        
        if ($user) {
            Security::clearLoginAttempts($email);
                
            session_regenerate_id(true);
            $_SESSION = [];

            $_SESSION['user_id'] = $user['NIK'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_role'] = $user['role'];
            $_SESSION['login_time'] = time();
            
            // Regenerate session ID
            session_regenerate_id(true);

            if($user['role'] === 'admin'){
                $_SESSION['success_message'] = 'Welcome, '. $user['name'] . '!';
                header('Location: '.BASE_URL.'admin/');
                exit;
            }elseif($user['role'] === 'customer'){
                $_SESSION['success_message'] = 'Welcome, ' . $user['name'] . '!';
                header('Location: ' . BASE_URL . 'user/');
                exit;
            }else{
                $error = 'Invalid role.';
                Security::recordLoginAttempt($email);
            }
        }else{
            $error = 'Incorrect email or password.';
            Security::recordLoginAttempt($email);
        }
        Security::generateCaptcha();
    }
}

include 'includes/header.php';
?>

<main class="py-5" style="background-color:#f8f9fa; min-height:100vh;">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                <!-- Login Card -->
                <div class="card shadow-sm border-0" style="border-radius:12px;">
                    <div class="card-body p-5">
                        <!-- Header -->
                        <div class="text-center mb-4">
                            <div class="d-inline-flex align-items-center justify-content-center mb-3"
                                 style="width: 100px; height: 100px; border-radius: 50%; background-color: #89919cff;">
                                <i class="bi bi-lock-fill fs-1 text-white"></i>
                            </div>
                            <h2 class="mb-2" style="color:#000;">Login</h2>
                            <p class="text-muted" style="color:#555;">Enter your credentials to access your account</p>
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
                                <label for="email" class="form-label" style="color:#000;">Email</label>
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
                                           required
                                           style="border-radius:6px;">
                                </div>
                            </div>

                            <!-- Password -->
                            <div class="mb-3">
                                <label for="password" class="form-label" style="color:#000;">Password</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="bi bi-key"></i>
                                    </span>
                                    <input type="password" 
                                           class="form-control" 
                                           id="password" 
                                           name="password" 
                                           placeholder="••••••••"
                                           required
                                           style="border-radius:6px;">
                                    <button class="btn btn-outline-secondary" 
                                            type="button"
                                            onclick="togglePassword('password', 'toggleIcon')">
                                        <i class="bi bi-eye" id="toggleIcon"></i>
                                    </button>
                                </div>
                            </div>

                            <!-- Captcha -->
                            <div class="mb-4">
                                <label for="captcha" class="form-label" style="color:#000;">Security Code (Captcha)</label>
                                
                                <div class="d-flex gap-2 mb-2">
                                    <div class="captcha-box flex-grow-1 text-center" id="captcha-display" style="background:#e9ecef; border-radius:6px; padding:0.5rem; font-weight:600; letter-spacing:3px;">
                                        <?php echo $_SESSION['captcha']; ?>
                                    </div>
                                    <button type="button" class="btn btn-outline-secondary" onclick="refreshCaptcha()" title="Refresh Captcha">
                                        <i class="bi bi-arrow-clockwise"></i>
                                    </button>
                                </div>
                                
                                <input type="text" 
                                       class="form-control" 
                                       id="captcha" 
                                       name="captcha" 
                                       placeholder="Enter the code above"
                                       required
                                       autocomplete="off"
                                       style="border-radius:6px;">
                            </div>

                            <!-- Submit Button -->
                            <div class="d-grid mb-3">
                                <button type="submit" class="btn btn-dark btn-lg" style="border-radius:6px;">
                                    <i class="bi bi-box-arrow-in-right me-2"></i>
                                    Login
                                </button>
                            </div>

                            <!-- Reset Password Link -->
                            <div class="text-center">
                                <a href="forget-password.php" class="text-decoration-none" style="color:#555;">
                                    <u><small>Forgot password? Reset here</small></u>
                                </a>
                            </div>
                        </form>

                        <!-- Workflow -->
                        <div class="panel mt-3">
                            <h6 class="panel-title mb-3" style="color:#000;">
                                <i class="bi bi-info-circle me-1"></i>
                                Workflow
                            </h6>

                            <div class="workflow-thumb d-flex justify-content-center">
                                <img src="<?= BASE_URL ?>public/images/workflow.jpg"
                                    class="popup-image rounded"
                                    style="max-width:380px; cursor:zoom-in; display:block;"
                                    alt="Workflow">
                            </div>
                        </div>

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

document.addEventListener('DOMContentLoaded', function () {

  document.querySelectorAll('.popup-image').forEach(img => {
    img.addEventListener('click', function () {

      if (document.getElementById('PURE_IMAGE_POPUP')) return;
      const overlay = document.createElement('div');
      overlay.id = 'PURE_IMAGE_POPUP';
      overlay.style.position = 'fixed';
      overlay.style.top = 0;
      overlay.style.left = 0;
      overlay.style.width = '100vw';
      overlay.style.height = '100vh';
      overlay.style.background = 'rgba(0,0,0,0.85)';
      overlay.style.display = 'flex';
      overlay.style.justifyContent = 'center';
      overlay.style.alignItems = 'center';
      overlay.style.zIndex = 9999999;
      overlay.style.cursor = 'zoom-out';

      const popupImg = document.createElement('img');
      popupImg.src = this.src;
      popupImg.style.maxWidth = '100vw';
      popupImg.style.maxHeight = '100vh';
      popupImg.style.boxShadow = '0 0 40px rgba(0,0,0,0.8)';
      popupImg.style.transform = 'scale(0.8)';
      popupImg.style.opacity = '0';
      popupImg.style.transition = 'all 0.25s ease';

      overlay.appendChild(popupImg);
      document.body.appendChild(overlay);

      requestAnimationFrame(() => {
        popupImg.style.transform = 'scale(1.3)';
        popupImg.style.opacity = '1';
      });

      overlay.addEventListener('click', () => overlay.remove());

      document.addEventListener('keydown', function esc(e) {
        if (e.key === 'Escape') {
          overlay.remove();
          document.removeEventListener('keydown', esc);
        }
      });

    });
  });

});

</script>

<?php include 'includes/footer.php'; ?>
