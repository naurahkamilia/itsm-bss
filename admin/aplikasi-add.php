<?php
define('APP_ACCESS', true);
require_once '../config/config.php';
require_once '../config/database.php';
require_once '../models/Aplikasi.php';
require_once '../helpers/slug.php';

Security::requireAdmin();

$pageTitle = "Add Application Data";
$currentPage = "aplikasi";

$aplikasiModel = new Aplikasi();

// If form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $apkID      = trim($_POST['ApkID']);
    $namaApk    = trim($_POST['NamaApk']);

    // REQUIRED VALIDATION
    if ($namaApk === '') {
        $error = "All fields are required!";
    } else {

        $aplikasiModel->create([
            'ApkID' => $apkID,
            'NamaApk' => $namaApk,
        ]);

        header("Location: aplikasi.php?success=1");
        exit;
    }
}

require_once __DIR__ . '/includes/header.php';
?>

<div class="container-fluid py-4">
    <h2 class="mb-4">Add Application</h2>

    <?php if (!empty($error)): ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>

    <form method="POST" class="card p-4 shadow-sm">
        <input type="hidden" name="ApkID" class="form-control">
        <div class="mb-3">
            <label class="form-label">Application Name</label>
            <input type="text" name="NamaApk" class="form-control" required>
        </div>

        <button class="btn btn-success">Save</button>
    </form>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
