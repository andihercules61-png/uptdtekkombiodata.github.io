<?php
/**
 * Session Management - Permanent Login System
 * 
 * This system keeps users logged in permanently until they manually logout.
 */

// Configure session to last forever
ini_set('session.gc_maxlifetime', 0);
ini_set('session.cookie_lifetime', 0);
ini_set('session.use_strict_mode', 1);

// Start session
session_start();

// Check if user is logged in
$isLoggedIn = isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'] === true;
$userName = $_SESSION['user_name'] ?? '';

// If not logged in, redirect to index
if (!$isLoggedIn) {
    header('Location: index.php');
    exit();
}

// Include database connection
require_once '../config/koneksi.php';

// Handle form submission
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = $_POST['nama'] ?? '';
    $tanggal = $_POST['tanggal'] ?? '';
    $waktu_masuk = $_POST['waktu_masuk'] ?? '';
    $status = $_POST['status'] ?? '';
    $keterangan = $_POST['keterangan'] ?? '';
    
    if (!empty($nama) && !empty($tanggal) && !empty($waktu_masuk) && !empty($status)) {
        // Check if attendance already exists for this user on this date
        $check_query = "SELECT id FROM absensi WHERE nama = ? AND tanggal = ?";
        $check_stmt = mysqli_prepare($db, $check_query);
        mysqli_stmt_bind_param($check_stmt, "ss", $nama, $tanggal);
        mysqli_stmt_execute($check_stmt);
        mysqli_stmt_store_result($check_stmt);
        
        if (mysqli_stmt_num_rows($check_stmt) > 0) {
            $message = 'error:Anda sudah melakukan absensi untuk tanggal ini.';
        } else {
            // Insert attendance data
            $insert_query = "INSERT INTO absensi (nama, tanggal, waktu_masuk, status, keterangan) VALUES (?, ?, ?, ?, ?)";
            $insert_stmt = mysqli_prepare($db, $insert_query);
            mysqli_stmt_bind_param($insert_stmt, "sssss", $nama, $tanggal, $waktu_masuk, $status, $keterangan);
            
            if (mysqli_stmt_execute($insert_stmt)) {
                $message = 'success:Absensi berhasil disimpan!';
            } else {
                $message = 'error:Terjadi kesalahan saat menyimpan absensi.';
            }
            mysqli_stmt_close($insert_stmt);
        }
        mysqli_stmt_close($check_stmt);
    } else {
        $message = 'error:Mohon lengkapi semua field yang diperlukan.';
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Absensi - UPTD TEKKOM</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: url('bg absensi.jpg') no-repeat center center;
            background-size: cover;
            background-attachment: fixed;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .container {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(15px);
            border-radius: 20px;
            border: 2px solid rgba(255, 255, 255, 0.3);
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.1);
            padding: 30px;
            max-width: 1000px;
            width: 100%;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            align-items: center;
        }

        .left-section {
            color: white;
        }

        .subtitle {
            font-size: 14px;
            font-weight: 500;
            margin-bottom: 10px;
            opacity: 0.9;
            letter-spacing: 0.5px;
        }

        .title {
            font-size: 32px;
            font-weight: 700;
            line-height: 1.2;
            margin-bottom: 20px;
        }

        .description {
            font-size: 16px;
            line-height: 1.6;
            margin-bottom: 30px;
            opacity: 0.9;
        }

        .contact-info {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .contact-item {
            display: flex;
            align-items: center;
            gap: 12px;
            color: white;
        }

        .contact-icon {
            width: 40px;
            height: 40px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
        }

        .contact-details {
            display: flex;
            flex-direction: column;
        }

        .contact-label {
            font-size: 12px;
            opacity: 0.8;
            margin-bottom: 2px;
        }

        .contact-value {
            font-size: 14px;
            font-weight: 500;
        }

        .form-section {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 15px;
            padding: 25px;
            backdrop-filter: blur(10px);
            border: 2px solid rgba(255, 255, 255, 0.3);
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-label {
            display: block;
            margin-bottom: 8px;
            font-size: 14px;
            font-weight: 500;
            color: white;
        }

        .form-input {
            width: 100%;
            padding: 10px 14px;
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 8px;
            font-size: 14px;
            background: rgba(255, 255, 255, 0.1);
            color: white;
            transition: all 0.3s ease;
        }

        .form-input:focus {
            outline: none;
            border-color: rgba(255, 255, 255, 0.6);
            background: rgba(255, 255, 255, 0.2);
            box-shadow: 0 0 0 3px rgba(255, 255, 255, 0.1);
        }

        .form-input::placeholder {
            color: rgba(255, 255, 255, 0.6);
        }

        .form-select {
            width: 100%;
            padding: 10px 14px;
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 8px;
            font-size: 14px;
            background: rgba(255, 255, 255, 0.1);
            color: white;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .form-select:focus {
            outline: none;
            border-color: rgba(255, 255, 255, 0.6);
            background: rgba(255, 255, 255, 0.2);
            box-shadow: 0 0 0 3px rgba(255, 255, 255, 0.1);
        }

        .form-select option {
            background: rgba(0, 0, 0, 0.8);
            color: white;
        }

        .form-textarea {
            width: 100%;
            padding: 10px 14px;
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 8px;
            font-size: 14px;
            background: rgba(255, 255, 255, 0.1);
            color: white;
            resize: vertical;
            min-height: 80px;
            font-family: inherit;
            transition: all 0.3s ease;
        }

        .form-textarea:focus {
            outline: none;
            border-color: rgba(255, 255, 255, 0.6);
            background: rgba(255, 255, 255, 0.2);
            box-shadow: 0 0 0 3px rgba(255, 255, 255, 0.1);
        }

        .form-textarea::placeholder {
            color: rgba(255, 255, 255, 0.6);
        }

        .submit-btn {
            background: rgba(255, 255, 255, 0.2);
            color: white;
            border: 2px solid rgba(255, 255, 255, 0.4);
            padding: 10px 25px;
            border-radius: 25px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .submit-btn:hover {
            background: rgba(255, 255, 255, 0.3);
            border-color: rgba(255, 255, 255, 0.6);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }

        .message {
            padding: 12px 16px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-weight: 500;
        }

        .message.success {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.3);
            color: #90EE90;
        }

        .message.error {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.3);
            color: #FFB6C1;
        }

        .back-btn {
            position: fixed;
            top: 20px;
            left: 20px;
            background: rgba(255, 255, 255, 0.2);
            color: white;
            text-decoration: none;
            padding: 10px 20px;
            border-radius: 25px;
            font-size: 14px;
            font-weight: 500;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            transition: all 0.3s ease;
        }

        .back-btn:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: translateY(-2px);
        }

        @media (max-width: 768px) {
            .container {
                grid-template-columns: 1fr;
                gap: 30px;
                padding: 30px 20px;
            }

            .title {
                font-size: 32px;
            }

            .back-btn {
                position: relative;
                top: auto;
                left: auto;
                margin-bottom: 20px;
                display: inline-block;
            }
        }
    </style>
</head>
<body>
    <a href="mydata.php" class="back-btn">← Kembali ke Dashboard</a>

    <div class="container">
        <div class="left-section">
            <div class="subtitle">UPTD TEKNOLOGI DAN KOMUNIKASI</div>
            <h1 class="title">Form Absensi Digital</h1>
            <p class="description">Sistem absensi digital yang memudahkan pencatatan kehadiran pegawai dengan teknologi terkini.</p>
            
            <div class="contact-info">
                <div class="contact-item">
                    <div class="contact-icon">👤</div>
                    <div class="contact-details">
                        <div class="contact-label">User</div>
                        <div class="contact-value"><?= htmlspecialchars($userName) ?></div>
                    </div>
                </div>
                <div class="contact-item">
                    <div class="contact-icon">📅</div>
                    <div class="contact-details">
                        <div class="contact-label">Tanggal</div>
                        <div class="contact-value"><?= date('d/m/Y') ?></div>
                    </div>
                </div>
                <div class="contact-item">
                    <div class="contact-icon">🕐</div>
                    <div class="contact-details">
                        <div class="contact-label">Waktu</div>
                        <div class="contact-value" id="current-time">00:00:00</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="form-section">
            <?php if (!empty($message)): ?>
                <?php 
                $messageType = strpos($message, 'success:') === 0 ? 'success' : 'error';
                $messageText = str_replace(['success:', 'error:'], '', $message);
                ?>
                <div class="message <?= $messageType ?>">
                    <?= $messageType === 'success' ? '✅' : '❌' ?> <?= htmlspecialchars($messageText) ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="form-group">
                    <label class="form-label">Nama Lengkap</label>
                    <input type="text" class="form-input" name="nama" value="<?= htmlspecialchars($userName) ?>" readonly>
                </div>

                <div class="form-group">
                    <label class="form-label">Tanggal</label>
                    <input type="date" class="form-input" name="tanggal" value="<?= date('Y-m-d') ?>" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Waktu Masuk</label>
                    <input type="time" class="form-input" name="waktu_masuk" id="waktu-masuk" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Status Kehadiran</label>
                    <select class="form-select" name="status" required>
                        <option value="">Pilih status...</option>
                        <option value="hadir">Hadir</option>
                        <option value="sakit">Sakit</option>
                        <option value="izin">Izin</option>
                        <option value="terlambat">Terlambat</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Keterangan (Opsional)</label>
                    <textarea class="form-textarea" name="keterangan" placeholder="Tambahkan keterangan jika diperlukan..."></textarea>
                </div>

                <button type="submit" class="submit-btn">
                    📝 Submit Absensi
                </button>
            </form>
        </div>
    </div>

    <script>
        // Real-time clock function
        function updateClock() {
            const now = new Date();
            const timeElement = document.getElementById('current-time');
            
            // Format time
            const timeString = now.toLocaleTimeString('id-ID', {
                hour12: false,
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit'
            });
            
            timeElement.textContent = timeString;
        }

        // Update clock every second
        updateClock();
        setInterval(updateClock, 1000);

        // Auto-fill current time when page loads
        document.addEventListener('DOMContentLoaded', function() {
            const now = new Date();
            const currentTime = now.toTimeString().slice(0, 5); // HH:MM format
            document.getElementById('waktu-masuk').value = currentTime;
        });

        // Form validation
        document.querySelector('form').addEventListener('submit', function(e) {
            const nama = document.querySelector('input[name="nama"]').value;
            const tanggal = document.querySelector('input[name="tanggal"]').value;
            const waktuMasuk = document.querySelector('input[name="waktu_masuk"]').value;
            const status = document.querySelector('select[name="status"]').value;

            if (!nama || !tanggal || !waktuMasuk || !status) {
                e.preventDefault();
                alert('Mohon lengkapi semua field yang diperlukan.');
                return false;
            }
        });
    </script>
</body>
</html>