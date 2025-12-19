<?php
define('APP_ACCESS', true);
require_once '../config/config.php';
require_once '../config/database.php';
require_once '../models/Aplikasi.php';

Security::requireAdmin();

$pageTitle = "Edit Application Data";
$currentPage = "aplikasi";

$aplikasiModel = new Aplikasi();
$error = '';
$success = '';

// Get application data
$id = isset($_GET['ApkID']) ? (int)$_GET['ApkID'] : 0;
$aplikasi = $aplikasiModel->getById($id);

if (!$aplikasi) {
    header('Location: aplikasi.php?error=notfound');
    exit;
}

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $apkID   = trim($_POST['ApkID']);
    $namaApk = trim($_POST['NamaApk']);

    if ($namaApk === '') {
        $error = "All fields are required!";
    } else {
        $data = [
            'NamaApk' => $namaApk,
        ];

        if ($aplikasiModel->update($apkID, $data)) {
            header("Location: aplikasi.php?updated=1");
            exit;
        } else {
            $error = "Failed to update application data!";
        }
    }
}

require_once __DIR__ . '/includes/header.php';
?>

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Edit Application</h2>
        <a href="aplikasi.php" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Back
        </a>
    </div>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>

    <div class="card shadow-sm">
        <div class="card-body">
            <form method="POST">
                <input type="hidden" name="ApkID" value="<?= htmlspecialchars($aplikasi['ApkID']) ?>">

                <div class="mb-3">
                    <label class="form-label">Application Name</label>
                    <input type="text" class="form-control" name="NamaApk"
                        value="<?= htmlspecialchars($aplikasi['NamaApk']) ?>" required>
                </div>

                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-pencil-square"></i> Update
                </button>
            </form>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
