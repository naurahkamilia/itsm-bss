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

<div class="container-fluid py-5">
    <h2 class="mb-4 fw-semibold text-center">Add Application</h2>

    <?php if (!empty($error)): ?>
        <div class="alert alert-danger shadow-sm"><?= $error ?></div>
    <?php endif; ?>

    <form method="POST" class="card p-5 shadow-lg border-0 mx-auto" style="max-width: 600px;">
        <input type="hidden" name="ApkID" class="form-control">

        <div class="mb-4">
            <label class="form-label fw-medium">Application Name</label>
            <input type="text" name="NamaApk" class="form-control rounded-3 py-2" placeholder="Enter Application Name" required>
        </div>

        <div class="text-end">
            <a href="aplikasi.php" class="btn btn-secondary rounded-3 px-4 py-2 shadow-sm me-2">
                Cancel
            </a>
            <button class="btn btn-primary rounded-3 px-5 py-2 shadow-sm" type="submit">
                Save
            </button>
        </div>
    </form>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
