<?php
/**
 * Home Page - Product Listing
 */

define('APP_ACCESS', true);
require_once 'config/config.php';
require_once 'config/database.php';
require_once 'models/Product.php';
require_once 'models/Category.php';

$currentPage = 'home';
$pageTitle = APP_NAME . ' - Belanja Online Mudah & Terpercaya';

// Initialize models
$productModel = new Product();
$karyawanModel = new Karyawan();

// Get filters from query string
$filters = [
    'category_id' => $_GET['category'] ?? null,
    'search' => Security::clean($_GET['search'] ?? ''),
    'sort' => $_GET['sort'] ?? 'newest',
    'is_active' => 1,
    'limit' => ITEMS_PER_PAGE,
    'offset' => (($_GET['page'] ?? 1) - 1) * ITEMS_PER_PAGE
];

// Get products and count
$products = $productModel->getAll($filters);
$totalProducts = $productModel->count($filters);
$totalPages = ceil($totalProducts / ITEMS_PER_PAGE);
$currentPageNum = $_GET['page'] ?? 1;

// Get categories for filter
$categories = $categoryModel->getAll(true);

// Get selected category name
$selectedCategory = null;
if ($filters['category_id']) {
    $selectedCategory = $categoryModel->getById($filters['category_id']);
}

include 'includes/header.php';
?>

