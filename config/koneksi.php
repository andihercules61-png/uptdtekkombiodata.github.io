<?php
// Konfigurasi Database untuk Hosting
// Pilih salah satu konfigurasi sesuai hosting Anda

// ===== KONFIGURASI LOCALHOST (XAMPP) =====
// Uncomment baris di bawah untuk development lokal
// $db = mysqli_connect('localhost','root','','db_biodata');

// ===== KONFIGURASI HOSTING =====
// Uncomment dan sesuaikan dengan kredensial hosting Anda

// Contoh untuk 000webhost
// $db = mysqli_connect('localhost','username_db','password_db','nama_database');

// Contoh untuk Hostinger
// $db = mysqli_connect('localhost','username_db','password_db','nama_database');

// Contoh untuk InfinityFree
// $db = mysqli_connect('sql.infinityfree.com','username_db','password_db','nama_database');

// ===== KONFIGURASI DEFAULT (LOCALHOST) =====
$db = mysqli_connect('localhost','root','','db_biodata');

if (!$db) {
    die("Connection failed: " . mysqli_connect_error());
}

// Set charset untuk mendukung karakter khusus
mysqli_set_charset($db, "utf8mb4");
?>