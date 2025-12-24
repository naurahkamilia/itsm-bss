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
$doneReq = $reqModel->countByStatusnNik('Selesai', $_SESSION['user_id']);
$processReqs = $reqModel->countByStatusnNik('Antrian', $_SESSION['user_id']);
$totalReqs = $reqModel->countByID($_SESSION['user_id']);

$pageTitle = 'Dashboard - User';
$currentPage = 'dashboard';

include 'includes/header.php';
?>

<div class="container-fluid py-4">

    <!-- HEADER -->
    <div class="mb-4">
        <h3 class="fw-semibold mb-1">
            Welcome back, <?= htmlspecialchars($_SESSION['user_name']); ?>
        </h3>
        <p class="text-muted small mb-0">
            Here’s a quick overview of your requests
        </p>
    </div>

<div class="row g-4 mb-5">

 <!-- IN PROGRESS -->
     <div class="col-12 col-md-6 col-lg">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <i class="bi bi-hourglass-split text-warning fs-2"></i>
                    <div class="ms-3">
                        <h6 class="text-muted mb-1">In Progress</h6>
                        <h3 class="mb-0"><?= $processReqs; ?></h3>
                    </div>
                </div>
            </div>
             <div class="card-footer bg-transparent border-0 text-end border-top pt-2">
                <a href="lihatRequest.php?StatusReq=Antrian" class="small text-decoration-none">
                    View All <i class="bi bi-arrow-right ms-1"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- FINISHED -->
    <div class="col-12 col-md-6 col-lg">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex align-items-center">
                   <i class="bi bi-check-circle text-success fs-2"></i>
                    <div class="ms-3">
                        <h6 class="text-muted mb-1">Finished</h6>
                        <h3 class="mb-0"><?= $doneReq; ?></h3>
                    </div>
                </div>
            </div>
             <div class="card-footer bg-transparent border-0 text-end border-top pt-2">
                <a href="lihatRequest.php?StatusReq=Selesai" class="small text-decoration-none">
                    View All <i class="bi bi-arrow-right ms-1"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- TOTAL -->
    <div class="col-12 col-md-6 col-lg">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <i class="bi bi-collection text-primary fs-2"></i>
                    <div class="ms-3">
                        <h6 class="text-muted mb-1">Total Request</h6>
                        <h3 class="mb-0"><?= $totalReqs; ?></h3>
                    </div>
                </div>
            </div>
            <div class="card-footer bg-transparent border-0 text-end border-top pt-2">
                <a href="lihatRequest.php" class="small text-decoration-none">
                    View All <i class="bi bi-arrow-right ms-1"></i>
                </a>
            </div>
        </div>
    </div>

</div>

<div class="user-empty text-center">
    <div class="empty-icon mb-3">
       <i class="bi bi-send-plus fs-1"></i>
    </div>

    <h5 class="fw-semibold mb-1">No Requests Found</h5>
    <p class="text-muted small mb-4">
        You haven’t created any support requests yet.
    </p>

    <a href="create-request.php?action=add" class="btn btn-primary btn-sm px-3">
        <i class="bi bi-plus-circle"></i>
        Create Request
    </a>
</div>

<?php include 'includes/footer.php'; ?>
