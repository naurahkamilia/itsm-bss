<?php
define('APP_ACCESS', true);
require_once '../config/config.php';
require_once '../config/database.php';
require_once '../models/Request.php';
require_once '../models/Aplikasi.php';
require_once '../models/Hardware.php';
require_once '../Version1/classes/Security.php';

Security::requireUser();

$pageTitle   = "Edit Permintaan";
$currentPage = "reqedit";

$reqModel      = new Request();
$apkModel      = new Aplikasi();
$hardwareModel = new Hardware();

$listAplikasi = $apkModel->getAll();
$listHardware = $hardwareModel->getAll();

$error = '';
$success = '';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$req = $reqModel->getById($id);

if (!$req) {
    header('Location: lihatRequest.php?error=notfound');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $jenisRequest = $_POST['JenisRequest'] ?? '';
    $apkID        = $_POST['ApkID'] ?? null;
    $hardwareID   = $_POST['HwID'] ?? null;
    $prioritas    = $_POST['Prioritas'] ?? '';
    $request      = trim($_POST['Request'] ?? '');

    $dokumentasiBaru = $req['Dokumentasi']; 

    if (!empty($_FILES['Dokumentasi']['name'])) {

        $allowedTypes = ['image/jpeg', 'image/jpg'];
        $maxSize = 2 * 1024 * 1024;

        $tmp  = $_FILES['Dokumentasi']['tmp_name'];
        $size = $_FILES['Dokumentasi']['size'];
        $type = mime_content_type($tmp);

        if (!in_array($type, $allowedTypes)) {
            $error = "Dokumentasi harus JPG / JPEG!";
        } elseif ($size > $maxSize) {
            $error = "Ukuran dokumentasi maksimal 2MB!";
        } else {

            $ext = pathinfo($_FILES['Dokumentasi']['name'], PATHINFO_EXTENSION);
            $fileName = 'doc_' . time() . '_' . rand(100,999) . '.' . $ext;

            $uploadDir = __DIR__ . '/../public/images/request/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            // hapus file lama
            if (!empty($req['Dokumentasi']) && file_exists($uploadDir . $req['Dokumentasi'])) {
                unlink($uploadDir . $req['Dokumentasi']);
            }

            move_uploaded_file($tmp, $uploadDir . $fileName);
            $dokumentasiBaru = $fileName;
        }
    }

    if ($jenisRequest === '') {
        $error = "Jenis request wajib dipilih!";
    } elseif ($jenisRequest === 'system' && empty($apkID)) {
        $error = "Aplikasi wajib dipilih!";
    } elseif ($jenisRequest === 'hardware' && empty($hardwareID)) {
        $error = "Hardware wajib dipilih!";
    } elseif ($prioritas === '' || $request === '') {
        $error = "Prioritas dan Request wajib diisi!";
    } else {

        $data = [
            'ApkID'       => $jenisRequest === 'system' ? $apkID : null,
            'HwID'        => $jenisRequest === 'hardware' ? $hardwareID : null,
            'Prioritas'   => $prioritas,
            'Request'     => $request,
            'Dokumentasi' => $dokumentasiBaru,
            'StatusReq'   => $req['StatusReq'] 
        ];

        if ($reqModel->update($id, $data)) {
            header("Location: lihatRequest.php?updated=1");
            exit;
        } else {
            $error = "Gagal mengupdate request!";
        }
    }
}

require_once __DIR__ . '/includes/header.php';
?>

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Edit Request</h2>
        <a href="lihatRequest.php" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>

    <div class="card shadow-sm">
        <div class="card-body">

            <form method="POST" enctype="multipart/form-data">

                <!-- JENIS REQUEST -->
                <div class="mb-3">
                    <label class="form-label">Jenis Request</label>
                    <select name="JenisRequest" id="jenisRequest" class="form-control" required>
                        <option value="">-- Pilih Jenis --</option>
                        <option value="system"   <?= $req['ApkID'] ? 'selected' : '' ?>>System</option>
                        <option value="hardware"<?= $req['HwID'] ? 'selected' : '' ?>>Hardware</option>
                    </select>
                </div>

                <!-- SYSTEM -->
                <div class="mb-3 d-none" id="formSystem">
                    <label class="form-label">Nama Aplikasi</label>
                    <select name="ApkID" class="form-control">
                        <option value="">-- Pilih Aplikasi --</option>
                        <?php foreach ($listAplikasi as $apk): ?>
                            <option value="<?= $apk['ApkID']; ?>"
                                <?= $req['ApkID'] == $apk['ApkID'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($apk['NamaApk']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- HARDWARE -->
                <div class="mb-3 d-none" id="formHardware">
                    <label class="form-label">Nama Hardware</label>
                    <select name="HwID" class="form-control">
                        <option value="">-- Pilih Hardware --</option>
                        <?php foreach ($listHardware as $hw): ?>
                            <option value="<?= $hw['HwID']; ?>"
                                <?= $req['HwID'] == $hw['HwID'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($hw['NamaHw']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- PRIORITAS -->
                <div class="mb-3">
                    <label class="form-label d-block">Prioritas</label>

                    <label class="me-3">
                        <input type="radio" name="Prioritas" value="Low"
                            <?= $req['Prioritas'] == 'Low' ? 'checked' : '' ?>> Low
                    </label>

                    <label class="me-3">
                        <input type="radio" name="Prioritas" value="Normal"
                            <?= $req['Prioritas'] == 'Normal' ? 'checked' : '' ?>> Normal
                    </label>

                    <label>
                        <input type="radio" name="Prioritas" value="High"
                            <?= $req['Prioritas'] == 'High' ? 'checked' : '' ?>> High
                    </label>
                </div>

                <div class="mb-3">
                    <label class="form-label">Request</label>
                    <textarea name="Request" class="form-control" required><?= htmlspecialchars($req['Request']) ?></textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label">Dokumentasi</label>

                    <?php if (!empty($req['Dokumentasi'])): ?>
                        <div class="mb-2">
                            <img src="<?= BASE_URL ?>public/images/request/<?= $req['Dokumentasi'] ?>"
                                alt="Dokumentasi" class="img-thumbnail" style="max-width: 250px;">
                        </div>
                        <small class="text-muted d-block mb-2">
                            Biarkan kosong jika tidak ingin mengganti gambar
                        </small>
                    <?php endif; ?>

                    <input type="file" name="Dokumentasi" class="form-control" accept=".jpg,.jpeg">
                </div>

                <button class="btn btn-primary">
                    <i class="bi bi-pencil-square"></i> Update Request
                </button>

            </form>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
