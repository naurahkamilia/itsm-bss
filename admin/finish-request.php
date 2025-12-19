<?php
session_start();
define('APP_ACCESS', true);
require_once '../config/config.php';
require_once '../config/database.php';
require_once '../models/Request.php';
require_once '../models/CompReqs.php';
require_once '../models/User.php';
require_once '../models/Review.php';
require_once '../models/Notification.php';
require_once '../Version1/classes/Security.php';

Security::requireAdmin();

$db = Database::getInstance()->getConnection();

$pageTitle = "Finish Request";
$currentPage = "form-finish";

$notifModel = new Notification($db);
$requestModel = new Request();
$compModel = new Comp();
$reviewModel = new Review();
$userModel = new User();

$error = '';
$reqID = $_GET['ReqID'] ?? '';
$requestDetail = null;
if ($reqID) {
    $requestDetail = $requestModel->getById($reqID);
}

$comptList = $compModel->getAll();
$finishData = $compModel->getByReqID($reqID); 
$reviewData = $reviewModel->getByReqID($reqID); // ambil data review terakhir

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (!isset($_SESSION['user_id'])) {
        die('Session user tidak valid. Silakan login ulang.');
    }

    $ReqID      = (int) $_POST['ReqID'];
    $NIK        = (int) $_SESSION['user_id'];
    $Catatan    = !empty($_POST['Catatan']) ? trim($_POST['Catatan']) : null;
    $LinkApk    = !empty($_POST['LinkApk']) ? trim($_POST['LinkApk']) : null;
    $TglSelesai = date('Y-m-d H:i:s');

    // --- Update saja record existing ---
    $dataToUpdate = [
        'Catatan'    => $Catatan,
        'LinkApk'    => $LinkApk,
        'TglSelesai' => $TglSelesai
    ];

    if ($finishData) {
        $compModel->updateByReqID($ReqID, $dataToUpdate);
    } else {
        $compModel->startWork($ReqID, $NIK); 
        $compModel->updateByReqID($ReqID, $dataToUpdate);
    }

    $requestModel->setMenungguReview($ReqID);

    $requestData = $requestModel->getById($ReqID);
    $userNik = $requestData['NIK'] ?? null;

    if ($userNik) {
        $userData  = $userModel->getByNik($userNik);
        $userEmail = $userData['email'] ?? '';
        $userName  = $userData['name'] ?? '';
        $itemName  = $requestDetail['NamaApk'] ?? $requestDetail['NamaHw'] ?? '-';

        if (!empty($userEmail)) {
            $emailSubject = "Request Completed";
            $pesan = urlencode("
                Hello $userName,<br><br>

                We would like to inform you that your submitted request has been <b>successfully completed</b> by the administrator and is now in the <b>Waiting for Review</b> stage.<br><br>

                At this stage, we kindly ask you to review the completed request to ensure that all details, functionality, or provided solutions meet your expectations before the request is officially finalized.<br><br>

                <b>Request Details:</b><br>
                <b>Item Name:</b> " . htmlspecialchars($itemName) . "<br>
                <b>Notes / Description:</b> " . htmlspecialchars($Catatan ?? '-') . "<br>
                <b>Submitted On:</b> " . htmlspecialchars($requestData['Tgl_request']) . "<br>
                <b>Finished On:</b> " . htmlspecialchars($TglSelesai) . "<br><br>

                To proceed with the review process, please log in to the system by clicking the link below:<br>
                <a href='" . BASE_URL . "'><b>Open Review Page</b></a><br><br>

                Please note that if no review action is taken within the designated review period, the request may be automatically marked as <b>completed</b> by the system.<br><br>

                Thank you for your cooperation and timely review. Your feedback helps us maintain service quality and ensures that your request is properly closed.<br><br>

                Best regards,<br>
                <b>System Notification</b>
                ");

            $url = BASE_URL . "send-notification.php?email=" . urlencode($userEmail)
                . "&subject=" . urlencode($emailSubject)
                . "&title=" . urlencode("Notifikasi Request")
                . "&message=" . $pesan;

            file_get_contents($url);
        }

        $notifModel->create(
            (int)$userNik,
            (int)$_SESSION['user_id'],
            $ReqID,
            'Request Completed',
            'Your request has been completed by the admin and is now waiting for review.'
        );
    }

    // Redirect
    header("Location: request-list.php?finished=1");
    exit;
}

function renderStatusBadge($status) {
    switch ($status) {
        case 'Pending':
            return '<span class="badge bg-secondary">Submitted</span>';

        case 'Disetujui':
            return '<span class="badge bg-primary">Approved</span>';

        case 'Antrian':
            return '<span class="badge bg-warning text-dark">In Progress</span>';

        case 'Menunggu Review':
            return '<span class="badge bg-warning">Waiting for Review</span>';

        case 'Revisi':
            return '<span class="badge bg-warning text-dark">Revision</span>';

        case 'Selesai':
            return '<span class="badge bg-success">Completed</span>';

        case 'Dibatalkan':
            return '<span class="badge bg-light text-dark border">Cancelled</span>';

        case 'Ditolak':
        default:
            return '<span class="badge bg-danger">Rejected</span>';
    }
}

function renderReviewBadge($status) {
    switch ($status) {
        case 'Approved':
            return '<span class="badge bg-success">Approved</span>';
        case 'Revision':
            return '<span class="badge bg-warning text-dark">Revision</span>';
        default:
            return '<span class="badge bg-secondary">'.htmlspecialchars($status).'</span>';
    }
}

