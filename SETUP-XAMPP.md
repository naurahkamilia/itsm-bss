# 🚀 Panduan Setup XAMPP - Toko Online Hijau

## ⚡ Langkah-Langkah Setup (Ikuti Berurutan!)

### ✅ Step 1: Install XAMPP
1. Download XAMPP dari https://www.apachefriends.org/download.html
2. Pilih versi dengan PHP 8.0 atau lebih tinggi
3. Install ke `C:\xampp\` (Windows) atau `/Applications/XAMPP/` (Mac)
4. Jalankan XAMPP Control Panel

### ✅ Step 2: Copy Folder Aplikasi
```
Pindahkan folder 'php-app' ke dalam htdocs:

Windows:
C:\xampp\htdocs\

Rename folder menjadi:
C:\xampp\htdocs\toko-online\

Mac/Linux:
/Applications/XAMPP/htdocs/toko-online/
```

### ✅ Step 3: Start Services
1. Buka **XAMPP Control Panel**
2. Klik **Start** pada **Apache**
3. Klik **Start** pada **MySQL**
4. Pastikan keduanya berwarna HIJAU

### ✅ Step 4: Buat Database
1. Buka browser
2. Akses: http://localhost/phpmyadmin
3. Klik tombol **"New"** di sidebar kiri
4. Database name: `toko_online`
5. Collation: pilih `utf8mb4_unicode_ci`
6. Klik **"Create"**

### ✅ Step 5: Import Database
1. Klik database **toko_online** yang baru dibuat
2. Klik tab **"Import"** di atas
3. Klik **"Choose File"**
4. Pilih file: `C:\xampp\htdocs\toko-online\database\migration.sql`
5. Scroll ke bawah dan klik **"Go"**
6. Tunggu sampai muncul pesan sukses (hijau)

### ✅ Step 6: Verifikasi Database
Di phpMyAdmin, pastikan ada tabel-tabel ini:
- ✅ `categories` (6 records)
- ✅ `products` (24 records)
- ✅ `users` (1 record)
- ✅ `product_images`
- ✅ `settings`

### ✅ Step 7: Edit Konfigurasi
1. Buka file: `C:\xampp\htdocs\toko-online\config\config.php`
2. Edit baris berikut:

```php
// Line 17-18: PENTING! Sesuaikan nama folder
define('BASE_URL', 'http://localhost/toko-online/');
// ^ Pastikan ada SLASH (/) di akhir!

// Line 28: Ganti dengan nomor WhatsApp Anda
define('WHATSAPP_NUMBER', '6281234567890'); 
// Format: 62 + nomor tanpa 0
// Contoh: 0812-3456-7890 → 6281234567890
```

3. **Save** file (Ctrl+S)

### ✅ Step 8: Buat Folder yang Diperlukan
Buka browser dan akses:
```
http://localhost/toko-online/create-folders.php
```
Klik tombol dan pastikan semua folder berhasil dibuat.

### ✅ Step 9: Buat Placeholder Image
Buka browser dan akses:
```
http://localhost/toko-online/create-placeholder.php
```
Anda akan melihat gambar placeholder yang berhasil dibuat.

### ✅ Step 10: Verifikasi Setup
Buka:
```
http://localhost/toko-online/setup-check.php
```

**Pastikan SEMUA checklist berwarna HIJAU!**

Jika ada yang merah:
- Database connection → Ulangi Step 4-5
- Config file → Cek Step 7
- Folders → Ulangi Step 8
- Placeholder → Ulangi Step 9

### ✅ Step 11: Akses Aplikasi

**Frontend (Toko):**
```
Homepage:   http://localhost/toko-online/
Kategori:   http://localhost/toko-online/categories.php
Tentang:    http://localhost/toko-online/about.php
Kontak:     http://localhost/toko-online/contact.php
Produk:     http://localhost/toko-online/product.php?id=1
```

**Admin Panel:**
```
Login:      http://localhost/toko-online/login.php
Dashboard:  http://localhost/toko-online/admin/

