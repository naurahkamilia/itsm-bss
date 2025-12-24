<?php
session_start();
define('APP_ACCESS', true);

require_once '../config/config.php';
require_once '../config/database.php';
require_once '../models/Request.php';
require_once '../models/User.php';
require_once '../models/Aplikasi.php';
require_once '../models/Hardware.php';
require_once '../models/Notification.php';
require_once '../models/CompReqs.php';
require_once '../models/Review.php';
require_once '../Version1/classes/Security.php';

Security::requireAdmin();

$pageTitle = "Data Request";
$currentPage = "request";

$db = Database::getInstance()->getConnection();

$notifModel   = new Notification($db);
$requestModel = new Request();
$compModel    = new Comp();
$reviewModel  = new Review();
$userModel      = new User();
$apkModel       = new Aplikasi();
$hardwareModel  = new Hardware();

$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10; 
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1; 
$offset = ($page - 1) * $limit; 

$filters = []; 
$expiredRequests = $requestModel->autoCancelExpired();

function h($value, $default = '') { 
    return htmlspecialchars((string) ($value ?? $default), ENT_QUOTES, 'UTF-8'); } 
if (!empty($_GET['search'])) { $filters['search'] = $_GET['search']; }

if (!empty($_GET['StatusReq'])) {
    $filters['StatusReq'] = $_GET['StatusReq'];
}

if (!empty($_GET['Prioritas'])) {
    $filters['Prioritas'] = $_GET['Prioritas'];
}

if (!empty($_GET['tanggal_dari'])) {
    $filters['tanggal_dari'] = $_GET['tanggal_dari'];
}

if (!empty($_GET['tanggal_sampai'])) {
    $filters['tanggal_sampai'] = $_GET['tanggal_sampai'];
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
            return '<span class="badge bg-danger">Cancelled</span>';

        case 'Ditolak':
        default:
            return '<span class="badge bg-danger">Rejected</span>';
    }
}

