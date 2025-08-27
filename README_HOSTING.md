# 🌐 Website Biodata UPTD TEKKOM - Panduan Hosting

## 📋 Deskripsi Proyek
Website sistem informasi biodata dengan fitur:
- ✅ Sistem login dan registrasi
- ✅ Manajemen biodata pengguna
- ✅ Sistem absensi
- ✅ Manajemen tugas
- ✅ Dashboard admin
- ✅ Responsive design

## 🚀 Langkah Cepat Hosting

### **Opsi 1: 000webhost (Gratis)**
1. Daftar di [000webhost.com](https://000webhost.com)
2. Upload semua file ke `public_html`
3. Buat database dan import `db_biodata.sql`
4. Update `config/koneksi.php`

### **Opsi 2: Hostinger (Berbayar)**
1. Beli hosting di [hostinger.co.id](https://hostinger.co.id)
2. Upload file via File Manager atau FTP
3. Setup database di hPanel
4. Konfigurasi domain

## 📁 Struktur File untuk Upload

```
public_html/
├── index.php              # Halaman utama
├── absensi.php            # Sistem absensi
├── biodata.php            # Manajemen biodata
├── dashboard.php          # Dashboard admin
├── mydata.php             # Data pribadi
├── tugas.php              # Manajemen tugas
├── inbox.php              # Pesan masuk
├── config/
│   └── koneksi.php        # Konfigurasi database
├── .htaccess              # Optimasi hosting
├── deploy_checklist.php   # Cek kesiapan deploy
├── backup_script.php      # Script backup
└── assets/                # Gambar dan CSS
    ├── bg absensi.jpg
    ├── bg mydata.jpg
    ├── forest 2.jpg
    └── rainforest.jpg
```

## 🗄️ Konfigurasi Database

### Update `config/koneksi.php`:

```php
<?php
// Pilih salah satu sesuai hosting Anda

// 000webhost
$db = mysqli_connect('localhost','username_db','password_db','nama_database');

// Hostinger
$db = mysqli_connect('localhost','username_db','password_db','nama_database');

// InfinityFree
$db = mysqli_connect('sql.infinityfree.com','username_db','password_db','nama_database');

if (!$db) {
    die("Connection failed: " . mysqli_connect_error());
}
mysqli_set_charset($db, "utf8mb4");
?>
```

## 🔧 Tools yang Disediakan

### 1. **deploy_checklist.php**
- Cek kesiapan website sebelum hosting
- Verifikasi file dan database
- Test koneksi dan ekstensi PHP

### 2. **backup_script.php**
- Backup database otomatis
- Backup file website
- Cleanup backup lama

### 3. **.htaccess**
- Optimasi performa
- Keamanan website
- Compression dan caching

## 📝 Langkah Detail Hosting

### **Step 1: Persiapan**
1. Jalankan `deploy_checklist.php` untuk cek kesiapan
2. Backup website dengan `backup_script.php`
3. Siapkan kredensial hosting

### **Step 2: Upload File**
1. Login ke hosting provider
2. Buka File Manager
3. Upload semua file ke `public_html`
4. Pastikan permission file benar (644 untuk file, 755 untuk folder)

### **Step 3: Setup Database**
1. Buat database baru di hosting
2. Import file `db_biodata.sql`
3. Catat kredensial database
4. Update `config/koneksi.php`

### **Step 4: Test Website**
1. Akses domain hosting
2. Test fitur login/register
3. Test semua menu
4. Cek responsive design

## 🔍 Troubleshooting

### **Error "Connection failed"**
```bash
# Cek kredensial database
# Pastikan database sudah dibuat
# Test koneksi manual
```

### **Error "404 Not Found"**
```bash
# Pastikan index.php ada di public_html
# Cek permission file
# Restart web server
```

### **Error "500 Internal Server Error"**
```bash
# Cek error log hosting
# Periksa syntax PHP
# Pastikan PHP version kompatibel (7.4+)
```

## 🛡️ Keamanan

### **Tips Keamanan:**
- ✅ Ganti password default
- ✅ Gunakan HTTPS/SSL
- ✅ Update PHP ke versi terbaru
- ✅ Backup database secara berkala
- ✅ Monitor log error

### **File yang Dilindungi:**
- `config/koneksi.php` (via .htaccess)
- `*.sql` files
- Backup files

## 📊 Monitoring

### **Cek Performa:**
- Akses `deploy_checklist.php` secara berkala
- Monitor error log hosting
- Cek uptime website
- Test kecepatan loading

### **Backup Rutin:**
- Jalankan `backup_script.php` setiap minggu
- Simpan backup di cloud storage
- Test restore backup

## 🌟 Fitur Website

### **Untuk Pengguna:**
- Login/Register dengan data lengkap
- Input dan edit biodata
- Sistem absensi harian
- Manajemen tugas pribadi
- Dashboard dengan statistik

### **Untuk Admin:**
- Dashboard dengan overview
- Manajemen user
- Laporan absensi
- Monitoring sistem

## 📞 Support

### **Jika Ada Masalah:**
1. Cek `PANDUAN_HOSTING.md` untuk troubleshooting
2. Jalankan `deploy_checklist.php` untuk diagnosis
3. Hubungi support hosting provider
4. Gunakan mode debug dengan `?debug=1`

### **Contact:**
- Email: support@uptd-tekkom.com
- WhatsApp: +62 812-3456-7890
- Website: https://uptd-tekkom.com

## 🎯 Langkah Selanjutnya

Setelah hosting berhasil:
1. **Setup SSL/HTTPS** untuk keamanan
2. **Optimasi SEO** untuk visibility
3. **Setup monitoring** untuk uptime
4. **Implementasi backup** otomatis
5. **Training user** untuk penggunaan

---

## 📚 Dokumentasi Tambahan

- [PANDUAN_HOSTING.md](PANDUAN_HOSTING.md) - Panduan detail hosting
- [CHANGELOG.md](pengguna/CHANGELOG.md) - Riwayat perubahan
- [README_LOGIN_SYSTEM.md](pengguna/README_LOGIN_SYSTEM.md) - Dokumentasi sistem login

---

**🎉 Selamat! Website Anda siap diakses publik!**

*Dibuat dengan ❤️ untuk UPTD TEKKOM*
