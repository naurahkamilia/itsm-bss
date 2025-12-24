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
<div class="container-fluid py-5">
    <h2 class="mb-4 fw-semibold text-center">Edit Application</h2>

    <?php if (!empty($error)): ?>
        <div class="alert alert-danger shadow-sm"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST" class="card p-5 shadow-lg border-0 mx-auto" style="max-width: 600px;">
        <input type="hidden" name="ApkID" value="<?= htmlspecialchars($aplikasi['ApkID']) ?>">

        <div class="mb-4">
            <label class="form-label fw-medium">Application Name</label>
            <input type="text" name="NamaApk" class="form-control rounded-3 py-2" 
                   placeholder="Enter Application Name" 
                   value="<?= htmlspecialchars($aplikasi['NamaApk']) ?>" required>
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
