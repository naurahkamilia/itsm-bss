# 📦 Panduan Instalasi - Toko Online Hijau

Panduan lengkap instalasi aplikasi e-commerce di XAMPP local.

## 📋 Persiapan

### 1. Download & Install XAMPP

1. Download XAMPP dari: https://www.apachefriends.org/download.html
2. Pilih versi dengan **PHP 8.0** atau lebih tinggi
3. Install XAMPP di `C:\xampp\` (Windows) atau `/opt/lampp/` (Linux)
4. Jalankan XAMPP Control Panel

### 2. Cek Requirement

Pastikan requirement terpenuhi:
- ✅ PHP >= 8.0
- ✅ MySQL >= 5.7
- ✅ Apache 2.4
- ✅ Extension PDO, pdo_mysql, mbstring, openssl sudah aktif

Cara cek PHP version:
```bash
# Buka XAMPP Shell
php -v
```

## 🚀 Instalasi Step-by-Step

### Step 1: Copy Project ke htdocs

1. Extract/Copy folder `php-app` ke dalam `C:\xampp\htdocs\`
2. Rename folder menjadi `toko-online`
3. Struktur akhir: `C:\xampp\htdocs\toko-online\`

```
C:\xampp\htdocs\toko-online\
├── config/
├── models/
├── classes/
├── includes/
├── public/
├── admin/
├── database/
├── index.php
└── ...
```

### Step 2: Import Database

#### Via phpMyAdmin (Recommended)

1. Start **MySQL** di XAMPP Control Panel
2. Buka browser: http://localhost/phpmyadmin
3. Klik **"New"** untuk membuat database baru
4. Nama database: `toko_online`
5. Collation: `utf8mb4_unicode_ci`
6. Klik **"Create"**

7. Klik database `toko_online` yang baru dibuat
8. Klik tab **"Import"**
9. Klik **"Choose File"**
10. Pilih file: `C:\xampp\htdocs\toko-online\database\migration.sql`
11. Klik **"Go"** di bagian bawah
12. Tunggu sampai muncul pesan sukses

#### Via Command Line (Alternative)

```bash
# Masuk ke direktori XAMPP
cd C:\xampp\mysql\bin

# Import database
mysql -u root -p toko_online < C:\xampp\htdocs\toko-online\database\migration.sql

# Jika root tidak ada password, kosongkan saja
```

### Step 3: Verifikasi Database

1. Buka phpMyAdmin
2. Klik database `toko_online`
3. Pastikan ada tabel:
   - ✅ categories (6 records)
   - ✅ products (24 records)
   - ✅ users (1 record - admin)
   - ✅ product_images
   - ✅ settings (6 records)

4. Klik tabel `users`, pastikan ada user:
   - Email: admin@tokoonline.com
   - Password: (hashed)
   - Role: admin

### Step 4: Konfigurasi Aplikasi

1. Buka file: `C:\xampp\htdocs\toko-online\config\config.php`
2. Edit konfigurasi berikut:

```php
// Base URL - PENTING! Sesuaikan dengan folder Anda
define('BASE_URL', 'http://localhost/toko-online/');

// Database Configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'toko_online');
define('DB_USER', 'root');
define('DB_PASS', ''); // Default XAMPP kosong, isi jika ada password

// WhatsApp Configuration - Ganti dengan nomor WA Toko Anda
define('WHATSAPP_NUMBER', '6281234567890'); // Format: 62xxx
```

**❗ Catatan Penting:**
- Pastikan `BASE_URL` sesuai dengan lokasi folder Anda
- Jika folder ada di subdirektori, contoh: `http://localhost/projects/toko-online/`
- Nomor WA harus format internasional: `62` + nomor tanpa `0` di depan
  - Contoh: `0812-3456-7890` → `6281234567890`

### Step 5: Setup Permissions (Opsional)

Untuk Windows, biasanya tidak perlu. Untuk Linux/Mac:

