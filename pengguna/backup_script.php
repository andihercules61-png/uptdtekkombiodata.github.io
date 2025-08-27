<?php
/**
 * Backup Script - Website Biodata UPTD TEKKOM
 * Script untuk backup database dan file secara otomatis
 */

// Konfigurasi backup
$backup_dir = 'backups/';
$db_backup_file = $backup_dir . 'db_backup_' . date('Y-m-d_H-i-s') . '.sql';
$file_backup_dir = $backup_dir . 'files_backup_' . date('Y-m-d_H-i-s') . '/';

// Buat direktori backup jika belum ada
if (!file_exists($backup_dir)) {
    mkdir($backup_dir, 0755, true);
}

// Fungsi untuk backup database
function backupDatabase($db, $backup_file) {
    try {
        // Dapatkan semua tabel
        $tables = [];
        $result = mysqli_query($db, "SHOW TABLES");
        while ($row = mysqli_fetch_row($result)) {
            $tables[] = $row[0];
        }
        
        $output = "-- Database Backup - Website Biodata UPTD TEKKOM\n";
        $output .= "-- Generated: " . date('Y-m-d H:i:s') . "\n";
        $output .= "-- --------------------------------------------------------\n\n";
        
        // Backup setiap tabel
        foreach ($tables as $table) {
            $output .= "-- Table structure for table `$table`\n";
            $output .= "DROP TABLE IF EXISTS `$table`;\n";
            
            $result = mysqli_query($db, "SHOW CREATE TABLE `$table`");
            $row = mysqli_fetch_row($result);
            $output .= $row[1] . ";\n\n";
            
            // Backup data
            $result = mysqli_query($db, "SELECT * FROM `$table`");
            if (mysqli_num_rows($result) > 0) {
                $output .= "-- Dumping data for table `$table`\n";
                while ($row = mysqli_fetch_assoc($result)) {
                    $output .= "INSERT INTO `$table` VALUES (";
                    $values = [];
                    foreach ($row as $value) {
                        $values[] = "'" . mysqli_real_escape_string($db, $value) . "'";
                    }
                    $output .= implode(', ', $values) . ");\n";
                }
                $output .= "\n";
            }
        }
        
        // Tulis ke file
        file_put_contents($backup_file, $output);
        return true;
        
    } catch (Exception $e) {
        error_log("Database backup failed: " . $e->getMessage());
        return false;
    }
}

// Fungsi untuk backup file
function backupFiles($source_dir, $backup_dir) {
    try {
        if (!file_exists($backup_dir)) {
            mkdir($backup_dir, 0755, true);
        }
        
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($source_dir, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::SELF_FIRST
        );
        
        foreach ($iterator as $file) {
            if ($file->isDir()) {
                mkdir($backup_dir . $file->getRelativePathname(), 0755, true);
            } else {
                // Skip file yang tidak perlu di-backup
                $skip_files = ['.git', 'backups', 'node_modules', '.DS_Store', 'Thumbs.db'];
                $should_skip = false;
                foreach ($skip_files as $skip) {
                    if (strpos($file->getPathname(), $skip) !== false) {
                        $should_skip = true;
                        break;
                    }
                }
                
                if (!$should_skip) {
                    copy($file->getPathname(), $backup_dir . $file->getRelativePathname());
                }
            }
        }
        return true;
        
    } catch (Exception $e) {
        error_log("File backup failed: " . $e->getMessage());
        return false;
    }
}

