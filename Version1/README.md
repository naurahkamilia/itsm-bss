# 🛒 Toko Online Hijau - E-Commerce Platform

Aplikasi e-commerce lengkap menggunakan **PHP 8**, **MySQL**, **Bootstrap 5**, dan **JavaScript** dengan arsitektur **MVC (Model-View-Controller)**.

## ✨ Fitur Lengkap

### 🎯 Frontend (Customer)
- ✅ **Halaman Produk** - Grid responsive (2 kolom di mobile, 4 kolom di desktop)
- ✅ **Detail Produk** - Informasi lengkap produk dengan gambar besar
- ✅ **WhatsApp Order** - Tombol order langsung ke WhatsApp dengan format pesan otomatis
- ✅ **Kategori** - Filter produk berdasarkan kategori
- ✅ **Pencarian** - Search produk by nama
- ✅ **Sorting** - Urutkan by harga, stok, nama, tanggal
- ✅ **Pagination** - Navigasi halaman produk
- ✅ **Harga Diskon** - Tampilan harga asli dicoret + persentase diskon
- ✅ **Status Stok** - Badge ready/habis
- ✅ **Halaman Tentang** - Informasi toko
- ✅ **Halaman Kontak** - Form kontak
- ✅ **Responsive Design** - Mobile-first dengan Bootstrap 5

### 🔐 Admin Panel
- ✅ **Login dengan Captcha** - Keamanan login dengan captcha
- ✅ **Rate Limiting** - Proteksi brute force attack
- ✅ **Reset Password** - Verifikasi email untuk reset password
- ✅ **CRUD Produk** - Tambah, edit, hapus produk
- ✅ **Manage Kategori** - Kelola kategori produk
- ✅ **Toggle Status** - Aktifkan/nonaktifkan produk
- ✅ **Upload Gambar** - Input URL gambar produk
- ✅ **Harga & Diskon** - Set harga normal dan diskon
- ✅ **Dashboard Stats** - Statistik produk dan stok

### 🔒 Keamanan
- ✅ **CSRF Protection** - Token CSRF di setiap form
- ✅ **XSS Protection** - Input sanitization
- ✅ **SQL Injection Protection** - Prepared statements (PDO)
- ✅ **Password Hashing** - Argon2ID algorithm
- ✅ **Session Security** - HttpOnly, Secure cookies
- ✅ **Login Rate Limiting** - Max 5 attempts per 5 minutes
- ✅ **Captcha Verification** - Anti-bot protection

## 📋 Requirements

- **PHP** >= 8.0
- **MySQL** >= 5.7 atau MariaDB >= 10.3
- **Apache** 2.4 (XAMPP recommended)
- **Extensions**: PDO, pdo_mysql, mbstring, openssl

## 🚀 Instalasi di XAMPP

