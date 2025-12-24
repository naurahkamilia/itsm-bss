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

Security::requireUser();
$db = Database::getInstance()->getConnection();

$userModel = new User();
$requestModel = new Request($db);
$notifModel = new Notification($db);
$compModel    = new Comp();
$reviewModel  = new Review();

$admins = $userModel->getAdmins();

$pageTitle = "Data Request";
$currentPage = "request";

$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10; 
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1; 
$offset = ($page - 1) * $limit; $filters = []; 

function h($value, $default = '') { 
    return htmlspecialchars((string) ($value ?? $default), ENT_QUOTES, 'UTF-8'); } 
if (!empty($_GET['search'])) { $filters['search'] = $_GET['search']; }


if (!empty($_GET['StatusReq'])) {
    $filters['StatusReq'] = $_GET['StatusReq'];
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

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'review') {

    $reqID    = (int) $_POST['ReqID'];
    $status   = $_POST['Status'];
    $komentar = trim($_POST['Komentar'] ?? '');

    $reviewModel->create([
        'ReqID'    => $reqID,
        'NIK'      => $_SESSION['user_id'],
        'Komentar' => $komentar ?: null,
        'Status'   => ($status === 'Revisi') ? 'Revision' : 'Approved',
        'Tanggal'  => date('Y-m-d H:i:s')
    ]);

    if ($status === 'Revisi') {
        $requestModel->revisi($reqID);
    } else {
        $requestModel->selesai($reqID);
    }

    $requestData = $requestModel->getById($reqID);
    $username    = $_SESSION['user_name'];
    $department  = htmlspecialchars($requestData['Departemen'] ?? '-', ENT_QUOTES);
    $request     = $requestData['Request'] ?? null;
    $requestType = !empty($requestData['ApkID']) ? 'Aplikasi' :
                   (!empty($requestData['HwID']) ? 'Hardware' : 'Unknown');
    $now         = date('Y-m-d H:i:s');

    $admins = $userModel->getAdmins();


    // ==== REVISI ====
    if ($status === 'Revisi') {

        foreach ($admins as $admin) {
            $notifModel->create(
                (int)$admin['NIK'],
                (int)$_SESSION['user_id'],
                $reqID,
                'Revision Request',
                $username . ' has submitted a revision on the previous request.'
            );

            if (!empty($admin['email'])) {
               $pesan = urlencode("
                Hello Admin,<br><br>

                This message is to inform you that a <b>revision</b> has been successfully submitted through the system.  
                Please review the updated information below and take the necessary action accordingly.<br><br>

                <b>Revision Information:</b><br>
                <b>User Name:</b> $username <br>
                <b>Department:</b> $department <br>
                <b>Request Type:</b> $requestType <br>
                <b>Priority: </b> $prioritas<br>
                <b>Original Request Details:</b> $request <br>
                <b>Revision Details:</b> $komentar <br>
                <b>Revision Date:</b> $now <br><br>

                To view the complete request details and manage this revision, please access the application using the link below:<br>
                <a href='" . BASE_URL . "'><b>Open Application</b></a><br><br>

                Your prompt review will help ensure the request is processed efficiently and without delay.<br><br>

                Thank you for your cooperation.<br><br>

                Best regards,<br>
                System Notification
                ");

                $url = BASE_URL."send-notification.php?email="
                    . urlencode($admin['email'])
                    . "&subject=" . urlencode("Revision Request")
                    . "&title=" . urlencode("Notification Request")
                    . "&message=" . $pesan;

                file_get_contents($url);
            }
        }

        echo 'reviewed';
        exit;
    }

    // ==== APPROVED ====
    if ($status === 'Approved') {

        foreach ($admins as $admin) {
            $notifModel->create(
                (int)$admin['NIK'],
                (int)$_SESSION['user_id'],
                $reqID,
                'Request Approved by User',
                "$username has reviewed and approved the completed request."
            );

            if (!empty($admin['email'])) {
                $pesan = urlencode("
                Hello Admin,<br><br>

                We are pleased to inform you that a completed request has been <b>successfully approved by the user</b>.  
                This approval indicates that the request has been reviewed and accepted without further revision.<br><br>

                <b>Approval Details:</b><br>
                <b>User Name:</b> $username <br>
                <b>Request Type:</b> $requestType <br>
                <b>Request Details:</b> $request <br>
                <b>Priority:<b> $prioritas<br>
                <b>Submitted On:</b> " . htmlspecialchars($requestData['Tgl_request'] ?? '-', ENT_QUOTES) . "<br>
                <b>Approved On:</b> $now <br><br>

                For more information and to view the complete request history, please access the application using the link below:<br>
                <a href='" . BASE_URL . "'><b>Open Application</b></a><br><br>

                Thank you for your attention and continued support in ensuring a smooth request handling process.<br><br>

                Best regards,<br>
                System Notification
                ");

                $url = BASE_URL."send-notification.php?email="
                    . urlencode($admin['email'])
                    . "&subject=" . urlencode("Request Complete")
                    . "&title=" . urlencode("Notification Request")
                    . "&message=" . $pesan;

                file_get_contents($url);
            }
        }

        echo 'reviewed';
        exit;
    }

} 

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'cancelled') {

    $reqID = (int) $_POST['ReqID'];
    if (!$reqID) {
        http_response_code(400);
        echo 'invalid';
        exit;
    }

    $success = $requestModel->cancelled($reqID);

    if ($success) {

        $requestData = $requestModel->getById($reqID);
        $now         = date('Y-m-d H:i:s');
        $username    = $_SESSION['user_name'];
        $request     = $requestData['Request'] ?? null;

        $requestType = !empty($requestData['ApkID']) ? 'Aplikasi' :
                       (!empty($requestData['HwID']) ? 'Hardware' : 'Unknown');

        $admins = $userModel->getAdmins();

        foreach ($admins as $admin) {

            // notif ke admin
            $notifModel->create(
                (int)$admin['NIK'],
                (int)$_SESSION['user_id'],
                $reqID,
                'Request Cancelled by User',
                'The user has cancelled this request.'
            );

            // email
            if (!empty($admin['email'])) {

                $pesan = urlencode("
                    Hello Admin,<br><br>

                    This notification is to inform you that a request submitted by the user has been <b>cancelled</b>.  
                    As a result, the request will no longer proceed to further processing unless it is resubmitted.<br><br>

                    <b>Cancellation Details:</b><br>
                    <b>User Name:</b> $username <br>
                    <b>Request Type:</b> $requestType <br>
                    <b>Request Details:</b> $request <br>
                    <b>Submitted On:</b> " . htmlspecialchars($requestData['Tgl_request'] ?? '-', ENT_QUOTES) . "<br>
                    <b>Cancelled On:</b> $now <br><br>

                    For reference or record purposes, you may review the request history by accessing the application through the link below:<br>
                    <a href='" . BASE_URL . "'><b>Open Application</b></a><br><br>

                    Thank you for your attention.<br><br>

                    Best regards,<br>
                    System Notification
                    ");

                $url = BASE_URL."send-notification.php?email="
                    . urlencode($admin['email'])
                    . "&subject=" . urlencode("Cancelled Request")
                    . "&title=" . urlencode("Notification Request")
                    . "&message=" . $pesan;

                file_get_contents($url);
            }
        }

        echo 'cancelled';
        exit;
    }

    http_response_code(500);
    echo 'failed';
    exit;
}


if (isset($_GET['ajax'], $_GET['ReqID'])) {

    $reqID = (int) $_GET['ReqID'];

    $request = $requestModel->getById($reqID);
    $finish  = $compModel->getByReqID($reqID);
    $review  = $reviewModel->getByReqID($reqID);

    if (!$request) {
        echo '<p class="text-muted">Request not found.</p>';
        exit;
    }

    echo '
    <div class="mb-3">
        <div class="row g-2 small">

            <div class="col-md-6">
                <div class="text-muted">Date</div>
                <div>' . h($request['Tgl_request']) . '</div>
            </div>

            <div class="col-md-6">
                <div class="text-muted">Application / Hardware</div>
                <div>' . h($request['NamaApk'] ?? $request['NamaHw']) . '</div>
            </div>

            <div class="col-md-6">
                <div class="text-muted">Status</div>
                ' . renderStatusBadge($request['StatusReq']) . '
            </div>

        </div>
    </div>';

    echo '
    <div class="mb-3">
        <div class="fw-semibold mb-1">Description</div>
        <p class="text-muted mb-0">'
            . nl2br(h($request['Request'])) .
        '</p>
    </div>';

    echo '
    <div class="mb-3">
        <div class="fw-semibold mb-1">Documentation</div>';

    if (!empty($request['Dokumentasi'])) {
        echo '
        <img src="' . BASE_URL . 'public/images/request/' . h($request['Dokumentasi']) . '"
             class="img-thumbnail"
             style="max-width:220px;">';
    } else {
        echo '<div class="text-muted small">No documentation attached</div>';
    }

    echo '</div>';

    if ($finish) {
        echo '
        <hr>
        <div class="mb-3">
            <div class="fw-semibold mb-2">Work Result</div>

            <div class="row g-2 small">
                <div class="col-md-6">
                    <div class="text-muted">Notes</div>
                    <div>' . h($finish['Catatan'], '-') . '</div>
                </div>

                <div class="col-md-6">
                    <div class="text-muted">Estimated Time</div>
                    <div>' . h($finish['EstWaktu'], '-') . ' hours</div>
                </div>
            </div>
        </div>';
    }

    if ($review) {
        echo '
        <hr>
        <div class="mb-3">
            <div class="fw-semibold mb-2">Latest Review</div>

            <div class="row g-2 small">
                <div class="col-md-6">
                    <div class="text-muted">Status</div>
                    ' . renderReviewBadge($review['Status']) . '
                </div>

                <div class="col-md-6">
                    <div class="text-muted">Date</div>
                    <div>' . h($review['Tanggal']) . '</div>
                </div>

                <div class="col-12">
                    <div class="text-muted">Comment</div>
                    <div>' . h($review['Komentar'], '-') . '</div>
                </div>
            </div>
        </div>';
    }

    if ($request['StatusReq'] === 'Pending') {
    echo '
    <hr>
    <div class="d-flex justify-content-between align-items-center">

        <div class="text-muted small">
            This request has not been processed yet
        </div>

        <button type="button"
                class="btn btn-sm btn-outline-danger btn-action-cancel px-3"
                data-reqid="' . $reqID . '">
            <i class="bi bi-x-circle me-1"></i>
            Cancel Request
        </button>

    </div>';
}

if ($request['StatusReq'] === 'Menunggu Review') {
    echo '
    <hr>
    <div class="mb-3">

        <div class="d-flex align-items-center mb-2">
            <i class="bi bi-check2-circle text-success me-2"></i>
            <div class="fw-semibold">Review Decision</div>
        </div>

        <textarea class="form-control form-control-sm mb-3"
            id="reviewKomentar"
            rows="3"
            placeholder="Add a note (optional)..."></textarea>

        <div class="d-flex justify-content-end gap-2">
            <button type="button"
                    class="btn btn-sm btn-success btn-review-approve px-3"
                    data-id="' . $reqID . '">
                <i class="bi bi-check-lg me-1"></i>
                Approve
            </button>

            <button type="button"
                    class="btn btn-sm btn-warning btn-review-revisi px-3"
                    data-id="' . $reqID . '">
                <i class="bi bi-arrow-counterclockwise me-1"></i>
                Request Revision
            </button>
        </div>

    </div>';
}


    exit;
}


$userNik = $_SESSION['user_id'];
$totalData  = $requestModel->countByUser($userNik, $filters);
$totalPages = ceil($totalData / $limit);
$filters['limit']  = $limit;
$filters['offset'] = $offset;
 
$reqs = $requestModel->getByUser($userNik, $filters);

require_once __DIR__ . '/includes/header.php';
?>

<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-xl-11">

            <!-- Header -->
            <div class="mb-4">
                <h3 class="fw-semibold mb-1">Data Request</h3>
                <p class="text-muted small mb-0">
                    List of your submitted support requests
                </p>
            </div>

            <!-- Alerts -->
            <div id="ajaxAlertContainer"></div>

            <?php if (isset($_GET['updated'])): ?>
                <div class="alert alert-primary">
                    Request updated successfully.
                </div>
            <?php endif; ?>

            <?php if (isset($_GET['deleted'])): ?>
                <div class="alert alert-danger">
                    Request deleted successfully.
                </div>
            <?php endif; ?>

            <!-- Filter -->
            <form method="GET" class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <div class="row g-3 align-items-end">

                        <div class="col-md-4">
                            <label class="form-label small fw-medium">Status</label>
                            <select name="StatusReq" class="form-select">
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

                        <div class="col-md-6">
                            <label class="form-label small fw-medium">Search</label>
                            <input type="text"
                                   name="search"
                                   class="form-control"
                                   placeholder="Search request..."
                                   value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
                        </div>

                        <div class="col-md-2 d-grid">
                            <button class="btn btn-primary">
                                Search
                            </button>
                        </div>

                    </div>
                </div>
            </form>

            <!-- Table -->
            <div class="card border-0 shadow-sm">
                <div class="card-body p-0">

                    <?php if (count($reqs) > 0): ?>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Date</th>
                                        <th>Application / Hardware</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                             <tbody>
                                <?php foreach ($reqs as $r): ?>
                                    <!-- MAIN ROW -->
                                    <tr>
                                        <td class="text-muted small">
                                            <?= htmlspecialchars($r['Tgl_request']); ?>
                                        </td>
                                        <td>
                                            <?= htmlspecialchars($r['NamaApk'] ?? $r['NamaHw'] ?? '-', ENT_QUOTES, 'UTF-8'); ?>
                                        </td>
                                        <td><?= renderStatusBadge($r['StatusReq']); ?></td>
                                        <td>
                                            <button
                                                type="button"
                                                class="btn btn-sm btn-outline-secondary btn-toggle-detail"
                                                data-id="<?= $r['ReqID']; ?>">
                                                <i class="bi bi-chevron-down"></i>
                                            </button>

                                            <?php if ($r['StatusReq'] == 'Pending'): ?>
                                                <a href="request-edit.php?id=<?= $r['ReqID']; ?>"
                                                class="btn btn-sm btn-outline-primary">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                            <?php endif; ?>
                                        </td>
                                    </tr>

                                    <tr class="detail-row d-none" id="detail-<?= $r['ReqID']; ?>">
                                    <td colspan="4" class="bg-light">
                                        <div class="p-3">
                                            <!-- WAJIB ADA -->
                                            <div id="actionBox" class="mb-3"></div>
                                            <div class="detail-body small text-muted">
                                                Loading...
                                            </div>
                                        </div>
                                    </td>
                                </tr>

                                <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>

                    <?php else: ?>
                        <!-- Empty state -->
                        <div class="text-center py-5">
                            <div class="mb-3">
                                <i class="bi bi-inbox fs-1 text-muted"></i>
                            </div>
                            <h6 class="fw-semibold">No requests found</h6>
                            <p class="text-muted small mb-0">
                                You haven’t submitted any requests yet.
                            </p>
                        </div>
                    <?php endif; ?>

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
        </div>
    </div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
document.querySelectorAll('.btn-toggle-detail').forEach(btn => {
    btn.addEventListener('click', function () {

        const id = this.dataset.id;
        const detailRow = document.getElementById('detail-' + id);
        const content = detailRow.querySelector('.detail-body');
        const icon = this.querySelector('i');

        document.querySelectorAll('.detail-row').forEach(r => {
            if (r !== detailRow) r.classList.add('d-none');
        });

        document.querySelectorAll('.btn-toggle-detail i').forEach(i => {
            i.classList.remove('bi-chevron-up');
            i.classList.add('bi-chevron-down');
        });

        detailRow.classList.toggle('d-none');

        icon.classList.toggle('bi-chevron-down');
        icon.classList.toggle('bi-chevron-up');

        // load only once
        if (!detailRow.dataset.loaded) {
            fetch(`lihatRequest.php?ajax=1&ReqID=${id}`)
                .then(res => res.text())
                .then(html => {
                    content.innerHTML = html;
                    detailRow.dataset.loaded = "true";
                });
        }

    });
});
function openActionBox(container, {
    title,
    message,
    type = 'warning',
    confirmText = 'Yes',
    showTextarea = false,
    onConfirm
}) {
    const html = `
        <div class="alert alert-${type} mb-0">
            <div class="fw-semibold mb-1">${title}</div>
            <p class="mb-2 small">${message}</p>

            ${showTextarea ? `
                <textarea class="form-control form-control-sm mb-2 action-comment"
                    rows="2"
                    placeholder="Add comment..."></textarea>
            ` : ''}

            <div class="text-end">
                <button type="button"
                        class="btn btn-sm btn-light me-2 btn-action-cancel-box">
                    Cancel
                </button>
                <button type="button"
                        class="btn btn-sm btn-${type} btn-action-confirm">
                    ${confirmText}
                </button>
            </div>
        </div>
    `;

    container.html(html);

    container.find('.btn-action-cancel-box').on('click', () => {
        container.empty();
    });

    container.find('.btn-action-confirm').on('click', function () {
        $(this).prop('disabled', true)
               .html('<span class="spinner-border spinner-border-sm me-1"></span> Processing');
        onConfirm(container);
    });
}

$('#requestDetailModal').on('show.bs.modal', function (event) {
    const button = $(event.relatedTarget);
    const reqID  = button.data('reqid');
    const modal  = $(this);

    modal.find('#actionBox').empty();
    modal.find('#requestDetailContent').html('Loading...');

    $.ajax({
        url: 'lihatRequest.php',
        type: 'GET',
        data: { ReqID: reqID, ajax: 1 },
        success: function (response) {
            modal.find('#requestDetailContent').html(response);
        },
        error: function () {
            modal.find('#requestDetailContent')
                .html('<div class="alert alert-danger">Gagal memuat data</div>');
        }
    });
});

$(document).on('click', '.btn-review-approve', function () {

    const row = $(this).closest('.detail-row');
    const box = row.find('#actionBox');
    const reqID = $(this).data('id');

    openActionBox(box, {
        title: 'Confirm Approval',
        message: 'Are you sure you want to approve this request?',
        type: 'success',
        confirmText: 'Approve',
        onConfirm: function (container) {

            const komentar = row.find('#reviewKomentar').val() || '';

            $.post('lihatRequest.php', {
                action: 'review',
                ReqID: reqID,
                Status: 'Approved',
                Komentar: komentar
            }).done(() => location.reload());
        }
    });
});

$(document).on('click', '.btn-review-revisi', function () {

   const row = $(this).closest('.detail-row');
    const box = row.find('#actionBox');
    const reqID = $(this).data('id');

    openActionBox(box, {
        title: 'Request Revision',
        message: 'Send this request back for revision?',
        type: 'warning',
        confirmText: 'Send Revision',
        onConfirm: function () {

            const komentar = row.find('#reviewKomentar').val() || '';

            $.post('lihatRequest.php', {
                action: 'review',
                ReqID: reqID,
                Status: 'Revisi',
                Komentar: komentar
            }).done(() => location.reload());
        }
    });
});

$(document).on('click', '.btn-action-cancel', function () {

   const row = $(this).closest('.detail-row');
    const box = row.find('#actionBox');
    const reqID = $(this).data('reqid');

    openActionBox(box, {
        title: 'Cancel Request',
        message: 'This action cannot be undone.',
        type: 'danger',
        confirmText: 'Cancel Request',
        onConfirm: function () {
            $.post('lihatRequest.php', {
                action: 'cancelled',
                ReqID: reqID
            }).done(() => location.reload());
        }
    });
});

const statusSelect   = document.getElementById('statusSelect');
const searchInput    = document.getElementById('searchInput');

function updateFilters() {
    const params = new URLSearchParams(window.location.search);

    params.set('StatusReq', statusSelect.value);
    params.set('search', searchInput.value.trim());

    window.location.search = params.toString();
}

statusSelect.addEventListener('change', updateFilters);
searchInput.addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        e.preventDefault();
        updateFilters();
    }
});

function showAjaxAlert(message, type = 'success') {
    const alertHTML = `
        <div class="alert alert-${type} alert-dismissible fade show mt-2" role="alert">
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `;
    $('#ajaxAlertContainer').html(alertHTML);

    setTimeout(() => {
        $('.alert').alert('close');
    }, 5000);
}

</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
