<?php
/**
 * Admin - Manage Categories
 */

define('APP_ACCESS', true);
require_once '../config/config.php';
require_once '../config/database.php';
require_once '../models/Karyawan.php';

Security::requireAdmin();

$currentPage = 'karyawan';
$pageTitle = 'Kelola Karayawan - Admin Panel';

$karyawanModel = new Karyawan();
$karyawans = $karyawanModel->getAllWithKaryawanCount();

include 'includes/header.php';
?>

<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col">
            <h2>Kelola Karyawan</h2>
            <p class="text-muted">Kelola kategori produk di toko Anda</p>
        </div>
    </div>

    <div class="row g-4">
        <?php foreach ($karyawans as $karyawan): ?>
        <div class="col-md-6 col-lg-4">
            <div class="card h-100 shadow-sm">
                <?php if ($karyawan['image']): ?>
                <img src="<?php echo htmlspecialchars($karyawan['image']); ?>" 
                     class="card-img-top" 
                     style="height: 200px; object-fit: cover;"
                     alt="<?php echo htmlspecialchars($karyawan['name']); ?>"
                     onerror="this.src='<?php echo BASE_URL; ?>public/images/placeholder.jpg'">
                <?php else: ?>
                <div class="card-img-top bg-gradient d-flex align-items-center justify-content-center" 
                     style="height: 200px; background: linear-gradient(135deg, var(--primary-green) 0%, var(--primary-blue) 100%);">
                    <i class="bi bi-grid-3x3-gap text-white" style="font-size: 4rem;"></i>
                </div>
                <?php endif; ?>
                
                <div class="card-body">
                    <h5 class="card-title"><?php echo htmlspecialchars($karyawan['name']); ?></h5>
                    <?php if ($karyawan['description']): ?>
                    <p class="card-text text-muted small">
                        <?php echo htmlspecialchars(substr($karyawan['description'], 0, 100)); ?>
                        <?php echo strlen($karyawan['description']) > 100 ? '...' : ''; ?>
                    </p>
                    <?php endif; ?>
                    
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <span class="badge bg-primary">
                            <?php echo $karyawan['product_count']; ?> Produk
                        </span>
                        <span class="badge <?php echo $karyawan['is_active'] ? 'bg-success' : 'bg-secondary'; ?>">
                            <?php echo $karyawan['is_active'] ? 'Aktif' : 'Nonaktif'; ?>
                        </span>
                    </div>
                </div>
                
                <div class="card-footer bg-transparent">
                    <div class="row g-2">
                        <div class="col-6">
                            <a href="<?php echo BASE_URL; ?>?category=<?php echo $karyawan['id']; ?>" 
                               class="btn btn-sm btn-outline-info w-100"
                               target="_blank">
                                <i class="bi bi-eye me-1"></i> Lihat
                            </a>
                        </div>
                        <div class="col-6">
                            <a href="products.php?category=<?php echo $karyawan['id']; ?>" 
                               class="btn btn-sm btn-outline-primary w-100">
                                <i class="bi bi-box-seam me-1"></i> Produk
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <div class="alert alert-info mt-4">
        <i class="bi bi-info-circle me-2"></i>
        <strong>Info:</strong> Untuk menambah atau mengedit kategori, silakan edit langsung di database melalui phpMyAdmin.
        <a href="http://localhost/phpmyadmin" target="_blank" class="alert-link">Buka phpMyAdmin</a>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