### 1. Install XAMPP
Download dan install XAMPP dari [https://www.apachefriends.org](https://www.apachefriends.org)

### 2. Clone/Copy Project
```bash
# Copy folder php-app ke dalam htdocs XAMPP
C:\xampp\htdocs\toko-online\
```

### 3. Import Database

1. Buka **phpMyAdmin**: http://localhost/phpmyadmin
2. Buat database baru dengan nama: `toko_online`
3. Import file SQL: `database/migration.sql`
   - Klik database `toko_online`
   - Tab **Import**
   - Pilih file `migration.sql`
   - Klik **Go**

### 4. Konfigurasi Aplikasi

Edit file `config/config.php`:

```php
// Base URL - Sesuaikan dengan lokasi folder Anda
define('BASE_URL', 'http://localhost/toko-online/');

// Database Configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'toko_online');
define('DB_USER', 'root');
define('DB_PASS', ''); // Default XAMPP kosong

// WhatsApp Configuration
define('WHATSAPP_NUMBER', '6281234567890'); // Ganti dengan nomor WA toko Anda
```

### 5. Setup Folder Permissions

Pastikan folder `public/uploads/` memiliki permission write:
```bash
chmod 755 public/uploads/
```

### 6. Jalankan Aplikasi

1. Start **Apache** dan **MySQL** di XAMPP Control Panel
2. Buka browser: http://localhost/toko-online/
3. Login admin: http://localhost/toko-online/admin/

## 🔑 Demo Credentials

### Admin Login
- **Email**: admin@tokoonline.com
- **Password**: admin123

## 📁 Struktur Project

```
toko-online/
├── config/
│   ├── config.php              # Konfigurasi utama
│   └── database.php            # Database connection class
├── models/
│   ├── Product.php             # Model produk
│   ├── Category.php            # Model kategori
│   └── User.php                # Model user
├── classes/
│   └── Security.php            # Security helper class
├── includes/
│   ├── header.php              # Header template
│   └── footer.php              # Footer template
├── admin/
│   ├── index.php               # Admin dashboard
│   ├── products.php            # Manage products
│   └── ...
├── public/
│   ├── css/
│   │   └── style.css           # Custom CSS
│   ├── js/
│   │   └── main.js             # Custom JavaScript
│   ├── images/                 # Images folder
│   └── uploads/                # Upload folder
├── database/
│   └── migration.sql           # SQL migration
├── index.php                   # Homepage (product list)
├── product.php                 # Product detail
├── categories.php              # Categories page
├── about.php                   # About page
├── contact.php                 # Contact page
├── login.php                   # Login page
├── logout.php                  # Logout handler
└── README.md                   # This file
```

## ⚙️ Konfigurasi

### WhatsApp Integration

Edit `config/config.php`:
```php
// Format: 62xxx (tanpa + dan tanpa 0 di depan)
define('WHATSAPP_NUMBER', '6281234567890');
```

### Email Configuration (untuk reset password)

Edit `config/config.php`:
```php
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USER', 'your-email@gmail.com');
define('SMTP_PASS', 'your-app-password'); // Generate di Google Account
```

**Catatan**: Untuk Gmail, gunakan App Password, bukan password biasa.

### Upload Configuration

Edit `config/config.php`:
```php
define('MAX_FILE_SIZE', 5242880); // 5MB
define('ALLOWED_EXTENSIONS', ['jpg', 'jpeg', 'png', 'gif', 'webp']);
```

## 🎨 Customization

### Mengubah Warna Tema

Edit `public/css/style.css`:
```css
:root {
    --primary-green: #059669;  /* Warna hijau utama */
    --primary-blue: #2563eb;   /* Warna biru utama */
    --dark-green: #047857;
    --dark-blue: #1d4ed8;
}
```

### Mengubah Logo/Nama Toko

Edit `config/config.php`:
```php
define('APP_NAME', 'Toko Online Hijau');
```

## 📱 WhatsApp Order Format

Ketika customer klik tombol "Order via WhatsApp", akan terbuka WhatsApp dengan format pesan:

```
Halo *Toko Online Hijau*,

Saya ingin order:

📦 Produk: [Nama Produk]
💰 Harga: Rp [Harga]
🔗 Link: [URL Produk]

Apakah produk ini masih tersedia?

Terima kasih.
```

Format ini bisa diubah di file `product.php`.

## 🔧 Troubleshooting

### Error: "Database connection failed"
- Pastikan MySQL di XAMPP sudah running
- Cek konfigurasi database di `config/config.php`
- Pastikan database `toko_online` sudah dibuat

### Error: "Call to undefined function"
- Pastikan PHP versi >= 8.0
- Aktifkan extension `pdo_mysql` di php.ini

### Gambar tidak muncul
- Pastikan folder `public/uploads/` ada dan writable
- Cek URL gambar di database

### Session error
- Pastikan folder session PHP writable
- Hapus session di `C:\xampp\tmp`

## 🛠️ Development

### Menambah Produk Baru (via Admin)
1. Login ke admin panel
2. Klik "Tambah Produk"
3. Isi form dengan data produk
4. Untuk gambar, gunakan URL gambar online atau upload ke folder uploads/

### Menambah Kategori Baru (via phpMyAdmin)
```sql
INSERT INTO categories (name, slug, description, is_active) 
VALUES ('Kategori Baru', 'kategori-baru', 'Deskripsi kategori', 1);
```

## 🚀 Production Deployment

### 1. Ubah Environment
Edit `config/config.php`:
```php
define('APP_ENV', 'production');
```

### 2. Update Base URL
```php
define('BASE_URL', 'https://yourdomain.com/');
```

### 3. Enable HTTPS
```php
ini_set('session.cookie_secure', 1);
```

### 4. Update .htaccess
```apache
RewriteEngine On
RewriteCond %{HTTPS} off
RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
```

## 📚 API Documentation (Optional)

Aplikasi ini bisa dengan mudah dikonversi menjadi REST API dengan menambahkan endpoint di folder `api/`.

## 🤝 Contributing

Silakan fork project ini dan kirim pull request untuk improvement.

## 📝 Lisensi

Project ini dibuat untuk keperluan pembelajaran dan development.

## 📧 Support

Jika ada pertanyaan atau butuh bantuan:
- Email: info@tokoonline.com
- WhatsApp: +62 812-3456-7890

---

**Developed with ❤️ using PHP 8, MySQL, Bootstrap 5, and JavaScript**

## 🎯 Next Features (Roadmap)

- [ ] Shopping cart
- [ ] Checkout system
- [ ] Payment gateway integration
- [ ] Order management
- [ ] Customer dashboard
- [ ] Product reviews & ratings
- [ ] Wishlist
- [ ] Email notifications
- [ ] Export products to CSV/Excel
- [ ] Multi-image per product
- [ ] Product variants (size, color)
- [ ] Stock alerts
- [ ] Sales reports
- [ ] Coupon/voucher system
