<?php
session_start(); // WAJIB!

define('APP_ACCESS', true);
require_once '../config/config.php';
require_once '../config/database.php';
require_once '../models/Request.php';
require_once '../models/User.php';
require_once '../models/Aplikasi.php';
require_once '../models/Hardware.php';
require_once '../models/Notification.php';
require_once '../Version1/classes/Security.php';

Security::requireUser();
$db = Database::getInstance()->getConnection();

$username = $_SESSION['user_name'] ?? null;

if (!$username) {
    die('User belum login.');
}

// Ambil NIK & Departemen dari karyawan
$stmt = Database::getInstance()->getConnection()->prepare(
    "SELECT NIK, Departemen FROM karyawan WHERE Nama = :nama"
);
$stmt->execute([
    ':nama'  => $_SESSION['user_name'],
]);
$userData = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$userData) {
    die('Data karyawan tidak ditemukan.');
}

$nikSession        = $userData['NIK'];
$departemenSession = $userData['Departemen'];

$pageTitle = "Data Request";
$currentPage = "create-request";

$notifModel = new Notification($db);
$userModel = new User();
$reqModel = new Request();
$apkModel  = new Aplikasi(); 
$hardwareModel = new Hardware();

$listHardware  = $hardwareModel->getAll();
$listAplikasi = $apkModel->getAll(); 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nik        = trim($_POST['NIK'] ?? '');
    $apkID      = $_POST['ApkID'] ?? null;
    $hwID       = $_POST['HwID'] ?? null;
    $departemen = trim($_POST['Departemen'] ?? '');
    $tglReq     = trim($_POST['Tgl_request'] ?? '');
    $prioritas  = trim($_POST['Prioritas'] ?? '');
    $request    = trim($_POST['Request'] ?? '');
    $status     = trim($_POST['StatusReq'] ?? 'Pending');

    $jenisRequest = $_POST['JenisRequest'] ?? '';

    if ($jenisRequest === 'system' && empty($apkID)) {
        die('Aplikasi wajib dipilih.');
    }

    if ($jenisRequest === 'hardware' && empty($hwID)) {
        die('Infrastruktur wajib dipilih.');
    }

    if (!empty($apkID) && !empty($hwID)) {
        die('Request hanya boleh Aplikasi ATAU Infrastruktur.');
    }

    if ($nik === '' || !ctype_digit($nik)) {
        $error = "NIK tidak valid!";
    } elseif ($prioritas === '' || $request === '') {
        $error = "Semua data wajib diisi!";
    } else {

        $db = Database::getInstance()->getConnection();
        if ($apkID === 'lainnya') {
            $namaApkBaru = trim($_POST['NamaApk'] ?? '');

            if ($namaApkBaru === '') {
                die('Nama aplikasi baru wajib diisi!');
            }

            $stmt = $db->prepare("INSERT INTO aplikasi (NamaApk) VALUES (?)");
            $stmt->execute([$namaApkBaru]);

            $apkID = $db->lastInsertId();
        }

        if ($hwID === 'lainnya') {
            $namaHwBaru = trim($_POST['NamaHw'] ?? '');

            if ($namaHwBaru === '') {
                die('Nama hardware baru wajib diisi!');
            }

            $stmt = $db->prepare("INSERT INTO hardware (NamaHw) VALUES (?)");
            $stmt->execute([$namaHwBaru]);

            $hwID = $db->lastInsertId();
        }

        if (!isset($_FILES['Dokumentasi']) || $_FILES['Dokumentasi']['error'] !== UPLOAD_ERR_OK) {
            die('Dokumentasi wajib diupload.');
        }

        $file = $_FILES['Dokumentasi'];

        if ($file['size'] > 2 * 1024 * 1024) {
            die('Ukuran gambar maksimal 2MB.');
        }

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime  = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);

        if ($mime !== 'image/jpeg') {
            die('Format file harus JPG / JPEG.');
        }

        $namaFile = 'req_' . time() . '_' . rand(1000,9999) . '.jpg';
        $uploadDir = __DIR__ . '/../public/images/request/';
        $uploadPath = $uploadDir . $namaFile;
        
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        if (!move_uploaded_file($file['tmp_name'], $uploadPath)) {
            die('Gagal upload dokumentasi.');
        }

       $reqID = $reqModel->create([
        'NIK'          => (int)$nik,
        'ApkID'        => $apkID ?: null,
        'HwID'         => $hwID ?: null,
        'Departemen'  => $departemen,
        'Tgl_request' => $tglReq,
        'Prioritas'   => $prioritas,
        'Request'     => $request,
        'Dokumentasi' => $namaFile,
        'StatusReq'   => $status
    ]);

        $admins = $userModel->getAdmins(); 
        $username    = $_SESSION['user_name'];
        $department  = $departemen;
        $requestType = $apkID ? 'Aplikasi' : 'Hardware';
        $reqDetails  = $request;

        foreach ($admins as $admin) {
        $notifModel->create(
            $admin['NIK'],      
            $nik,
            $reqID,
            'New Request',
            'A new request has been submitted by user ' . $_SESSION['user_name']
        );

        if (!empty($admin['email'])) {
            $pesan = urlencode("
            Hello Admin,<br><br>

            We would like to inform you that a new request has just been successfully submitted through the system.  
            Please review the details below carefully to ensure proper follow-up and processing.<br><br>

            <b>Request Information:</b><br>
            <b>User Name:</b> $username <br>
            <b>Department:</b> $department <br>
            <b>Request Type:</b> $requestType <br>
            <b>Details:</b> $reqDetails <br>
            <b>Priority:</b> $prioritas <br>
            <b>Picture:</b><br> $namaFile <br><br>

            To view and manage this request in full detail, please open the application by clicking the following link:<br>
            <a href='" . BASE_URL . "'><b>Open Application</b></a><br><br>

            Your prompt attention to this request is highly appreciated to ensure smooth operational workflow.<br><br>

            Thank you for your cooperation.<br><br>

            Best regards,<br>
            System Notification
            ");


        $url = BASE_URL."send-notification.php?email="
                . urlencode($admin['email'])
                . "&subject=" . urlencode("New User Request")
                . "&title=" . urlencode("A New Request Has Been Submitted")
                . "&message=" . $pesan;

        file_get_contents($url);
        }
    }

    header("Location: index.php?success=1");
    exit;
    }
}