require_once __DIR__ . '/includes/header.php';
?>
<div class="container-fluid py-4">

    <?php if ($requestDetail): ?>
        <div class="card mb-4 shadow-sm">
            <div class="card-header bg-success text-white">
                <div class="d-flex justify-content-between">
                <h5 class="mb-0">Request Details</h5>
                    <a href="karyawan-list.php" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Back
                    </a>
                </div>
            </div>
            <div class="card-body">
                <p><strong>Request Date:</strong> <?= htmlspecialchars($requestDetail['Tgl_request']); ?></p>
                <p><strong>User:</strong> <?= htmlspecialchars($requestDetail['name']); ?></p>
                <p><strong>Department:</strong> <?= htmlspecialchars($requestDetail['Departemen']); ?></p>
                <p><strong>Application / Hardware Name:</strong> <?= 
                    htmlspecialchars($requestDetail['NamaApk'] ?? $requestDetail['NamaHw'] ?? '-'); 
                ?></p>
                <p><strong>Priority:</strong> <?= htmlspecialchars($requestDetail['Prioritas']); ?></p>
                <p><strong>Status:</strong> <?= renderStatusBadge($requestDetail['StatusReq']); ?></p>
                <p><strong>Description:</strong> <?= htmlspecialchars($requestDetail['Request']); ?></p>

                <?php if ($reviewData): ?>
                    <hr>
                    <p><strong>Review Status:</strong> <?= renderReviewBadge($reviewData['Status']); ?></p>
                    <p><strong>Comment:</strong> <?= htmlspecialchars($reviewData['Komentar'] ?? '-'); ?></p>
                    <p><strong>Date:</strong> <?= htmlspecialchars($reviewData['Tanggal'] ?? '-'); ?></p>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <div class="card shadow-sm">
        <div class="card-header bg-secondary text-white">
            <h5 class="mb-0">Complete Request Form</h5>
        </div>
        <div class="card-body">
            <form method="POST">
    <input type="hidden" name="ReqID" value="<?= htmlspecialchars($reqID); ?>">
    <input type="hidden" name="TglSelesai" value="<?= date('Y-m-d H:i:s'); ?>">

    <div class="mb-3">
        <label class="form-label">Notes / Description</label>
        <textarea name="Catatan" class="form-control" rows="4" placeholder="Optional notes" required><?= htmlspecialchars($finishData['Catatan'] ?? '') ?></textarea>
    </div>

    <div class="mb-3">
        <label class="form-label">Proof Link (e.g., APK / Document)</label>
        <input type="url" name="LinkApk" class="form-control" 
            placeholder="https://example.com/..." 
            value="<?= htmlspecialchars($finishData['LinkApk'] ?? '') ?>">
    </div>

    <?php if (!empty($finishData['MulaiKerja']) && !empty($finishData['Deadline'])): ?>
        <div class="mb-3">
            <label class="form-label">Time Left</label>
            <div id="countdown" class="fw-bold text-danger"></div>
        </div>
    <?php endif; ?>

    <div class="d-flex justify-content-end">
        <button class="btn btn-success" id="btnSubmit">
            <i class="bi bi-floppy-fill"></i> Submit
        </button>
    </div>
</form>
</div>
    </div>
</div>

<?php if (!empty($finishData['Deadline'])): ?>
<script>
function startCountdown(endTime) {
    const countdownEl = document.getElementById('countdown');
    const deadline = new Date(endTime).getTime();

    function updateCountdown() {
        const now = new Date().getTime();
        const distance = deadline - now;

        if (distance < 0) {
            countdownEl.innerHTML = "Time exceeded! Request will be cancelled.";
            // Optional: auto-submit untuk cancel via AJAX
            clearInterval(interval);
            return;
        }

        const hours = Math.floor(distance / (1000 * 60 * 60));
        const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000*60));
        const seconds = Math.floor((distance % (1000 * 60)) / 1000);

        countdownEl.innerHTML = hours + "h " + minutes + "m " + seconds + "s";
    }

    updateCountdown();
    const interval = setInterval(updateCountdown, 1000);
}

startCountdown("<?= $finishData['Deadline'] ?? '' ?>");

let countdownInterval;

function startCountdown(endTime) {
    const countdownEl = document.getElementById('countdown');
    const deadline = new Date(endTime).getTime();

    function updateCountdown() {
        const now = new Date().getTime();
        const distance = deadline - now;

        if (distance < 0) {
            countdownEl.innerHTML = "Time exceeded! Request will be cancelled.";
            clearInterval(countdownInterval);
            return;
        }

        const hours = Math.floor(distance / (1000 * 60 * 60));
        const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000*60));
        const seconds = Math.floor((distance % (1000 * 60)) / 1000);

        countdownEl.innerHTML = hours + "h " + minutes + "m " + seconds + "s";
    }

    updateCountdown();
    countdownInterval = setInterval(updateCountdown, 1000);
}

<?php if (!empty($finishData['Deadline'])): ?>
startCountdown("<?= $finishData['Deadline'] ?>");
<?php endif; ?>

// Stop countdown saat form disubmit
document.querySelector('form').addEventListener('submit', function() {
    clearInterval(countdownInterval);
});
</script>
<?php endif; ?>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
