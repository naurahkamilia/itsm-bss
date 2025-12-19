# 🛒 Toko Online Hijau - E-Commerce PHP Application

![PHP Version](https://img.shields.io/badge/PHP-8.0%2B-blue)
![MySQL](https://img.shields.io/badge/MySQL-5.7%2B-orange)
![Bootstrap](https://img.shields.io/badge/Bootstrap-5.3-purple)
![License](https://img.shields.io/badge/License-MIT-green)

Aplikasi e-commerce lengkap dengan tema hijau dan biru, sistem admin, integrasi WhatsApp, dan fitur-fitur modern.

## ✨ Fitur Utama

### Frontend (Customer)
- 🏠 **Halaman Produk** dengan grid responsive (2 kolom mobile)
- 🔍 **Pencarian & Filter** produk berdasarkan kategori dan harga
- 📱 **Mobile Responsive** dengan desain modern
- 💰 **Harga Diskon** dengan tampilan harga asli dicoret
- 📦 **Status Stok** (Aktif/Nonaktif) dengan badge
- 🔢 **Pagination** untuk navigasi produk
- 📄 **Detail Produk** dengan deskripsi lengkap
- 💬 **WhatsApp Order** dengan pesan otomatis terformat
- 📑 **Halaman Kategori** dengan product count
- ℹ️ **Tentang Toko** dan **Kontak** page

### Admin Panel
- 🎯 **Dashboard** dengan statistik produk
- ➕ **CRUD Produk** (Create, Read, Update, Delete)
- 📂 **Manajemen Kategori** 
- 🔄 **Toggle Status Produk** (Aktif/Nonaktif)
- 🔐 **Login dengan Captcha** untuk keamanan
- 🛡️ **Proteksi CSRF & XSS**
- ⚙️ **Pengaturan** toko dan konfigurasi

### Keamanan
- 🔒 **CSRF Protection**
- 🛡️ **XSS Protection** dengan input sanitization
- 🔑 **Password Hashing** dengan Argon2ID
- 📝 **SQL Injection Protection** dengan PDO prepared statements
- 🤖 **Captcha** di halaman login
- ⏱️ **Rate Limiting** untuk login attempts

## 🚀 Instalasi Cepat (5 Menit)

### 1️⃣ Copy ke XAMPP
```bash
# Windows
C:\xampp\htdocs\toko-online\

# Mac/Linux
/Applications/XAMPP/htdocs/toko-online/
```

### 2️⃣ Buat Database
1. Start **MySQL** di XAMPP Control Panel
2. Buka http://localhost/phpmyadmin
3. Klik **"New"** → Database name: `toko_online`
4. Collation: `utf8mb4_unicode_ci`
5. Klik **"Create"**

### 3️⃣ Import Database
1. Klik database `toko_online`
2. Tab **"Import"**
3. Choose file: `database/migration.sql`
4. Klik **"Go"**

### 4️⃣ Konfigurasi
Edit file `config/config.php`:

```php
// PENTING: Sesuaikan dengan nama folder Anda!
define('BASE_URL', 'http://localhost/toko-online/');

// Ganti dengan nomor WhatsApp Anda
define('WHATSAPP_NUMBER', '6281234567890'); // Format: 62xxx
```

### 5️⃣ Setup Check
Buka: http://localhost/toko-online/setup-check.php

✅ Pastikan semua hijau!

### 6️⃣ Buat Placeholder Image
Buka: http://localhost/toko-online/create-placeholder.php

### 7️⃣ Akses Aplikasi

**Frontend:**
- Homepage: http://localhost/toko-online/
- Kategori: http://localhost/toko-online/categories.php
- Tentang: http://localhost/toko-online/about.php
- Kontak: http://localhost/toko-online/contact.php

**Admin:**
- Login: http://localhost/toko-online/login.php
- Email: `admin@tokoonline.com`
- Password: `admin123`

## 📁 Struktur Folder

```
toko-online/
├── config/
│   ├── config.php          # Konfigurasi utama
│   └── database.php        # Database connection
├── classes/
│   └── Security.php        # Security helper
├── models/
│   ├── Product.php         # Product model
│   ├── Category.php        # Category model
│   └── User.php            # User model
├── includes/
│   ├── header.php          # Header template
│   └── footer.php          # Footer template
├── admin/
│   ├── index.php           # Admin dashboard
│   ├── products.php        # Manage products
│   ├── categories.php      # Manage categories
│   └── settings.php        # Settings page
├── public/
│   ├── css/
│   │   └── style.css       # Custom styles
│   ├── js/
│   │   └── script.js       # Custom JavaScript
│   ├── images/             # Images folder
│   └── uploads/            # Upload folder
├── database/
│   └── migration.sql       # Database schema & data
├── index.php               # Homepage (products)
├── product.php             # Product detail
├── categories.php          # Categories page
├── about.php               # About page
├── contact.php             # Contact page
├── login.php               # Admin login
├── logout.php              # Logout
├── .htaccess               # Apache config
└── README.md               # This file
```

## ⚙️ Konfigurasi

### BASE_URL
```php
// Jika folder ada di subdirektori
define('BASE_URL', 'http://localhost/projects/toko-online/');

// Jika menggunakan port custom
define('BASE_URL', 'http://localhost:8080/toko-online/');
```

### WhatsApp Number
```php
// Format: 62 + nomor tanpa 0 di depan
// Contoh: 0812-3456-7890 → 6281234567890
define('WHATSAPP_NUMBER', '6281234567890');
```

### Database
```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'toko_online');
define('DB_USER', 'root');
define('DB_PASS', ''); // Default XAMPP kosong
```

## 🔧 Troubleshooting

### ❌ Database Connection Failed
```bash
✅ Solusi:
1. Pastikan MySQL running di XAMPP
2. Cek database 'toko_online' sudah dibuat
3. Cek file migration.sql sudah di-import
4. Cek DB_PASS di config.php (default kosong)
```

### ❌ Gambar Tidak Muncul
```bash
✅ Solusi:
1. Jalankan create-placeholder.php
2. Pastikan internet aktif (gambar dari Unsplash)
3. Cek folder public/images/ ada
4. Cek folder public/uploads/ ada
```

### ❌ Login Error
```bash
✅ Solusi:
1. Pastikan database sudah di-import
2. Cek tabel 'users' ada data admin
3. Clear browser cache (Ctrl+Shift+Delete)
4. Email: admin@tokoonline.com
5. Password: admin123
```

### ❌ Page Not Found / 404
```bash
✅ Solusi:
1. Cek BASE_URL di config/config.php
   define('BASE_URL', 'http://localhost/[folder-name]/');
   
2. Pastikan ada slash (/) di akhir URL
   
3. Cek .htaccess RewriteBase:
   RewriteBase /[folder-name]/
```

### ❌ Captcha Tidak Muncul
```bash
✅ Solusi:
1. Hapus file di C:\xampp\tmp\
2. Restart Apache di XAMPP
3. Refresh browser (Ctrl+F5)
```

### ❌ Port 80/3306 Sudah Digunakan
```bash
✅ Solusi Port 80 (Apache):
1. XAMPP → Config → Service and Port Settings
2. Ubah Main Port: 80 → 8080
3. Edit BASE_URL: http://localhost:8080/toko-online/

✅ Solusi Port 3306 (MySQL):
1. Stop MySQL service di Windows
2. Atau ubah port MySQL di XAMPP ke 3307
3. Edit DB_HOST: localhost:3307
```

## 💡 Tips & Trik

### Menambah Produk
```sql
-- Via phpMyAdmin → SQL
INSERT INTO products (category_id, name, description, price, discount_price, stock, is_active, image) 
VALUES (
    1, 
    'Nama Produk', 
    'Deskripsi produk...', 
    100000, 
    85000, 
    50, 
    1, 
    'https://images.unsplash.com/photo-xxxxx?w=800'
);
```

### Menambah Kategori
```sql
INSERT INTO categories (name, description, is_active) 
VALUES ('Nama Kategori', 'Deskripsi kategori', 1);
```

### Ganti Password Admin
```sql
-- Password baru: newpassword123
UPDATE users 
SET password = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi' 
WHERE email = 'admin@tokoonline.com';
```

### Mencari Gambar Produk
1. Buka https://unsplash.com/
2. Cari gambar produk yang sesuai
3. Klik gambar → Download → Copy URL
4. Gunakan format: `https://images.unsplash.com/photo-xxxxx?w=800`

## 📊 Database Schema

### Tabel: products
- `id` - Primary key
- `category_id` - Foreign key to categories
- `name` - Nama produk
- `slug` - URL-friendly name
- `description` - Deskripsi produk
- `price` - Harga normal
- `discount_price` - Harga diskon (nullable)
- `stock` - Jumlah stok
- `is_active` - Status (1=aktif, 0=nonaktif)
- `image` - URL gambar
- `created_at` - Timestamp
- `updated_at` - Timestamp

### Tabel: categories
- `id` - Primary key
- `name` - Nama kategori
- `slug` - URL-friendly name
- `description` - Deskripsi (nullable)
- `image` - URL gambar (nullable)
- `is_active` - Status

### Tabel: users
- `id` - Primary key
- `name` - Nama user
- `email` - Email (unique)
- `password` - Hashed password
- `role` - admin/customer
- `email_verified` - Status verifikasi

## 🎨 Customization

### Mengubah Warna
Edit file `public/css/style.css`:

```css
:root {
    --primary-green: #059669;  /* Ganti hijau */
    --primary-blue: #2563eb;   /* Ganti biru */
}
```

### Mengubah Nama Toko
Edit `config/config.php`:

```php
define('APP_NAME', 'Nama Toko Anda');
```

### Mengubah Jumlah Produk per Halaman
```php
define('ITEMS_PER_PAGE', 16); // Default: 12
```

## 🔐 Security Notes

### Production Checklist
```php
// 1. Ubah environment ke production
define('APP_ENV', 'production');

// 2. Ganti password admin
// Via phpMyAdmin atau admin panel

// 3. Enable HTTPS
// Uncomment di .htaccess

// 4. Set secure cookie
ini_set('session.cookie_secure', 1);

// 5. Ganti database password
define('DB_PASS', 'strong-password-here');
```

## 📚 Teknologi yang Digunakan

- **PHP 8.0+** - Backend language
- **MySQL 5.7+** - Database
- **Bootstrap 5.3** - CSS framework
- **Bootstrap Icons** - Icon library
- **PDO** - Database abstraction
- **Argon2ID** - Password hashing
- **JavaScript ES6** - Frontend interactivity

## 📝 License

MIT License - Bebas digunakan untuk proyek pribadi atau komersial.

## 🤝 Support

Jika ada masalah:
1. Cek file `INSTALL.md` untuk panduan lengkap
2. Cek file `README-QUICK-START.md` untuk panduan cepat
3. Jalankan `setup-check.php` untuk verifikasi instalasi

## 📸 Screenshots

### Homepage
- Grid produk 2 kolom di mobile
- Filter & pencarian produk
- Harga diskon dengan badge

### Admin Panel
- Dashboard dengan statistik
- Manajemen produk & kategori
- Toggle status produk

### WhatsApp Integration
- Order langsung via WhatsApp
- Pesan otomatis terformat
- Floating WhatsApp button

---

**Made with ❤️ using PHP & Bootstrap**

Version: 1.0.0 | Last Updated: 2024
