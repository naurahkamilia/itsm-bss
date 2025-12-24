<?php
define('APP_ACCESS', true);
require_once '../config/config.php';
require_once '../config/database.php';
require_once '../models/Hardware.php';
require_once '../helpers/slug.php';

Security::requireAdmin();

$pageTitle = "Add Hardware Data";
$currentPage = "hardware";

$hardwareModel = new Hardware();

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $hwID   = trim($_POST['HwID']);
    $namaHw = trim($_POST['NamaHw']);

    // REQUIRED VALIDATION
    if ($namaHw === '') {
        $error = "All fields are required!";
    } else {

        $hardwareModel->create([
            'HwID'   => $hwID,
            'NamaHw' => $namaHw
        ]);

        header("Location: hardware-list.php?success=1");
        exit;
    }
}

require_once __DIR__ . '/includes/header.php';
?>

<div class="container-fluid py-4">
    <h2 class="mb-4">Add Hardware</h2>

    <?php if (!empty($error)): ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>

    <form method="POST" class="card p-4 shadow-sm">
        <input type="hidden" name="HwID" class="form-control">
        <div class="mb-3">
            <label class="form-label">Hardware Name</label>
            <input type="text" name="NamaHw" class="form-control" required>
        </div>

        <button class="btn btn-success">Save</button>
    </form>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