```bash
# Berikan permission write untuk folder uploads
chmod 755 public/uploads/
chmod 755 public/images/

# Atau jika masih error
chmod -R 777 public/
```

### Step 6: Edit .htaccess

1. Buka file: `C:\xampp\htdocs\toko-online\.htaccess`
2. Edit baris `RewriteBase`:

```apache
RewriteBase /toko-online/
```

Sesuaikan dengan nama folder Anda.

### Step 7: Start Services

1. Buka **XAMPP Control Panel**
2. Klik **Start** pada:
   - ✅ Apache
   - ✅ MySQL
3. Pastikan keduanya berwarna hijau (running)

### Step 8: Test Aplikasi

1. Buka browser (Chrome/Firefox/Edge recommended)
2. Akses: http://localhost/toko-online/
3. Anda akan melihat halaman produk

4. **Test Login Admin:**
   - URL: http://localhost/toko-online/login.php
   - Email: `admin@tokoonline.com`
   - Password: `admin123`
   - Isi Captcha yang muncul
   - Klik **Login**

5. **Test Admin Panel:**
   - Setelah login, Anda akan diarahkan ke admin panel
   - URL: http://localhost/toko-online/admin/
   - Coba tambah, edit, atau hapus produk

6. **Test WhatsApp Order:**
   - Klik produk
   - Klik "Order via WhatsApp"
   - Akan terbuka WhatsApp Web/App dengan pesan otomatis

## ✅ Verifikasi Instalasi

### Checklist Instalasi Berhasil:

- [ ] Homepage bisa dibuka
- [ ] Produk tampil dengan gambar
- [ ] Bisa search dan filter produk
- [ ] Pagination berfungsi
- [ ] Halaman detail produk bisa dibuka
- [ ] Tombol WhatsApp order berfungsi
- [ ] Bisa login ke admin panel
- [ ] Bisa tambah/edit/hapus produk di admin
- [ ] Captcha muncul di halaman login
- [ ] Tidak ada error PHP di halaman

## 🔧 Troubleshooting

### Error: "Page Not Found" / 404

**Penyebab:** Base URL salah

**Solusi:**
1. Cek nama folder di htdocs
2. Edit `config/config.php`:
   ```php
   define('BASE_URL', 'http://localhost/[nama-folder-anda]/');
   ```
3. Edit `.htaccess`:
   ```apache
   RewriteBase /[nama-folder-anda]/
   ```

### Error: "Database connection failed"

**Penyebab:** Database belum dibuat atau konfigurasi salah

**Solusi:**
1. Pastikan MySQL running di XAMPP
2. Buat database `toko_online` di phpMyAdmin
3. Import file `migration.sql`
4. Cek konfigurasi di `config/config.php`:
   ```php
   define('DB_HOST', 'localhost');
   define('DB_NAME', 'toko_online');
   define('DB_USER', 'root');
   define('DB_PASS', '');
   ```

### Error: "Call to undefined function..."

**Penyebab:** PHP extension tidak aktif

**Solusi:**
1. Buka XAMPP Control Panel
2. Klik **Config** → **PHP (php.ini)**
3. Cari dan uncomment (hapus `;`):
   ```ini
   extension=pdo_mysql
   extension=mbstring
   extension=openssl
   ```
4. Save dan restart Apache

### Gambar Produk Tidak Muncul

**Penyebab:** URL gambar tidak valid atau internet mati

**Solusi:**
1. Produk menggunakan gambar dari Unsplash (butuh internet)
2. Atau ganti dengan gambar lokal:
   - Simpan gambar di `public/images/products/`
   - Edit URL di database atau via admin panel

### Tidak Bisa Login Admin

**Penyebab:** Data user belum di-import

**Solusi:**
1. Buka phpMyAdmin
2. Klik database `toko_online`
3. Klik tab **SQL**
4. Jalankan query:
   ```sql
   INSERT INTO users (name, email, password, role, email_verified) 
   VALUES ('Admin', 'admin@tokoonline.com', 
   '$argon2id$v=19$m=65536,t=4,p=1$RzUxSlh4YVNDZmpRazJCWQ$2Z+3i5wGdXKqPf8BqYw8cQ', 
   'admin', 1);
   ```
