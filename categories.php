<?php
/**
 * Categories Page
 */

define('APP_ACCESS', true);
require_once 'config/config.php';
require_once 'config/database.php';
require_once 'models/Category.php';

$currentPage = 'categories';
$pageTitle = 'Kategori Produk - ' . APP_NAME;

// Initialize model
$karyawanModel = new Karyawan();
$categories = $categoryModel->getAllWithProductCount();

include 'includes/header.php';
?>

<main class="py-5">
    <div class="container">
        <!-- Page Header -->
        <div class="text-center mb-5">
            <h1 class="mb-2">Kategori Produk</h1>
            <p class="text-muted">Pilih kategori untuk melihat produk</p>
        </div>

        <!-- Categories Grid -->
        <?php if (count($categories) > 0): ?>
        <div class="row g-4">
            <?php foreach ($categories as $category): ?>
            <div class="col-6 col-md-4 col-lg-3">
                <a href="index.php?category=<?php echo $category['id']; ?>" 
                   class="text-decoration-none">
                    <div class="card category-card h-100 shadow-sm hover-shadow">
                        <!-- Category Image -->
                        <div class="position-relative overflow-hidden" style="height: 200px;">
                            <?php if ($category['image']): ?>
                            <img src="<?php echo htmlspecialchars($category['image']); ?>" 
                                 class="card-img-top h-100 w-100 object-fit-cover" 
                                 alt="<?php echo htmlspecialchars($category['name']); ?>"
                                 onerror="this.src='<?php echo BASE_URL; ?>public/images/placeholder.jpg'">
                            <?php else: ?>
                            <div class="card-img-top h-100 w-100 d-flex align-items-center justify-content-center bg-gradient" 
                                 style="background: linear-gradient(135deg, var(--primary-green) 0%, var(--primary-blue) 100%);">
                                <i class="bi bi-grid-3x3-gap text-white" style="font-size: 4rem;"></i>
                            </div>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Category Info -->
                        <div class="card-body text-center">
                            <h5 class="card-title mb-2">
                                <?php echo htmlspecialchars($category['name']); ?>
                            </h5>
                            
                            <?php if ($category['description']): ?>
                            <p class="card-text text-muted small mb-2">
                                <?php echo htmlspecialchars(substr($category['description'], 0, 80)); ?>
                                <?php echo strlen($category['description']) > 80 ? '...' : ''; ?>
                            </p>
                            <?php endif; ?>
                            
                            <span class="badge bg-success">
                                <?php echo $category['product_count']; ?> Produk
                            </span>
                        </div>
                    </div>
                </a>
            </div>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
        <!-- No Categories -->
        <div class="text-center py-5">
            <i class="bi bi-inbox display-1 text-muted"></i>
            <h3 class="mt-3">Belum ada kategori</h3>
            <p class="text-muted">Kategori produk akan ditampilkan di sini</p>
        </div>
        <?php endif; ?>
    </div>
</main>

<!-- WhatsApp Float Button -->
<a href="https://wa.me/<?php echo WHATSAPP_NUMBER; ?>?text=<?php echo urlencode('Halo Toko Online Hijau, saya ingin bertanya tentang produk.'); ?>" 
   class="whatsapp-float" 
   target="_blank"
   title="Chat via WhatsApp">
    <i class="bi bi-whatsapp"></i>
</a>

<style>
.category-card {
    transition: all 0.3s ease;
    border: none;
}

.category-card:hover {
    transform: translateY(-5px);
}

.hover-shadow {
    transition: box-shadow 0.3s ease;
}

.hover-shadow:hover {
    box-shadow: 0 10px 30px rgba(0,0,0,0.2) !important;
}
</style>

<?php include 'includes/footer.php'; ?>
