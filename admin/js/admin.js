/**
 * Admin Panel JavaScript
 */

// Toggle sidebar on mobile
function toggleSidebar() {
    const sidebar = document.querySelector('.admin-sidebar');
    sidebar.classList.toggle('show');
}

// Add click event to hamburger icon (if exists)
document.addEventListener('DOMContentLoaded', function() {
    const hamburger = document.querySelector('.navbar-toggler');
    if (hamburger && window.innerWidth < 992) {
        hamburger.addEventListener('click', toggleSidebar);
    }
});

// Close sidebar when clicking outside on mobile
document.addEventListener('click', function(event) {
    const sidebar = document.querySelector('.admin-sidebar');
    const hamburger = document.querySelector('.navbar-toggler');
    
    if (window.innerWidth < 992 && sidebar.classList.contains('show')) {
        if (!sidebar.contains(event.target) && !hamburger.contains(event.target)) {
            sidebar.classList.remove('show');
        }
    }
});

// Image preview for file upload
function previewProductImage(input) {
    const preview = document.getElementById('imagePreview');
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.src = e.target.result;
            preview.style.display = 'block';
        };
        reader.readAsDataURL(input.files[0]);
    }
}

// Confirm delete with custom message
function confirmDelete(itemName = 'item') {
    return confirm(`Apakah Anda yakin ingin menghapus ${itemName} ini? Aksi ini tidak dapat dibatalkan.`);
}

// Toggle product status (active/inactive)
function toggleProductStatus(productId) {
    if (confirm('Apakah Anda yakin ingin mengubah status produk ini?')) {
        // Create form and submit
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = 'products.php?action=toggle&id=' + productId;
        
        const csrfInput = document.createElement('input');
        csrfInput.type = 'hidden';
        csrfInput.name = 'csrf_token';
        csrfInput.value = document.querySelector('meta[name="csrf-token"]')?.content || '';
        
        form.appendChild(csrfInput);
        document.body.appendChild(form);
        form.submit();
    }
}

// Format currency input (remove non-numeric characters)
function formatCurrencyInput(input) {
    let value = input.value.replace(/[^0-9]/g, '');
    input.value = value;
}

// Calculate discount percentage
function calculateDiscount() {
    const price = parseFloat(document.getElementById('price')?.value) || 0;
    const discountPrice = parseFloat(document.getElementById('discount_price')?.value) || 0;
    const discountPercentageEl = document.getElementById('discountPercentage');
    
    if (price > 0 && discountPrice > 0 && discountPrice < price) {
        const percentage = ((price - discountPrice) / price * 100).toFixed(0);
        if (discountPercentageEl) {
            discountPercentageEl.textContent = `Diskon: ${percentage}%`;
            discountPercentageEl.classList.remove('d-none');
        }
    } else {
        if (discountPercentageEl) {
            discountPercentageEl.classList.add('d-none');
        }
    }
}

// Add event listeners for price inputs
document.addEventListener('DOMContentLoaded', function() {
    const priceInput = document.getElementById('price');
    const discountPriceInput = document.getElementById('discount_price');
    
    if (priceInput) {
        priceInput.addEventListener('input', function() {
            formatCurrencyInput(this);
            calculateDiscount();
        });
    }
    
    if (discountPriceInput) {
        discountPriceInput.addEventListener('input', function() {
            formatCurrencyInput(this);
            calculateDiscount();
        });
    }
    
    // Calculate on page load
    calculateDiscount();
});

// Form validation
function validateProductForm() {
    const name = document.getElementById('name')?.value.trim();
    const price = document.getElementById('price')?.value;
    const stock = document.getElementById('stock')?.value;
    const categoryId = document.getElementById('category_id')?.value;
    
    if (!name) {
        alert('Nama produk harus diisi');
        return false;
    }
    
    if (!price || price <= 0) {
        alert('Harga produk harus lebih dari 0');
        return false;
    }
    
    if (!stock || stock < 0) {
        alert('Stok tidak boleh kurang dari 0');
        return false;
    }
    
    if (!categoryId) {
        alert('Kategori harus dipilih');
        return false;
    }
    
    return true;
}

// Copy product link
function copyProductLink(productId) {
    const url = window.location.origin + '/toko-online/product.php?id=' + productId;
    navigator.clipboard.writeText(url).then(() => {
        showToast('Link produk berhasil disalin!', 'success');
    }).catch(err => {
        console.error('Failed to copy: ', err);
        showToast('Gagal menyalin link', 'error');
    });
}

// Auto-save draft (optional feature)
let autoSaveTimeout;
function enableAutoSave() {
    const formInputs = document.querySelectorAll('form input, form textarea, form select');
    formInputs.forEach(input => {
        input.addEventListener('change', function() {
            clearTimeout(autoSaveTimeout);
            autoSaveTimeout = setTimeout(() => {
                // Save to localStorage
                const formData = {};
                formInputs.forEach(inp => {
                    if (inp.name && inp.value) {
                        formData[inp.name] = inp.value;
                    }
                });
                localStorage.setItem('productDraft', JSON.stringify(formData));
                console.log('Draft saved');
            }, 1000);
        });
    });
}

// Load draft from localStorage
function loadDraft() {
    const draft = localStorage.getItem('productDraft');
    if (draft && confirm('Ada draft yang tersimpan. Muat draft?')) {
        const formData = JSON.parse(draft);
        Object.keys(formData).forEach(key => {
            const input = document.querySelector(`[name="${key}"]`);
            if (input && !input.value) {
                input.value = formData[key];
            }
        });
    }
}

// Clear draft
function clearDraft() {
    localStorage.removeItem('productDraft');
}

// Search/Filter products
function filterProducts() {
    const searchInput = document.getElementById('searchProduct');
    const statusFilter = document.getElementById('statusFilter');
    const categoryFilter = document.getElementById('categoryFilter');
    
    if (searchInput || statusFilter || categoryFilter) {
        const params = new URLSearchParams();
        
        if (searchInput && searchInput.value) {
            params.append('search', searchInput.value);
        }
        
        if (statusFilter && statusFilter.value) {
            params.append('status', statusFilter.value);
        }
        
        if (categoryFilter && categoryFilter.value) {
            params.append('category', categoryFilter.value);
        }
        
        window.location.href = 'products.php?' + params.toString();
    }
}

// Bulk actions for products
function handleBulkAction() {
    const action = document.getElementById('bulkAction')?.value;
    const checkboxes = document.querySelectorAll('.product-checkbox:checked');
    
    if (!action) {
        alert('Pilih aksi terlebih dahulu');
        return;
    }
    
    if (checkboxes.length === 0) {
        alert('Pilih minimal satu produk');
        return;
    }
    
    const productIds = Array.from(checkboxes).map(cb => cb.value);
    
    if (confirm(`Apakah Anda yakin ingin ${action} ${productIds.length} produk?`)) {
        // Submit form
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = 'products.php?action=bulk';
        
        const actionInput = document.createElement('input');
        actionInput.type = 'hidden';
        actionInput.name = 'bulk_action';
        actionInput.value = action;
        form.appendChild(actionInput);
        
        productIds.forEach(id => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'product_ids[]';
            input.value = id;
            form.appendChild(input);
        });
        
        document.body.appendChild(form);
        form.submit();
    }
}

// Select all checkboxes
function toggleSelectAll(checkbox) {
    const checkboxes = document.querySelectorAll('.product-checkbox');
    checkboxes.forEach(cb => {
        cb.checked = checkbox.checked;
    });
}

console.log('Admin panel loaded');
