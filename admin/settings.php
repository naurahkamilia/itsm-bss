<?php
/**
 * Admin - Settings
 */

define('APP_ACCESS', true);
require_once '../config/config.php';
require_once '../config/database.php';

Security::requireAdmin();

$currentPage = 'settings';
$pageTitle = 'Pengaturan - Admin Panel';

include 'includes/header.php';
?>

<div class="container-fluid py-4">
    <h2 class="mb-4">Pengaturan Toko</h2>

    <div class="row g-4">
        <!-- General Settings -->
        <div class="col-lg-6">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">
                        <i class="bi bi-gear me-2"></i>
                        Pengaturan Umum
                    </h5>
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <th width="200">Nama Toko</th>
                            <td><?php echo APP_NAME; ?></td>
                        </tr>
                        <tr>
                            <th>Versi</th>
                            <td><?php echo APP_VERSION; ?></td>
                        </tr>
                        <tr>
                            <th>Environment</th>
                            <td>
                                <span class="badge <?php echo APP_ENV === 'production' ? 'bg-success' : 'bg-warning'; ?>">
                                    <?php echo strtoupper(APP_ENV); ?>
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <th>Base URL</th>
                            <td><code><?php echo BASE_URL; ?></code></td>
                        </tr>
                        <tr>
                            <th>Admin URL</th>
                            <td><code><?php echo ADMIN_URL; ?></code></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <!-- WhatsApp Settings -->
        <div class="col-lg-6">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">
                        <i class="bi bi-whatsapp me-2"></i>
                        WhatsApp
                    </h5>
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <th width="200">Nomor WhatsApp</th>
                            <td>
                                <code>+<?php echo WHATSAPP_NUMBER; ?></code>
                                <a href="https://wa.me/<?php echo WHATSAPP_NUMBER; ?>" 
                                   class="btn btn-sm btn-success ms-2"
                                   target="_blank">
                                    <i class="bi bi-whatsapp me-1"></i> Test
                                </a>
                            </td>
                        </tr>
                    </table>
                    
                    <div class="alert alert-info mb-0">
                        <small>
                            <i class="bi bi-info-circle me-1"></i>
                            Untuk mengubah nomor WhatsApp, edit file <code>config/config.php</code>
                        </small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Database Settings -->
        <div class="col-lg-6">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">
                        <i class="bi bi-database me-2"></i>
                        Database
                    </h5>
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <th width="200">Host</th>
                            <td><code><?php echo DB_HOST; ?></code></td>
                        </tr>
                        <tr>
                            <th>Database Name</th>
                            <td><code><?php echo DB_NAME; ?></code></td>
                        </tr>
                        <tr>
                            <th>Username</th>
                            <td><code><?php echo DB_USER; ?></code></td>
                        </tr>
                        <tr>
                            <th>Charset</th>
                            <td><code><?php echo DB_CHARSET; ?></code></td>
                        </tr>
                    </table>
                    
                    <a href="http://localhost/phpmyadmin" 
                       class="btn btn-outline-primary btn-sm"
                       target="_blank">
                        <i class="bi bi-database me-1"></i>
                        Buka phpMyAdmin
                    </a>
                </div>
            </div>
        </div>

        <!-- System Info -->
        <div class="col-lg-6">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">
                        <i class="bi bi-info-circle me-2"></i>
                        System Info
                    </h5>
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <th width="200">PHP Version</th>
                            <td><?php echo phpversion(); ?></td>
                        </tr>
                        <tr>
                            <th>Server Software</th>
                            <td><?php echo $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown'; ?></td>
                        </tr>
                        <tr>
                            <th>Max Upload</th>
                            <td><?php echo ini_get('upload_max_filesize'); ?></td>
                        </tr>
                        <tr>
                            <th>Memory Limit</th>
                            <td><?php echo ini_get('memory_limit'); ?></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <!-- Configuration Help -->
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">
                        <i class="bi bi-question-circle me-2"></i>
                        Cara Mengubah Konfigurasi
                    </h5>
                </div>
                <div class="card-body">
                    <ol>
                        <li class="mb-2">
                            Buka file <code>config/config.php</code> menggunakan text editor
                        </li>
                        <li class="mb-2">
                            Edit nilai konstanta yang ingin diubah, contoh:
                            <pre class="bg-light p-3 rounded mt-2"><code>define('WHATSAPP_NUMBER', '6281234567890'); // Ganti dengan nomor Anda
define('BASE_URL', 'http://localhost/toko-online/'); // Sesuaikan folder</code></pre>
                        </li>
                        <li class="mb-2">
                            Save file dan refresh halaman
                        </li>
                    </ol>

                    <div class="alert alert-warning">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        <strong>Penting:</strong> Pastikan <code>BASE_URL</code> sesuai dengan lokasi folder Anda di htdocs.
                        Jika salah, aplikasi tidak akan berfungsi dengan baik.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
