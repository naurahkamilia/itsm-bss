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
    <div class="row justify-content-center">
        <div class="col-lg-8 col-xl-7">

            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h3 class="fw-semibold mb-1">Edit Request</h3>
                    <p class="text-muted small mb-0">
                        Update your existing support request
                    </p>
                </div>

                <a href="lihatRequest.php" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-arrow-left me-1"></i> Back
                </a>
            </div>

            <?php if ($error): ?>
                <div class="alert alert-danger"><?= $error ?></div>
            <?php endif; ?>

            <form method="POST" enctype="multipart/form-data"
                  class="card border-0 shadow-sm">

                <div class="card-body p-4">

                    <!-- Request Type -->
                    <div class="mb-4">
                        <label class="form-label fw-medium">Request Type</label>
                        <select name="JenisRequest" id="jenisRequest"
                                class="form-select" required>
                            <option value="">Select type</option>
                            <option value="system"
                                <?= $req['ApkID'] ? 'selected' : '' ?>>
                                System
                            </option>
                            <option value="hardware"
                                <?= $req['HwID'] ? 'selected' : '' ?>>
                                Infrastructure
                            </option>
                        </select>
                    </div>

                    <div id="formLanjutan">

                        <!-- System -->
                        <div id="formSystem"
                             class="<?= $req['ApkID'] ? '' : 'd-none' ?> mb-4">
                            <label class="form-label fw-medium">Application</label>
                            <select name="ApkID" class="form-select">
                                <option value="">Select application</option>
                                <?php foreach ($listAplikasi as $apk): ?>
                                    <option value="<?= $apk['ApkID']; ?>"
                                        <?= $req['ApkID'] == $apk['ApkID'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($apk['NamaApk']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Hardware -->
                        <div id="formHardware"
                             class="<?= $req['HwID'] ? '' : 'd-none' ?> mb-4">
                            <label class="form-label fw-medium">Infrastructure</label>
                            <select name="HwID" class="form-select">
                                <option value="">Select infrastructure</option>
                                <?php foreach ($listHardware as $hw): ?>
                                    <option value="<?= $hw['HwID']; ?>"
                                        <?= $req['HwID'] == $hw['HwID'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($hw['NamaHw']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Priority -->
                        <div class="mb-4">
                            <label class="form-label fw-medium d-block">Priority</label>
                            <div class="d-flex gap-4">
                                <div class="form-check">
                                    <input class="form-check-input"
                                           type="radio"
                                           name="Prioritas"
                                           value="Low"
                                           <?= $req['Prioritas'] == 'Low' ? 'checked' : '' ?>>
                                    <label class="form-check-label">Low</label>
                                </div>

                                <div class="form-check">
                                    <input class="form-check-input"
                                           type="radio"
                                           name="Prioritas"
                                           value="Normal"
                                           <?= $req['Prioritas'] == 'Normal' ? 'checked' : '' ?>>
                                    <label class="form-check-label">Normal</label>
                                </div>

                                <div class="form-check">
                                    <input class="form-check-input"
                                           type="radio"
                                           name="Prioritas"
                                           value="High"
                                           <?= $req['Prioritas'] == 'High' ? 'checked' : '' ?>>
                                    <label class="form-check-label">High</label>
                                </div>
                            </div>
                        </div>

                        <!-- Request -->
                        <div class="mb-4">
                            <label class="form-label fw-medium">Request Details</label>
                            <textarea name="Request"
                                      class="form-control"
                                      rows="4"
                                      required><?= htmlspecialchars($req['Request']) ?></textarea>
                        </div>

                        <!-- Documentation -->
                        <div class="mb-4">
                            <label class="form-label fw-medium">Documentation Image</label>

                            <?php if (!empty($req['Dokumentasi'])): ?>
                                <div class="mb-3">
                                    <img src="<?= BASE_URL ?>public/images/request/<?= $req['Dokumentasi'] ?>"
                                         class="img-thumbnail"
                                         style="max-width:220px;">
                                </div>
                                <small class="text-muted d-block mb-2">
                                    Leave empty if you don’t want to change the image
                                </small>
                            <?php endif; ?>

                            <input type="file"
                                   name="Dokumentasi"
                                   class="form-control"
                                   accept=".jpg,.jpeg">
                        </div>

                    </div>
                </div>

                <!-- Footer -->
                <div class="card-footer bg-transparent border-0 text-end px-4 pb-4">
                    <a href="lihatRequest.php"
                       class="btn btn-outline-secondary me-2">
                        Cancel
                    </a>
                    <button class="btn btn-primary px-4">
                        Update Request
                    </button>
                </div>

            </form>

        </div>
    </div>
</div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
