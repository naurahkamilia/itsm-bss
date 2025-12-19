    <!-- Footer -->
    <footer class="bg-dark text-white mt-5 py-4">
        <div class="container">
            <div class="row g-4">
        
            
            <!-- Copyright -->
            <div class="text-center text-white-50">
                <small>
                    &copy; <?php echo date('Y'); ?> <?php echo APP_NAME; ?>.  Information Technology Service Management. 
                    | Version <?php echo APP_VERSION; ?>
                </small>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom JS -->
    <script src="<?php echo BASE_URL; ?>public/js/script.js"></script>
    
    <?php if (isset($_SESSION['success_message'])): ?>
    <script>
        // Show success message
        alert('<?php echo addslashes($_SESSION['success_message']); ?>');
    </script>
    <?php 
        unset($_SESSION['success_message']);
    endif; 
    ?>
    
    <?php if (isset($_SESSION['error_message'])): ?>
    <script>
        // Show error message
        alert('<?php echo addslashes($_SESSION['error_message']); ?>');
    </script>
    <?php 
        unset($_SESSION['error_message']);
    endif; 
    ?>
</body>
</html>
