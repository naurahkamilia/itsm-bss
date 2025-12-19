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
$currentPage = "lihatReq";

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
        echo '<p>Requests Not Found.</p>';
        exit;
    }

    echo '<p><b>Date:</b> ' . h($request['Tgl_request']) . '</p>';
    echo '<p><b>Name:</b> ' . h($request['NamaApk'] ?? $request['NamaHw']) . '</p>';
    echo '<p><b>Status:</b> ' . renderStatusBadge($request['StatusReq']) . '</p>';
    echo '<p><b>Description:</b> ' . h($request['Request']) . '</p>';
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
        echo '<p><b>Estimated Time:</b> ' . h($finish['EstWaktu'], '-') . ' hours</p>';
    }

    if ($review) {
        echo '<hr><h6>Latest Review</h6>';
        echo '<p><b>Status:</b> ' . renderReviewBadge($review['Status']) . '</p>';
        echo '<p><b>Comment:</b> ' . h($review['Komentar'], '-') . '</p>';
        echo '<p><b>Date:</b> ' . h($review['Tanggal']) . '</p>';
    }

    if ($request['StatusReq'] === 'Menunggu Review') {
        echo '
        <hr>
        <h6>Review</h6>
        <textarea class="form-control mb-2" id="reviewKomentar"
            placeholder="Notes (Optional)"></textarea>

        <div class="d-flex justify-content-end gap-2">
            <button type="button"
                class="btn btn-success btn-review-approve"
                data-id="' . $reqID . '">
                Approve
            </button>
            <button type="button"
                class="btn btn-warning btn-review-revisi"
                data-id="' . $reqID . '">
                Request Revision
            </button>
        </div>';
    }

    echo '<hr>';

    if ($request['StatusReq'] === 'Pending') {
        echo '
        <div class="d-flex justify-content-end gap-2">
            <button class="btn btn-danger btn-action-cancel"
                    data-reqid="' . $request['ReqID'] . '">
                <i class="bi bi-x-lg"></i> Cancel
            </button>
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

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Data Request</h2>
    </div>

    <div id="ajaxAlertContainer"></div>
    <?php if (isset($_GET['updated'])): ?>
    <div class="alert alert-primary">The request data has been successfully updated.</div>
    <?php endif; ?>

    <?php if (isset($_GET['deleted'])): ?>
        <div class="alert alert-danger">The request data has been successfully deleted.</div>
    <?php endif; ?>

      <form id="searchForm" method="GET" class="mb-3">


    <div class="row g-2">

        <div class="col-md-5">
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

        <div class="col-md-5">
            <input type="text"
                   name="search"
                   id="searchInput"
                   class="form-control"
                   placeholder="Search Requests..."
                   value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
        </div>

        <div class="col-md-2 d-grid">
            <button type="submit" class="btn btn-primary w-100">
                <i class="bi bi-search"></i> Search
            </button>
        </div>
    </div>
</form>

    <div class="row">
        <div class="card-body p-0">
            <?php if (count($reqs) > 0): ?>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th width="70">Request Date</th>
                                <th width="150">Application / Hardware Name</th>
                                <th width="100">Status</th>
                                <th width="120">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($reqs as $r): ?>
                            <tr>
                        <td><?= htmlspecialchars($r['Tgl_request']); ?></td>
                        <td>
                            <?= htmlspecialchars($r['NamaApk'] ?? $r['NamaHw'] ?? '-', ENT_QUOTES, 'UTF-8'); ?>
                        </td>
                        <td><?= renderStatusBadge($r['StatusReq']); ?></td>
                        <td>   
                            <?php
                                $btnClass = 'btn-dark';
                                $icon     = 'bi-info-circle';

                                if ($r['StatusReq'] === 'Menunggu Review') {
                                    $btnClass = 'btn-primary';
                                    $icon     = 'bi-star';
                                } 
                                ?>

                                <a href="#"
                                class="btn btn-sm <?= $btnClass ?> btn-detail"
                                data-reqid="<?= $r['ReqID']; ?>"
                                data-bs-toggle="modal"
                                data-bs-target="#requestDetailModal">
                                <i class="bi <?= $icon ?>"></i>
                                </a>

                            <?php if ($r['StatusReq'] == 'Pending'): ?>
                                <a href="request-edit.php?id=<?= $r['ReqID']; ?>" 
                                class="btn btn-sm btn-primary">
                                    <i class="bi bi-pencil"></i>
                                </a>
                            <?php endif; ?>
                    </td>
                    </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                    <div class="text-center py-5">
                        <i class="bi bi-send display-1 text-muted"></i>
                            <h5 class="mt-3">No request yet</h5>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

   <div class="modal fade" id="requestDetailModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">Detail Request</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <div id="actionBox"></div>

                <div id="requestDetailContent">
                    Loading...
                </div>
            </div>

        </div>
    </div>
</div>

   
    <!-- PAGINATION -->
    <nav class="mt-3">
        <ul class="pagination">

            <!-- Previous -->
            <li class="page-item <?= ($page <= 1) ? 'disabled' : '' ?>">
                <a class="page-link" 
                   href="?page=<?= $page - 1 ?>&search=<?= $_GET['search'] ?? '' ?>">
                    &laquo;
                </a>
            </li>

            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
                    <a class="page-link" 
                       href="?page=<?= $i ?>&search=<?= $_GET['search'] ?? '' ?>">
                        <?= $i ?>
                    </a>
                </li>
            <?php endfor; ?>

            <!-- Next -->
            <li class="page-item <?= ($page >= $totalPages) ? 'disabled' : '' ?>">
                <a class="page-link" 
                   href="?page=<?= $page + 1 ?>&search=<?= $_GET['search'] ?? '' ?>">
                    &raquo;
                </a>
            </li>

        </ul>
    </nav>
    

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
function openActionBox({
    title,
    message,
    type = 'warning',
    confirmText = 'Ya',
    showTextarea = false,
    onConfirm
}) {
    const html = `
        <div class="alert alert-${type}">
            <strong>${title}</strong>
            <p class="mb-2">${message}</p>

            ${showTextarea ? `
                <textarea class="form-control mb-2"
                    id="actionComment"
                    placeholder="Tulis komentar..."></textarea>
            ` : ''}

            <div class="text-end">
                <button class="btn btn-secondary btn-sm me-2" id="actionCancel">
                    Batal
                </button>
                <button class="btn btn-${type} btn-sm" id="actionConfirm">
                    ${confirmText}
                </button>
            </div>
        </div>
    `;

    $('#actionBox').html(html);

    $('#actionCancel').on('click', function () {
        $('#actionBox').empty();
    });

    $('#actionConfirm').on('click', function () {
        const btn = $(this);
        btn.prop('disabled', true)
           .html('<span class="spinner-border spinner-border-sm"></span> Memproses...');

        $('#actionCancel').prop('disabled', true);
        onConfirm();
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
    const reqID = $(this).data('id');

    openActionBox({
        title: 'Konfirmasi Approve',
        message: 'Apakah Anda yakin ingin menyetujui request ini?',
        type: 'success',
        confirmText: 'Ya, Approve',
        showTextarea: false,
        onConfirm: function () {

            const komentar = $('#reviewKomentar').val() || '';

            $('#actionBox').empty();
            $('#requestDetailContent').html(`
                <div class="alert alert-info d-flex align-items-center">
                    <span class="spinner-border spinner-border-sm me-2"></span>
                    Menyetujui request...
                </div>
            `);

            $.post('lihatRequest.php', {
                action: 'review',
                ReqID: reqID,
                Status: 'Approved',
                Komentar: komentar
            }, function () {

                $('#requestDetailContent').html(`
                    <div class="alert alert-success">
                        Request berhasil di-approve
                    </div>
                `);

                setTimeout(() => {
                    $('#requestDetailModal').modal('hide');
                    location.reload();
                }, 700);
            });
        }
    });
});

$(document).on('click', '.btn-review-revisi', function () {
    const reqID = $(this).data('id');

    openActionBox({
        title: 'Konfirmasi Revisi',
        message: 'Apakah Anda yakin ingin mengirim revisi untuk request ini?',
        type: 'warning',
        confirmText: 'Ya, Revisi',
        showTextarea: false,
        onConfirm: function () {

            const komentar = $('#reviewKomentar').val() || '';

            $('#actionBox').empty();
            $('#requestDetailContent').html(`
                <div class="alert alert-warning d-flex align-items-center">
                    <span class="spinner-border spinner-border-sm me-2"></span>
                    Mengirim revisi...
                </div>
            `);

            $.post('lihatRequest.php', {
                action: 'review',
                ReqID: reqID,
                Status: 'Revisi',
                Komentar: komentar
            }, function () {

                $('#requestDetailContent').html(`
                    <div class="alert alert-warning">
                        Request berhasil direvisi
                    </div>
                `);

                setTimeout(() => {
                    $('#requestDetailModal').modal('hide');
                    location.reload();
                }, 700);
            });
        }
    });
});


$(document).on('click', '.btn-action-cancel', function () {
    const reqID = $(this).data('reqid');

    openActionBox({
        title: 'Konfirmasi Pembatalan',
        message: 'Apakah Anda yakin ingin membatalkan permintaan ini?',
        type: 'danger',
        confirmText: 'Ya, Batalkan',
        onConfirm: function () {

            $('#actionBox').empty();
            $('#requestDetailContent').html(`
                <div class="alert alert-warning d-flex align-items-center">
                    <span class="spinner-border spinner-border-sm me-2"></span>
                    Membatalkan permintaan...
                </div>
            `);

            $.post('lihatRequest.php', {
                action: 'cancelled',
                ReqID: reqID
            }, function () {

                $('#requestDetailContent').html(`
                    <div class="alert alert-danger">
                        Permintaan berhasil dibatalkan
                    </div>
                `);

                setTimeout(() => {
                    $('#requestDetailModal').modal('hide');
                    location.reload();
                }, 700);
            }).fail(function () {

                $('#requestDetailContent').html(`
                    <div class="alert alert-danger">
                        Gagal membatalkan permintaan
                    </div>
                `);
            });
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
