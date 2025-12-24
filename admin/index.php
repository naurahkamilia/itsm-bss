<?php
/**
 * Admin Dashboard
 */

define('APP_ACCESS', true);
require_once '../config/config.php';
require_once '../config/database.php';
require_once '../models/Request.php';
require_once '../models/Karyawan.php';
require_once '../models/User.php';

Security::requireAdmin();

$currentPage = 'dashboard';
$pageTitle = 'Dashboard - Admin Panel';

$reqModel = new Request();
$karyawanModel = new Karyawan();

$totalReqs = $reqModel->count([]);
$progressReq  = $reqModel->countByStatus('Antrian');
$doneReq      = $reqModel->countByStatus('Selesai');
$rejectReq      = $reqModel->countByStatus('Ditolak');

$todayReq     = $reqModel->countToday();

$recentReqs = $reqModel->listReq('Antrian', 5, 0);

include 'includes/header.php';
?>

<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-12">
            <h2>Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?>!</h2>
             <p class="page-subtitle">
                    Overview of today’s IT service requests
                </p>
        </div>
    </div>

<div class="row g-3 mb-5">

    <div class="col-12 col-md-6 col-lg">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="bg-primary bg-opacity-10 p-3 rounded">
                        <i class="bi bi-envelope-arrow-down-fill text-primary fs-2"></i>
                    </div>
                    <div class="ms-3">
                        <h6 class="text-muted mb-1">Request Today</h6>
                        <h3 class="mb-0"><?= $todayReq; ?></h3>
                    </div>
                </div>
            </div>
            <div class="card-footer bg-transparent border-0 text-end">
                <a href="request-list.php?tanggal_dari=<?= date('Y-m-d') ?>" class="small text-decoration-none">
                    View All <i class="bi bi-arrow-right ms-1"></i>
                </a>
            </div>
        </div>
    </div>

    <div class="col-12 col-md-6 col-lg">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="bg-danger bg-opacity-10 p-3 rounded">
                        <i class="bi bi-x-circle text-danger fs-2"></i>
                    </div>
                    <div class="ms-3">
                        <h6 class="text-muted mb-1">Rejected</h6>
                        <h3 class="mb-0"><?= $rejectReq; ?></h3>
                    </div>
                </div>
            </div>
            <div class="card-footer bg-transparent border-0 text-end">
                <a href="request-list.php?StatusReq=Ditolak" class="small text-decoration-none">
                    View All <i class="bi bi-arrow-right ms-1"></i>
                </a>
            </div>
        </div>
    </div>

    <div class="col-12 col-md-6 col-lg">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="bg-warning bg-opacity-10 p-3 rounded">
                        <i class="bi bi-hourglass-split text-warning fs-2"></i>
                    </div>
                    <div class="ms-3">
                        <h6 class="text-muted mb-1">In Progress</h6>
                        <h3 class="mb-0"><?= $progressReq; ?></h3>
                    </div>
                </div>
            </div>
            <div class="card-footer bg-transparent border-0 text-end">
                <a href="request-list.php?StatusReq=Antrian" class="small text-decoration-none">
                    View All <i class="bi bi-arrow-right ms-1"></i>
                </a>
            </div>
        </div>
    </div>

    <div class="col-12 col-md-6 col-lg">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="bg-success bg-opacity-10 p-3 rounded">
                        <i class="bi bi-check-circle text-success fs-2"></i>
                    </div>
                    <div class="ms-3">
                        <h6 class="text-muted mb-1">Finished</h6>
                        <h3 class="mb-0"><?= $doneReq; ?></h3>
                    </div>
                </div>
            </div>
            <div class="card-footer bg-transparent border-0 text-end">
                <a href="request-list.php?StatusReq=Selesai" class="small text-decoration-none">
                    View All <i class="bi bi-arrow-right ms-1"></i>
                </a>
            </div>
        </div>
    </div>

    <div class="col-12 col-md-6 col-lg">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="bg-primary bg-opacity-10 p-3 rounded">
                        <i class="bi bi-collection text-primary fs-2"></i>
                    </div>
                    <div class="ms-3">
                        <h6 class="text-muted mb-1">Total Request</h6>
                        <h3 class="mb-0"><?= $totalReqs; ?></h3>
                    </div>
                </div>
            </div>
            <div class="card-footer bg-transparent border-0 text-end">
                <a href="request-list.php" class="small text-decoration-none">
                    View All <i class="bi bi-arrow-right ms-1"></i>
                </a>
            </div>
        </div>
    </div>

</div>


    <!-- Recent Products -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="mb-0">
                        <i class="bi bi-clock-history me-2"></i>
                        Request In Progress
                    </h5>
                </div>
                <div class="card-body p-0">
                    <?php if (count($recentReqs) > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Tanggal Permintaan</th>
                                    <th>Nama Aplikasi / Hardware</th>
                                    <th>Prioritas</th>
                                    <th>Status</th>
                                    <th width="120">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recentReqs as $rq): ?>
                                <tr>
                                    <td>
                                        <div class="fw-semibold"><?php echo htmlspecialchars($rq['Tgl_request']); ?></div>
                                    </td>
                                    <td> 
                                        <?php echo htmlspecialchars($rq['NamaApk'] ?? $rq['NamaHw'] ?? '-', ENT_QUOTES, 'UTF-8'); ?>
                                    </td>
                                    <td>
                                        <div class="fw-semibold"><?php echo htmlspecialchars($rq['Prioritas']); ?></div>
                                    </td>
                                    <td>
                                    <?php if ($rq['StatusReq'] == 'Antrian'): ?>
                                    <span class="badge bg-warning text-dark">In Progress</span>
                                    <?php endif;?>
                                    </td>
                                    <td>
                                    <a href="finish-request.php?ReqID=<?= $rq['ReqID']; ?>" 
                                    class="btn btn-sm btn-outline-dark" 
                                    title="Mark Program as Completed">
                                        <i class="bi bi-pencil"></i>
                                    </a>

                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php else: ?>
                    <div class="text-center py-5">
                        <i class="bi bi-inbox display-1 text-muted"></i>
                        <h5 class="mt-3">No Progress Yet</h5>
                        <a href="request-list.php?StatusReq=Disetujui" class="btn btn-gradient">
                           <i class="bi bi-folder-plus"></i> Start Process
                        </a>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
