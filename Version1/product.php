<?php
/**
 * Product Detail Page
 */

define('APP_ACCESS', true);
require_once 'config/config.php';
require_once 'config/database.php';
require_once 'models/Product.php';
require_once 'models/Category.php';

// Get product ID
$productId = $_GET['id'] ?? null;

if (!$productId) {
    header('Location: ' . BASE_URL);
    exit;
}

// Initialize models
$productModel = new Product();
$product = $productModel->getById($productId);

if (!$product) {
    $_SESSION['error_message'] = 'Produk tidak ditemukan.';
    header('Location: ' . BASE_URL);
    exit;
}

$pageTitle = htmlspecialchars($product['name']) . ' - ' . APP_NAME;
$currentPage = 'product';

// Calculate discount
$discountPercentage = $product['discount_price'] 
    ? round((($product['price'] - $product['discount_price']) / $product['price']) * 100) 
    : 0;
$finalPrice = $product['discount_price'] ?? $product['price'];
$savings = $product['discount_price'] ? $product['price'] - $product['discount_price'] : 0;

// Prepare WhatsApp message
$waMessage = "Halo *Toko Online Hijau*,\n\n";
$waMessage .= "Saya ingin order:\n\n";
$waMessage .= "📦 Produk: " . $product['name'] . "\n";
$waMessage .= "💰 Harga: Rp " . number_format($finalPrice, 0, ',', '.') . "\n";
$waMessage .= "🔗 Link: " . BASE_URL . "product.php?id=" . $product['id'] . "\n\n";
$waMessage .= "Apakah produk ini masih tersedia?\n\nTerima kasih.";

include 'includes/header.php';
?>

