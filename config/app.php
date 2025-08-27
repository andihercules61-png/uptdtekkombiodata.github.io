<?php
// Start session
session_start();

// Include required files
require_once 'koneksi.php';
require_once 'controller.php';

// Initialize controller
$controller = new BiodataController($db);

// Handle different actions
$action = isset($_GET['action']) ? $_GET['action'] : 'home';

// Set content type for AJAX requests
if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
    header('Content-Type: application/json');
}

// Route handling
switch ($action) {
    case 'home':
        // Redirect to main form
        header('Location: ../pengguna/biodata.php');
        exit;
        break;
        
    case 'data':
        // Redirect to data view
        header('Location: ../pengguna/data_biodata.php');
        exit;
        break;
        
    case 'add':
        // Handle add data via AJAX
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $response = [];
            
            // Validate input
            $errors = $controller->validateInput($_POST);
            
            if (empty($errors)) {
                // Process add data
                $result = $controller->tambahBiodata(
                    $_POST['nama_lengkap'],
                    $_POST['tanggal_lahir'],
                    $_POST['asal_sekolah'],
                    $_POST['password']
                );
                
                // If successful, save session data
                if ($result['status'] === 'success') {
                    $_SESSION['user_logged_in'] = true;
                    $_SESSION['user_name'] = $_POST['nama_lengkap'];
                    $_SESSION['login_time'] = time();
                }
                
                $response = $result;
            } else {
                $response = [
                    'status' => 'error',
                    'message' => implode(', ', $errors)
                ];
            }
            
            echo json_encode($response);
            exit;
        }
        break;
        
    case 'get_all':
        // Get all data
        $data = $controller->getAllBiodata();
        echo json_encode([
            'status' => 'success',
            'data' => $data
        ]);
        exit;
        break;
        
    case 'get_by_id':
        // Get data by ID
        if (isset($_GET['id'])) {
            $data = $controller->getBiodataById($_GET['id']);
            if ($data) {
                echo json_encode([
                    'status' => 'success',
                    'data' => $data
                ]);
            } else {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Data tidak ditemukan'
                ]);
            }
        } else {
            echo json_encode([
                'status' => 'error',
                'message' => 'ID tidak diberikan'
            ]);
        }
        exit;
        break;
        
    case 'update':
        // Handle update data
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['id'])) {
            $response = $controller->updateBiodata(
                $_POST['id'],
                $_POST['nama_lengkap'],
                $_POST['tanggal_lahir'],
                $_POST['asal_sekolah'],
                isset($_POST['password']) ? $_POST['password'] : null
            );
            
            echo json_encode($response);
            exit;
        }
        break;
        
    case 'delete':
        // Handle delete data
        if (isset($_GET['id'])) {
            $response = $controller->deleteBiodata($_GET['id']);
            echo json_encode($response);
            exit;
        }
        break;
        
    case 'check_connection':
        // Check database connection
        if ($db) {
            echo json_encode([
                'status' => 'success',
                'message' => 'Database terhubung dengan baik'
            ]);
        } else {
            echo json_encode([
                'status' => 'error',
                'message' => 'Gagal terhubung ke database'
            ]);
        }
        exit;
        break;
        
    case 'stats':
        // Get statistics
        $allData = $controller->getAllBiodata();
        $totalRecords = count($allData);
        
        // Get today's registrations
        $today = date('Y-m-d');
        $todayCount = 0;
        foreach ($allData as $data) {
            if (date('Y-m-d', strtotime($data['created_at'])) === $today) {
                $todayCount++;
            }
        }
        
        echo json_encode([
            'status' => 'success',
            'stats' => [
                'total_records' => $totalRecords,
                'today_registrations' => $todayCount
            ]
        ]);
        exit;
        break;
        
    default:
        // Default action - show error
        if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
            echo json_encode([
                'status' => 'error',
                'message' => 'Action tidak valid'
            ]);
            exit;
        } else {
            header('Location: ../pengguna/biodata.php');
            exit;
        }
        break;
}

// If not AJAX request and no action specified, redirect to main page
if (!isset($_SERVER['HTTP_X_REQUESTED_WITH'])) {
    header('Location: ../pengguna/biodata.php');
    exit;
}
?>
