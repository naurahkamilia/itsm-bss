<?php
define('APP_ACCESS', true);
require_once '../config/config.php';
require_once '../config/database.php';
require_once '../models/Hardware.php';
require_once '../helpers/slug.php';

Security::requireAdmin();

$pageTitle = "Add Infrastructure Data";
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

<div class="container-fluid py-5">
    <h2 class="mb-4 fw-semibold text-center">Add Infrastructure</h2>

    <?php if (!empty($error)): ?>
        <div class="alert alert-danger shadow-sm"><?= $error ?></div>
    <?php endif; ?>

    <form method="POST" class="card p-5 shadow-lg border-0 mx-auto" style="max-width: 600px;">
        <input type="hidden" name="HwID" class="form-control">

        <div class="mb-4">
            <label class="form-label fw-medium">Infrastructure Name</label>
            <input type="text" name="NamaHw" class="form-control rounded-3 py-2" placeholder="Enter Infrastructure Name" required>
        </div>

        <div class="text-end">
            <a href="hardware-list.php" class="btn btn-secondary rounded-3 px-4 py-2 shadow-sm me-2">
                Cancel
            </a>
            <button class="btn btn-primary rounded-3 px-5 py-2 shadow-sm" type="submit">
                Save
            </button>
        </div>
    </form>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