function renderReviewBadge($status) {
    if ($status === 'Approved') {
        return '<span class="badge bg-success">Approved</span>';
    }
    if ($status === 'Revision') {
        return '<span class="badge bg-warning text-dark">Revision</span>';
    }
    return '<span class="badge bg-secondary">'.$status.'</span>';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {

    $reqID  = (int) ($_POST['ReqID'] ?? 0);
    $action = $_POST['action'];

    if (!$reqID) {
        http_response_code(400);
        echo 'Invalid ReqID';
        exit;
    }

    $requestData = $requestModel->getById($reqID);
    if (!$requestData) {
        http_response_code(404);
        echo 'Request not found';
        exit;
    }

    $userNik = $requestData['NIK'];
    $userData = $userModel->getByNik($userNik); 

    if (!$userData) {
        http_response_code(404);
        echo 'User not found';
        exit;
    }

    if (!empty($requestData['ApkID'])) {
        $apk = $apkModel->getById($requestData['ApkID']);
        $itemName = $apk['NamaApk'];
        $requestType = 'Aplikasi';
    } elseif (!empty($requestData['HwID'])) {
        $hw = $hardwareModel->getById($requestData['HwID']);
        $itemName = $hw['NamaHw'];
        $requestType = 'Hardware';
    } else {
        $itemName = 'N/A';
        $requestType = 'Unknown';
    }

    $requestDate    = $requestData['Tgl_request'];
    $reqDetails     = $requestData['Request'];
    $now            = date('Y-m-d H:i:s');
    $extraDateText  = '';

    switch ($action) {
        case 'approve':
            $requestModel->approve($reqID);
            $extraDateText = "<b>Approved On:</b> $now <br>";
            $notifTitle = 'Request Approved';
            $notifMessage = "Your request has been approved by the admin";
            $emailSubject = "Request Approved";
            break;

        case 'reject':
            $requestModel->reject($reqID);
            $extraDateText = "<b>Rejected On:</b> $now <br>";
            $notifTitle = 'Request Rejected';
            $notifMessage = 'Your request for "' . $itemName . '" has been rejected by the admin.';
            $emailSubject = "Request Rejected";
            break;

        case 'process':
            $requestModel->setAntrian($reqID);
            $extraDateText = "<b>Work Started On:</b> $now <br>";
            $notifTitle = 'Request In Progress';
            $notifMessage = 'The admin has started processing your request for "' . $itemName . '".';
            $emailSubject = "Request In Progress";
            break;

        case 'start_revisi':
            $requestModel->approve($reqID);
            $extraDateText = "<b>Revision Started On:</b> $now <br>";
            $notifTitle = 'Revising Request';
            $notifMessage = 'The admin will revise your request regarding "' . $itemName . '".';
            $emailSubject = "Request Revision";
            break;

        case 'start_work':
            $estJam = isset($_POST['estJam']) ? (int)$_POST['estJam'] : 0;
            if ($estJam <= 0) {
                http_response_code(400);
                echo 'Invalid estimate';
                exit;
            }

            $compModel->startWork($reqID, $userNik);   
            $compModel->setEstimasi($reqID, $estJam); 
            $requestModel->setAntrian($reqID);

            $notifTitle = 'Request In Progress';
            $emailSubject = 'Request In Progress';
            $notifMessage = 'Work started for your request with estimated ' . $estJam . ' hours';
            $extraDateText = "<b>Work Started On:</b> $now <br>";

            break;

        default:
            http_response_code(400);
            echo 'Invalid action';
            exit;
    }
    
    $notifModel->create(
        (int)$userNik,
        (int)$_SESSION['user_id'],
        $reqID,
        $notifTitle,
        $notifMessage
    );

    if (!empty($userData['email'])) {
       $pesan = urlencode("
        Hello,<br><br>

        This notification is to inform you that the status of your request has been <b>updated</b>.  
        Please review the information below for the latest details regarding your request.<br><br>

        <b>Current Status:</b> $notifTitle <br>
        <b>Request Type:</b> $requestType <br>
        <b>Item / Subject:</b> $itemName <br>
        <b>Request Details:</b> $reqDetails <br>
        <b>Request Date:</b> $requestDate <br>
        $extraDateText
        <br>

        To view the complete status update and any additional information, please access the application using the link below:<br>
        <a href='" . BASE_URL . "'><b>Open Application</b></a><br><br>

        If you have any questions or require further clarification, please refer to the system for more details.<br><br>

        Thank you for your attention.<br><br>

        Best regards,<br>
        System Notification
        ");

        $url = BASE_URL . "send-notification.php?email="
            . urlencode($userData['email'])
            . "&subject=" . urlencode($emailSubject)
            . "&title=" . urlencode("Notifikasi Request")
            . "&message=" . $pesan;

        file_get_contents($url);
    }

    echo $action;
    exit;
}

if (isset($_GET['ajax'], $_GET['ReqID'])) {

    $reqID = (int) $_GET['ReqID'];

    $request = $requestModel->getById($reqID);
    $finish  = $compModel->getByReqID($reqID);
    $review  = $reviewModel->getByReqID($reqID);

    if (!$request) {
        echo '<p>Request tidak ditemukan.</p>';
        exit;
    }

    echo '<p><b>Request Date:</b> '.h($request['Tgl_request']).'</p>';
    echo '<p><b>Application / Hardware:</b> '.h($request['NamaApk'] ?? $request['NamaHw']).'</p>';
    echo '<p><b>Status:</b> '.renderStatusBadge($request['StatusReq']).'</p>';    
    echo '<p><b>Decription:</b> '.h($request['Request']).'</p>';
    if (!empty($request['Dokumentasi'])) {
        echo '<p><b>Photo:</b><br>
            <img src="' . BASE_URL . 'public/images/request/' . h($request['Dokumentasi']) . '"
                alt="Documentation Photo"
                class="img-thumbnail"
                style="max-width:200px;">
        </p>';
    } else {
        echo '<p><b>Photo:</b> -</p>';
    }

    if ($finish) {
    echo '<hr><h6>Work Result</h6>';
    echo '<p><b>Notes:</b> ' . h($finish['Catatan'], '-') . '</p>';

    $estText = h($finish['EstWaktu'], '-'); 
    $estText .= ' hours'; 

    if (!empty($review['Status']) && $review['Status'] === 'Revision') { 
        $estText .= ' <b>(Revision)</b>'; 
    }

    echo '<p><b>Estimate:</b> ' . $estText . '</p>';
}

    if ($review) {
        echo '<hr><h6>Review</h6>';
        echo '<p><b>Review Status:</b> '.renderReviewBadge($review['Status']).'</p>';
        echo '<p><b>Comments:</b> '.h($review['Komentar'], '-').'</p>';
        echo '<p><b>Completion Date:</b> '.h($review['Tanggal']).'</p>';
    }

        echo '<hr>';

    if ($request['StatusReq'] === 'Pending') {

        echo '
         <div class="d-flex justify-content-end gap-2">
            <button class="btn btn-success btn-action"
                    data-action="approve">
                <i class="bi bi-check-circle"></i> Accept
            </button>

            <button class="btn btn-danger btn-action"
                    data-action="reject">
                <i class="bi bi-x-circle"></i> Reject
            </button>
        </div>';

    }

    elseif ($request['StatusReq'] === 'Disetujui') {
        echo '
        <h6>Estimasi Pengerjaan (Hours)</h6>
        <input type="text" class="form-control mb-2" id="estimasi"
            placeholder="Estimasi..." required>
        <div class="d-flex justify-content-end gap-2">
        <button class="btn btn-primary btn-action"
                data-action="process">
            <i class="bi bi-play-circle"></i> Work On
        </button>
        </div>';

    }

    elseif ($request['StatusReq'] === 'Revisi') {

        echo '
        <h6>Estimasi Revisi (Hours)</h6>
        <input type="text" class="form-control mb-2" id="estimasi"
            placeholder="Estimasi..." required>
        <div class="d-flex justify-content-end gap-2">
        <button class="btn btn-primary btn-action"
                data-action="process">
            <i class="bi bi-play-circle"></i> Start Revision
        </button>
        </div>';

    }

    exit;
}

$totalData  = $requestModel->count($filters);
$totalPages = ceil($totalData / $limit);

$filters['limit']  = $limit;
$filters['offset'] = $offset;

$userNik = $_SESSION['user_id'];
$reqs = $requestModel->getAll($filters);

require_once __DIR__ . '/includes/header.php';
?><div class="container-fluid py-4">

    <!-- HEADER -->
    <div class="page-header d-flex align-items-center mb-4">
        <h2>Requests</h2>
    </div>

    <!-- ALERT -->
    <div class="mb-3">
        <?php if (isset($_GET['updated'])): ?>
            <div class="alert alert-primary mb-2">Request updated successfully.</div>
        <?php endif; ?>

        <?php if (isset($_GET['deleted'])): ?>
            <div class="alert alert-danger mb-2">Request deleted successfully.</div>
        <?php endif; ?>
    </div>

    <!-- FILTER -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">

            <form id="searchForm" method="GET">
                <div class="row g-3">

                    <div class="col-md-3">
                        <label class="form-label">Status</label>
                        <select name="StatusReq" id="statusSelect" class="form-select">
                            <option value="">All Status</option>
                            <option value="Pending" <?= ($_GET['StatusReq'] ?? '')=='Pending'?'selected':'' ?>>Submitted</option>
                            <option value="Disetujui" <?= ($_GET['StatusReq'] ?? '')=='Disetujui'?'selected':'' ?>>Approved</option>
                            <option value="Antrian" <?= ($_GET['StatusReq'] ?? '')=='Antrian'?'selected':'' ?>>In Progress</option>
                            <option value="Menunggu Review" <?= ($_GET['StatusReq'] ?? '')=='Menunggu Review'?'selected':'' ?>>Waiting for Review</option>
                            <option value="Revisi" <?= ($_GET['StatusReq'] ?? '')=='Revisi'?'selected':'' ?>>Revision</option>
                            <option value="Selesai" <?= ($_GET['StatusReq'] ?? '')=='Selesai'?'selected':'' ?>>Completed</option>
                            <option value="Ditolak" <?= ($_GET['StatusReq'] ?? '')=='Ditolak'?'selected':'' ?>>Rejected</option>
                            <option value="Dibatalkan" <?= ($_GET['StatusReq'] ?? '')=='Dibatalkan'?'selected':'' ?>>Cancelled</option>
                        </select>
                    </div>

                    <div class="col-md-2">
                        <label class="form-label">Priority</label>
                        <select name="Prioritas" id="prioSelect" class="form-select">
                            <option value="">All</option>
                            <option value="Low" <?= ($_GET['Prioritas'] ?? '')=='Low'?'selected':'' ?>>Low</option>
                            <option value="Normal" <?= ($_GET['Prioritas'] ?? '')=='Normal'?'selected':'' ?>>Normal</option>
                            <option value="High" <?= ($_GET['Prioritas'] ?? '')=='High'?'selected':'' ?>>High</option>
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">From</label>
                        <input type="date" name="tanggal_dari" id="tanggal_dari"
                               class="form-control"
                               value="<?= $_GET['tanggal_dari'] ?? '' ?>">
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">To</label>
                        <input type="date" name="tanggal_sampai" id="tanggal_sampai"
                               class="form-control"
                               value="<?= $_GET['tanggal_sampai'] ?? '' ?>">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Search</label>
                        <input type="text"
                               name="search"
                               id="searchInput"
                               class="form-control"
                               placeholder="Search request..."
                               value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
                    </div>

                    <div class="col-md-2 d-grid align-self-end">
                        <button class="btn btn-primary">
                            <i class="bi bi-search"></i> Search
                        </button>
                    </div>

                </div>
            </form>

        </div>
    </div>

    <!-- TABLE -->
    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th width="60">#</th>
                        <th>Request Date</th>
                        <th>Completion</th>
                        <th>User</th>
                        <th>Application / Hardware</th>
                        <th>Priority</th>
                        <th>Status</th>
                        <th width="120">Action</th>
                    </tr>
                </thead>

                <tbody>
                <?php if (empty($reqs)): ?>
                    <tr>
                        <td colspan="8" class="text-center text-muted py-4">
                            No requests found
                        </td>
                    </tr>
                <?php endif; ?>

                <?php 
                $no = $offset + 1;
                foreach ($reqs as $r): 
                    $finish = $compModel->getByReqID($r['ReqID']);
                    $tanggalSelesai = $finish['TglSelesai'] ?? null;
                ?>
                    <tr>
                        <td><?= $no++; ?></td>
                        <td><?= h($r['Tgl_request']); ?></td>
                        <td><?= $tanggalSelesai ? h($tanggalSelesai) : '-' ?></td>
                        <td><?= h($r['Nama']); ?></td>
                        <td><?= h($r['NamaApk'] ?? $r['NamaHw'] ?? '-'); ?></td>
                        <td><?= h($r['Prioritas']); ?></td>
                        <td><?= renderStatusBadge($r['StatusReq']); ?></td>
                        <td>
                            <button class="btn btn-sm btn-outline-dark detail-btn"
                                    data-reqid="<?= $r['ReqID']; ?>"
                                    data-bs-toggle="modal"
                                    data-bs-target="#requestDetailModal">
                                <i class="bi bi-info-circle"></i>
                            </button>

                            <?php if ($r['StatusReq'] === 'Antrian'): ?>
                                <a href="finish-request.php?ReqID=<?= $r['ReqID']; ?>"
                                   class="btn btn-sm btn-outline-success">
                                    <i class="bi bi-check2-circle"></i>
                                </a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>

            </table>
        </div>
    </div>

<!-- Panel Request Detail -->
<div id="requestDetailPanel" class="card shadow-sm border-0 position-fixed bottom-0 start-0 end-0"
     style="height:10%; max-height:60%; transition: height 0.3s; z-index:1050; display:none;">
    <div class="card-header d-flex justify-content-between align-items-center bg-white border-bottom">
        <div>
            <h5 class="mb-0 fw-semibold">Request Detail</h5>
            <small class="text-secondary">Request information & progress</small>
        </div>
        <div class="d-flex gap-2">
            <button id="minimizePanel" class="btn btn-sm btn-outline-secondary">_</button>
            <button id="maximizePanel" class="btn btn-sm btn-outline-secondary">⬆</button>
            <button id="closePanel" class="btn btn-sm btn-outline-secondary">&times;</button>
        </div>
    </div>
    <div class="card-body overflow-auto" id="requestDetailContentPanel">
        <div class="text-center text-muted py-5">
            <div class="spinner-border spinner-border-sm mb-2"></div><br>
            Loading...
        </div>
    </div>
</div>

<!-- Modal Konfirmasi Action -->
<div class="modal fade" id="confirmActionModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content shadow-sm border-0">
      <div class="modal-body">
        <p id="confirmText" class="mb-3"></p>
        <div id="confirmLoading" class="text-center py-2" style="display:none;">
            <div class="spinner-border spinner-border-sm"></div> Processing...
        </div>
        <div class="d-flex justify-content-end gap-2">
          <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
          <button type="button" id="confirmActionBtn" class="btn btn-primary btn-sm">Confirm</button>
        </div>
      </div>
    </div>
  </div>
</div>
<?php if ($totalPages > 1): ?>
<nav class="mt-3 d-flex justify-content-center">
    <ul class="pagination pagination-sm mb-0">

        <?php
        $prevParams = $_GET;
        $prevParams['page'] = max(1, $page - 1);
        $nextParams = $_GET;
        $nextParams['page'] = min($totalPages, $page + 1);
        ?>

        <!-- Prev -->
        <li class="page-item <?= ($page <= 1) ? 'disabled' : '' ?>">
            <a class="page-link rounded-pill px-2" href="?<?= http_build_query($prevParams) ?>">&laquo;</a>
        </li>

        <!-- Pages -->
        <?php for ($i = 1; $i <= $totalPages; $i++):
            $params = $_GET;
            $params['page'] = $i;
        ?>
            <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
                <a class="page-link rounded-pill px-3 py-1" href="?<?= http_build_query($params) ?>"><?= $i ?></a>
            </li>
        <?php endfor; ?>

        <!-- Next -->
        <li class="page-item <?= ($page >= $totalPages) ? 'disabled' : '' ?>">
            <a class="page-link rounded-pill px-2" href="?<?= http_build_query($nextParams) ?>">&raquo;</a>
        </li>

    </ul>
</nav>
<?php endif; ?>

</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
let currentAction = null;
let currentReqID = null;

$(document).on('click', '.detail-btn', function() {
    var reqID = $(this).data('reqid');
    var panel = $('#requestDetailPanel');
    var content = $('#requestDetailContentPanel');

    panel.data('reqid', reqID);
    content.html('<div class="text-center text-muted py-5"><div class="spinner-border spinner-border-sm mb-2"></div><br>Loading...</div>');
    panel.show().css('height', '40%');

    $.ajax({
        url: 'request-list.php',
        type: 'GET',
        data: { ReqID: reqID, ajax: 1 },
        success: function(response) {
            content.html(response);
        },
        error: function() {
            content.html('<div class="alert alert-danger">Failed to load data</div>');
        }
    });
});

// Panel tombol
$('#closePanel').on('click', function() { $('#requestDetailPanel').hide(); });
$('#minimizePanel').on('click', function() { $('#requestDetailPanel').css('height', '40%'); });
$('#maximizePanel').on('click', function() { $('#requestDetailPanel').css('height', '80%'); });

// Tombol Action -> buka modal konfirmasi
$(document).on('click', '.btn-action', function() {
    const action = $(this).data('action');
    const panel  = $('#requestDetailPanel');
    const reqID  = panel.data('reqid');

    if (!reqID) return;

    currentAction = action;
    currentReqID = reqID;

    let confirmText = '';
    if (action === 'approve') confirmText = 'Accept this request?';
    if (action === 'reject')  confirmText = 'Reject this request?';
    if (action === 'start_revisi') confirmText = 'Start revision for this request?';
    if (action === 'process') {
        const estJam = $('#estimasi').val();
        if (!estJam || estJam <= 0) {
            alert('Please enter valid estimated hours');
            return;
        }
        confirmText = 'Start work with estimated ' + estJam + ' hours?';
    }

    $('#confirmText').text(confirmText);
    $('#confirmLoading').hide();
    $('#confirmActionBtn').prop('disabled', false);

    var confirmModal = new bootstrap.Modal(document.getElementById('confirmActionModal'));
    confirmModal.show();
});

// Tombol Confirm di modal
$('#confirmActionBtn').on('click', function() {
    const panel = $('#requestDetailPanel');
    let estJam = $('#estimasi').val();

    $(this).prop('disabled', true);
    $('#confirmLoading').show();

    let postData = { action: currentAction, ReqID: currentReqID };
    if (currentAction === 'process') postData.estJam = estJam;

    $.post('request-list.php', postData)
        .done(function() {
            panel.hide();
        })
        .fail(function() {
            alert('Failed to update request');
        })
        .always(function() {
            var confirmModalEl = document.getElementById('confirmActionModal');
            var confirmModal = bootstrap.Modal.getInstance(confirmModalEl);
            confirmModal.hide();

            // reload page sedikit delay biar panel hide dulu
            setTimeout(() => location.reload(), 300);
        });
});

const statusSelect   = document.getElementById('statusSelect');
const prioSelect     = document.getElementById('prioSelect');
const tanggalDari    = document.getElementById('tanggal_dari');
const tanggalSampai  = document.getElementById('tanggal_sampai');
const searchInput    = document.getElementById('searchInput');

function updateFilters() {
    const params = new URLSearchParams(window.location.search);

    params.set('StatusReq', statusSelect.value);
    params.set('Prioritas', prioSelect.value);
    params.set('tanggal_dari', tanggalDari.value);
    params.set('tanggal_sampai', tanggalSampai.value);

    params.set('search', searchInput.value.trim());

    window.location.search = params.toString();
}

statusSelect.addEventListener('change', updateFilters);
prioSelect.addEventListener('change', updateFilters);

tanggalDari.addEventListener('change', updateFilters);
tanggalSampai.addEventListener('change', updateFilters);

searchInput.addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        e.preventDefault();
        updateFilters();
    }
});

</script>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
