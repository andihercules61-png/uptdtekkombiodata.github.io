<?php
require_once '../config/app.php';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - UPTD TEKKOM</title>
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

        .nav-buttons {
            display: flex;
            gap: 15px;
            justify-content: center;
            margin-bottom: 30px;
            flex-wrap: wrap;
        }

        .nav-btn {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: white;
            padding: 12px 24px;
            border-radius: 8px;
            text-decoration: none;
            font-size: 14px;
            transition: all 0.3s ease;
        }

        .nav-btn:hover {
            background: rgba(255, 255, 255, 0.2);
            transform: translateY(-2px);
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 40px;
        }

        .stat-card {
            background: rgba(0, 0, 0, 0.4);
            backdrop-filter: blur(8px);
            border-radius: 16px;
            padding: 30px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            text-align: center;
            color: white;
        }

        .stat-number {
            font-size: 48px;
            font-weight: 700;
            margin-bottom: 10px;
            color: #4CAF50;
        }

        .stat-label {
            font-size: 16px;
            opacity: 0.8;
        }

        .data-section {
            background: rgba(0, 0, 0, 0.4);
            backdrop-filter: blur(8px);
            border-radius: 16px;
            padding: 30px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            margin-bottom: 20px;
        }

        .section-title {
            color: white;
            font-size: 24px;
            margin-bottom: 20px;
            text-align: center;
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

        .loading {
            text-align: center;
            color: white;
            padding: 40px;
        }

        .connection-status {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
        }

        .status-connected {
            background: rgba(76, 175, 80, 0.2);
            border: 1px solid rgba(76, 175, 80, 0.3);
            color: #4CAF50;
        }

        .status-disconnected {
            background: rgba(244, 67, 54, 0.2);
            border: 1px solid rgba(244, 67, 54, 0.3);
            color: #f44336;
        }

        @media (max-width: 768px) {
            .container {
                padding: 20px 10px;
            }
            
            .header h1 {
                font-size: 28px;
            }
            
            .stats-grid {
                grid-template-columns: 1fr;
            }
            
            .nav-buttons {
                flex-direction: column;
                align-items: center;
            }
            
            th, td {
                padding: 10px 8px;
                font-size: 14px;
            }
        }
    </style>
</head>
<body>
    <div class="background-overlay"></div>
    
    <div id="connectionStatus" class="connection-status">Memeriksa koneksi...</div>

    <div class="container">
        <div class="header">
            <h1>Dashboard Sistem Biodata</h1>
            <p>Kelola dan pantau data pendaftaran</p>
        </div>

        <div class="nav-buttons">
            <a href="biodata.php" class="nav-btn">Tambah Data Baru</a>
            <a href="data_biodata.php" class="nav-btn">Lihat Semua Data</a>
            <a href="#" class="nav-btn" onclick="refreshStats()">Refresh Statistik</a>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <div id="totalRecords" class="stat-number">-</div>
                <div class="stat-label">Total Data</div>
            </div>
            <div class="stat-card">
                <div id="todayRecords" class="stat-number">-</div>
                <div class="stat-label">Pendaftaran Hari Ini</div>
            </div>
        </div>

        <div class="data-section">
            <h2 class="section-title">Data Terbaru</h2>
            <div id="recentData" class="loading">
                Memuat data...
            </div>
        </div>
    </div>

    <script>
        // Check connection status
        function checkConnection() {
            fetch('../config/app.php?action=check_connection')
                .then(response => response.json())
                .then(data => {
                    const statusDiv = document.getElementById('connectionStatus');
                    if (data.status === 'success') {
                        statusDiv.className = 'connection-status status-connected';
                        statusDiv.textContent = '✓ Terhubung';
                    } else {
                        statusDiv.className = 'connection-status status-disconnected';
                        statusDiv.textContent = '✗ Terputus';
                    }
                })
                .catch(error => {
                    const statusDiv = document.getElementById('connectionStatus');
                    statusDiv.className = 'connection-status status-disconnected';
                    statusDiv.textContent = '✗ Error';
                });
        }

        // Load statistics
        function loadStats() {
            fetch('../config/app.php?action=stats')
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        document.getElementById('totalRecords').textContent = data.stats.total_records;
                        document.getElementById('todayRecords').textContent = data.stats.today_registrations;
                    }
                })
                .catch(error => {
                    console.error('Error loading stats:', error);
                });
        }

        // Load recent data
        function loadRecentData() {
            fetch('../config/app.php?action=get_all')
                .then(response => response.json())
                .then(data => {
                    const container = document.getElementById('recentData');
                    
                    if (data.status === 'success' && data.data.length > 0) {
                        const recentData = data.data.slice(0, 5); // Show only 5 most recent
                        
                        let html = `
                            <div class="table-container">
                                <table>
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Nama Lengkap</th>
                                            <th>Tanggal Lahir</th>
                                            <th>Asal Sekolah</th>
                                            <th>Tanggal Dibuat</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                        `;
                        
                        recentData.forEach((item, index) => {
                            html += `
                                <tr>
                                    <td>${index + 1}</td>
                                    <td>${item.nama_lengkap}</td>
                                    <td>${new Date(item.tanggal_lahir).toLocaleDateString('id-ID')}</td>
                                    <td>${item.asal_sekolah}</td>
                                    <td>${new Date(item.created_at).toLocaleString('id-ID')}</td>
                                </tr>
                            `;
                        });
                        
                        html += `
                                    </tbody>
                                </table>
                            </div>
                        `;
                        
                        container.innerHTML = html;
                    } else {
                        container.innerHTML = '<p style="text-align: center; color: white; opacity: 0.7;">Belum ada data tersimpan</p>';
                    }
                })
                .catch(error => {
                    console.error('Error loading data:', error);
                    document.getElementById('recentData').innerHTML = '<p style="text-align: center; color: #f44336;">Error memuat data</p>';
                });
        }

        // Refresh statistics
        function refreshStats() {
            loadStats();
            loadRecentData();
        }

        // Initialize dashboard
        document.addEventListener('DOMContentLoaded', function() {
            checkConnection();
            loadStats();
            loadRecentData();
            
            // Auto refresh every 30 seconds
            setInterval(() => {
                loadStats();
            }, 30000);
        });
    </script>
</body>
</html> 