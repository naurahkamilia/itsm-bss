<?php
define('APP_ACCESS', true);
require_once '../config/config.php';
require_once '../config/database.php';
require_once '../models/Hardware.php';

Security::requireAdmin();

$pageTitle = "Edit Hardware Data";
$currentPage = "hardware";

$hardwareModel = new Hardware();
$error = '';
$success = '';

// Get hardware data
$id = isset($_GET['HwID']) ? (int)$_GET['HwID'] : 0;
$hardwares = $hardwareModel->getById($id);

if (!$hardwares) {
    header('Location: hardware-list.php?error=notfound');
    exit;
}

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $HwID   = trim($_POST['HwID']);
    $namaHw = trim($_POST['NamaHw']);

    if ($namaHw === '') {
        $error = "All fields are required!";
    } else {
        $data = [
            'NamaHw' => $namaHw,
        ];

        if ($hardwareModel->update($HwID, $data)) {
            header("Location: hardware-list.php?updated=1");
            exit;
        } else {
            $error = "Failed to update hardware data!";
        }
    }
}

require_once __DIR__ . '/includes/header.php';
?>
<div class="container-fluid py-5">
    <h2 class="mb-4 fw-semibold text-center">Edit Hardware</h2>

    <?php if (!empty($error)): ?>
        <div class="alert alert-danger shadow-sm"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST" class="card p-5 shadow-lg border-0 mx-auto" style="max-width: 600px;">
        <input type="hidden" name="HwID" value="<?= htmlspecialchars($hardwares['HwID']) ?>">

        <div class="mb-4">
            <label class="form-label fw-medium">Hardware Name</label>
            <input type="text" name="NamaHw" class="form-control rounded-3 py-2" 
                   placeholder="Enter Hardware Name" 
                   value="<?= htmlspecialchars($hardwares['NamaHw']) ?>" required>
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