<main class="py-4">
    <div class="container">
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>">Home</a></li>
                <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>categories.php">Kategori</a></li>
                <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>?category=<?php echo $product['category_id']; ?>">
                    <?php echo htmlspecialchars($product['category_name']); ?>
                </a></li>
                <li class="breadcrumb-item active"><?php echo htmlspecialchars($product['name']); ?></li>
            </ol>
        </nav>

        <div class="card">
            <div class="card-body p-lg-5">
                <div class="row g-4">
                    <!-- Product Image -->
                    <div class="col-lg-6">
                        <div class="position-relative">
                            <img src="<?php echo htmlspecialchars($product['image']); ?>" 
                                 class="img-fluid rounded shadow-sm w-100" 
                                 alt="<?php echo htmlspecialchars($product['name']); ?>"
                                 style="max-height: 500px; object-fit: cover;"
                                 onerror="this.src='<?php echo BASE_URL; ?>public/images/placeholder.jpg'">
                            
                            <!-- Badges on Image -->
                            <div class="position-absolute top-0 start-0 m-3">
                                <?php if (!$product['is_active']): ?>
                                <span class="badge badge-out-of-stock fs-6 shadow">Stok Habis</span>
                                <?php elseif ($discountPercentage > 0): ?>
                                <span class="badge badge-discount fs-6 shadow">DISKON <?php echo $discountPercentage; ?>%</span>
                                <?php endif; ?>
                            </div>
                            
                            <?php if ($product['is_active']): ?>
                            <div class="position-absolute top-0 end-0 m-3">
                                <span class="badge badge-ready fs-6 shadow">Stok Ready</span>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Product Details -->
                    <div class="col-lg-6">
                        <!-- Product Name -->
                        <h1 class="mb-3"><?php echo htmlspecialchars($product['name']); ?></h1>
                        
                        <!-- Category -->
                        <div class="mb-3">
                            <i class="bi bi-tag text-muted me-1"></i>
                            <a href="<?php echo BASE_URL; ?>?category=<?php echo $product['category_id']; ?>" 
                               class="text-decoration-none">
                                <?php echo htmlspecialchars($product['category_name']); ?>
                            </a>
                        </div>

                        <!-- Price -->
                        <div class="card bg-light mb-4">
                            <div class="card-body">
                                <?php if ($product['discount_price']): ?>
                                <div class="d-flex align-items-baseline gap-3 mb-2">
                                    <h2 class="mb-0 text-success">
                                        Rp <?php echo number_format($product['discount_price'], 0, ',', '.'); ?>
                                    </h2>
                                    <span class="badge bg-danger">
                                        Hemat Rp <?php echo number_format($savings, 0, ',', '.'); ?>
                                    </span>
                                </div>
                                <div class="d-flex align-items-center gap-2">
                                    <span class="text-decoration-line-through text-muted h5">
                                        Rp <?php echo number_format($product['price'], 0, ',', '.'); ?>
                                    </span>
                                    <span class="text-danger fw-bold">-<?php echo $discountPercentage; ?>%</span>
                                </div>
                                <?php else: ?>
                                <h2 class="mb-0 text-success">
                                    Rp <?php echo number_format($product['price'], 0, ',', '.'); ?>
                                </h2>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Stock Status -->
                        <div class="card border-primary mb-4">
                            <div class="card-body d-flex align-items-center gap-3">
                                <i class="bi bi-box-seam fs-3 text-primary"></i>
                                <div>
                                    <div class="fw-bold text-muted mb-1">Ketersediaan Stok</div>
                                    <div class="h5 mb-0 <?php echo $product['is_active'] ? 'text-success' : 'text-danger'; ?>">
                                        <?php if ($product['is_active']): ?>
                                            <?php echo $product['stock']; ?> unit tersedia
                                        <?php else: ?>
                                            Stok habis
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Description -->
                        <div class="mb-4">
                            <h5 class="mb-3">Deskripsi Produk</h5>
                            <p class="text-muted lh-lg">
                                <?php echo nl2br(htmlspecialchars($product['description'])); ?>
                            </p>
                        </div>

                        <!-- Product Info -->
                        <div class="card bg-light mb-4">
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-6">
                                        <div class="d-flex align-items-center gap-2">
                                            <i class="bi bi-calendar3 text-muted"></i>
                                            <div>
                                                <small class="text-muted d-block">Ditambahkan</small>
                                                <small class="fw-bold">
                                                    <?php echo date('d M Y', strtotime($product['created_at'])); ?>
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="d-flex align-items-center gap-2">
                                            <i class="bi bi-upc-scan text-muted"></i>
                                            <div>
                                                <small class="text-muted d-block">SKU</small>
                                                <small class="fw-bold">PRD-<?php echo str_pad($product['id'], 4, '0', STR_PAD_LEFT); ?></small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="d-grid gap-3">
                            <?php if ($product['is_active']): ?>
                            <a href="https://wa.me/<?php echo WHATSAPP_NUMBER; ?>?text=<?php echo urlencode($waMessage); ?>" 
                               target="_blank"
                               class="btn btn-whatsapp btn-lg">
                                <i class="bi bi-whatsapp fs-5 me-2"></i>
                                Order via WhatsApp
                            </a>
                            <?php else: ?>
                            <button class="btn btn-secondary btn-lg" disabled>
                                <i class="bi bi-x-circle me-2"></i>
                                Stok Habis
                            </button>
                            <?php endif; ?>
                            
                            <a href="<?php echo BASE_URL; ?>" class="btn btn-outline-primary btn-lg">
                                <i class="bi bi-arrow-left me-2"></i>
                                Kembali ke Produk
                            </a>
                        </div>

                        <!-- Share -->
                        <div class="mt-4">
                            <small class="text-muted d-block mb-2">Bagikan produk ini:</small>
                            <div class="d-flex gap-2">
                                <button onclick="copyToClipboard('<?php echo BASE_URL; ?>product.php?id=<?php echo $product['id']; ?>')" 
                                        class="btn btn-sm btn-outline-secondary"
                                        title="Salin link">
                                    <i class="bi bi-link-45deg"></i>
                                </button>
                                <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode(BASE_URL . 'product.php?id=' . $product['id']); ?>" 
                                   target="_blank"
                                   class="btn btn-sm btn-outline-primary"
                                   title="Share ke Facebook">
                                    <i class="bi bi-facebook"></i>
                                </a>
                                <a href="https://twitter.com/intent/tweet?url=<?php echo urlencode(BASE_URL . 'product.php?id=' . $product['id']); ?>&text=<?php echo urlencode($product['name']); ?>" 
                                   target="_blank"
                                   class="btn btn-sm btn-outline-info"
                                   title="Share ke Twitter">
                                    <i class="bi bi-twitter"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Related Products -->
        <?php
        $relatedProducts = $productModel->getByCategory($product['category_id'], 4);
        $relatedProducts = array_filter($relatedProducts, function($p) use ($productId) {
            return $p['id'] != $productId;
        });
        ?>
        
        <?php if (count($relatedProducts) > 0): ?>
        <div class="mt-5">
            <h3 class="mb-4">Produk Terkait</h3>
            <div class="row g-3">
                <?php foreach (array_slice($relatedProducts, 0, 4) as $relProd): 
                    $relDiscount = $relProd['discount_price'] 
                        ? round((($relProd['price'] - $relProd['discount_price']) / $relProd['price']) * 100) 
                        : 0;
                ?>
                <div class="col-6 col-md-3">
                    <div class="card product-card h-100">
                        <div class="position-relative overflow-hidden" style="height: 200px;">
                            <img src="<?php echo htmlspecialchars($relProd['image']); ?>" 
                                 class="card-img-top h-100 w-100 object-fit-cover" 
                                 alt="<?php echo htmlspecialchars($relProd['name']); ?>"
                                 onerror="this.src='<?php echo BASE_URL; ?>public/images/placeholder.jpg'">
                            
                            <?php if ($relDiscount > 0): ?>
                            <div class="badge-container">
                                <span class="badge badge-discount">-<?php echo $relDiscount; ?>%</span>
                            </div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="card-body">
                            <h6 class="card-title mb-2" style="min-height: 2.5rem;">
                                <?php echo htmlspecialchars($relProd['name']); ?>
                            </h6>
                            <div class="price-discount mb-2">
                                Rp <?php echo number_format($relProd['discount_price'] ?? $relProd['price'], 0, ',', '.'); ?>
                            </div>
                            <a href="product.php?id=<?php echo $relProd['id']; ?>" class="btn btn-gradient btn-sm w-100">
                                Lihat Detail
                            </a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
</main>

<!-- WhatsApp Float Button -->
<a href="https://wa.me/<?php echo WHATSAPP_NUMBER; ?>?text=<?php echo urlencode($waMessage); ?>" 
   class="whatsapp-float" 
   target="_blank"
   title="Order via WhatsApp">
    <i class="bi bi-whatsapp"></i>
</a>

<?php include 'includes/footer.php'; ?>
