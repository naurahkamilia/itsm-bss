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

// Total kategori
$totalData  = $karyawanModel->count($filters);
$totalPages = ceil($totalData / $limit);

// Ambil data kategori
$filters['limit']  = $limit;
$filters['offset'] = $offset;

$karyawans = $karyawanModel->getAll($filters);

require_once __DIR__ . '/includes/header.php';
?>

<div class="container-fluid py-4">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Data Employees</h2>
        <a href="karyawan-add.php" class="btn btn-success">
            <i class="bi bi-person-add"></i> Add Employee
        </a>
    </div>

    <!-- Alert -->
    <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success">Employee data added successfully!</div>
    <?php endif; ?>

    <?php if (isset($_GET['updated'])): ?>
        <div class="alert alert-primary">Employee data updated successfully!</div>
    <?php endif; ?>

    <?php if (isset($_GET['deleted'])): ?>
        <div class="alert alert-danger">Employee data added successfully!</div>
    <?php endif; ?>

    <!-- SEARCH FORM -->
    <form method="GET" class="mb-3">
        <div class="input-group">
            <input type="text"
                   name="search"
                   class="form-control"
                   placeholder="Search employees......"
                   value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>">
            <button class="btn btn-primary"><i class="bi bi-search"></i> Search</button>
        </div>
    </form>

    <!-- TABLE CATEGORY -->
    <div class="card shadow-sm">
        <div class="card-body p-0">

            <table class="table table-striped table-hover mb-0">
                <thead class="table-dark">
                    <tr>
                        <th width="120">NIK</th>
                        <th width="250">Employee Name</th>
                        <th width="150">Position</th>
                        <th width="190">Department</th>
                        <th width="170">Date Joined</th>
                        <th width="150">Plant</th>
                        <th width="150">Action</th>
                    </tr>
                </thead>

                <tbody>
                <?php if (empty($karyawans)): ?>
                    <tr>
                        <td colspan="7" class="text-center py-4 text-muted">
                            No employees found.
                        </td>
                    </tr>
                <?php endif; ?>

                <?php 
                $no = $offset + 1;
                foreach ($karyawans as $k): 
                ?>
                    <tr>
                        <td><?= htmlspecialchars($k['NIK']); ?></td>
                        <td><?= htmlspecialchars($k['Nama']); ?></td>
                        <td><?= htmlspecialchars($k['Jabatan']); ?></td>
                        <td><?= htmlspecialchars($k['Departemen']); ?></td>
                        <td><?= htmlspecialchars($k['Tgl_masuk_kerja']); ?></td>
                        <td><?= htmlspecialchars($k['Plant']); ?></td>
                        <td>
                            <a href="karyawan-edit.php?id=<?= $k['NIK']; ?>" 
                               class="btn btn-sm btn-primary">
                                <i class="bi bi-pencil"></i>
                            </a>

                            <a href="karyawan-delete.php?id=<?= $k['NIK']; ?>"
                               class="btn btn-sm btn-danger"
                               onclick="return confirm('Yakin ingin menghapus data ini?')">
                                <i class="bi bi-trash"></i>
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>

            </table>

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

</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