Kredensial:
Email:      admin@tokoonline.com
Password:   admin123
```

## 🎉 Selesai!

Jika semua langkah diikuti dengan benar, aplikasi seharusnya sudah berjalan sempurna!

---

## 🔧 Troubleshooting Umum

### ❌ Problem: "Database connection failed"
**Solusi:**
1. Pastikan MySQL di XAMPP sudah running (hijau)
2. Buka phpMyAdmin, pastikan database `toko_online` ada
3. Cek file `config/config.php`, pastikan:
   ```php
   define('DB_NAME', 'toko_online');
   define('DB_PASS', ''); // Harus kosong untuk XAMPP default
   ```

### ❌ Problem: "Page not found" atau 404
**Solusi:**
1. Cek `config/config.php` baris 17:
   ```php
   define('BASE_URL', 'http://localhost/toko-online/');
   ```
   Pastikan:
   - `toko-online` sesuai nama folder Anda di htdocs
   - Ada SLASH (/) di akhir
   
2. Jika folder Anda bukan `toko-online`, ganti:
   ```php
   define('BASE_URL', 'http://localhost/nama-folder-anda/');
   ```

### ❌ Problem: Gambar tidak muncul
**Solusi:**
1. Jalankan: http://localhost/toko-online/create-placeholder.php
2. Pastikan internet aktif (gambar produk dari Unsplash)
3. Refresh halaman dengan Ctrl+F5

### ❌ Problem: Login gagal
**Solusi:**
1. Pastikan database sudah di-import (Step 5)
2. Buka phpMyAdmin → database `toko_online` → tabel `users`
3. Pastikan ada 1 record dengan:
   - email: `admin@tokoonline.com`
   - role: `admin`
4. Gunakan kredensial:
   - Email: `admin@tokoonline.com`
   - Password: `admin123`
5. Isi captcha yang muncul dengan benar

### ❌ Problem: Captcha tidak muncul
**Solusi:**
1. Hapus semua file di folder: `C:\xampp\tmp\`
2. Restart Apache di XAMPP Control Panel
3. Refresh browser dengan Ctrl+Shift+R

### ❌ Problem: Port 80 sudah digunakan
**Solusi:**
1. XAMPP Control Panel → Apache → Config → httpd.conf
2. Cari baris: `Listen 80`
3. Ganti menjadi: `Listen 8080`
4. Save dan restart Apache
5. Edit `config/config.php`:
   ```php
   define('BASE_URL', 'http://localhost:8080/toko-online/');
   ```

### ❌ Problem: Port 3306 (MySQL) sudah digunakan
**Solusi:**
1. Buka Task Manager (Ctrl+Shift+Esc)
2. Cari proses "MySQL" atau "mysqld"
3. End process
4. Restart MySQL di XAMPP

---

## 💡 Tips Penting

### ✅ Selalu Start Services
Sebelum mengakses aplikasi, pastikan Apache dan MySQL di XAMPP sudah running (hijau).

### ✅ Gunakan Browser Modern
Recommended: Chrome, Firefox, Edge (versi terbaru)

### ✅ Clear Cache
Jika ada perubahan tidak muncul:
- Chrome/Edge: Ctrl + Shift + Delete
- Firefox: Ctrl + Shift + Delete

### ✅ Check Error Log
Jika ada error, cek:
```
C:\xampp\apache\logs\error.log
C:\xampp\mysql\data\mysql_error.log
```

### ✅ Backup Database
Backup secara berkala via phpMyAdmin:
1. Klik database `toko_online`
2. Tab "Export"
3. Klik "Go"
4. Save file .sql

---

## 📞 Butuh Bantuan?

Baca file-file berikut:
- `README.md` - Dokumentasi lengkap
- `INSTALL.md` - Panduan instalasi detail
- `README-QUICK-START.md` - Panduan cepat

Atau jalankan:
- `setup-check.php` - Cek status instalasi
- `create-folders.php` - Buat folder otomatis
- `create-placeholder.php` - Buat gambar placeholder

---

**Selamat Menggunakan! 🎉**
