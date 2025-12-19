        </main>
    </div>

    <!-- Footer -->
    <footer class="admin-footer">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-6">
                    <small class="text-muted">
                        &copy; <?php echo date('Y'); ?> <?php echo APP_NAME; ?>. Information Technology Service Management.
                    </small>
                </div>
                <div class="col-md-6 text-md-end">
                    <small class="text-muted">
                        Version <?php echo APP_VERSION; ?>
                    </small>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom JS -->
    <script src="<?php echo BASE_URL; ?>public/js/script.js"></script>
    <script src="<?php echo BASE_URL; ?>admin/js/admin.js"></script>
    
    <?php if (isset($_SESSION['success_message'])): ?>
    <script>
        showToast('<?php echo addslashes($_SESSION['success_message']); ?>', 'success');
    </script>
    <?php 
        unset($_SESSION['success_message']);
    endif; 
    ?>
    
    <?php if (isset($_SESSION['error_message'])): ?>
    <script>
        showToast('<?php echo addslashes($_SESSION['error_message']); ?>', 'error');
    </script>
    <?php 
        unset($_SESSION['error_message']);
    endif; 
    ?>
</body>
</html>
