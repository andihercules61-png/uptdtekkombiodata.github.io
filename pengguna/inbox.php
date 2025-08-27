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

// Get attendance data for the current user
$attendance_data = [];
$query = "SELECT * FROM absensi WHERE nama = ? ORDER BY tanggal DESC, waktu_masuk DESC";
$stmt = mysqli_prepare($db, $query);
mysqli_stmt_bind_param($stmt, "s", $userName);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

while ($row = mysqli_fetch_assoc($result)) {
    $attendance_data[] = $row;
}
mysqli_stmt_close($stmt);

// Handle logout
if (isset($_GET['logout'])) {
    // Clear session data
    session_unset();
    session_destroy();
    
    // Clear cookies
    if (isset($_COOKIE[session_name()])) {
        setcookie(session_name(), '', time() - 3600, '/');
    }
    
    // Redirect to index
    header('Location: index.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inbox - Riwayat Absensi - UPTD TEKKOM</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: url('bg mydata.jpg') no-repeat center center;
            background-size: cover;
            background-attachment: fixed;
            min-height: 100vh;
            padding: 30px;
        }

        .container {
            background: rgba(0, 0, 0, 0.3);
            backdrop-filter: blur(25px);
            border-radius: 20px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.3);
            max-width: 1400px;
            width: 95%;
            margin: 0 auto;
            overflow: hidden;
            display: flex;
            min-height: calc(100vh - 60px);
        }

        .sidebar {
            width: 300px;
            background: rgba(0, 0, 0, 0.2);
            backdrop-filter: blur(20px);
            padding: 25px;
            border-right: 1px solid rgba(255, 255, 255, 0.2);
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 30px;
            font-size: 18px;
            font-weight: 600;
            color: white;
        }

        .logo-icon {
            width: 32px;
            height: 32px;
            background: #6366f1;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
        }

        .nav-section {
            margin-bottom: 30px;
        }

        .nav-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 16px;
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.3s ease;
            color: white;
            font-size: 14px;
            margin-bottom: 5px;
            text-decoration: none;
        }

        .nav-item:hover, .nav-item.active {
            background: rgba(255, 255, 255, 0.1);
            color: white;
        }

        .nav-icon {
            width: 20px;
            height: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .friends-section {
            margin-top: 20px;
        }

        .friends-title {
            font-size: 12px;
            color: rgba(255, 255, 255, 0.6);
            margin-bottom: 15px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .friend-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 8px 0;
            cursor: pointer;
        }

        .friend-avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: linear-gradient(45deg, #6366f1, #8b5cf6);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 12px;
            font-weight: 600;
        }

        .friend-info {
            flex: 1;
        }

        .friend-name {
            font-size: 14px;
            font-weight: 500;
            color: white;
        }

        .friend-status {
            font-size: 12px;
            color: rgba(255, 255, 255, 0.7);
        }

        .main-content {
            flex: 1;
            padding: 30px 35px;
            background: rgba(0, 0, 0, 0.1);
        }

        .header {
            display: flex;
            justify-content: between;
            align-items: center;
            margin-bottom: 30px;
        }

        .search-bar {
            flex: 1;
            max-width: 400px;
            position: relative;
        }

        .search-input {
            width: 100%;
            padding: 12px 16px 12px 40px;
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 10px;
            background: rgba(255, 255, 255, 0.2);
            color: white;
            font-size: 14px;
            backdrop-filter: blur(10px);
        }

        .search-input::placeholder {
            color: rgba(255, 255, 255, 0.7);
        }

        .search-icon {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: rgba(255, 255, 255, 0.7);
        }

        .user-profile {
            display: flex;
            align-items: center;
            gap: 10px;
            color: white;
            margin-left: auto;
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(45deg, #f59e0b, #ef4444);
        }

        .hero-section {
            background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
            border-radius: 20px;
            padding: 40px;
            margin-bottom: 30px;
            color: white;
            position: relative;
            overflow: hidden;
        }

        .hero-content h1 {
            font-size: 32px;
            font-weight: 700;
            margin-bottom: 10px;
            line-height: 1.2;
        }

        .hero-content p {
            font-size: 16px;
            opacity: 0.9;
            margin-bottom: 20px;
        }

        .attendance-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 15px;
            padding: 20px;
            text-align: center;
            backdrop-filter: blur(15px);
        }

        .stat-number {
            font-size: 32px;
            font-weight: 700;
            margin-bottom: 5px;
            color: white;
        }

        .stat-label {
            font-size: 14px;
            opacity: 0.8;
            color: white;
        }

        .attendance-list {
            background: rgba(0, 0, 0, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 15px;
            padding: 25px;
            backdrop-filter: blur(15px);
        }

        .section-title {
            color: white;
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 20px;
        }

        .attendance-item {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 20px;
            transition: all 0.3s ease;
        }

        .attendance-item:hover {
            background: rgba(255, 255, 255, 0.15);
            transform: translateY(-2px);
        }

        .attendance-icon {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            color: white;
        }

        .attendance-icon.hadir {
            background: linear-gradient(45deg, #10b981, #06b6d4);
        }

        .attendance-icon.sakit {
            background: linear-gradient(45deg, #f59e0b, #ef4444);
        }

        .attendance-icon.izin {
            background: linear-gradient(45deg, #8b5cf6, #ec4899);
        }

        .attendance-icon.terlambat {
            background: linear-gradient(45deg, #f97316, #dc2626);
        }

        .attendance-details {
            flex: 1;
        }

        .attendance-date {
            font-size: 16px;
            font-weight: 600;
            color: white;
            margin-bottom: 5px;
        }

        .attendance-time {
            font-size: 14px;
            color: white;
            margin-bottom: 5px;
        }

        .attendance-status {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .attendance-status.hadir {
            background: rgba(16, 185, 129, 0.2);
            color: #10b981;
            border: 1px solid rgba(16, 185, 129, 0.3);
        }

        .attendance-status.sakit {
            background: rgba(245, 158, 11, 0.2);
            color: #f59e0b;
            border: 1px solid rgba(245, 158, 11, 0.3);
        }

        .attendance-status.izin {
            background: rgba(139, 92, 246, 0.2);
            color: #8b5cf6;
            border: 1px solid rgba(139, 92, 246, 0.3);
        }

        .attendance-status.terlambat {
            background: rgba(249, 115, 22, 0.2);
            color: #f97316;
            border: 1px solid rgba(249, 115, 22, 0.3);
        }

        .attendance-note {
            font-size: 12px;
            color: rgba(255, 255, 255, 0.7);
            font-style: italic;
        }

        .no-data {
            text-align: center;
            padding: 60px 20px;
            color: rgba(255, 255, 255, 0.7);
        }

        .no-data-icon {
            font-size: 48px;
            margin-bottom: 20px;
            opacity: 0.5;
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
            z-index: 1000;
        }

        .back-btn:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: translateY(-2px);
        }

        @media (max-width: 1024px) {
            .container {
                flex-direction: column;
                width: 98%;
                max-width: 100%;
            }
            
            .sidebar {
                width: 100%;
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
    <a href="mydata.php" class="back-btn">← Kembali ke Beranda</a>

    <div class="container">
        <div class="sidebar">
            <div class="logo">
                <div class="logo-icon">U</div>
                <span>UPTD TEKKOM</span>
            </div>

            <div class="nav-section">
                <a href="mydata.php" class="nav-item">
                    <div class="nav-icon">📊</div>
                    <span>Beranda</span>
                </a>
                <div class="nav-item active">
                    <div class="nav-icon">📥</div>
                    <span>Kotak Masuk</span>
                </div>
                <a href="absensi.php" class="nav-item">
                    <div class="nav-icon">📚</div>
                    <span>Absensi</span>
                </a>
                <a href="tugas.php" class="nav-item" style="text-decoration: none; color: inherit;">
                    <div class="nav-icon">📝</div>
                    <span>Tugas</span>
                </a>
               
            </div>

            <div class="friends-section">
                <div class="friends-title">Teman</div>
                <div class="friend-item">
                    <div class="friend-avatar">BM</div>
                    <div class="friend-info">
                        <div class="friend-name">Bagus Mahase</div>
                        <div class="friend-status">UI/UX Design</div>
                    </div>
                </div>
                <div class="friend-item">
                    <div class="friend-avatar">SD</div>
                    <div class="friend-info">
                        <div class="friend-name">Sir Dandy</div>
                        <div class="friend-status">3D Artist</div>
                    </div>
                </div>
                <div class="friend-item">
                    <div class="friend-avatar">JT</div>
                    <div class="friend-info">
                        <div class="friend-name">Jhon Tosdo</div>
                        <div class="friend-status">Mentor</div>
                    </div>
                </div>
            </div>

            <div style="margin-top: auto; padding-top: 30px;">
                <div class="nav-item">
                    <div class="nav-icon">⚙️</div>
                    <span>Pengaturan</span>
                </div>
                <a href="?logout=1" class="nav-item" style="text-decoration: none; color: inherit;">
                    <div class="nav-icon">🚪</div>
                    <span>Keluar</span>
                </a>
            </div>
        </div>

        <div class="main-content">
            <div class="header">
                <div class="search-bar">
                    <div class="search-icon">🔍</div>
                    <input type="text" class="search-input" placeholder="Cari riwayat absensi...">
                </div>
                <div class="user-profile">
                    <span>📧</span>
                    <span>🔔</span>
                    <div class="user-avatar"></div>
                    <span><?= htmlspecialchars($userName) ?></span>
                </div>
            </div>

            <div class="hero-section">
                <div class="hero-content">
                    <div style="font-size: 14px; opacity: 0.8; margin-bottom: 10px;">UPTD TEKNOLOGI DAN KOMUNIKASI</div>
                    <h1>Riwayat Absensi Digital</h1>
                    <p>Lihat dan kelola data kehadiran Anda</p>
                </div>
            </div>

            <?php
            // Calculate statistics
            $total_attendance = count($attendance_data);
            $hadir_count = 0;
            $sakit_count = 0;
            $izin_count = 0;
            $terlambat_count = 0;

            foreach ($attendance_data as $attendance) {
                switch ($attendance['status']) {
                    case 'hadir':
                        $hadir_count++;
                        break;
                    case 'sakit':
                        $sakit_count++;
                        break;
                    case 'izin':
                        $izin_count++;
                        break;
                    case 'terlambat':
                        $terlambat_count++;
                        break;
                }
            }
            ?>

            <div class="attendance-stats">
                <div class="stat-card">
                    <div class="stat-number"><?= $total_attendance ?></div>
                    <div class="stat-label">Total Absensi</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number"><?= $hadir_count ?></div>
                    <div class="stat-label">Hadir</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number"><?= $sakit_count ?></div>
                    <div class="stat-label">Sakit</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number"><?= $izin_count ?></div>
                    <div class="stat-label">Izin</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number"><?= $terlambat_count ?></div>
                    <div class="stat-label">Terlambat</div>
                </div>
            </div>

            <div class="attendance-list">
                <div class="section-title">Riwayat Absensi Terbaru</div>
                
                <?php if (empty($attendance_data)): ?>
                    <div class="no-data">
                        <div class="no-data-icon">📭</div>
                        <h3>Belum ada data absensi</h3>
                        <p>Anda belum melakukan absensi. Silakan lakukan absensi terlebih dahulu.</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($attendance_data as $attendance): ?>
                        <div class="attendance-item">
                            <div class="attendance-icon <?= $attendance['status'] ?>">
                                <?php
                                switch ($attendance['status']) {
                                    case 'hadir':
                                        echo '✅';
                                        break;
                                    case 'sakit':
                                        echo '🤒';
                                        break;
                                    case 'izin':
                                        echo '📝';
                                        break;
                                    case 'terlambat':
                                        echo '⏰';
                                        break;
                                }
                                ?>
                            </div>
                            <div class="attendance-details">
                                <div class="attendance-date">
                                    <?= date('l, d F Y', strtotime($attendance['tanggal'])) ?>
                                </div>
                                <div class="attendance-time">
                                    Waktu: <?= date('H:i', strtotime($attendance['waktu_masuk'])) ?>
                                </div>
                                <div class="attendance-status <?= $attendance['status'] ?>">
                                    <?= ucfirst($attendance['status']) ?>
                                </div>
                                <?php if (!empty($attendance['keterangan'])): ?>
                                    <div class="attendance-note">
                                        "<?= htmlspecialchars($attendance['keterangan']) ?>"
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script>
        // Logout confirmation
        document.querySelector('a[href*="logout"]').addEventListener('click', function(e) {
            if (!confirm('Apakah Anda yakin ingin logout? Session akan dihapus.')) {
                e.preventDefault();
            }
        });

        // Search functionality
        document.querySelector('.search-input').addEventListener('input', function(e) {
            const searchTerm = e.target.value.toLowerCase();
            const attendanceItems = document.querySelectorAll('.attendance-item');
            
            attendanceItems.forEach(item => {
                const text = item.textContent.toLowerCase();
                if (text.includes(searchTerm)) {
                    item.style.display = 'flex';
                } else {
                    item.style.display = 'none';
                }
            });
        });
    </script>
</body>
</html> 