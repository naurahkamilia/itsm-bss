    <!-- Footer -->
    <footer class="mt-5" style="background: linear-gradient(135deg, #059669 0%, #2563eb 100%);">
        <div class="container py-5 text-white">
            <div class="row g-4">
                <div class="col-md-4">
                    <h5 class="mb-3">
                        <i class="bi bi-shop me-2"></i>
                        <?php echo APP_NAME; ?>
                    </h5>
                    <p class="opacity-75">
                        Toko online terpercaya dengan produk berkualitas dan harga terjangkau.
                    </p>
                </div>
                
                <div class="col-md-4">
                    <h5 class="mb-3">Menu</h5>
                    <ul class="list-unstyled">
                        <li class="mb-2">
                            <a href="<?php echo BASE_URL; ?>" class="text-white text-decoration-none opacity-75 hover-opacity-100">
                                <i class="bi bi-chevron-right me-1"></i> Produk
                            </a>
                        </li>
                        <li class="mb-2">
                            <a href="<?php echo BASE_URL; ?>categories.php" class="text-white text-decoration-none opacity-75 hover-opacity-100">
                                <i class="bi bi-chevron-right me-1"></i> Kategori
                            </a>
                        </li>
                        <li class="mb-2">
                            <a href="<?php echo BASE_URL; ?>about.php" class="text-white text-decoration-none opacity-75 hover-opacity-100">
                                <i class="bi bi-chevron-right me-1"></i> Tentang Toko
                            </a>
                        </li>
                        <li class="mb-2">
                            <a href="<?php echo BASE_URL; ?>contact.php" class="text-white text-decoration-none opacity-75 hover-opacity-100">
                                <i class="bi bi-chevron-right me-1"></i> Kontak
                            </a>
                        </li>
                    </ul>
                </div>
                
                <div class="col-md-4">
                    <h5 class="mb-3">Kontak Kami</h5>
                    <ul class="list-unstyled opacity-75">
                        <li class="mb-2">
                            <i class="bi bi-envelope me-2"></i> info@tokoonline.com
                        </li>
                        <li class="mb-2">
                            <i class="bi bi-telephone me-2"></i> +62 812-3456-7890
                        </li>
                        <li class="mb-2">
                            <i class="bi bi-geo-alt me-2"></i> Jl. Contoh No. 123, Jakarta
                        </li>
                    </ul>
                </div>
            </div>
            
            <hr class="my-4 opacity-25">
            
            <div class="text-center opacity-75">
                <p class="mb-0">&copy; <?php echo date('Y'); ?> <?php echo APP_NAME; ?>. All rights reserved.</p>
            </div>
        </div>
    </footer>
    
    <!-- Bootstrap Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom JS -->
    <script src="<?php echo BASE_URL; ?>public/js/main.js"></script>
    
    <?php if (isset($_SESSION['success_message'])): ?>
    <script>
        showAlert('<?php echo addslashes($_SESSION['success_message']); ?>', 'success');
    </script>
    <?php unset($_SESSION['success_message']); endif; ?>
    
    <?php if (isset($_SESSION['error_message'])): ?>
    <script>
        showAlert('<?php echo addslashes($_SESSION['error_message']); ?>', 'danger');
    </script>
    <?php unset($_SESSION['error_message']); endif; ?>
</body>
</html>
