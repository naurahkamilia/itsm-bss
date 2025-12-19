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

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Edit Hardware</h2>
        <a href="hardware-list.php" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Back
        </a>
    </div>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>

    <div class="card shadow-sm">
        <div class="card-body">
            <form method="POST">
                <input type="hidden" name="HwID" value="<?= htmlspecialchars($hardwares['HwID']) ?>">

                <div class="mb-3">
                    <label class="form-label">Hardware Name</label>
                    <input type="text" class="form-control" name="NamaHw"
                        value="<?= htmlspecialchars($hardwares['NamaHw']) ?>" required>
                </div>

                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-pencil-square"></i> Update
                </button>
            </form>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
