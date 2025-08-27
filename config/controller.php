<?php
require_once 'koneksi.php';

class BiodataController {
    private $db;

    public function __construct($database) {
        $this->db = $database;
    }

    // Fungsi untuk menambah data biodata
    public function tambahBiodata($nama_lengkap, $tanggal_lahir, $asal_sekolah, $password) {
        // Hash password untuk keamanan
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        // Escape string untuk mencegah SQL injection
        $nama_lengkap = mysqli_real_escape_string($this->db, $nama_lengkap);
        $tanggal_lahir = mysqli_real_escape_string($this->db, $tanggal_lahir);
        $asal_sekolah = mysqli_real_escape_string($this->db, $asal_sekolah);
        
        $query = "INSERT INTO signup (nama_lengkap, tanggal_lahir, asal_sekolah, password, created_at) 
                  VALUES ('$nama_lengkap', '$tanggal_lahir', '$asal_sekolah', '$hashed_password', NOW())";
        
        if (mysqli_query($this->db, $query)) {
            return [
                'status' => 'success',
                'message' => 'Data berhasil disimpan!'
            ];
        } else {
            return [
                'status' => 'error',
                'message' => 'Gagal menyimpan data: ' . mysqli_error($this->db)
            ];
        }
    }

    // Fungsi untuk mengambil semua data biodata
    public function getAllBiodata() {
        $query = "SELECT * FROM signup ORDER BY created_at DESC";
        $result = mysqli_query($this->db, $query);
        
        $data = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $data[] = $row;
        }
        
        return $data;
    }

    // Fungsi untuk mengambil data berdasarkan ID
    public function getBiodataById($id) {
        $id = mysqli_real_escape_string($this->db, $id);
        $query = "SELECT * FROM signup WHERE id = '$id'";
        $result = mysqli_query($this->db, $query);
        
        return mysqli_fetch_assoc($result);
    }

    // Fungsi untuk update data biodata
    public function updateBiodata($id, $nama_lengkap, $tanggal_lahir, $asal_sekolah, $password = null) {
        $id = mysqli_real_escape_string($this->db, $id);
        $nama_lengkap = mysqli_real_escape_string($this->db, $nama_lengkap);
        $tanggal_lahir = mysqli_real_escape_string($this->db, $tanggal_lahir);
        $asal_sekolah = mysqli_real_escape_string($this->db, $asal_sekolah);
        
        if ($password) {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $query = "UPDATE signup SET 
                      nama_lengkap = '$nama_lengkap', 
                      tanggal_lahir = '$tanggal_lahir', 
                      asal_sekolah = '$asal_sekolah', 
                      password = '$hashed_password', 
                      updated_at = NOW() 
                      WHERE id = '$id'";
        } else {
            $query = "UPDATE signup SET 
                      nama_lengkap = '$nama_lengkap', 
                      tanggal_lahir = '$tanggal_lahir', 
                      asal_sekolah = '$asal_sekolah', 
                      updated_at = NOW() 
                      WHERE id = '$id'";
        }
        
        if (mysqli_query($this->db, $query)) {
            return [
                'status' => 'success',
                'message' => 'Data berhasil diupdate!'
            ];
        } else {
            return [
                'status' => 'error',
                'message' => 'Gagal mengupdate data: ' . mysqli_error($this->db)
            ];
        }
    }

    // Fungsi untuk menghapus data biodata
    public function deleteBiodata($id) {
        $id = mysqli_real_escape_string($this->db, $id);
        $query = "DELETE FROM signup WHERE id = '$id'";
        
        if (mysqli_query($this->db, $query)) {
            return [
                'status' => 'success',
                'message' => 'Data berhasil dihapus!'
            ];
        } else {
            return [
                'status' => 'error',
                'message' => 'Gagal menghapus data: ' . mysqli_error($this->db)
            ];
        }
    }

    // Fungsi untuk validasi input
    public function validateInput($data) {
        $errors = [];
        
        if (empty($data['nama_lengkap'])) {
            $errors[] = 'Nama lengkap harus diisi';
        }
        
        if (empty($data['tanggal_lahir'])) {
            $errors[] = 'Tanggal lahir harus diisi';
        }
        
        if (empty($data['asal_sekolah'])) {
            $errors[] = 'Asal sekolah harus diisi';
        }
        
        if (empty($data['password'])) {
            $errors[] = 'Password harus diisi';
        } elseif (strlen($data['password']) < 6) {
            $errors[] = 'Password minimal 6 karakter';
        }
        
        return $errors;
    }
}

// Inisialisasi controller
$controller = new BiodataController($db);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $response = [];
    
    // Validasi input
    $errors = $controller->validateInput($_POST);
    
    if (empty($errors)) {
        // Proses tambah data
        $result = $controller->tambahBiodata(
            $_POST['nama_lengkap'],
            $_POST['tanggal_lahir'],
            $_POST['asal_sekolah'],
            $_POST['password']
        );
        
        $response = $result;
    } else {
        $response = [
            'status' => 'error',
            'message' => implode(', ', $errors)
        ];
    }
    
    // Return JSON response
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}
?>