5. Coba login lagi

### Captcha Tidak Muncul

**Penyebab:** Session error

**Solusi:**
1. Hapus semua file di `C:\xampp\tmp\`
2. Restart Apache di XAMPP
3. Refresh browser (Ctrl+F5)

### WhatsApp Order Tidak Berfungsi

**Penyebab:** Nomor WA belum dikonfigurasi

**Solusi:**
1. Edit `config/config.php`:
   ```php
   define('WHATSAPP_NUMBER', '6281234567890'); // Ganti dengan nomor Anda
   ```
2. Format: `62` + nomor tanpa `0` di depan
3. Contoh: `0812-3456-7890` → `6281234567890`

### Port 80 atau 3306 Sudah Digunakan

**Penyebab:** Ada aplikasi lain yang menggunakan port tersebut

**Solusi untuk Port 80 (Apache):**
1. Klik **Config** di XAMPP Control Panel
2. Pilih **Service and Port Settings**
3. Ubah Main Port dari `80` ke `8080`
4. Edit `config/config.php`:
   ```php
   define('BASE_URL', 'http://localhost:8080/toko-online/');
   ```

**Solusi untuk Port 3306 (MySQL):**
1. Stop service MySQL di Windows (jika ada)
2. Atau ubah port MySQL di XAMPP ke `3307`
3. Edit `config/config.php`:
   ```php
   define('DB_HOST', 'localhost:3307');
   ```

## 🔐 Keamanan Production

Jika ingin deploy ke server production:

### 1. Ubah Environment

Edit `config/config.php`:
```php
define('APP_ENV', 'production');
```

### 2. Ganti Admin Password

1. Login ke admin panel
2. Atau via phpMyAdmin:
```sql
UPDATE users 
SET password = '$argon2id$v=19$m=65536,t=4,p=1$...' 
WHERE email = 'admin@tokoonline.com';
```

### 3. Enable HTTPS

Edit `.htaccess`:
```apache
RewriteCond %{HTTPS} off
RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
```

Edit `config/config.php`:
```php
ini_set('session.cookie_secure', 1);
define('BASE_URL', 'https://yourdomain.com/');
```

### 4. Update Database Password

```sql
ALTER USER 'root'@'localhost' IDENTIFIED BY 'strong-password-here';
FLUSH PRIVILEGES;
```

Update `config/config.php`:
```php
define('DB_PASS', 'strong-password-here');
```

### 5. Disable Error Display

Edit `php.ini`:
```ini
display_errors = Off
log_errors = On
error_log = /path/to/error.log
```

## 📚 Referensi

- **XAMPP Documentation:** https://www.apachefriends.org/docs/
- **PHP 8 Manual:** https://www.php.net/manual/en/
- **Bootstrap 5 Docs:** https://getbootstrap.com/docs/5.3/
- **MySQL Documentation:** https://dev.mysql.com/doc/

## 💡 Tips

1. **Backup Database Berkala:**
   ```bash
   mysqldump -u root -p toko_online > backup.sql
   ```

2. **Clear Browser Cache:**
   - Chrome: Ctrl + Shift + Delete
   - Firefox: Ctrl + Shift + Delete

3. **Check PHP Errors:**
   - Lihat file: `C:\xampp\apache\logs\error.log`

4. **Database Too Large:**
   - Edit `my.ini` di XAMPP
   - Increase `max_allowed_packet`

## 🎉 Selesai!

Jika semua langkah sudah diikuti, aplikasi seharusnya sudah berjalan dengan baik.

**Demo URLs:**
- Homepage: http://localhost/toko-online/
- Login: http://localhost/toko-online/login.php
- Admin: http://localhost/toko-online/admin/

**Demo Login:**
- Email: admin@tokoonline.com
- Password: admin123

---

**Need Help?**
Jika masih ada masalah, cek file `README.md` atau hubungi developer.