// Fungsi untuk cleanup backup lama
function cleanupOldBackups($backup_dir, $days_to_keep = 7) {
    try {
        $files = glob($backup_dir . '*');
        $now = time();
        
        foreach ($files as $file) {
            if (is_file($file)) {
                if ($now - filemtime($file) >= $days_to_keep * 24 * 60 * 60) {
                    unlink($file);
                }
            } elseif (is_dir($file)) {
                if ($now - filemtime($file) >= $days_to_keep * 24 * 60 * 60) {
                    // Hapus direktori dan isinya
                    $iterator = new RecursiveIteratorIterator(
                        new RecursiveDirectoryIterator($file, RecursiveDirectoryIterator::SKIP_DOTS),
                        RecursiveIteratorIterator::CHILD_FIRST
                    );
                    
                    foreach ($iterator as $child) {
                        if ($child->isDir()) {
                            rmdir($child->getPathname());
                        } else {
                            unlink($child->getPathname());
                        }
                    }
                    rmdir($file);
                }
            }
        }
        return true;
        
    } catch (Exception $e) {
        error_log("Cleanup failed: " . $e->getMessage());
        return false;
    }
}

// Jalankan backup jika script dipanggil langsung
if (basename(__FILE__) == basename($_SERVER['SCRIPT_NAME'])) {
    // Include koneksi database
    include 'config/koneksi.php';
    
    echo "<!DOCTYPE html>
    <html lang='id'>
    <head>
        <meta charset='UTF-8'>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <title>Backup Website - Biodata UPTD TEKKOM</title>
        <style>
            body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; margin: 20px; background: #f5f5f5; }
            .container { max-width: 800px; margin: 0 auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
            h1 { color: #2c3e50; text-align: center; margin-bottom: 30px; }
            .status { padding: 15px; margin: 10px 0; border-radius: 5px; }
            .success { background: #d4edda; border: 1px solid #c3e6cb; color: #155724; }
            .error { background: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; }
            .info { background: #d1ecf1; border: 1px solid #bee5eb; color: #0c5460; }
            .backup-list { background: #f8f9fa; padding: 15px; border-radius: 5px; margin: 10px 0; }
        </style>
    </head>
    <body>
        <div class='container'>
            <h1>💾 Backup Website - Biodata UPTD TEKKOM</h1>";
    
    // Backup database
    echo "<h3>🗄️ Backup Database</h3>";
    if (backupDatabase($db, $db_backup_file)) {
        echo "<div class='status success'>✅ Database berhasil di-backup ke: $db_backup_file</div>";
    } else {
        echo "<div class='status error'>❌ Gagal backup database</div>";
    }
    
    // Backup file
    echo "<h3>📁 Backup File</h3>";
    if (backupFiles('.', $file_backup_dir)) {
        echo "<div class='status success'>✅ File berhasil di-backup ke: $file_backup_dir</div>";
    } else {
        echo "<div class='status error'>❌ Gagal backup file</div>";
    }
    
    // Cleanup backup lama
    echo "<h3>🧹 Cleanup Backup Lama</h3>";
    if (cleanupOldBackups($backup_dir, 7)) {
        echo "<div class='status info'>✅ Backup lama berhasil dibersihkan (menyimpan 7 hari terakhir)</div>";
    } else {
        echo "<div class='status error'>❌ Gagal cleanup backup lama</div>";
    }
    
    // Tampilkan daftar backup
    echo "<h3>📋 Daftar Backup</h3>";
    $backups = glob($backup_dir . '*');
    if (empty($backups)) {
        echo "<div class='backup-list'>Tidak ada file backup</div>";
    } else {
        echo "<div class='backup-list'>";
        foreach ($backups as $backup) {
            $size = is_dir($backup) ? 'Directory' : number_format(filesize($backup) / 1024, 2) . ' KB';
            $date = date('Y-m-d H:i:s', filemtime($backup));
            echo "<div>📄 " . basename($backup) . " - $size - $date</div>";
        }
        echo "</div>";
    }
    
    echo "<div class='status info'>
            <strong>💡 Tips:</strong><br>
            • Backup otomatis bisa dijadwalkan dengan cron job<br>
            • Backup database penting sebelum update website<br>
            • Simpan backup di lokasi yang aman (cloud storage)
          </div>";
    
    echo "</div></body></html>";
}
?>