require_once __DIR__ . '/includes/header.php';
?>

<div class="container-fluid py-4">
    <h2 class="mb-4">Create Request</h2>

    <?php if (!empty($error)): ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>

<form method="POST" enctype="multipart/form-data" class="card p-4 shadow-sm">

    <input type="hidden" name="NIK" value="<?= htmlspecialchars($nikSession); ?>">
    <input type="hidden" name="Departemen" value="<?= htmlspecialchars($departemenSession); ?>">
    <input type="hidden" name="Tgl_request" value="<?= date('Y-m-d H:i:s'); ?>">
    <input type="hidden" name="StatusReq" value="Pending">
    <div class="mb-3">
        <label class="form-label">Request Type</label>
        <select name="JenisRequest" id="jenisRequest" class="form-control" required>
            <option value="">-- Select Type --</option>
            <option value="system">System</option>
            <option value="hardware">Infrastuktur</option>
        </select>
    </div>

  <div id="formLanjutan" class="d-none">

    <div id="formSystem" class="d-none mb-3">
        <label class="form-label">Application</label>
        <select name="ApkID" id="ApkID" class="form-control">
            <option value="">-- Select Application --</option>
            <?php foreach ($listAplikasi as $apk): ?>
                <option value="<?= $apk['ApkID']; ?>">
                    <?= htmlspecialchars($apk['NamaApk']); ?>
                </option>
            <?php endforeach; ?>
            <option value="lainnya">+ Others</option>
        </select>

        <input type="text" name="NamaApk" id="NamaApkBaru"
               class="form-control mt-2 d-none"
               placeholder="Enter application name...">
    </div>

    <div id="formHardware" class="d-none mb-3">
        <label class="form-label">Infrastuktur</label>
        <select name="HwID" id="HwID" class="form-control">
            <option value="">-- Select Infrastuktur --</option>
            <?php foreach ($listHardware as $hw): ?>
                <option value="<?= $hw['HwID']; ?>">
                    <?= htmlspecialchars($hw['NamaHw']); ?>
                </option>
            <?php endforeach; ?>
            <option value="lainnya">+ Others</option>
        </select>

        <input type="text" name="NamaHw" id="NamaHwBaru"
               class="form-control mt-2 d-none"
               placeholder="Enter Infrastuktur name...">
    </div>

    <div class="mb-3">
        <label class="form-label d-block">Priority</label>
        <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" name="Prioritas" value="Low">
            Low
        </div>
        <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" name="Prioritas" value="Normal">
            Normal
        </div>
        <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" name="Prioritas" value="High">
            High
        </div>
    </div>

    <div class="mb-3">
        <label class="form-label">Request</label>
        <textarea name="Request" class="form-control"></textarea>
    </div>

    <div class="mb-3">
        <label class="form-label">
            Documentation Image 
        </label>
        <input type="file"name="Dokumentasi" id="Dokumentasi" class="form-control" accept=".jpg,.jpeg">
        <small class="text-muted">
            <i>Format type: JPG / JPEG (Max. 2 MB)</i>
        </small>
        <img id="previewImg" class="img-thumbnail mt-2 d-none" style="max-width:200px;">
    </div>

    <button class="btn btn-success">Submit</button>
</div>
</form>
<script>
    
    document.getElementById('jenisRequest').addEventListener('change', function () {

        const formLanjutan = document.getElementById('formLanjutan');
        const systemForm  = document.getElementById('formSystem');
        const hardwareForm= document.getElementById('formHardware');

        // Sembunyikan semua dulu
        formLanjutan.classList.add('d-none');
        systemForm.classList.add('d-none');
        hardwareForm.classList.add('d-none');

        if (this.value === 'system') {
            formLanjutan.classList.remove('d-none');
            systemForm.classList.remove('d-none');
        }

        if (this.value === 'hardware') {
            formLanjutan.classList.remove('d-none');
            hardwareForm.classList.remove('d-none');
        }

    });

    document.getElementById('ApkID').addEventListener('change', function () {
        document.getElementById('NamaApkBaru').classList.toggle(
            'd-none', this.value !== 'lainnya'
        );
    });

    document.getElementById('HwID').addEventListener('change', function () {
        document.getElementById('NamaHwBaru').classList.toggle(
            'd-none', this.value !== 'lainnya'
        );
    });

document.querySelector('textarea[name="Request"]').required = true;
document.querySelectorAll('input[name="Prioritas"]').forEach(el => el.required = true);

document.getElementById('Dokumentasi').addEventListener('change', function () {
    const file = this.files[0];
    if (!file) return;

    const reader = new FileReader();
    reader.onload = e => {
        const img = document.getElementById('previewImg');
        img.src = e.target.result;
        img.classList.remove('d-none');
    };
    reader.readAsDataURL(file);
});
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
