<?php
/**
 * Script to create required folders
 * Run this once: http://localhost/toko-online/create-folders.php
 */

$folders = [
    __DIR__ . '/public/uploads',
    __DIR__ . '/public/uploads/products',
    __DIR__ . '/public/images',
    __DIR__ . '/public/images/products',
    __DIR__ . '/public/css',
    __DIR__ . '/public/js',
];

echo "<!DOCTYPE html>
<html lang='id'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Create Folders - Toko Online Hijau</title>
    <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css' rel='stylesheet'>
</head>
<body class='bg-light py-5'>
    <div class='container'>
        <div class='row justify-content-center'>
            <div class='col-lg-8'>
                <div class='card shadow'>
                    <div class='card-header bg-primary text-white'>
                        <h4 class='mb-0'>📁 Create Required Folders</h4>
                    </div>
                    <div class='card-body'>";

$created = 0;
$existing = 0;
$errors = 0;

echo "<ul class='list-group list-group-flush'>";

foreach ($folders as $folder) {
    $relativePath = str_replace(__DIR__ . '/', '', $folder);
    
    if (file_exists($folder)) {
        echo "<li class='list-group-item'>
                <i class='bi bi-check-circle-fill text-success'></i> 
                <strong>$relativePath</strong> 
                <span class='badge bg-info'>Already Exists</span>
              </li>";
        $existing++;
    } else {
        if (mkdir($folder, 0755, true)) {
            echo "<li class='list-group-item'>
                    <i class='bi bi-check-circle-fill text-success'></i> 
                    <strong>$relativePath</strong> 
                    <span class='badge bg-success'>Created</span>
                  </li>";
            $created++;
        } else {
            echo "<li class='list-group-item'>
                    <i class='bi bi-x-circle-fill text-danger'></i> 
                    <strong>$relativePath</strong> 
                    <span class='badge bg-danger'>Failed</span>
                  </li>";
            $errors++;
        }
    }
}

echo "</ul>";

echo "<div class='mt-4'>";

if ($errors === 0) {
    echo "<div class='alert alert-success'>
            <i class='bi bi-check-circle me-2'></i>
            <strong>Success!</strong> All folders are ready.
          </div>";
} else {
    echo "<div class='alert alert-danger'>
            <i class='bi bi-exclamation-triangle me-2'></i>
            <strong>Error!</strong> Some folders failed to create. Check permissions.
          </div>";
}

echo "<div class='row text-center'>
        <div class='col-4'>
            <div class='card bg-success text-white'>
                <div class='card-body'>
                    <h2 class='mb-0'>$created</h2>
                    <small>Created</small>
                </div>
            </div>
        </div>
        <div class='col-4'>
            <div class='card bg-info text-white'>
                <div class='card-body'>
                    <h2 class='mb-0'>$existing</h2>
                    <small>Already Exist</small>
                </div>
            </div>
        </div>
        <div class='col-4'>
            <div class='card bg-" . ($errors > 0 ? 'danger' : 'secondary') . " text-white'>
                <div class='card-body'>
                    <h2 class='mb-0'>$errors</h2>
                    <small>Errors</small>
                </div>
            </div>
        </div>
      </div>";

echo "</div>";

echo "<div class='mt-4'>
        <a href='setup-check.php' class='btn btn-primary'>
            <i class='bi bi-arrow-right me-2'></i>
            Next: Run Setup Check
        </a>
        <a href='index.php' class='btn btn-outline-secondary ms-2'>
            Go to Homepage
        </a>
      </div>";

echo "      </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>";
?>
