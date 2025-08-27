<?php
require_once '../config/controller.php';
$allBiodata = $controller->getAllBiodata();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Biodata - UPTD TEKKOM</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background: url('forest 2.jpg') center center/cover no-repeat fixed;
            min-height: 100vh;
            position: relative;
        }

        .background-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.3);
            backdrop-filter: blur(2px);
            z-index: 1;
        }

        .container {
            position: relative;
            z-index: 2;
            padding: 40px 20px;
            max-width: 1200px;
            margin: 0 auto;
        }

        .header {
            text-align: center;
            margin-bottom: 40px;
            color: white;
        }

        .header h1 {
            font-size: 36px;
            margin-bottom: 10px;
        }

        .header p {
            font-size: 16px;
            opacity: 0.8;
        }

        .back-btn {
            position: absolute;
            top: 20px;
            left: 20px;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: white;
            padding: 12px 20px;
            border-radius: 8px;
            text-decoration: none;
            font-size: 14px;
            transition: all 0.3s ease;
        }

        .back-btn:hover {
            background: rgba(255, 255, 255, 0.2);
            transform: translateY(-2px);
        }

        .data-card {
            background: rgba(0, 0, 0, 0.4);
            backdrop-filter: blur(8px);
            border-radius: 16px;
            padding: 30px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            margin-bottom: 20px;
        }

        .table-container {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            color: white;
        }

        th, td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        th {
            background: rgba(255, 255, 255, 0.1);
            font-weight: 600;
            font-size: 14px;
        }

        tr:hover {
            background: rgba(255, 255, 255, 0.05);
        }

        .action-btn {
            background: linear-gradient(135deg, #4CAF50, #66BB6A);
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 12px;
            margin-right: 5px;
            transition: all 0.3s ease;
        }

        .action-btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(76, 175, 80, 0.3);
        }

        .delete-btn {
            background: linear-gradient(135deg, #f44336, #e53935);
        }

        .delete-btn:hover {
            box-shadow: 0 4px 12px rgba(244, 67, 54, 0.3);
        }

        .empty-state {
            text-align: center;
            color: white;
            padding: 60px 20px;
        }

        .empty-state h3 {
            font-size: 24px;
            margin-bottom: 10px;
        }

        .empty-state p {
            opacity: 0.7;
            margin-bottom: 20px;
        }

        .add-btn {
            background: linear-gradient(135deg, #4CAF50, #66BB6A);
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 14px;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s ease;
        }

        .add-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(76, 175, 80, 0.3);
        }

        @media (max-width: 768px) {
            .container {
                padding: 20px 10px;
            }
            
            .header h1 {
                font-size: 28px;
            }
            
            th, td {
                padding: 10px 8px;
                font-size: 14px;
            }
            
            .action-btn {
                padding: 6px 12px;
                font-size: 11px;
            }
        }
    </style>
</head>
<body>
    <div class="background-overlay"></div>
    
    <a href="biodata.php" class="back-btn">← Kembali</a>

    <div class="container">
        <div class="header">
            <h1>Data Biodata</h1>
            <p>Daftar semua data yang telah tersimpan</p>
        </div>

        <div class="data-card">
            <?php if (empty($allBiodata)): ?>
                <div class="empty-state">
                    <h3>Belum ada data</h3>
                    <p>Data biodata akan muncul di sini setelah Anda menambahkan data baru.</p>
                    <a href="biodata.php" class="add-btn">Tambah Data Baru</a>
                </div>
            <?php else: ?>
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Lengkap</th>
                                <th>Tanggal Lahir</th>
                                <th>Asal Sekolah</th>
                                <th>Tanggal Dibuat</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($allBiodata as $index => $data): ?>
                                <tr>
                                    <td><?= $index + 1 ?></td>
                                    <td><?= htmlspecialchars($data['nama_lengkap']) ?></td>
                                    <td><?= date('d/m/Y', strtotime($data['tanggal_lahir'])) ?></td>
                                    <td><?= htmlspecialchars($data['asal_sekolah']) ?></td>
                                    <td><?= date('d/m/Y H:i', strtotime($data['created_at'])) ?></td>
                                    <td>
                                        <button class="action-btn" onclick="editData(<?= $data['id'] ?>)">Edit</button>
                                        <button class="action-btn delete-btn" onclick="deleteData(<?= $data['id'] ?>)">Hapus</button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script>
        function editData(id) {
            // Implementasi edit data
            alert('Fitur edit akan segera tersedia!');
        }

        function deleteData(id) {
            if (confirm('Apakah Anda yakin ingin menghapus data ini?')) {
                // Implementasi delete data
                alert('Fitur hapus akan segera tersedia!');
            }
        }
    </script>
</body>
</html> 