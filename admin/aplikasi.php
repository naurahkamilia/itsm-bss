<?php
define('APP_ACCESS', true);
require_once '../config/config.php';
require_once '../config/database.php';
require_once '../models/Aplikasi.php';

Security::requireAdmin();

$pageTitle = "";
$currentPage = "aplikasi";

$aplikasiModel = new Aplikasi();

$limit  = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
$page   = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

$filters = [];

if (!empty($_GET['search'])) {
    $filters['search'] = $_GET['search'];
}

// Total applications
$totalData  = $aplikasiModel->count($filters);
$totalPages = ceil($totalData / $limit);

// Get application data
$filters['limit']  = $limit;
$filters['offset'] = $offset;

$aplikasi = $aplikasiModel->getAll($filters);

require_once __DIR__ . '/includes/header.php';
?>

<div class="container-fluid py-4">

    <!-- HEADER -->
    <div class="page-header d-flex align-items-center mb-4">
        <h2>Applications</h2>

        <div class="ms-auto">
            <a href="aplikasi-add.php" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Add Application
            </a>
        </div>
    </div>

    <!-- ALERT -->
    <div class="mb-3">
        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success mb-2">
                Application added successfully.
            </div>
        <?php endif; ?>

        <?php if (isset($_GET['updated'])): ?>
            <div class="alert alert-primary mb-2">
                Application updated successfully.
            </div>
        <?php endif; ?>

        <?php if (isset($_GET['deleted'])): ?>
            <div class="alert alert-danger mb-2">
                Application deleted successfully.
            </div>
        <?php endif; ?>
    </div>

    <!-- SEARCH -->
    <form method="GET" class="search-box mb-4">
        <div class="input-group">
            <input type="text"
                   name="search"
                   class="form-control"
                   placeholder="Search application..."
                   value="<?= $_GET['search'] ?? '' ?>">
            <button class="btn btn-primary">
                <i class="bi bi-search"></i>
            </button>
        </div>
    </form>

    <!-- TABLE -->
    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th width="120">ID</th>
                        <th>Application Name</th>
                        <th width="120">Action</th>
                    </tr>
                </thead>
                <tbody>

                <?php if (empty($aplikasi)): ?>
                    <tr>
                        <td colspan="3" class="text-center text-muted py-4">
                            No applications found
                        </td>
                    </tr>
                <?php endif; ?>

                <?php 
                $no = $offset + 1;
                foreach ($aplikasi as $apk): 
                ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td><?= htmlspecialchars($apk['NamaApk']); ?></td>
                        <td>
                            <a href="aplikasi-edit.php?ApkID=<?= $apk['ApkID']; ?>" class="btn btn-sm btn-outline-secondary me-1">
                                <i class="bi bi-pencil"></i>
                            </a>

                            <button type="button"
                                    class="btn btn-sm btn-outline-danger btn-delete-trigger"
                                    data-bs-toggle="modal"
                                    data-bs-target="#deleteModal"
                                    data-id="<?= $apk['ApkID']; ?>"
                                    data-name="<?= htmlspecialchars($apk['NamaApk']); ?>">
                                <i class="bi bi-trash"></i>
                            </button>
                        </td>

                    </tr>
                <?php endforeach; ?>

                </tbody>
            </table>
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

<!-- Modal Delete -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content" style="border-radius:12px; box-shadow:0 4px 20px rgba(0,0,0,0.15); border:none; background-color:#f8f9fa;">

      <!-- Header -->
      <div style="padding:1rem 1.5rem; border-bottom:1px solid #e0e0e0; display:flex; align-items:center; justify-content:space-between;">
        <h5 style="margin:0; font-weight:600; color:#e74c3c;">
          <i class="bi bi-exclamation-circle me-2"></i>Confirm Deletion
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <!-- Body -->
      <div style="padding:1rem 1.5rem; font-size:0.95rem; color:#333;">
        Are you sure you want to delete <strong id="applicationName"></strong>?
      </div>

      <!-- Footer -->
      <div style="padding:1rem 1.5rem; display:flex; justify-content:flex-end; gap:0.5rem; border-top:1px solid #e0e0e0;">
        <button type="button" class="btn btn-light" data-bs-dismiss="modal" style="border-radius:6px; border:1px solid #ccc;">Cancel</button>
        <a href="#" id="confirmDelete" class="btn" style="background-color:#e74c3c; color:#fff; border-radius:6px; padding:0.5rem 1rem;">Delete</a>
      </div>

    </div>
  </div>
</div>

</div>

<script>
document.querySelectorAll('.btn-delete-trigger').forEach(btn => {
    btn.addEventListener('click', function() {
        const id = this.dataset.id;
        const name = this.dataset.name;

        document.getElementById('applicationName').textContent = name;

        document.getElementById('confirmDelete').href = 'aplikasi-delete.php?ApkID=' + id;
    });
});
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
