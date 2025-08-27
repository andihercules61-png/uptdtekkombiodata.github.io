-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 08 Agu 2025 pada 04.03
-- Versi server: 10.4.32-MariaDB
-- Versi PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_biodata`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `absensi`
--

CREATE TABLE `absensi` (
  `id` int(11) NOT NULL,
  `nama` varchar(255) NOT NULL,
  `tanggal` date NOT NULL,
  `waktu_masuk` time NOT NULL,
  `status` enum('hadir','sakit','izin','terlambat') NOT NULL,
  `keterangan` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `absensi`
--

INSERT INTO `absensi` (`id`, `nama`, `tanggal`, `waktu_masuk`, `status`, `keterangan`, `created_at`) VALUES
(1, 'Test User', '2025-08-05', '08:00:00', 'hadir', 'Test attendance record', '2025-08-05 00:43:53'),
(2, 'M Baihaqi Ibrani', '2025-08-05', '08:45:00', 'hadir', 'hadir', '2025-08-05 00:45:07'),
(3, 'ibran beku', '2025-08-07', '08:04:00', 'hadir', 'hadir', '2025-08-07 00:04:55'),
(4, 'ibran beku', '2025-08-08', '08:21:00', 'hadir', 'hadir', '2025-08-08 00:22:04');

-- --------------------------------------------------------

--
-- Struktur dari tabel `signup`
--

CREATE TABLE `signup` (
  `id` int(11) NOT NULL,
  `nama_lengkap` varchar(255) NOT NULL,
  `tanggal_lahir` date NOT NULL,
  `asal_sekolah` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `signup`
--

INSERT INTO `signup` (`id`, `nama_lengkap`, `tanggal_lahir`, `asal_sekolah`, `password`, `created_at`, `updated_at`) VALUES
(17, 'M Baihaqi Ibrani', '2008-03-21', 'SMK 7 SAMARINDA', '$2y$10$mS1j8D6xJrSd3OYlM/XcBey96lW7nvuyVOgySC5doOIt3ak5dV9fG', '2025-08-05 00:35:28', '2025-08-05 00:35:28'),
(18, 'ibran beku', '2008-03-21', 'SMK NEGERI 7 SAMARINDA', '$2y$10$NrpMdUqmhuj6SQYauEIVwekC4Ndu5sv6OfZZuDn.6TL56d1RmJ1R.', '2025-08-07 00:04:26', '2025-08-07 00:04:26');

-- --------------------------------------------------------

--
-- Struktur dari tabel `tugas`
--

CREATE TABLE `tugas` (
  `id` int(11) NOT NULL,
  `nama` varchar(255) NOT NULL,
  `judul_tugas` varchar(255) NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `prioritas` enum('rendah','sedang','tinggi') DEFAULT 'sedang',
  `status` enum('belum_selesai','selesai') DEFAULT 'belum_selesai',
  `deadline` date DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `tugas`
--

INSERT INTO `tugas` (`id`, `nama`, `judul_tugas`, `deskripsi`, `prioritas`, `status`, `deadline`, `created_at`, `updated_at`) VALUES
(2, 'ibran beku', 'website biodata', 'menganalisis website biodata', 'tinggi', 'selesai', '2025-09-08', '2025-08-07 00:06:34', '2025-08-08 00:28:14');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `absensi`
--
ALTER TABLE `absensi`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_absensi_nama` (`nama`),
  ADD KEY `idx_absensi_tanggal` (`tanggal`),
  ADD KEY `idx_absensi_created_at` (`created_at`);

--
-- Indeks untuk tabel `signup`
--
ALTER TABLE `signup`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_nama_lengkap` (`nama_lengkap`),
  ADD KEY `idx_created_at` (`created_at`);

--
-- Indeks untuk tabel `tugas`
--
ALTER TABLE `tugas`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `absensi`
--
ALTER TABLE `absensi`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT untuk tabel `signup`
--
ALTER TABLE `signup`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT untuk tabel `tugas`
--
ALTER TABLE `tugas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
