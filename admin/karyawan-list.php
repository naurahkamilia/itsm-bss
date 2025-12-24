<?php
define('APP_ACCESS', true);
require_once '../config/config.php';
require_once '../config/database.php';
require_once '../models/Karyawan.php';

Security::requireAdmin();

$pageTitle = "Data Employees";
$currentPage = "karyawans";

$karyawanModel = new Karyawan();

$limit  = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
$page   = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

$filters = [];

if (!empty($_GET['search'])) {
    $filters['search'] = $_GET['search'];
}

// Total karyawan
$totalData  = $karyawanModel->count($filters);
$totalPages = ceil($totalData / $limit);

// Ambil data karyawan
$filters['limit']  = $limit;
$filters['offset'] = $offset;

$karyawans = $karyawanModel->getAll($filters);

require_once __DIR__ . '/includes/header.php';
?>

<div class="container-fluid py-4">

    <!-- HEADER -->
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h2>Employees</h2>
        <a href="karyawan-add.php" class="btn btn-primary">
            <i class="bi bi-person-plus"></i> Add Employee
        </a>
    </div>

    <!-- ALERT -->
    <div class="mb-3">
        <?php if (!empty($_GET['success'])): ?>
            <div class="alert alert-success">Employee added successfully.</div>
        <?php endif; ?>
        <?php if (!empty($_GET['updated'])): ?>
            <div class="alert alert-primary">Employee updated successfully.</div>
        <?php endif; ?>
        <?php if (!empty($_GET['deleted'])): ?>
            <div class="alert alert-danger">Employee deleted successfully.</div>
        <?php endif; ?>
    </div>

    <!-- SEARCH -->
    <form method="GET" class="mb-4">
        <div class="input-group">
            <input type="text" name="search" class="form-control" placeholder="Search employee..." value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
            <button class="btn btn-primary"><i class="bi bi-search"></i></button>
        </div>
    </form>

    <!-- TABLE -->
    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th width="120">NIK</th>
                        <th>Employee Name</th>
                        <th>Position</th>
                        <th>Department</th>
                        <th>Date Joined</th>
                        <th>Plant</th>
                        <th width="120">Action</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (empty($karyawans)): ?>
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">No employees found</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($karyawans as $k): ?>
                        <tr>
                            <td><?= htmlspecialchars($k['NIK']); ?></td>
                            <td><?= htmlspecialchars($k['Nama']); ?></td>
                            <td><?= htmlspecialchars($k['Jabatan']); ?></td>
                            <td><?= htmlspecialchars($k['Departemen']); ?></td>
                            <td><?= htmlspecialchars($k['Tgl_masuk_kerja']); ?></td>
                            <td><?= htmlspecialchars($k['Plant']); ?></td>
                            <td>
                                <a href="karyawan-edit.php?id=<?= $k['NIK']; ?>" class="btn btn-sm btn-outline-secondary me-1">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <button type="button"
                                        class="btn btn-sm btn-outline-danger btn-delete-trigger"
                                        data-bs-toggle="modal"
                                        data-bs-target="#deleteModal"
                                        data-nik="<?= $k['NIK']; ?>"
                                        data-nama="<?= htmlspecialchars($k['Nama']); ?>">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- PAGINATION -->
    <?php if ($totalPages > 1): ?>
    <nav class="mt-3 d-flex justify-content-center">
        <ul class="pagination pagination-sm mb-0">
            <?php
            $prevParams = $_GET; $prevParams['page'] = max(1, $page - 1);
            $nextParams = $_GET; $nextParams['page'] = min($totalPages, $page + 1);
            ?>
            <li class="page-item <?= ($page <= 1) ? 'disabled' : '' ?>">
                <a class="page-link rounded-pill px-2" href="?<?= http_build_query($prevParams) ?>">&laquo;</a>
            </li>

            <?php for ($i = 1; $i <= $totalPages; $i++):
                $params = $_GET; $params['page'] = $i;
            ?>
                <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
                    <a class="page-link rounded-pill px-3 py-1" href="?<?= http_build_query($params) ?>"><?= $i ?></a>
                </li>
            <?php endfor; ?>

            <li class="page-item <?= ($page >= $totalPages) ? 'disabled' : '' ?>">
                <a class="page-link rounded-pill px-2" href="?<?= http_build_query($nextParams) ?>">&raquo;</a>
            </li>
        </ul>
    </nav>
    <?php endif; ?>

</div><!-- Modal Delete -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content" style="border-radius:12px; box-shadow:0 4px 20px rgba(0,0,0,0.15); border:none; background-color:#f8f9fa;">

      <!-- Custom Header -->
      <div style="padding:1rem 1.5rem; border-bottom:1px solid #e0e0e0; display:flex; align-items:center; justify-content:space-between;">
        <h5 style="margin:0; font-weight:600; color:#e74c3c;">
          <i class="bi bi-exclamation-circle me-2"></i>Confirm Deletion
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <!-- Body -->
      <div style="padding:1rem 1.5rem; font-size:0.95rem; color:#333;">
        Are you sure you want to delete <strong id="employeeName"></strong>?
      </div>

      <!-- Footer -->
      <div style="padding:1rem 1.5rem; display:flex; justify-content:flex-end; gap:0.5rem; border-top:1px solid #e0e0e0;">
        <button type="button" class="btn btn-light" data-bs-dismiss="modal" style="border-radius:6px; border:1px solid #ccc;">Cancel</button>
        <a href="#" id="confirmDelete" class="btn" style="background-color:#e74c3c; color:#fff; border-radius:6px; padding:0.5rem 1rem;">Delete</a>
      </div>

    </div>
  </div>
</div>


<!-- JS untuk modal -->
<script>
document.querySelectorAll('.btn-delete-trigger').forEach(btn => {
    btn.addEventListener('click', function() {
        const nik = this.dataset.nik;
        const nama = this.dataset.nama;
        document.getElementById('employeeName').textContent = nama;
        document.getElementById('confirmDelete').href = 'karyawan-delete.php?id=' + nik;
    });
});
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
