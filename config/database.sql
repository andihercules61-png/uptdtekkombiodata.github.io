-- Database: db_biodata
-- Tabel: signup

CREATE DATABASE IF NOT EXISTS db_biodata;
USE db_biodata;

CREATE TABLE IF NOT EXISTS signup (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_lengkap VARCHAR(255) NOT NULL,
    tanggal_lahir DATE NOT NULL,
    asal_sekolah VARCHAR(255) NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Tabel: absensi
CREATE TABLE IF NOT EXISTS absensi (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(255) NOT NULL,
    tanggal DATE NOT NULL,
    waktu_masuk TIME NOT NULL,
    status ENUM('hadir', 'sakit', 'izin', 'terlambat') NOT NULL,
    keterangan TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Index untuk optimasi query
CREATE INDEX idx_nama_lengkap ON signup(nama_lengkap);
CREATE INDEX idx_created_at ON signup(created_at);
CREATE INDEX idx_absensi_nama ON absensi(nama);
CREATE INDEX idx_absensi_tanggal ON absensi(tanggal);
CREATE INDEX idx_absensi_created_at ON absensi(created_at);

-- Contoh data untuk testing (opsional)
-- INSERT INTO signup (nama_lengkap, tanggal_lahir, asal_sekolah, password) VALUES
-- ('John Doe', '1995-05-15', 'SMA Negeri 1 Jakarta', '$2y$10$example_hash'),
-- ('Jane Smith', '1998-08-20', 'SMA Negeri 2 Bandung', '$2y$10$example_hash'); 