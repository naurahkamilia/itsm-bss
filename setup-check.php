<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Setup Check - Toko Online Hijau</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body class="bg-light">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0">
                            <i class="bi bi-gear-fill me-2"></i>
                            Setup Verification - Toko Online Hijau
                        </h4>
                    </div>
                    <div class="card-body">
                        <?php
                        error_reporting(E_ALL);
                        ini_set('display_errors', 1);

                        $checks = [];
                        $allPassed = true;

                        // Check PHP version
                        $phpVersion = phpversion();
                        $phpOk = version_compare($phpVersion, '8.0.0', '>=');
                        $checks[] = [
                            'name' => 'PHP Version',
                            'status' => $phpOk,
                            'message' => "PHP $phpVersion " . ($phpOk ? '(OK)' : '(Need PHP 8.0+)'),
                            'required' => true
                        ];
                        if (!$phpOk) $allPassed = false;

                        // Check PDO extension
                        $pdoOk = extension_loaded('pdo') && extension_loaded('pdo_mysql');
                        $checks[] = [
                            'name' => 'PDO MySQL Extension',
                            'status' => $pdoOk,
                            'message' => $pdoOk ? 'Installed' : 'Not installed',
                            'required' => true
                        ];
                        if (!$pdoOk) $allPassed = false;

                        // Check mbstring extension
                        $mbstringOk = extension_loaded('mbstring');
                        $checks[] = [
                            'name' => 'Mbstring Extension',
                            'status' => $mbstringOk,
                            'message' => $mbstringOk ? 'Installed' : 'Not installed',
                            'required' => true
                        ];

                        // Check GD extension (for images)
                        $gdOk = extension_loaded('gd');
                        $checks[] = [
                            'name' => 'GD Extension',
                            'status' => $gdOk,
                            'message' => $gdOk ? 'Installed' : 'Not installed',
                            'required' => false
                        ];

                        // Check config file
                        $configFile = __DIR__ . '/config/config.php';
                        $configOk = file_exists($configFile);
                        $checks[] = [
                            'name' => 'Config File',
                            'status' => $configOk,
                            'message' => $configOk ? 'Found' : 'Not found',
                            'required' => true
                        ];
                        if (!$configOk) $allPassed = false;

                        // Check database connection
                        $dbOk = false;
                        $dbMessage = '';
                        if ($configOk) {
                            define('APP_ACCESS', true);
                            require_once $configFile;
                            require_once __DIR__ . '/config/database.php';
                            
                            try {
                                $db = Database::getInstance()->getConnection();
                                $dbOk = true;
                                $dbMessage = 'Connected to database: ' . DB_NAME;
                            } catch (Exception $e) {
                                $dbMessage = 'Failed: ' . $e->getMessage();
                                $allPassed = false;
                            }
                        } else {
                            $dbMessage = 'Config file not found';
                            $allPassed = false;
                        }
                        
                        $checks[] = [
                            'name' => 'Database Connection',
                            'status' => $dbOk,
                            'message' => $dbMessage,
                            'required' => true
                        ];

                        // Check uploads directory
                        $uploadsDir = __DIR__ . '/public/uploads/';
                        $uploadsDirOk = is_dir($uploadsDir);
                        $uploadsWritable = $uploadsDirOk && is_writable($uploadsDir);
                        $checks[] = [
                            'name' => 'Uploads Directory',
                            'status' => $uploadsDirOk,
                            'message' => $uploadsDirOk ? 
                                ($uploadsWritable ? 'Exists and writable' : 'Exists but not writable') : 
                                'Not found',
                            'required' => true
                        ];

                        // Check images directory
                        $imagesDir = __DIR__ . '/public/images/';
                        $imagesDirOk = is_dir($imagesDir);
                        $checks[] = [
                            'name' => 'Images Directory',
                            'status' => $imagesDirOk,
                            'message' => $imagesDirOk ? 'Exists' : 'Not found',
                            'required' => true
                        ];

                        // Check placeholder image
                        $placeholderOk = file_exists(__DIR__ . '/public/images/placeholder.jpg');
                        $checks[] = [
                            'name' => 'Placeholder Image',
                            'status' => $placeholderOk,
                            'message' => $placeholderOk ? 'Found' : 'Not found (run create-placeholder.php)',
                            'required' => false
                        ];

                        // Check .htaccess file
                        $htaccessOk = file_exists(__DIR__ . '/.htaccess');
                        $checks[] = [
                            'name' => '.htaccess File',
                            'status' => $htaccessOk,
                            'message' => $htaccessOk ? 'Found' : 'Not found',
                            'required' => false
                        ];

                        // Display results
                        ?>

                        <div class="mb-4">
                            <?php if ($allPassed): ?>
                                <div class="alert alert-success">
                                    <i class="bi bi-check-circle-fill me-2"></i>
                                    <strong>All critical checks passed!</strong> Your system is ready.
                                </div>
                            <?php else: ?>
                                <div class="alert alert-danger">
                                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                                    <strong>Some critical checks failed.</strong> Please fix the issues below.
                                </div>
                            <?php endif; ?>
                        </div>

                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th width="30">Status</th>
                                    <th>Check</th>
                                    <th>Result</th>
                                    <th width="100">Required</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($checks as $check): ?>
                                <tr>
                                    <td class="text-center">
                                        <?php if ($check['status']): ?>
                                            <i class="bi bi-check-circle-fill text-success fs-5"></i>
                                        <?php else: ?>
                                            <i class="bi bi-x-circle-fill text-danger fs-5"></i>
                                        <?php endif; ?>
                                    </td>
                                    <td><strong><?php echo $check['name']; ?></strong></td>
                                    <td><?php echo $check['message']; ?></td>
                                    <td>
                                        <?php if ($check['required']): ?>
                                            <span class="badge bg-danger">Required</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary">Optional</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>

                        <hr class="my-4">

                        <h5 class="mb-3">Quick Actions</h5>
                        <div class="row g-2">
                            <div class="col-md-6">
                                <a href="create-placeholder.php" class="btn btn-outline-primary w-100">
                                    <i class="bi bi-image me-2"></i>
                                    Create Placeholder Image
                                </a>
                            </div>
                            <div class="col-md-6">
                                <a href="index.php" class="btn btn-outline-success w-100">
                                    <i class="bi bi-house-door me-2"></i>
                                    Go to Homepage
                                </a>
                            </div>
                            <div class="col-md-6">
                                <a href="login.php" class="btn btn-outline-info w-100">
                                    <i class="bi bi-box-arrow-in-right me-2"></i>
                                    Go to Login
                                </a>
                            </div>
                            <div class="col-md-6">
                                <a href="<?php echo defined('BASE_URL') ? BASE_URL : './'; ?>INSTALL.md" 
                                   class="btn btn-outline-warning w-100"
                                   target="_blank">
                                    <i class="bi bi-book me-2"></i>
                                    View Installation Guide
                                </a>
                            </div>
                        </div>

                        <?php if (defined('BASE_URL')): ?>
                        <div class="alert alert-info mt-4 mb-0">
                            <strong><i class="bi bi-info-circle me-2"></i>Configuration:</strong><br>
                            Base URL: <code><?php echo BASE_URL; ?></code><br>
                            Database: <code><?php echo DB_NAME; ?></code><br>
                            WhatsApp: <code>+<?php echo WHATSAPP_NUMBER; ?></code>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- System Info -->
                <div class="card shadow-sm mt-3">
                    <div class="card-header bg-secondary text-white">
                        <h5 class="mb-0">
                            <i class="bi bi-info-circle me-2"></i>
                            System Information
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <p class="mb-2"><strong>PHP Version:</strong> <?php echo phpversion(); ?></p>
                                <p class="mb-2"><strong>Server Software:</strong> <?php echo $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown'; ?></p>
                                <p class="mb-2"><strong>Document Root:</strong> <code><?php echo $_SERVER['DOCUMENT_ROOT'] ?? 'Unknown'; ?></code></p>
                            </div>
                            <div class="col-md-6">
                                <p class="mb-2"><strong>Max Upload Size:</strong> <?php echo ini_get('upload_max_filesize'); ?></p>
                                <p class="mb-2"><strong>Max Post Size:</strong> <?php echo ini_get('post_max_size'); ?></p>
                                <p class="mb-2"><strong>Memory Limit:</strong> <?php echo ini_get('memory_limit'); ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
