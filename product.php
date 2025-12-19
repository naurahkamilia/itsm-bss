<?php
/**
 * Product Detail Page
 */

define('APP_ACCESS', true);
require_once 'config/config.php';
require_once 'config/database.php';
require_once 'models/Product.php';
require_once 'models/Category.php';

$currentPage = 'product';

// Get product by ID
$productId = $_GET['id'] ?? null;

if (!$productId) {
    header('Location: ' . BASE_URL);
    exit;
}

// Initialize models
$productModel = new Product();
$karyawanModel = new Karyawan();

// Get product
$product = $productModel->getById($productId);

if (!$product) {
    header('Location: ' . BASE_URL);
    exit;
}

$pageTitle = $product['name'] . ' - ' . APP_NAME;

// Calculate discount
$discountPercentage = $product['discount_price'] 
    ? round((($product['price'] - $product['discount_price']) / $product['price']) * 100) 
    : 0;
$finalPrice = $product['discount_price'] ?? $product['price'];

// Get related products from same category
$relatedProducts = $productModel->getByCategory($product['category_id'], 4);
// Remove current product from related
$relatedProducts = array_filter($relatedProducts, function($p) use ($productId) {
    return $p['id'] != $productId;
});

include 'includes/header.php';
?>

<main class="py-4">
    <div class="container">
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="<?php echo BASE_URL; ?>" class="text-decoration-none">
                        <i class="bi bi-house-door me-1"></i>Home
                    </a>
                </li>
                <?php if ($product['category_name']): ?>
                <li class="breadcrumb-item">
                    <a href="<?php echo BASE_URL; ?>?category=<?php echo $product['category_id']; ?>" 
                       class="text-decoration-none">
                        <?php echo htmlspecialchars($product['category_name']); ?>
                    </a>
                </li>
                <?php endif; ?>
                <li class="breadcrumb-item active" aria-current="page">
                    <?php echo htmlspecialchars($product['name']); ?>
                </li>
            </ol>
        </nav>

        <!-- Product Detail -->
        <div class="row g-4 mb-5">
            <!-- Product Image -->
            <div class="col-lg-5">
                <div class="card border-0 shadow-sm">
                    <div class="position-relative overflow-hidden" style="height: 500px;">
                        <img src="public/images/<?php echo htmlspecialchars($product['image']); ?>" 
                             class="card-img-top h-100 w-100 object-fit-cover" 
                             alt="<?php echo htmlspecialchars($product['name']); ?>"
                             onerror="this.src='<?php echo BASE_URL; ?>public/images/placeholder.jpg'">
                        
                        <!-- Badges -->
                        <?php if ($discountPercentage > 0): ?>
                        <div class="position-absolute top-0 start-0 m-3">
                            <span class="badge bg-danger fs-6 px-3 py-2">
                                Hemat <?php echo $discountPercentage; ?>%
                            </span>
                        </div>
                        <?php endif; ?>

                        <?php if ($product['is_active']): ?>
                        <div class="position-absolute top-0 end-0 m-3">
                            <span class="badge bg-success fs-6 px-3 py-2">
                                <i class="bi bi-check-circle me-1"></i> Ready Stock
                            </span>
                        </div>
                        <?php else: ?>
                        <div class="position-absolute top-0 end-0 m-3">
                            <span class="badge bg-secondary fs-6 px-3 py-2">
                                Stok Habis
                            </span>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Product Info -->
            <div class="col-lg-7">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">
                        <!-- Category -->
                        <?php if ($product['category_name']): ?>
                        <div class="mb-2">
                            <a href="<?php echo BASE_URL; ?>?category=<?php echo $product['category_id']; ?>" 
                               class="badge bg-primary text-decoration-none">
                                <?php echo htmlspecialchars($product['category_name']); ?>
                            </a>
                        </div>
                        <?php endif; ?>

                        <!-- Product Name -->
                        <h1 class="mb-3">
                            <?php echo htmlspecialchars($product['name']); ?>
                        </h1>

                        <!-- Price -->
                        <div class="mb-4">
                            <?php if ($product['discount_price']): ?>
                            <div class="d-flex align-items-center gap-3 mb-2">
                                <h2 class="text-success mb-0">
                                    Rp <?php echo number_format($product['discount_price'], 0, ',', '.'); ?>
                                </h2>
                                <h4 class="text-muted text-decoration-line-through mb-0">
                                    Rp <?php echo number_format($product['price'], 0, ',', '.'); ?>
                                </h4>
                            </div>
                            <?php else: ?>
                            <h2 class="text-success mb-0">
                                Rp <?php echo number_format($product['price'], 0, ',', '.'); ?>
                            </h2>
                            <?php endif; ?>
                        </div>

                        <hr>

                        <!-- Stock Info -->
                        <div class="mb-4">
                            <h6 class="mb-2">Ketersediaan:</h6>
                            <?php if ($product['is_active']): ?>
                            <div class="d-flex align-items-center gap-2">
                                <i class="bi bi-box-seam text-success"></i>
                                <span class="text-success">Stok tersedia (<?php echo $product['stock']; ?> unit)</span>
                            </div>
                            <?php else: ?>
                            <div class="d-flex align-items-center gap-2">
                                <i class="bi bi-x-circle text-danger"></i>
                                <span class="text-danger">Stok habis</span>
                            </div>
                            <?php endif; ?>
                        </div>

                        <hr>

                        <!-- Description -->
                        <?php if ($product['description']): ?>
                        <div class="mb-4">
                            <h6 class="mb-2">Deskripsi Produk:</h6>
                            <p class="text-muted" style="white-space: pre-line;">
                                <?php echo htmlspecialchars($product['description']); ?>
                            </p>
                        </div>
                        <?php endif; ?>

                        <!-- Order Button -->
                        <?php if ($product['is_active']): ?>
                        <div class="d-grid gap-2">
                            <a href="https://wa.me/<?php echo WHATSAPP_NUMBER; ?>?text=<?php echo urlencode(WHATSAPP_MESSAGE_PREFIX . 
                                "*{$product['name']}*\n" .
                                "Harga: Rp " . number_format($finalPrice, 0, ',', '.') . "\n" .
                                "Link: " . BASE_URL . "product.php?id={$product['id']}\n\n" .
                                "Jumlah: 1\n\n" .
                                "Terima kasih!"); ?>" 
                               class="btn btn-gradient btn-lg"
                               target="_blank">
                                <i class="bi bi-whatsapp me-2"></i>
                                Order via WhatsApp
                            </a>
                            
                            <button type="button" 
                                    class="btn btn-outline-success btn-lg"
                                    onclick="copyProductLink()">
                                <i class="bi bi-share me-2"></i>
                                Bagikan Produk
                            </button>
                        </div>
                        <?php else: ?>
                        <div class="alert alert-warning mb-0">
                            <i class="bi bi-info-circle me-2"></i>
                            Produk ini sedang tidak tersedia. Hubungi kami untuk informasi lebih lanjut.
                        </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Product Features -->
                <div class="card border-0 shadow-sm mt-3">
                    <div class="card-body p-4">
                        <h6 class="mb-3">Keuntungan Berbelanja di Toko Kami</h6>
                        <div class="row g-3">
                            <div class="col-6">
                                <div class="d-flex align-items-start">
                                    <i class="bi bi-shield-check text-success me-2 fs-5"></i>
                                    <small>Produk Original</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="d-flex align-items-start">
                                    <i class="bi bi-truck text-primary me-2 fs-5"></i>
                                    <small>Pengiriman Cepat</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="d-flex align-items-start">
                                    <i class="bi bi-arrow-repeat text-warning me-2 fs-5"></i>
                                    <small>Mudah Retur</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="d-flex align-items-start">
                                    <i class="bi bi-headset text-info me-2 fs-5"></i>
                                    <small>CS Responsif</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Related Products -->
        <?php if (count($relatedProducts) > 0): ?>
        <div class="mt-5">
            <h4 class="mb-4">Produk Terkait</h4>
            <div class="row g-3">
                <?php foreach (array_slice($relatedProducts, 0, 4) as $relatedProduct): 
                    $relatedDiscountPercentage = $relatedProduct['discount_price'] 
                        ? round((($relatedProduct['price'] - $relatedProduct['discount_price']) / $relatedProduct['price']) * 100) 
                        : 0;
                    $relatedFinalPrice = $relatedProduct['discount_price'] ?? $relatedProduct['price'];
                ?>
                <div class="col-6 col-md-3">
                    <div class="card product-card h-100">
                        <div class="position-relative overflow-hidden" style="height: 200px;">
                            <img src="<?php echo htmlspecialchars($relatedProduct['image']); ?>" 
                                 class="card-img-top h-100 w-100 object-fit-cover" 
                                 alt="<?php echo htmlspecialchars($relatedProduct['name']); ?>"
                                 onerror="this.src='<?php echo BASE_URL; ?>public/images/placeholder.jpg'">
                            
                            <?php if ($relatedDiscountPercentage > 0): ?>
                            <div class="badge-container">
                                <span class="badge badge-discount">-<?php echo $relatedDiscountPercentage; ?>%</span>
                            </div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="card-body">
                            <h6 class="card-title mb-2" style="min-height: 2.5rem; line-height: 1.25rem;">
                                <?php echo htmlspecialchars($relatedProduct['name']); ?>
                            </h6>
                            
                            <div class="mb-2">
                                <?php if ($relatedProduct['discount_price']): ?>
                                <div class="price-discount mb-1">
                                    Rp <?php echo number_format($relatedProduct['discount_price'], 0, ',', '.'); ?>
                                </div>
                                <div class="price-original">
                                    Rp <?php echo number_format($relatedProduct['price'], 0, ',', '.'); ?>
                                </div>
                                <?php else: ?>
                                <div class="price-discount">
                                    Rp <?php echo number_format($relatedProduct['price'], 0, ',', '.'); ?>
                                </div>
                                <?php endif; ?>
                            </div>
                            
                            <a href="product.php?id=<?php echo $relatedProduct['id']; ?>" 
                               class="btn btn-gradient btn-sm w-100">
                                <i class="bi bi-eye me-1"></i> Lihat Detail
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
<a href="https://wa.me/<?php echo WHATSAPP_NUMBER; ?>?text=<?php echo urlencode('Halo Toko Online Hijau, saya ingin bertanya tentang ' . $product['name']); ?>" 
   class="whatsapp-float" 
   target="_blank"
   title="Chat via WhatsApp">
    <i class="bi bi-whatsapp"></i>
</a>

<script>
function copyProductLink() {
    const url = window.location.href;
    navigator.clipboard.writeText(url).then(() => {
        alert('Link produk berhasil disalin!');
    }).catch(err => {
        console.error('Failed to copy: ', err);
    });
}
</script>

<?php include 'includes/footer.php'; ?>
