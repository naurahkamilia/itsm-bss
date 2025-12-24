<?php
session_start();
define('APP_ACCESS', true);

require_once '../config/config.php';
require_once '../config/database.php';
require_once '../models/User.php';
require_once '../models/Request.php';

// KHUSUS USER
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'customer') {
    header('Location: ' . BASE_URL . 'login.php');
    exit;
}
$reqModel = new Request();
$processReqs = $reqModel->countByStatusnNik('Antrian', $_SESSION['user_id']);
$totalReqs = $reqModel->countByID($_SESSION['user_id']);

$pageTitle = 'Dashboard - User';
$currentPage = 'dashboard';

include 'includes/header.php';
?>

<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-12">
            <h2>Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?>!</h2>
        </div>
    </div>

    <!-- Statistics Cards -->
<div class="row mb-4 g-4">

    <div class="col-md-6 col-lg-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center">
                <div class="flex-shrink-0">
                    <div class="bg-primary bg-opacity-10 p-3 rounded">
                        <i class="bi bi-box-seam text-primary fs-2"></i>
                    </div>
                </div>
                <div class="flex-grow-1 ms-3">
                    <h6 class="text-muted mb-1">Total Request</h6>
                    <h3 class="mb-0 fw-bold"><?php echo $totalReqs; ?></h3>
                </div>
            </div>
            <div class="card-footer bg-transparent border-0 text-end">
                <a href="lihatRequest.php" class="text-decoration-none small">
                    View All <i class="bi bi-arrow-right ms-1"></i>
                </a>
            </div>
        </div>
    </div>

    <div class="col-md-6 col-lg-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center">
                <div class="flex-shrink-0">
                    <div class="bg-success bg-opacity-10 p-3 rounded">
                        <i class="bi bi-check-circle text-success fs-2"></i>
                    </div>
                </div>
                <div class="flex-grow-1 ms-3">
                    <h6 class="text-muted mb-1">Processed</h6>
                    <h3 class="mb-0 fw-bold"><?php echo $processReqs; ?></h3>
                </div>
            </div>
            <div class="card-footer bg-transparent border-0 text-end">
                <a href="lihatRequest.php?StatusReq=Antrian" class="text-decoration-none small">
                    View All <i class="bi bi-arrow-right ms-1"></i>
                </a>
            </div>
        </div>
    </div>

</div> 

<div class="text-center py-5">
    <i class="bi bi-send-plus display-1 text-muted"></i>
    <p class="text-muted small mt-2">
        Please submit a new request to meet your needs.
    </p>
    <a href="create-request.php?action=add" class="btn btn-gradient">
        <i class="bi bi-plus-circle me-2"></i> Create Request
    </a>
</div>
<?php include 'includes/footer.php'; ?>
