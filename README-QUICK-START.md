# 🚀 Quick Start Guide - XAMPP Local

## ⚡ Panduan Cepat (5 Menit)

### 1️⃣ Copy Folder ke XAMPP
```bash
# Copy folder php-app ke
C:\xampp\htdocs\toko-online
```

### 2️⃣ Buat Database
1. Jalankan **MySQL** di XAMPP Control Panel
2. Buka **phpMyAdmin**: http://localhost/phpmyadmin
3. Klik **"New"** → Database name: `toko_online`
4. Klik **"Create"**

### 3️⃣ Import Database
1. Klik database `toko_online`
2. Klik tab **"Import"**
3. Choose file: `C:\xampp\htdocs\toko-online\database\migration.sql`
4. Klik **"Go"**

### 4️⃣ Edit Konfigurasi
Edit file: `config/config.php`

```php
define('BASE_URL', 'http://localhost/toko-online/');
define('WHATSAPP_NUMBER', '6281234567890'); // Ganti dengan nomor WA Anda
```

### 5️⃣ Jalankan Setup Check
Buka browser: http://localhost/toko-online/setup-check.php

✅ Jika semua hijau, lanjut ke langkah berikutnya!

### 6️⃣ Buat Placeholder Image
Buka: http://localhost/toko-online/create-placeholder.php

### 7️⃣ Akses Aplikasi
- **Homepage**: http://localhost/toko-online/
- **Login Admin**: http://localhost/toko-online/login.php
  - Email: `admin@tokoonline.com`
  - Password: `admin123`

## ❗ Troubleshooting Cepat

### Masalah: Database connection failed
**Solusi:**
```bash
1. Pastikan MySQL running di XAMPP
2. Cek nama database sudah benar: toko_online
3. Cek password di config.php (default kosong)
```

### Masalah: Gambar tidak muncul
**Solusi:**
```bash
1. Jalankan: http://localhost/toko-online/create-placeholder.php
2. Pastikan koneksi internet aktif (gambar produk dari Unsplash)
3. Cek folder public/images/ dan public/uploads/ ada
```

### Masalah: Login error
**Solusi:**
```bash
1. Pastikan database sudah di-import
2. Cek di phpMyAdmin, tabel 'users' ada data admin
3. Clear browser cache (Ctrl+Shift+Delete)
```

### Masalah: Page not found / 404
**Solusi:**
```php
// Edit config/config.php
define('BASE_URL', 'http://localhost/toko-online/'); // Pastikan slash di akhir!

// Edit .htaccess
RewriteBase /toko-online/ // Sesuaikan nama folder
```

## 📱 Ganti Nomor WhatsApp

Edit `config/config.php`:
```php
// Format: 62 + nomor tanpa 0
// Contoh: 0812-3456-7890 menjadi 6281234567890
define('WHATSAPP_NUMBER', '6281234567890');
```

## 🎯 Langkah Selanjutnya

1. ✅ Login ke admin panel
2. ✅ Tambah kategori baru
3. ✅ Tambah produk dengan foto
4. ✅ Test order via WhatsApp
5. ✅ Customize tampilan (opsional)

## 📞 Butuh Bantuan?

Baca file `INSTALL.md` untuk panduan lengkap dan troubleshooting detail.

---
**Selamat Menggunakan Toko Online Hijau! 🎉**
