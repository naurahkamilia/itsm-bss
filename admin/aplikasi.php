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

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Application Data</h2>
        <a href="aplikasi-add.php" class="btn btn-success">
            <i class="bi bi-plus-circle"></i> Add Application
        </a>
    </div>

    <!-- Alert -->
    <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success">Application data added successfully!</div>
    <?php endif; ?>

    <?php if (isset($_GET['updated'])): ?>
        <div class="alert alert-primary">Application data updated successfully!</div>
    <?php endif; ?>

    <?php if (isset($_GET['deleted'])): ?>
        <div class="alert alert-danger">Application data deleted successfully!</div>
    <?php endif; ?>

    <!-- SEARCH FORM -->
    <form method="GET" class="mb-3">
        <div class="input-group">
            <input type="text"
                   name="search"
                   class="form-control"
                   placeholder="Search applications..."
                   value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>">
            <button class="btn btn-primary"><i class="bi bi-search"></i> Search</button>
        </div>
    </form>

    <!-- APPLICATION TABLE -->
    <div class="card shadow-sm">
        <div class="card-body p-0">

            <table class="table table-striped table-hover mb-0">
                <thead class="table-dark">
                    <tr>
                        <th width="150">ID</th>
                        <th width="180">Application Name</th>
                        <th width="40">Action</th>
                    </tr>
                </thead>

                <tbody>
                <?php if (empty($aplikasi)): ?>
                    <tr>
                        <td colspan="3" class="text-center py-4 text-muted">
                            No applications found.
                        </td>
                    </tr>
                <?php endif; ?>

                <?php 
                $no = $offset + 1;
                foreach ($aplikasi as $apk): 
                ?>
                    <tr>
                        <td><?= $no++; ?></td>
                        <td><?= htmlspecialchars($apk['NamaApk']); ?></td>
                        <td>
                            <a href="aplikasi-edit.php?ApkID=<?= $apk['ApkID']; ?>" 
                               class="btn btn-sm btn-primary">
                                <i class="bi bi-pencil"></i>
                            </a>

                            <a href="aplikasi-delete.php?ApkID=<?= $apk['ApkID']; ?>"
                               class="btn btn-sm btn-danger"
                               onclick="return confirm('Are you sure you want to delete this application?')">
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
