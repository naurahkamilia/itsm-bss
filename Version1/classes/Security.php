<?php
/**
 * Security Helper Class
 * Handles CSRF, XSS, input validation, etc.
 */

defined('APP_ACCESS') or die('Direct access not permitted');

class Security {
    
    /**
     * Generate CSRF Token
     */
    public static function generateCsrfToken() {
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }
    
    /**
     * Verify CSRF Token
     */
    public static function verifyCsrfToken($token) {
        if (!isset($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $token)) {
            return false;
        }
        return true;
    }
    
    /**
     * Clean input data (XSS Protection)
     */
    public static function clean($data) {
        if (is_array($data)) {
            return array_map([self::class, 'clean'], $data);
        }
        return htmlspecialchars(strip_tags(trim($data)), ENT_QUOTES, 'UTF-8');
    }
    
    /**
     * Validate email
     */
    public static function validateEmail($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL);
    }
    
    /**
     * Hash password
     */
    public static function hashPassword($password) {
        return password_hash($password, PASSWORD_ARGON2ID);
    }
    
    /**
     * Verify password
     */
    public static function verifyPassword($password, $hash) {
        return password_verify($password, $hash);
    }
    
    /**
     * Generate random string
     */
    public static function generateRandomString($length = 32) {
        return bin2hex(random_bytes($length / 2));
    }
    
    /**
     * Sanitize filename
     */
    public static function sanitizeFilename($filename) {
        $filename = preg_replace('/[^a-zA-Z0-9._-]/', '', $filename);
        return substr($filename, 0, 255);
    }
    
    /**
     * Check if user is logged in
     */
    public static function isLoggedIn() {
        return isset($_SESSION['user_id']) && isset($_SESSION['user_role']);
    }
    
    /**
     * Check if user is admin
     */
    public static function isAdmin() {
        return self::isLoggedIn() && $_SESSION['user_role'] === 'admin';
    }
    
    /**
     * Require login
     */
    public static function requireLogin() {
        if (!self::isLoggedIn()) {
            header('Location: ' . BASE_URL . 'login.php');
            exit;
        }
    }
    
    /**
     * Require admin
     */
    public static function requireAdmin() {
        if (!self::isAdmin()) {
            header('Location: ' . BASE_URL);
            exit;
        }
    }

    public static function requireUser() {
    if (!self::isLoggedIn() || $_SESSION['user_role'] !== 'customer') {
        header('Location: ' . BASE_URL);
        exit;
    }
}
    
    /**
     * Generate Captcha
     */
    public static function generateCaptcha() {
        $chars = 'ABCDEFGHJKLMNPQRSTUVWXYZabcdefghjkmnpqrstuvwxyz23456789';
        $captcha = '';
        for ($i = 0; $i < 6; $i++) {
            $captcha .= $chars[random_int(0, strlen($chars) - 1)];
        }
        $_SESSION['captcha'] = $captcha;
        return $captcha;
    }
    
    /**
     * Verify Captcha
     */
    public static function verifyCaptcha($input) {
        if (!isset($_SESSION['captcha'])) {
            return false;
        }
        return $_SESSION['captcha'] === $input;
    }
    
    /**
     * Rate limiting for login attempts
     */
    public static function checkLoginAttempts($email) {
        if (!isset($_SESSION['login_attempts'])) {
            $_SESSION['login_attempts'] = [];
        }
        
        $attempts = &$_SESSION['login_attempts'];
        $now = time();
        
        // Clean old attempts
        foreach ($attempts as $key => $attempt) {
            if ($now - $attempt['time'] > LOGIN_TIMEOUT) {
                unset($attempts[$key]);
            }
        }
        
        // Check if email has too many attempts
        $emailAttempts = array_filter($attempts, function($a) use ($email) {
            return $a['email'] === $email;
        });
        
        if (count($emailAttempts) >= MAX_LOGIN_ATTEMPTS) {
            return false;
        }
        
        return true;
    }
    
    /**
     * Record login attempt
     */
    public static function recordLoginAttempt($email) {
        if (!isset($_SESSION['login_attempts'])) {
            $_SESSION['login_attempts'] = [];
        }
        
        $_SESSION['login_attempts'][] = [
            'email' => $email,
            'time' => time()
        ];
    }
    
    /**
     * Clear login attempts for email
     */
    public static function clearLoginAttempts($email) {
        if (isset($_SESSION['login_attempts'])) {
            $_SESSION['login_attempts'] = array_filter(
                $_SESSION['login_attempts'],
                function($a) use ($email) {
                    return $a['email'] !== $email;
                }
            );
        }
    }
}