<main class="py-4">
    <div class="container">
        <!-- Page Header -->
        <div class="mb-4">
            <h1 class="mb-2">
                <?php if ($selectedCategory): ?>
                    Kategori: <?php echo htmlspecialchars($selectedCategory['name']); ?>
                <?php else: ?>
                    Semua Produk
                <?php endif; ?>
            </h1>
            <p class="text-muted">Menampilkan <?php echo count($products); ?> dari <?php echo $totalProducts; ?> produk</p>
        </div>

        <!-- Search and Filter -->
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" action="" class="row g-3">
                    <!-- Preserve category filter -->
                    <?php if (isset($_GET['category'])): ?>
                    <input type="hidden" name="category" value="<?php echo htmlspecialchars($_GET['category']); ?>">
                    <?php endif; ?>
                    
                    <!-- Search -->
                    <div class="col-md-6">
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="bi bi-search"></i>
                            </span>
                            <input type="text" 
                                   name="search" 
                                   class="form-control search-box" 
                                   placeholder="Cari produk..." 
                                   value="<?php echo htmlspecialchars($filters['search']); ?>">
                        </div>
                    </div>
                    
                    <!-- Sort -->
                    <div class="col-md-4">
                        <select name="sort" class="form-select" onchange="this.form.submit()">
                            <option value="newest" <?php echo $filters['sort'] == 'newest' ? 'selected' : ''; ?>>Terbaru</option>
                            <option value="price-low" <?php echo $filters['sort'] == 'price-low' ? 'selected' : ''; ?>>Harga Terendah</option>
                            <option value="price-high" <?php echo $filters['sort'] == 'price-high' ? 'selected' : ''; ?>>Harga Tertinggi</option>
                            <option value="stock-high" <?php echo $filters['sort'] == 'stock-high' ? 'selected' : ''; ?>>Stok Terbanyak</option>
                            <option value="stock-low" <?php echo $filters['sort'] == 'stock-low' ? 'selected' : ''; ?>>Stok Tersedikit</option>
                            <option value="name-asc" <?php echo $filters['sort'] == 'name-asc' ? 'selected' : ''; ?>>Nama A-Z</option>
                            <option value="name-desc" <?php echo $filters['sort'] == 'name-desc' ? 'selected' : ''; ?>>Nama Z-A</option>
                        </select>
                    </div>
                    
                    <!-- Submit -->
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-gradient w-100">
                            <i class="bi bi-funnel me-1"></i> Filter
                        </button>
                    </div>
                </form>
                
                <!-- Active Filters -->
                <?php if ($filters['search'] || $selectedCategory): ?>
                <div class="mt-3">
                    <span class="text-muted me-2">Filter aktif:</span>
                    <?php if ($selectedCategory): ?>
                    <a href="index.php" class="badge bg-secondary text-decoration-none me-1">
                        <?php echo htmlspecialchars($selectedCategory['name']); ?>
                        <i class="bi bi-x"></i>
                    </a>
                    <?php endif; ?>
                    
                    <?php if ($filters['search']): ?>
                    <a href="index.php<?php echo $selectedCategory ? '?category=' . $selectedCategory['id'] : ''; ?>" 
                       class="badge bg-secondary text-decoration-none">
                        "<?php echo htmlspecialchars($filters['search']); ?>"
                        <i class="bi bi-x"></i>
                    </a>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Products Grid -->
        <?php if (count($products) > 0): ?>
        <div class="row g-3 mb-4">
            <?php foreach ($products as $product): 
                $discountPercentage = $product['discount_price'] 
                    ? round((($product['price'] - $product['discount_price']) / $product['price']) * 100) 
                    : 0;
                $finalPrice = $product['discount_price'] ?? $product['price'];
            ?>
            <div class="col-6 col-md-4 col-lg-3">
                <div class="card product-card h-100">
                    <!-- Product Image -->
                    <div class="position-relative overflow-hidden" style="height: 250px;">
                        <img src="<?php echo htmlspecialchars($product['image']); ?>" 
                             class="card-img-top h-100 w-100 object-fit-cover" 
                             alt="<?php echo htmlspecialchars($product['name']); ?>"
                             onerror="this.src='<?php echo BASE_URL; ?>public/images/placeholder.jpg'">
                        
                        <!-- Badges -->
                        <div class="badge-container">
                            <?php if (!$product['is_active']): ?>
                            <span class="badge badge-out-of-stock">Stok Habis</span>
                            <?php elseif ($discountPercentage > 0): ?>
                            <span class="badge badge-discount">-<?php echo $discountPercentage; ?>%</span>
                            <?php endif; ?>
                        </div>
                        
                        <?php if ($product['is_active']): ?>
                        <div class="status-badge">
                            <span class="badge badge-ready">Ready</span>
                        </div>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Product Info -->
                    <div class="card-body d-flex flex-column">
                        <h6 class="card-title mb-2" style="min-height: 3rem; line-height: 1.5rem;">
                            <?php echo htmlspecialchars($product['name']); ?>
                        </h6>
                        
                        <!-- Price -->
                        <div class="mb-2">
                            <?php if ($product['discount_price']): ?>
                            <div class="price-discount mb-1">
                                Rp <?php echo number_format($product['discount_price'], 0, ',', '.'); ?>
                            </div>
                            <div class="price-original">
                                Rp <?php echo number_format($product['price'], 0, ',', '.'); ?>
                            </div>
                            <?php else: ?>
                            <div class="price-discount">
                                Rp <?php echo number_format($product['price'], 0, ',', '.'); ?>
                            </div>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Stock -->
                        <p class="text-muted small mb-3">
                            Stok: <?php echo $product['is_active'] ? $product['stock'] : 'Habis'; ?>
                        </p>
                        
                        <!-- Actions -->
                        <div class="mt-auto">
                            <a href="product.php?id=<?php echo $product['id']; ?>" 
                               class="btn btn-gradient btn-sm w-100">
                                <i class="bi bi-eye me-1"></i> Lihat Detail
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        
        <!-- Pagination -->
        <?php if ($totalPages > 1): ?>
        <nav aria-label="Product pagination">
            <ul class="pagination justify-content-center">
                <!-- Previous -->
                <li class="page-item <?php echo $currentPageNum <= 1 ? 'disabled' : ''; ?>">
                    <a class="page-link" href="?page=<?php echo $currentPageNum - 1; ?><?php echo isset($_GET['category']) ? '&category=' . $_GET['category'] : ''; ?><?php echo isset($_GET['search']) ? '&search=' . urlencode($_GET['search']) : ''; ?><?php echo isset($_GET['sort']) ? '&sort=' . $_GET['sort'] : ''; ?>">
                        <i class="bi bi-chevron-left"></i>
                    </a>
                </li>
                
                <!-- Page Numbers -->
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <?php if ($i == 1 || $i == $totalPages || ($i >= $currentPageNum - 1 && $i <= $currentPageNum + 1)): ?>
                    <li class="page-item <?php echo $i == $currentPageNum ? 'active' : ''; ?>">
                        <a class="page-link" href="?page=<?php echo $i; ?><?php echo isset($_GET['category']) ? '&category=' . $_GET['category'] : ''; ?><?php echo isset($_GET['search']) ? '&search=' . urlencode($_GET['search']) : ''; ?><?php echo isset($_GET['sort']) ? '&sort=' . $_GET['sort'] : ''; ?>">
                            <?php echo $i; ?>
                        </a>
                    </li>
                    <?php elseif ($i == $currentPageNum - 2 || $i == $currentPageNum + 2): ?>
                    <li class="page-item disabled">
                        <span class="page-link">...</span>
                    </li>
                    <?php endif; ?>
                <?php endfor; ?>
                
                <!-- Next -->
                <li class="page-item <?php echo $currentPageNum >= $totalPages ? 'disabled' : ''; ?>">
                    <a class="page-link" href="?page=<?php echo $currentPageNum + 1; ?><?php echo isset($_GET['category']) ? '&category=' . $_GET['category'] : ''; ?><?php echo isset($_GET['search']) ? '&search=' . urlencode($_GET['search']) : ''; ?><?php echo isset($_GET['sort']) ? '&sort=' . $_GET['sort'] : ''; ?>">
                        <i class="bi bi-chevron-right"></i>
                    </a>
                </li>
            </ul>
        </nav>
        <?php endif; ?>
        
        <?php else: ?>
        <!-- No Products -->
        <div class="text-center py-5">
            <i class="bi bi-inbox display-1 text-muted"></i>
            <h3 class="mt-3">Tidak ada produk ditemukan</h3>
            <p class="text-muted">Coba ubah filter atau kata kunci pencarian</p>
            <a href="index.php" class="btn btn-gradient">
                <i class="bi bi-arrow-left me-1"></i> Kembali ke Semua Produk
            </a>
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

<?php include 'includes/footer.php'; ?>
