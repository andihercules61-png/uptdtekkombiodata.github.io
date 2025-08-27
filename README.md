# UPTD TEKKOM - Sistem Informasi Biodata

Sistem informasi biodata untuk Unit Pelaksana Teknis Daerah Teknologi dan Komunikasi (UPTD TEKKOM).

## 🌟 Fitur Utama

- **Sistem Login Permanen** - Session tidak kadaluarsa sampai logout manual
- **Dashboard Real-time** - Informasi cuaca dan data biodata real-time
- **Sistem Absensi** - Pencatatan kehadiran dengan deteksi keterlambatan
- **Manajemen Biodata** - CRUD data biodata lengkap
- **Responsive Design** - Tampilan optimal di semua perangkat
- **Weather API Integration** - Informasi cuaca real-time menggunakan OpenWeatherMap

## 🚀 Teknologi yang Digunakan

- **Backend**: PHP 7.4+
- **Database**: MySQL
- **Frontend**: HTML5, CSS3, JavaScript (ES6+)
- **API**: OpenWeatherMap API
- **Styling**: Custom CSS dengan animasi
- **Icons**: Font Awesome 6.0

## 📁 Struktur Proyek

```
boidata-pkl/
├── index.php                 # Halaman utama (landing page)
├── config/                   # Konfigurasi database dan aplikasi
│   ├── app.php              # API endpoints
│   ├── koneksi.php          # Koneksi database
│   └── database.sql         # Struktur database
├── pengguna/                 # Halaman pengguna
│   ├── biodata.php          # Form input biodata
│   ├── absensi.php          # Sistem absensi
│   ├── mydata.php           # Dashboard pengguna
│   └── dashboard.php        # Dashboard admin
├── db/                      # Backup database
└── README.md               # Dokumentasi ini
```

## 🛠️ Instalasi

### Persyaratan Sistem
- PHP 7.4 atau lebih tinggi
- MySQL 5.7 atau lebih tinggi
- Web server (Apache/Nginx)
- Extensions PHP: mysqli, json, curl

### Langkah Instalasi

1. **Clone Repository**
   ```bash
   git clone https://github.com/username/boidata-pkl.git
   cd boidata-pkl
   ```

2. **Setup Database**
   - Buat database MySQL baru
   - Import file `config/database.sql`
   - Atau jalankan script di `db/db_biodata.sql`

3. **Konfigurasi Database**
   - Edit file `config/koneksi.php`
   - Sesuaikan host, username, password, dan nama database

4. **Setup Web Server**
   - Pastikan web server mengarah ke direktori proyek
   - File `index.php` akan menjadi halaman utama

5. **Konfigurasi API Weather** (Opsional)
   - Daftar di [OpenWeatherMap](https://openweathermap.org/api)
   - Dapatkan API key
   - Update API key di file JavaScript (baris dengan `apiKey`)

## 🔧 Konfigurasi

### Database Configuration
File: `config/koneksi.php`
```php
<?php
$host = 'localhost';
$username = 'your_username';
$password = 'your_password';
$database = 'your_database';
?>
```

### API Weather Configuration
Update API key di file JavaScript:
```javascript
const apiKey = 'your_openweathermap_api_key';
```

## 📱 Fitur Detail

### 1. Sistem Login Permanen
- Session tidak kadaluarsa otomatis
- LocalStorage backup untuk auto-login
- Deteksi keterlambatan login (setelah 08:30)
- Logout manual untuk mengakhiri session

### 2. Dashboard Real-time
- Jam dan tanggal real-time
- Informasi cuaca berdasarkan lokasi
- Statistik data biodata
- Data terbaru pengguna

### 3. Sistem Absensi
- Pencatatan waktu masuk
- Deteksi keterlambatan otomatis
- Riwayat absensi
- Notifikasi keterlambatan

### 4. Manajemen Biodata
- Input data lengkap
- Edit dan update data
- Hapus data
- Export data

## 🌐 Deployment

### Local Development
1. Setup XAMPP/WAMP
2. Clone repository ke `htdocs`
3. Import database
4. Akses via `http://localhost/boidata-pkl`

### Production Hosting
1. Upload semua file ke hosting
2. Import database
3. Update konfigurasi database
4. Pastikan `.htaccess` aktif

## 🔒 Keamanan

- Session management yang aman
- SQL injection protection
- XSS protection
- File upload validation
- Error handling yang baik

## 📊 Database Schema

### Tabel `biodata`
- `id` (Primary Key)
- `nama_lengkap`
- `tanggal_lahir`
- `jenis_kelamin`
- `alamat`
- `asal_sekolah`
- `created_at`
- `updated_at`

### Tabel `absensi`
- `id` (Primary Key)
- `user_id`
- `tanggal`
- `waktu_masuk`
- `status`
- `keterangan`

## 🤝 Kontribusi

1. Fork repository
2. Buat branch fitur baru (`git checkout -b feature/AmazingFeature`)
3. Commit perubahan (`git commit -m 'Add some AmazingFeature'`)
4. Push ke branch (`git push origin feature/AmazingFeature`)
5. Buat Pull Request

## 📝 Lisensi

Proyek ini dilisensikan di bawah Lisensi MIT - lihat file [LICENSE](LICENSE) untuk detail.

## 📞 Kontak

- **Email**: admin@uptdtekkom.com
- **Website**: https://uptdtekkom.com
- **Alamat**: UPTD TEKKOM, Samarinda, Kalimantan Timur

## 🙏 Ucapan Terima Kasih

- [OpenWeatherMap](https://openweathermap.org/) untuk API cuaca
- [Font Awesome](https://fontawesome.com/) untuk icons
- [Google Fonts](https://fonts.google.com/) untuk typography

---

**Dibuat dengan ❤️ untuk UPTD TEKKOM**
