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

// Debug: Tampilkan informasi session
if (isset($_GET['debug'])) {
    echo "Session Info:<br>";
    echo "isLoggedIn: " . ($isLoggedIn ? 'true' : 'false') . "<br>";
    echo "userName: " . $userName . "<br>";
    echo "Session data: <pre>" . print_r($_SESSION, true) . "</pre>";
    exit();
}

// If not logged in, redirect to index
if (!$isLoggedIn) {
    header('Location: index.php');
    exit();
}

// Include database connection
require_once '../config/koneksi.php';

// Handle form submission for adding new task
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'add_task') {
        $judul_tugas = trim($_POST['judul_tugas'] ?? '');
        $deskripsi = trim($_POST['deskripsi'] ?? '');
        $prioritas = $_POST['prioritas'] ?? 'sedang';
        $deadline = $_POST['deadline'] ?? '';
        
        if (!empty($judul_tugas)) {
            $insert_query = "INSERT INTO tugas (nama, judul_tugas, deskripsi, prioritas, deadline) VALUES (?, ?, ?, ?, ?)";
            $insert_stmt = mysqli_prepare($db, $insert_query);
            
            if ($insert_stmt) {
                mysqli_stmt_bind_param($insert_stmt, "sssss", $userName, $judul_tugas, $deskripsi, $prioritas, $deadline);
                
                if (mysqli_stmt_execute($insert_stmt)) {
                    $message = 'success:Tugas berhasil ditambahkan!';
                } else {
                    $message = 'error:Terjadi kesalahan saat menambah tugas: ' . mysqli_stmt_error($insert_stmt);
                }
                mysqli_stmt_close($insert_stmt);
            } else {
                $message = 'error:Terjadi kesalahan saat menyiapkan query: ' . mysqli_error($db);
            }
        } else {
            $message = 'error:Judul tugas tidak boleh kosong.';
        }
    } elseif ($_POST['action'] === 'update_status') {
        $task_id = $_POST['task_id'] ?? '';
        $new_status = $_POST['new_status'] ?? '';
        
        if (!empty($task_id) && !empty($new_status)) {
            $update_query = "UPDATE tugas SET status = ? WHERE id = ? AND nama = ?";
            $update_stmt = mysqli_prepare($db, $update_query);
            
            if ($update_stmt) {
                mysqli_stmt_bind_param($update_stmt, "sis", $new_status, $task_id, $userName);
                
                if (mysqli_stmt_execute($update_stmt)) {
                    $message = 'success:Status tugas berhasil diperbarui!';
                } else {
                    $message = 'error:Terjadi kesalahan saat memperbarui status: ' . mysqli_stmt_error($update_stmt);
                }
                mysqli_stmt_close($update_stmt);
            } else {
                $message = 'error:Terjadi kesalahan saat menyiapkan query update: ' . mysqli_error($db);
            }
        }
    } elseif ($_POST['action'] === 'delete_task') {
        $task_id = $_POST['task_id'] ?? '';
        
        if (!empty($task_id)) {
            $delete_query = "DELETE FROM tugas WHERE id = ? AND nama = ?";
            $delete_stmt = mysqli_prepare($db, $delete_query);
            
            if ($delete_stmt) {
                mysqli_stmt_bind_param($delete_stmt, "is", $task_id, $userName);
                
                if (mysqli_stmt_execute($delete_stmt)) {
                    $message = 'success:Tugas berhasil dihapus!';
                } else {
                    $message = 'error:Terjadi kesalahan saat menghapus tugas: ' . mysqli_stmt_error($delete_stmt);
                }
                mysqli_stmt_close($delete_stmt);
            } else {
                $message = 'error:Terjadi kesalahan saat menyiapkan query delete: ' . mysqli_error($db);
            }
        }
    }
}

// Get tasks for the current user
$tasks_data = [];
$query = "SELECT * FROM tugas WHERE nama = ? ORDER BY created_at DESC";
$stmt = mysqli_prepare($db, $query);

if ($stmt) {
    mysqli_stmt_bind_param($stmt, "s", $userName);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $tasks_data[] = $row;
        }
    } else {
        $message = 'error:Terjadi kesalahan saat mengambil data tugas: ' . mysqli_error($db);
    }
    mysqli_stmt_close($stmt);
} else {
    $message = 'error:Terjadi kesalahan saat menyiapkan query select: ' . mysqli_error($db);
}

