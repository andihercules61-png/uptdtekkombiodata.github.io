<?php
/**
 * Deployment Checklist - Website Biodata UPTD TEKKOM
 * Script untuk mengecek kesiapan website sebelum hosting
 */

echo "<!DOCTYPE html>
<html lang='id'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Deployment Checklist - Website Biodata</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; margin: 20px; background: #f5f5f5; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h1 { color: #2c3e50; text-align: center; margin-bottom: 30px; }
        .checklist { background: #f8f9fa; padding: 20px; border-radius: 8px; margin: 20px 0; }
        .item { display: flex; align-items: center; margin: 10px 0; padding: 10px; border-radius: 5px; }
        .item.success { background: #d4edda; border-left: 4px solid #28a745; }
        .item.warning { background: #fff3cd; border-left: 4px solid #ffc107; }
        .item.error { background: #f8d7da; border-left: 4px solid #dc3545; }
        .status { margin-left: 10px; font-weight: bold; }
        .success .status { color: #28a745; }
        .warning .status { color: #856404; }
        .error .status { color: #721c24; }
        .info { background: #d1ecf1; border: 1px solid #bee5eb; padding: 15px; border-radius: 5px; margin: 20px 0; }
        .code { background: #f8f9fa; padding: 10px; border-radius: 5px; font-family: monospace; margin: 10px 0; }
    </style>
</head>
<body>
    <div class='container'>
        <h1>🚀 Deployment Checklist - Website Biodata</h1>";

// Fungsi untuk mengecek file
function checkFile($file, $description) {
    if (file_exists($file)) {
        echo "<div class='item success'>
                <span>✅</span>
                <span class='status'>$description</span>
              </div>";
        return true;
    } else {
        echo "<div class='item error'>
                <span>❌</span>
                <span class='status'>$description - File tidak ditemukan!</span>
              </div>";
        return false;
    }
}

// Fungsi untuk mengecek koneksi database
function checkDatabase() {
    try {
        include 'config/koneksi.php';
        if ($db && mysqli_ping($db)) {
            echo "<div class='item success'>
                    <span>✅</span>
                    <span class='status'>Koneksi Database - Berhasil</span>
                  </div>";
            return true;
        } else {
            echo "<div class='item error'>
                    <span>❌</span>
                    <span class='status'>Koneksi Database - Gagal</span>
                  </div>";
            return false;
        }
    } catch (Exception $e) {
        echo "<div class='item error'>
                <span>❌</span>
                <span class='status'>Koneksi Database - Error: " . $e->getMessage() . "</span>
              </div>";
        return false;
    }
}

// Fungsi untuk mengecek tabel database
function checkTables() {
    try {
        include 'config/koneksi.php';
        $tables = ['signup', 'absensi', 'tugas'];
        $allExist = true;
        
        foreach ($tables as $table) {
            $result = mysqli_query($db, "SHOW TABLES LIKE '$table'");
            if (mysqli_num_rows($result) > 0) {
                echo "<div class='item success'>
                        <span>✅</span>
                        <span class='status'>Tabel $table - Ada</span>
                      </div>";
            } else {
                echo "<div class='item error'>
                        <span>❌</span>
                        <span class='status'>Tabel $table - Tidak ditemukan!</span>
                      </div>";
                $allExist = false;
            }
        }
        return $allExist;
    } catch (Exception $e) {
        echo "<div class='item error'>
                <span>❌</span>
                <span class='status'>Cek Tabel - Error: " . $e->getMessage() . "</span>
              </div>";
        return false;
    }
}

// Mulai pengecekan
echo "<div class='checklist'>
        <h3>📁 Pengecekan File</h3>";

$files = [
    'index.php' => 'File Utama (index.php)',
    'config/koneksi.php' => 'Konfigurasi Database',
    'db_biodata.sql' => 'File Database SQL',
    'absensi.php' => 'Halaman Absensi',
    'biodata.php' => 'Halaman Biodata',
    'dashboard.php' => 'Halaman Dashboard',
    'mydata.php' => 'Halaman My Data',
    'tugas.php' => 'Halaman Tugas'
];

$fileCheck = true;
foreach ($files as $file => $desc) {
    if (!checkFile($file, $desc)) {
        $fileCheck = false;
    }
}

echo "</div>";

echo "<div class='checklist'>
        <h3>🗄️ Pengecekan Database</h3>";

$dbCheck = checkDatabase();
$tableCheck = checkTables();

echo "</div>";

echo "<div class='checklist'>
        <h3>🔧 Pengecekan Konfigurasi</h3>";

// Cek PHP version
$phpVersion = phpversion();
if (version_compare($phpVersion, '7.4.0', '>=')) {
    echo "<div class='item success'>
            <span>✅</span>
            <span class='status'>PHP Version: $phpVersion (Kompatibel)</span>
          </div>";
} else {
    echo "<div class='item warning'>
            <span>⚠️</span>
            <span class='status'>PHP Version: $phpVersion (Disarankan 7.4+)</span>
          </div>";
}

// Cek ekstensi yang diperlukan
$extensions = ['mysqli', 'session', 'json'];
foreach ($extensions as $ext) {
    if (extension_loaded($ext)) {
        echo "<div class='item success'>
                <span>✅</span>
                <span class='status'>Ekstensi $ext - Tersedia</span>
              </div>";
    } else {
        echo "<div class='item error'>
                <span>❌</span>
                <span class='status'>Ekstensi $ext - Tidak tersedia!</span>
              </div>";
    }
}

echo "</div>";

// Kesimpulan
echo "<div class='info'>
        <h3>📋 Kesimpulan</h3>";

if ($fileCheck && $dbCheck && $tableCheck) {
    echo "<p><strong>🎉 Website siap untuk di-deploy ke hosting!</strong></p>
          <p>Semua file dan database sudah siap. Anda bisa melanjutkan ke langkah hosting.</p>";
} else {
    echo "<p><strong>⚠️ Ada beberapa masalah yang perlu diperbaiki sebelum hosting:</strong></p>
          <ul>";
    if (!$fileCheck) echo "<li>Beberapa file tidak ditemukan</li>";
    if (!$dbCheck) echo "<li>Koneksi database bermasalah</li>";
    if (!$tableCheck) echo "<li>Tabel database tidak lengkap</li>";
    echo "</ul>";
}

echo "</div>";

echo "<div class='info'>
        <h3>📝 Langkah Selanjutnya</h3>
        <ol>
            <li>Pilih provider hosting (000webhost untuk gratis, Hostinger untuk berbayar)</li>
            <li>Upload semua file ke folder public_html</li>
            <li>Buat database di hosting dan import db_biodata.sql</li>
            <li>Update konfigurasi database di config/koneksi.php</li>
            <li>Test website di domain hosting</li>
        </ol>
      </div>";

echo "<div class='code'>
        <strong>Command untuk upload via FTP:</strong><br>
        ftp://your-domain.com<br>
        Upload semua file ke folder: /public_html/
      </div>";

echo "</div></body></html>";
?>
