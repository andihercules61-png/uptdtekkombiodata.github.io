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

// Include news fetcher
require_once 'news_fetcher.php';

// Debug: Print news data
error_log("News data in mydata.php: " . json_encode($news_data));

// Get attendance count for notification
$attendance_count = 0;
$query = "SELECT COUNT(*) as count FROM absensi WHERE nama = ?";
$stmt = mysqli_prepare($db, $query);
mysqli_stmt_bind_param($stmt, "s", $userName);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$row = mysqli_fetch_assoc($result);
$attendance_count = $row['count'];
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
    <title>Course Dashboard - UPTD TEKKOM</title>
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
        }

        .nav-item:hover, .nav-item.active {
            background: rgba(255, 255, 255, 0.1);
            color: white;
        }

        .nav-item {
            position: relative;
        }

        .notification-badge {
            position: absolute;
            top: 8px;
            right: 8px;
            background: #ef4444;
            color: white;
            border-radius: 50%;
            width: 18px;
            height: 18px;
            font-size: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
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

        .hero-btn {
            background: rgba(255, 255, 255, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.3);
            color: white;
            padding: 12px 24px;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .hero-btn:hover {
            background: rgba(255, 255, 255, 0.3);
        }

        .course-categories {
            display: flex;
            gap: 20px;
            margin-bottom: 30px;
        }

        .category-item {
            background: rgba(0, 0, 0, 0.3);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 12px;
            padding: 15px 20px;
            color: white;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
            backdrop-filter: blur(15px);
        }

        .category-item:hover {
            background: rgba(0, 0, 0, 0.4);
        }

        .content-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 30px;
        }

        .courses-section {
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

        .course-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 20px;
        }

        .course-card {
            background: rgba(0, 0, 0, 0.3);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 12px;
            overflow: hidden;
            transition: all 0.3s ease;
            cursor: pointer;
            backdrop-filter: blur(15px);
        }

        .course-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        }

        .course-image {
            width: 100%;
            height: 160px;
            background: linear-gradient(45deg, #f59e0b, #ef4444);
            position: relative;
            overflow: hidden;
        }
        
        .course-image::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(45deg, rgba(0,0,0,0.3), rgba(0,0,0,0.1));
            z-index: 1;
        }

        .course-content {
            padding: 15px;
        }

        .course-title {
            font-size: 16px;
            font-weight: 600;
            color: white;
            margin-bottom: 8px;
        }

        .course-instructor {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 12px;
            color: white;
        }

        .instructor-avatar {
            width: 20px;
            height: 20px;
            border-radius: 50%;
            background: linear-gradient(45deg, #6366f1, #8b5cf6);
        }

        .sidebar-right {
            background: rgba(0, 0, 0, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 15px;
            padding: 25px;
            backdrop-filter: blur(15px);
        }

        .stats-section {
            margin-bottom: 30px;
        }

        .stats-chart {
            width: 100%;
            height: 200px;
            background: rgba(0, 0, 0, 0.2);
            border-radius: 10px;
            margin-bottom: 15px;
            position: relative;
            overflow: hidden;
        }

        .chart-bars {
            display: flex;
            align-items: end;
            justify-content: space-around;
            height: 100%;
            padding: 20px;
        }

        .chart-bar {
            width: 20px;
            background: linear-gradient(to top, #6366f1, #8b5cf6);
            border-radius: 2px;
        }

        .mentors-section {
            color: white;
        }

        .mentor-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .mentor-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(45deg, #10b981, #06b6d4);
        }

        .mentor-info {
            flex: 1;
        }

        .mentor-name {
            font-size: 14px;
            font-weight: 500;
            color: white;
        }

        .mentor-role {
            font-size: 12px;
            color: rgba(255, 255, 255, 0.7);
        }

        .follow-btn {
            background: rgba(255, 255, 255, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.3);
            color: white;
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 12px;
            cursor: pointer;
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
            
            .content-grid {
                grid-template-columns: 1fr;
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
    <a href="index.php" class="back-btn">← Kembali ke Beranda</a>

    <div class="container">
        <div class="sidebar">
            <div class="logo">
                <div class="logo-icon">U</div>
                <span>UPTD TEKKOM</span>
            </div>

            <div class="nav-section">
                <div class="nav-item active">
                    <div class="nav-icon">📊</div>
                    <span>Beranda</span>
                </div>
                <a href="inbox.php" class="nav-item" style="text-decoration: none; color: inherit;">
                    <div class="nav-icon">📥</div>
                    <span>Kotak Masuk</span>
                    <?php if ($attendance_count > 0): ?>
                        <div class="notification-badge"><?= $attendance_count ?></div>
                    <?php endif; ?>
                </a>
                <a href="absensi.php" class="nav-item" style="text-decoration: none; color: inherit;">
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
                    <input type="text" class="search-input" placeholder="Cari menu...">
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
            <h1>Selamat Datang di Dashboard<br>UPTD TEKKOM</h1>
                    <p>Kelola data dan informasi Anda dengan mudah</p>
                    
                </div>
            </div>

            <div class="course-categories">
                <div class="category-item">📊 Data Management</div>
                <div class="category-item">👥 User Management</div>
                <div class="category-item">📈 Analytics</div>
            </div>

            <div class="content-grid">
                <div class="courses-section">
                    <div class="section-title">Berita Terkini</div>
                    <div class="course-grid">
                        <?php 
                        // Debug: Print news data
                        echo "<!-- Debug: News data loaded -->";
                        foreach ($news_data as $index => $news): 
                            echo "<!-- News $index URL: " . htmlspecialchars($news['url']) . " -->";
                        ?>
                        <div class="course-card">
                            <div class="course-image" style="background-image: url('<?= htmlspecialchars($news['image']) ?>'); background-size: cover; background-position: center;">
                                <div style="position: absolute; top: 10px; right: 10px; background: rgba(0,0,0,0.7); color: white; padding: 4px 8px; border-radius: 4px; font-size: 10px; font-weight: 600;">
                                    <?= htmlspecialchars($news['category']) ?>
                                </div>
                            </div>
                            <div class="course-content">
                                <div class="course-title"><?= htmlspecialchars($news['title']) ?></div>
                                <div class="course-instructor">
                                    <div class="instructor-avatar"></div>
                                    <span><?= htmlspecialchars($news['category']) ?> • <?= htmlspecialchars($news['time_ago']) ?></span>
                                </div>
                                <p style="color: white; font-size: 12px; margin-top: 8px; line-height: 1.4;">
                                    <?= htmlspecialchars($news['summary']) ?>
                                </p>
                                <div style="margin-top: 10px;">
                                    <a href="<?= htmlspecialchars($news['url']) ?>" style="color: #6366f1; text-decoration: none; font-size: 12px; font-weight: 500;" onclick="console.log('Direct link clicked:', this.href);">
                                        Baca Selengkapnya →
                                    </a>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="sidebar-right">
                    <div class="stats-section">
                        <div class="section-title">Trending Topics</div>
                        <div style="display: flex; align-items: center; gap: 15px; margin-bottom: 15px;">
                            <div style="width: 60px; height: 60px; border-radius: 50%; background: rgba(255,255,255,0.2); display: flex; align-items: center; justify-content: center; color: white; font-size: 24px;">🔥</div>
                            <div style="color: white;">
                                <div style="font-size: 12px; opacity: 0.7;">Trending Hari Ini</div>
                            </div>
                        </div>
                        <div style="background: rgba(0,0,0,0.2); border-radius: 10px; padding: 15px; margin-bottom: 15px;">
                            <div style="color: white; font-size: 14px; font-weight: 600; margin-bottom: 10px;">Topik Terpopuler</div>
                            <div style="color: white; font-size: 12px; line-height: 1.6;">
                                <div style="margin-bottom: 8px;">1. #TeknologiAI</div>
                                <div style="margin-bottom: 8px;">2. #DigitalIndonesia</div>
                                <div style="margin-bottom: 8px;">3. #StartupTech</div>
                                <div style="margin-bottom: 8px;">4. #EGovernment</div>
                                <div>5. #CyberSecurity</div>
                            </div>
                        </div>
                    </div>

                    <div class="mentors-section">
                        <div class="section-title">Kategori Berita</div>
                        <div class="mentor-item">
                            <div class="mentor-avatar" style="background: linear-gradient(45deg, #ef4444, #dc2626);"></div>
                            <div class="mentor-info">
                                <div class="mentor-name">Teknologi</div>
                                <div class="mentor-role">AI, IoT, Blockchain</div>
                            </div>
                            <button class="follow-btn">Lihat</button>
                        </div>
                        <div class="mentor-item">
                            <div class="mentor-avatar" style="background: linear-gradient(45deg, #10b981, #06b6d4);"></div>
                            <div class="mentor-info">
                                <div class="mentor-name">Pendidikan</div>
                                <div class="mentor-role">E-Learning, Digital</div>
                            </div>
                            <button class="follow-btn">Lihat</button>
                        </div>
                        <div class="mentor-item">
                            <div class="mentor-avatar" style="background: linear-gradient(45deg, #8b5cf6, #ec4899);"></div>
                            <div class="mentor-info">
                                <div class="mentor-name">Bisnis</div>
                                <div class="mentor-role">Startup, Investasi</div>
                            </div>
                            <button class="follow-btn">Lihat</button>
                        </div>
                    </div>
                </div>
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
        
        // Auto refresh news every 24 hours
        function refreshNews() {
            // Check if news cache is older than 24 hours
            const lastRefresh = localStorage.getItem('lastNewsRefresh');
            const now = Date.now();
            const oneDay = 24 * 60 * 60 * 1000; // 24 hours in milliseconds
            
            if (!lastRefresh || (now - parseInt(lastRefresh)) > oneDay) {
                // Refresh the page to get new news
                window.location.reload();
            }
        }
        
        // Run refresh check when page loads
        document.addEventListener('DOMContentLoaded', function() {
            refreshNews();
            localStorage.setItem('lastNewsRefresh', Date.now().toString());
        });
        
        // Add click handlers for news cards
        document.querySelectorAll('.course-card').forEach(card => {
            card.addEventListener('click', function() {
                const link = this.querySelector('a[href]');
                console.log('Link clicked:', link ? link.href : 'No link found');
                if (link && link.href && link.href !== '#' && link.href !== window.location.href + '#') {
                    console.log('Opening URL:', link.href);
                    window.open(link.href, '_blank');
                } else {
                    console.log('Link not valid or empty');
                }
            });
        });
    </script>
</body>
</html>