// Debug: Tampilkan jumlah tugas yang ditemukan
if (isset($_GET['debug'])) {
    echo "Jumlah tugas untuk user '$userName': " . count($tasks_data) . "<br>";
    echo "Data tugas: <pre>" . print_r($tasks_data, true) . "</pre>";
    exit();
}

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
    <title>Manajemen Tugas - UPTD TEKKOM</title>
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

        .task-form {
            background: rgba(0, 0, 0, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 30px;
            backdrop-filter: blur(15px);
        }

        .form-title {
            color: white;
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 20px;
        }

        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 20px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
        }

        .form-group.full-width {
            grid-column: 1 / -1;
        }

        .form-label {
            color: white;
            font-size: 14px;
            font-weight: 500;
            margin-bottom: 8px;
        }

        .form-input, .form-select, .form-textarea {
            padding: 12px 16px;
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 10px;
            background: rgba(255, 255, 255, 0.2);
            color: white;
            font-size: 14px;
            backdrop-filter: blur(10px);
        }

        .form-input::placeholder, .form-textarea::placeholder {
            color: rgba(255, 255, 255, 0.7);
        }

        .form-textarea {
            resize: vertical;
            min-height: 100px;
        }

        .btn {
            padding: 12px 24px;
            border: none;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-primary {
            background: linear-gradient(45deg, #6366f1, #8b5cf6);
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(99, 102, 241, 0.4);
        }

        .btn-danger {
            background: linear-gradient(45deg, #ef4444, #dc2626);
            color: white;
        }

        .btn-danger:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(239, 68, 68, 0.4);
        }

        .btn-success {
            background: linear-gradient(45deg, #10b981, #059669);
            color: white;
        }

        .btn-success:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(16, 185, 129, 0.4);
        }

        .tasks-section {
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

        .task-item {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 15px;
            transition: all 0.3s ease;
        }

        .task-item:hover {
            background: rgba(255, 255, 255, 0.15);
            transform: translateY(-2px);
        }

        .task-item.completed {
            opacity: 0.7;
            background: rgba(16, 185, 129, 0.1);
            border-color: rgba(16, 185, 129, 0.3);
        }

        .task-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 10px;
        }

        .task-title {
            font-size: 16px;
            font-weight: 600;
            color: white;
            flex: 1;
        }

        .task-title.completed {
            text-decoration: line-through;
            color: rgba(255, 255, 255, 0.7);
        }

        .task-priority {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-left: 10px;
        }

        .task-priority.rendah {
            background: rgba(16, 185, 129, 0.2);
            color: #10b981;
            border: 1px solid rgba(16, 185, 129, 0.3);
        }

        .task-priority.sedang {
            background: rgba(245, 158, 11, 0.2);
            color: #f59e0b;
            border: 1px solid rgba(245, 158, 11, 0.3);
        }

        .task-priority.tinggi {
            background: rgba(239, 68, 68, 0.2);
            color: #ef4444;
            border: 1px solid rgba(239, 68, 68, 0.3);
        }

        .task-description {
            color: white;
            font-size: 14px;
            margin-bottom: 10px;
            line-height: 1.5;
        }

        .task-meta {
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 12px;
            color: rgba(255, 255, 255, 0.7);
        }

        .task-deadline {
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .task-actions {
            display: flex;
            gap: 10px;
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

        .message {
            padding: 12px 16px;
            border-radius: 10px;
            margin-bottom: 20px;
            font-size: 14px;
            font-weight: 500;
        }

        .message.success {
            background: rgba(16, 185, 129, 0.2);
            color: #10b981;
            border: 1px solid rgba(16, 185, 129, 0.3);
        }

        .message.error {
            background: rgba(239, 68, 68, 0.2);
            color: #ef4444;
            border: 1px solid rgba(239, 68, 68, 0.3);
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

            .form-grid {
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
                <a href="inbox.php" class="nav-item">
                    <div class="nav-icon">📥</div>
                    <span>Kotak Masuk</span>
                </a>
                <a href="absensi.php" class="nav-item">
                    <div class="nav-icon">📚</div>
                    <span>Absensi</span>
                </a>
                <div class="nav-item active">
                    <div class="nav-icon">📝</div>
                    <span>Tugas</span>
                </div>
                
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
                    <input type="text" class="search-input" placeholder="Cari tugas...">
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
                    <h1>Manajemen Tugas</h1>
                    <p>Kelola dan lacak tugas Anda dengan mudah</p>
                    <div style="margin-top: 15px;">
                        
                    </div>
                </div>
            </div>

            <?php if (isset($message)): ?>
                <?php
                $message_parts = explode(':', $message, 2);
                $message_type = $message_parts[0];
                $message_text = $message_parts[1] ?? '';
                ?>
                <div class="message <?= $message_type ?>">
                    <?= htmlspecialchars($message_text) ?>
                </div>
            <?php endif; ?>

            <div class="task-form">
                <div class="form-title">Tambah Tugas Baru</div>
                <form method="POST" action="">
                    <input type="hidden" name="action" value="add_task">
                    <div class="form-grid">
                        <div class="form-group">
                            <label class="form-label">Judul Tugas *</label>
                            <input type="text" name="judul_tugas" class="form-input" placeholder="Masukkan judul tugas" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Prioritas</label>
                            <select name="prioritas" class="form-select">
                                <option value="rendah">Rendah</option>
                                <option value="sedang" selected>Sedang</option>
                                <option value="tinggi">Tinggi</option>
                            </select>
                        </div>
                        <div class="form-group full-width">
                            <label class="form-label">Deskripsi</label>
                            <textarea name="deskripsi" class="form-textarea" placeholder="Masukkan deskripsi tugas (opsional)"></textarea>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Deadline</label>
                            <input type="date" name="deadline" class="form-input">
                        </div>
                        <div class="form-group">
                            <label class="form-label">&nbsp;</label>
                            <button type="submit" class="btn btn-primary">
                                ➕ Tambah Tugas
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <div class="tasks-section">
                <div class="section-title">Daftar Tugas</div>
                
                <?php if (empty($tasks_data)): ?>
                    <div class="no-data">
                        <div class="no-data-icon">📝</div>
                        <h3>Belum ada tugas</h3>
                        <p>Tambahkan tugas pertama Anda di atas.</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($tasks_data as $task): ?>
                        <div class="task-item <?= $task['status'] === 'selesai' ? 'completed' : '' ?>">
                            <div class="task-header">
                                <div class="task-title <?= $task['status'] === 'selesai' ? 'completed' : '' ?>">
                                    <?= htmlspecialchars($task['judul_tugas']) ?>
                                </div>
                                <div class="task-priority <?= $task['prioritas'] ?>">
                                    <?= ucfirst($task['prioritas']) ?>
                                </div>
                            </div>
                            
                            <?php if (!empty($task['deskripsi'])): ?>
                                <div class="task-description">
                                    <?= htmlspecialchars($task['deskripsi']) ?>
                                </div>
                            <?php endif; ?>
                            
                            <div class="task-meta">
                                <div class="task-deadline">
                                    <?php if (!empty($task['deadline'])): ?>
                                        <span>📅</span>
                                        <span>Deadline: <?= date('d/m/Y', strtotime($task['deadline'])) ?></span>
                                    <?php endif; ?>
                                    <span>📅</span>
                                    <span>Dibuat: <?= date('d/m/Y H:i', strtotime($task['created_at'])) ?></span>
                                </div>
                                <div class="task-actions">
                                    <form method="POST" action="" style="display: inline;">
                                        <input type="hidden" name="action" value="update_status">
                                        <input type="hidden" name="task_id" value="<?= $task['id'] ?>">
                                        <input type="hidden" name="new_status" value="<?= $task['status'] === 'selesai' ? 'belum_selesai' : 'selesai' ?>">
                                        <button type="submit" class="btn <?= $task['status'] === 'selesai' ? 'btn-danger' : 'btn-success' ?>">
                                            <?= $task['status'] === 'selesai' ? '❌ Batal Selesai' : '✅ Tandai Selesai' ?>
                                        </button>
                                    </form>
                                    <form method="POST" action="" style="display: inline;" onsubmit="return confirm('Apakah Anda yakin ingin menghapus tugas ini?')">
                                        <input type="hidden" name="action" value="delete_task">
                                        <input type="hidden" name="task_id" value="<?= $task['id'] ?>">
                                        <button type="submit" class="btn btn-danger">
                                            🗑️ Hapus
                                        </button>
                                    </form>
                                </div>
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
            const taskItems = document.querySelectorAll('.task-item');
            
            taskItems.forEach(item => {
                const text = item.textContent.toLowerCase();
                if (text.includes(searchTerm)) {
                    item.style.display = 'block';
                } else {
                    item.style.display = 'none';
                }
            });
        });

        // Auto-hide messages after 5 seconds
        setTimeout(function() {
            const messages = document.querySelectorAll('.message');
            messages.forEach(message => {
                message.style.opacity = '0';
                message.style.transition = 'opacity 0.5s ease';
                setTimeout(() => {
                    message.remove();
                }, 500);
            });
        }, 5000);
    </script>
</body>
</html> 