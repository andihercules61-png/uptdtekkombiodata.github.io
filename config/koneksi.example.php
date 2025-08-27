<?php
/**
 * Template Konfigurasi Database
 * 
 * Copy file ini menjadi 'koneksi.php' dan sesuaikan dengan kredensial database Anda
 * 
 * PERINGATAN: Jangan upload file koneksi.php yang berisi kredensial asli ke GitHub!
 */

// ===== KONFIGURASI LOCALHOST (XAMPP/WAMP) =====
// Untuk development lokal
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'db_biodata';

// ===== KONFIGURASI HOSTING =====
// Uncomment dan sesuaikan dengan kredensial hosting Anda

// Contoh untuk 000webhost
// $host = 'localhost';
// $username = 'your_username';
// $password = 'your_password';
// $database = 'your_database';

// Contoh untuk Hostinger
// $host = 'localhost';
// $username = 'your_username';
// $password = 'your_password';
// $database = 'your_database';

// Contoh untuk InfinityFree
// $host = 'sql.infinityfree.com';
// $username = 'your_username';
// $password = 'your_password';
// $database = 'your_database';

// ===== KONEKSI DATABASE =====
$db = mysqli_connect($host, $username, $password, $database);

// ===== VALIDASI KONEKSI =====
if (!$db) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

// Set charset untuk mendukung karakter khusus
mysqli_set_charset($db, "utf8mb4");

// ===== KONFIGURASI TAMBAHAN =====
// Set timezone
date_default_timezone_set('Asia/Makassar');

// Enable error reporting untuk development (disable di production)
if ($_SERVER['SERVER_NAME'] === 'localhost' || $_SERVER['SERVER_NAME'] === '127.0.0.1') {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

echo "<!-- Database connected successfully -->";
?>
