-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 03, 2025 at 03:04 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.4.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `aplikasimonitoringkelas`
--

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(191) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(191) NOT NULL,
  `owner` varchar(191) NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `failed_import_rows`
--

CREATE TABLE `failed_import_rows` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `import_id` bigint(20) UNSIGNED NOT NULL,
  `data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`data`)),
  `validation_error` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(191) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `gurus`
--

CREATE TABLE `gurus` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `nip` varchar(191) NOT NULL,
  `nama` varchar(191) NOT NULL,
  `email` varchar(191) NOT NULL,
  `no_telp` varchar(191) DEFAULT NULL,
  `alamat` text DEFAULT NULL,
  `jenis_kelamin` enum('L','P') NOT NULL,
  `tanggal_lahir` date DEFAULT NULL,
  `status` enum('aktif','nonaktif') NOT NULL DEFAULT 'aktif',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `gurus`
--

INSERT INTO `gurus` (`id`, `user_id`, `nip`, `nama`, `email`, `no_telp`, `alamat`, `jenis_kelamin`, `tanggal_lahir`, `status`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 3, '198001012010011001', 'Bu Siti Rahayu', 'siti@sekolah.com', '081234567890', 'Jl. Merdeka No. 1 Jakarta Pusat', 'P', '1980-01-01', 'aktif', '2025-11-29 00:05:51', '2025-11-29 00:05:51', NULL),
(2, NULL, '197502022005022002', 'Pak Budi Santoso', 'budi@sekolah.com', '081234567891', 'Jl. Sudirman No. 5 Jakarta Pusat', 'L', '1975-02-02', 'aktif', '2025-11-29 00:05:51', '2025-11-29 00:05:51', NULL),
(3, NULL, '198203032012033003', 'Bu Lina Marlina', 'lina@sekolah.com', '081234567892', 'Jl. Thamrin No. 10 Jakarta Pusat', 'P', '1982-03-03', 'aktif', '2025-11-29 00:05:51', '2025-11-29 00:05:51', NULL),
(4, NULL, '197804042008044004', 'Pak Agus Prasetyo', 'agus@sekolah.com', '081234567893', 'Jl. Gatot Subroto No. 15 Jakarta Selatan', 'L', '1978-04-04', 'aktif', '2025-11-29 00:05:51', '2025-11-29 00:05:51', NULL),
(5, NULL, '198505052015055005', 'Bu Dian Lestari', 'dian@sekolah.com', '081234567894', 'Jl. HR Rasuna Said No. 20 Jakarta Selatan', 'P', '1985-05-05', 'aktif', '2025-11-29 00:05:51', '2025-11-29 00:05:51', NULL),
(6, NULL, '197606062006066006', 'Pak Joko Widodo', 'joko@sekolah.com', '081234567895', 'Jl. Asia Afrika No. 25 Bandung', 'L', '1976-06-06', 'aktif', '2025-11-29 00:05:51', '2025-11-29 00:05:51', NULL),
(7, NULL, '198307072013077007', 'Bu Ratna Sari', 'ratna@sekolah.com', '081234567896', 'Jl. Diponegoro No. 30 Bandung', 'P', '1983-07-07', 'aktif', '2025-11-29 00:05:51', '2025-11-29 00:05:51', NULL),
(8, NULL, '197908082009088008', 'Pak Fajar Satria', 'fajar@sekolah.com', '081234567897', 'Jl. Veteran No. 35 Surabaya', 'L', '1979-08-08', 'aktif', '2025-11-29 00:05:51', '2025-11-29 00:05:51', NULL),
(9, NULL, '198409092014099009', 'Bu Lili Marliana', 'lili@sekolah.com', '081234567898', 'Jl. Gubernur Suryadarma No. 40 Surabaya', 'P', '1984-09-09', 'aktif', '2025-11-29 00:05:51', '2025-11-29 00:05:51', NULL),
(10, NULL, '197710102007101010', 'Pak Adi Prasetyo', 'adi@sekolah.com', '081234567899', 'Jl. Ahmad Yani No. 45 Medan', 'L', '1977-10-10', 'aktif', '2025-11-29 00:05:51', '2025-11-29 00:05:51', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `guru_pengganties`
--

CREATE TABLE `guru_pengganties` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `jadwal_id` bigint(20) UNSIGNED NOT NULL,
  `tanggal` date NOT NULL,
  `guru_asli_id` bigint(20) UNSIGNED NOT NULL,
  `guru_pengganti_id` bigint(20) UNSIGNED NOT NULL,
  `status_penggantian` enum('pending','dijadwalkan','selesai','dibatalkan') DEFAULT 'pending',
  `keterangan` text DEFAULT NULL,
  `catatan_approval` text DEFAULT NULL,
  `dibuat_oleh` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `guru_pengganties`
--

INSERT INTO `guru_pengganties` (`id`, `jadwal_id`, `tanggal`, `guru_asli_id`, `guru_pengganti_id`, `status_penggantian`, `keterangan`, `catatan_approval`, `dibuat_oleh`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 8, '2025-11-24', 7, 5, 'selesai', NULL, NULL, 1, '2025-11-29 00:07:40', '2025-11-29 00:07:40', NULL),
(2, 6, '2025-11-27', 2, 1, 'pending', NULL, NULL, 1, '2025-11-29 00:07:40', '2025-11-29 00:07:40', NULL),
(3, 1, '2025-11-19', 4, 2, 'selesai', NULL, NULL, 1, '2025-11-29 00:07:40', '2025-11-29 00:07:40', NULL),
(4, 1, '2025-11-21', 1, 8, 'selesai', NULL, NULL, 1, '2025-11-29 00:07:40', '2025-11-29 00:07:40', NULL),
(5, 2, '2025-11-14', 3, 6, 'dibatalkan', NULL, NULL, 1, '2025-11-29 00:07:40', '2025-11-29 00:07:40', NULL),
(6, 1, '2025-11-26', 10, 4, 'pending', NULL, NULL, 1, '2025-11-29 00:07:40', '2025-11-29 00:07:40', NULL),
(7, 7, '2025-11-17', 5, 6, 'selesai', NULL, NULL, 1, '2025-11-29 00:07:40', '2025-11-29 00:07:40', NULL),
(8, 3, '2025-11-22', 5, 7, 'selesai', NULL, NULL, 1, '2025-11-29 00:07:40', '2025-11-29 00:07:40', NULL),
(9, 3, '2025-11-30', 5, 8, 'pending', NULL, NULL, 1, '2025-11-29 00:07:40', '2025-11-29 00:07:40', NULL),
(10, 9, '2025-11-09', 6, 5, 'selesai', NULL, NULL, 1, '2025-11-29 00:07:40', '2025-11-29 00:07:40', NULL),
(11, 1, '2025-11-29', 1, 2, 'pending', 'Guru pengganti untuk menggantikan guru asli yang sakit', NULL, NULL, '2025-11-28 17:00:00', '2025-11-28 17:00:00', NULL),
(12, 1, '2025-12-01', 1, 2, 'selesai', 'Guru pengganti untuk menggantikan guru asli', NULL, NULL, '2025-11-30 17:00:00', '2025-12-03 05:32:08', NULL),
(13, 2, '2025-12-02', 2, 3, 'selesai', 'Guru pengganti untuk menggantikan guru asli', NULL, NULL, '2025-12-01 17:00:00', '2025-12-01 17:00:00', NULL),
(14, 3, '2025-12-03', 3, 4, 'dibatalkan', 'Guru pengganti untuk menggantikan guru asli', NULL, NULL, '2025-12-02 17:00:00', '2025-12-02 17:00:00', NULL),
(15, 4, '2025-12-04', 4, 5, 'dibatalkan', 'Guru pengganti untuk menggantikan guru asli', NULL, NULL, '2025-12-03 17:00:00', '2025-12-03 17:00:00', NULL),
(16, 5, '2025-12-05', 5, 6, 'dibatalkan', 'Guru pengganti untuk menggantikan guru asli', NULL, NULL, '2025-12-04 17:00:00', '2025-12-04 17:00:00', NULL),
(17, 6, '2025-12-06', 6, 7, 'pending', 'Guru pengganti untuk menggantikan guru asli', NULL, NULL, '2025-12-05 17:00:00', '2025-12-05 17:00:00', NULL),
(18, 7, '2025-12-07', 7, 8, 'selesai', 'Guru pengganti untuk menggantikan guru asli', NULL, NULL, '2025-12-06 17:00:00', '2025-12-06 17:00:00', NULL),
(19, 8, '2025-12-08', 8, 9, 'selesai', 'Guru pengganti untuk menggantikan guru asli', NULL, NULL, '2025-12-07 17:00:00', '2025-12-03 04:41:22', NULL),
(20, 9, '2025-12-09', 9, 10, 'selesai', 'Guru pengganti untuk menggantikan guru asli', NULL, NULL, '2025-12-08 17:00:00', '2025-12-08 17:00:00', NULL),
(21, 10, '2025-12-10', 10, 1, 'dibatalkan', 'Guru pengganti untuk menggantikan guru asli', NULL, NULL, '2025-12-09 17:00:00', '2025-12-09 17:00:00', NULL),
(22, 1, '2025-12-02', 1, 5, 'selesai', NULL, NULL, 1, '2025-12-02 08:55:17', '2025-12-03 04:47:49', NULL),
(23, 8, '2025-12-03', 1, 7, 'selesai', NULL, NULL, 1, '2025-12-02 19:13:26', '2025-12-03 04:45:44', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `imports`
--

CREATE TABLE `imports` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `file_name` varchar(191) NOT NULL,
  `file_path` varchar(191) NOT NULL,
  `importer` varchar(191) NOT NULL,
  `processed_rows` int(11) NOT NULL DEFAULT 0,
  `successful_rows` int(11) NOT NULL DEFAULT 0,
  `failed_rows_count` int(11) NOT NULL DEFAULT 0,
  `total_rows` int(11) NOT NULL,
  `failed_rows` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`failed_rows`)),
  `completed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `izin_gurus`
--

CREATE TABLE `izin_gurus` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `guru_id` bigint(20) UNSIGNED NOT NULL,
  `tanggal_mulai` date NOT NULL,
  `tanggal_selesai` date NOT NULL,
  `durasi_hari` int(11) NOT NULL DEFAULT 0,
  `jenis_izin` enum('sakit','izin','cuti','dinas_luar','lainnya') NOT NULL,
  `keterangan` text DEFAULT NULL,
  `file_surat` varchar(191) DEFAULT NULL,
  `status_approval` enum('pending','disetujui','ditolak') NOT NULL DEFAULT 'pending',
  `disetujui_oleh` bigint(20) UNSIGNED DEFAULT NULL,
  `tanggal_approval` timestamp NULL DEFAULT NULL,
  `catatan_approval` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `izin_gurus`
--

INSERT INTO `izin_gurus` (`id`, `guru_id`, `tanggal_mulai`, `tanggal_selesai`, `durasi_hari`, `jenis_izin`, `keterangan`, `file_surat`, `status_approval`, `disetujui_oleh`, `tanggal_approval`, `catatan_approval`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 9, '2025-11-24', '2025-11-26', 2, 'sakit', 'Sakit demam tinggi', NULL, 'disetujui', 1, '2025-11-23 00:07:54', NULL, '2025-11-29 00:07:54', '2025-11-29 00:07:54', NULL),
(2, 8, '2025-11-19', '2025-11-20', 1, 'izin', 'Acara keluarga', NULL, 'disetujui', 1, '2025-11-18 00:07:54', NULL, '2025-11-29 00:07:54', '2025-11-29 00:07:54', NULL),
(3, 7, '2025-11-14', '2025-11-14', 0, 'izin', 'Keperluan pribadi', NULL, 'ditolak', 1, '2025-11-13 00:07:54', NULL, '2025-11-29 00:07:54', '2025-11-29 00:07:54', NULL),
(4, 1, '2025-12-01', '2025-12-03', 2, 'sakit', 'Sakit berobat', NULL, 'pending', NULL, NULL, NULL, '2025-11-29 00:07:54', '2025-11-29 00:07:54', NULL),
(5, 3, '2025-11-09', '2025-11-11', 2, 'cuti', 'Cuti tahunan', NULL, 'disetujui', 1, '2025-11-08 00:07:54', NULL, '2025-11-29 00:07:54', '2025-11-29 00:07:54', NULL),
(6, 7, '2025-11-21', '2025-11-22', 1, 'dinas_luar', 'Dinas ke luar kota', NULL, 'disetujui', 1, '2025-11-20 00:07:54', NULL, '2025-11-29 00:07:54', '2025-11-29 00:07:54', NULL),
(7, 6, '2025-11-17', '2025-11-17', 0, 'izin', 'Acara pernikahan keluarga', NULL, 'disetujui', 1, '2025-11-16 00:07:54', NULL, '2025-11-29 00:07:54', '2025-11-29 00:07:54', NULL),
(8, 6, '2025-11-04', '2025-11-05', 1, 'sakit', 'Sakit gigi', NULL, 'disetujui', 1, '2025-11-03 00:07:54', NULL, '2025-11-29 00:07:54', '2025-11-29 00:07:54', NULL),
(9, 2, '2025-10-30', '2025-10-31', 1, 'izin', 'Menghadiri undangan resmi', NULL, 'disetujui', 1, '2025-10-29 00:07:54', NULL, '2025-11-29 00:07:54', '2025-11-29 00:07:54', NULL),
(10, 6, '2025-11-26', '2025-11-28', 2, 'cuti', 'Cuti melahirkan', NULL, 'pending', NULL, NULL, NULL, '2025-11-29 00:07:54', '2025-11-29 00:07:54', NULL),
(11, 1, '2025-11-29', '2025-11-30', 2, 'sakit', 'Sakit flu berat', NULL, 'disetujui', NULL, NULL, NULL, '2025-11-28 17:00:00', '2025-11-28 17:00:00', NULL),
(12, 1, '2025-12-01', '2025-12-02', 1, 'lainnya', 'Menghadiri undangan resmi', NULL, 'pending', NULL, NULL, NULL, '2025-11-30 17:00:00', '2025-11-30 17:00:00', NULL),
(13, 2, '2025-12-02', '2025-12-03', 1, 'dinas_luar', 'Sakit flu berat', NULL, 'pending', NULL, NULL, NULL, '2025-12-01 17:00:00', '2025-12-01 17:00:00', NULL),
(14, 3, '2025-12-03', '2025-12-04', 1, 'sakit', 'Menghadiri undangan resmi', NULL, 'disetujui', NULL, NULL, NULL, '2025-12-02 17:00:00', '2025-12-02 17:00:00', NULL),
(15, 4, '2025-12-04', '2025-12-05', 1, 'lainnya', 'Izin pribadi', NULL, 'disetujui', NULL, NULL, NULL, '2025-12-03 17:00:00', '2025-12-03 17:00:00', NULL),
(16, 5, '2025-12-05', '2025-12-06', 1, 'sakit', 'Dinas luar kota', NULL, 'disetujui', NULL, NULL, NULL, '2025-12-04 17:00:00', '2025-12-04 17:00:00', NULL),
(17, 6, '2025-12-06', '2025-12-07', 1, 'cuti', 'Menghadiri undangan resmi', NULL, 'ditolak', NULL, NULL, NULL, '2025-12-05 17:00:00', '2025-12-05 17:00:00', NULL),
(18, 7, '2025-12-07', '2025-12-08', 1, 'sakit', 'Acara keluarga penting', NULL, 'disetujui', NULL, NULL, NULL, '2025-12-06 17:00:00', '2025-12-06 17:00:00', NULL),
(19, 8, '2025-12-08', '2025-12-09', 1, 'sakit', 'Cuti tahunan', NULL, 'ditolak', NULL, NULL, NULL, '2025-12-07 17:00:00', '2025-12-07 17:00:00', NULL),
(20, 9, '2025-12-09', '2025-12-10', 1, 'dinas_luar', 'Keperluan keluarga', NULL, 'pending', NULL, NULL, NULL, '2025-12-08 17:00:00', '2025-12-08 17:00:00', NULL),
(21, 10, '2025-12-10', '2025-12-11', 1, 'cuti', 'Keperluan keluarga', NULL, 'ditolak', 1, '2025-12-02 17:00:00', NULL, '2025-12-09 17:00:00', '2025-12-03 04:27:14', NULL),
(22, 1, '2025-12-02', '2025-12-02', 1, 'izin', 'jj', NULL, 'pending', NULL, NULL, NULL, '2025-12-02 08:54:10', '2025-12-02 08:54:10', NULL),
(23, 1, '2025-12-03', '2025-12-03', 1, 'izin', 'ggghh', NULL, 'pending', NULL, NULL, NULL, '2025-12-02 19:12:56', '2025-12-02 19:12:56', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `jadwals`
--

CREATE TABLE `jadwals` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `kelas_id` bigint(20) UNSIGNED NOT NULL,
  `mata_pelajaran_id` bigint(20) UNSIGNED NOT NULL,
  `guru_id` bigint(20) UNSIGNED NOT NULL,
  `hari` varchar(191) NOT NULL,
  `jam_ke` int(11) NOT NULL,
  `jam_mulai` varchar(20) NOT NULL,
  `jam_selesai` varchar(20) NOT NULL,
  `ruangan` varchar(191) DEFAULT NULL,
  `status` enum('aktif','nonaktif') NOT NULL DEFAULT 'aktif',
  `keterangan` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `jadwals`
--

INSERT INTO `jadwals` (`id`, `kelas_id`, `mata_pelajaran_id`, `guru_id`, `hari`, `jam_ke`, `jam_mulai`, `jam_selesai`, `ruangan`, `status`, `keterangan`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 4, 2, 1, 'Selasa', 4, '13:00', '14:40', 'Ruang 176', 'aktif', 'Jadwal pelajaran reguler', '2025-11-29 00:06:02', '2025-11-29 00:06:02', NULL),
(2, 1, 9, 1, 'Jumat', 4, '13:00', '14:40', 'Ruang 125', 'aktif', 'Jadwal pelajaran reguler', '2025-11-29 00:06:02', '2025-11-29 00:06:02', NULL),
(3, 2, 10, 8, 'Selasa', 2, '08:40', '10:20', 'Ruang 117', 'aktif', 'Jadwal pelajaran reguler', '2025-11-29 00:06:02', '2025-11-29 00:06:02', NULL),
(4, 1, 6, 5, 'Sabtu', 4, '13:00', '14:40', 'Ruang 112', 'aktif', 'Jadwal pelajaran reguler', '2025-11-29 00:06:02', '2025-11-29 00:06:02', NULL),
(5, 1, 6, 8, 'Sabtu', 4, '13:00', '14:40', 'Ruang 138', 'aktif', 'Jadwal pelajaran reguler', '2025-11-29 00:06:02', '2025-11-29 00:06:02', NULL),
(6, 2, 2, 7, 'Kamis', 5, '14:40', '16:20', 'Ruang 169', 'aktif', 'Jadwal pelajaran reguler', '2025-11-29 00:06:02', '2025-11-29 00:06:02', NULL),
(7, 5, 2, 2, 'Sabtu', 6, '16:20', '18:00', 'Ruang 107', 'aktif', 'Jadwal pelajaran reguler', '2025-11-29 00:06:02', '2025-11-29 00:06:02', NULL),
(8, 1, 5, 1, 'Rabu', 4, '13:00', '14:40', 'Ruang 122', 'aktif', 'Jadwal pelajaran reguler', '2025-11-29 00:06:02', '2025-11-29 00:06:02', NULL),
(9, 1, 8, 4, 'Rabu', 4, '13:00', '14:40', 'Ruang 104', 'aktif', 'Jadwal pelajaran reguler', '2025-11-29 00:06:02', '2025-11-29 00:06:02', NULL),
(10, 5, 10, 9, 'Rabu', 2, '08:40', '10:20', 'Ruang 191', 'aktif', 'Jadwal pelajaran reguler', '2025-11-29 00:06:02', '2025-11-29 00:06:02', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(191) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(191) NOT NULL,
  `name` varchar(191) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `kehadirans`
--

CREATE TABLE `kehadirans` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `siswa_id` bigint(20) UNSIGNED NOT NULL,
  `jadwal_id` bigint(20) UNSIGNED NOT NULL,
  `tanggal` date NOT NULL,
  `status` enum('hadir','alpha','sakit','izin') NOT NULL DEFAULT 'hadir',
  `keterangan` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `kehadirans`
--

INSERT INTO `kehadirans` (`id`, `siswa_id`, `jadwal_id`, `tanggal`, `status`, `keterangan`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 10, 1, '2025-11-24', 'hadir', 'Hadir', '2025-11-29 00:06:07', '2025-11-29 00:06:07', NULL),
(2, 7, 4, '2025-11-25', 'izin', 'Izin karena acara keluarga', '2025-11-29 00:06:07', '2025-11-29 00:06:07', NULL),
(3, 6, 4, '2025-11-26', 'sakit', 'Sakit pilek', '2025-11-29 00:06:07', '2025-11-29 00:06:07', NULL),
(4, 8, 1, '2025-11-24', 'hadir', 'Hadir', '2025-11-29 00:06:32', '2025-11-29 00:06:32', NULL),
(5, 5, 5, '2025-11-25', 'izin', 'Izin karena acara keluarga', '2025-11-29 00:06:32', '2025-11-29 00:06:32', NULL),
(6, 8, 9, '2025-11-26', 'sakit', 'Sakit pilek', '2025-11-29 00:06:32', '2025-11-29 00:06:32', NULL),
(7, 1, 2, '2025-11-24', 'hadir', 'Hadir', '2025-11-29 00:06:54', '2025-11-29 00:06:54', NULL),
(8, 3, 3, '2025-11-25', 'izin', 'Izin karena acara keluarga', '2025-11-29 00:06:54', '2025-11-29 00:06:54', NULL),
(9, 7, 3, '2025-11-26', 'sakit', 'Sakit pilek', '2025-11-29 00:06:54', '2025-11-29 00:06:54', NULL),
(10, 8, 3, '2025-11-27', 'alpha', 'Tanpa keterangan', '2025-11-29 00:06:54', '2025-11-29 00:06:54', NULL),
(11, 2, 3, '2025-11-28', 'hadir', 'Hadir', '2025-11-29 00:06:54', '2025-11-29 00:06:54', NULL),
(12, 10, 10, '2025-11-23', 'alpha', 'Tidak hadir tanpa izin', '2025-11-29 00:06:54', '2025-11-29 00:06:54', NULL),
(13, 6, 5, '2025-11-22', 'hadir', 'Hadir', '2025-11-29 00:06:54', '2025-11-29 00:06:54', NULL),
(14, 7, 9, '2025-11-21', 'izin', 'Izin karena sakit', '2025-11-29 00:06:54', '2025-11-29 00:06:54', NULL),
(15, 8, 10, '2025-11-20', 'sakit', 'Sakit demam', '2025-11-29 00:06:54', '2025-11-29 00:06:54', NULL),
(16, 8, 4, '2025-11-19', 'hadir', 'Hadir', '2025-11-29 00:06:54', '2025-11-29 00:06:54', NULL),
(17, 1, 1, '2025-11-29', 'hadir', 'Hadir dalam pelajaran', '2025-11-28 17:00:00', '2025-11-28 17:00:00', NULL),
(18, 1, 1, '2025-12-01', 'alpha', 'Hadir dengan sedikit keterlambatan', '2025-11-30 17:00:00', '2025-11-30 17:00:00', NULL),
(19, 2, 2, '2025-12-01', 'izin', 'Hadir lebih awal', '2025-11-30 17:00:00', '2025-11-30 17:00:00', NULL),
(20, 3, 3, '2025-12-01', 'izin', 'Tidak hadir tanpa izin', '2025-11-30 17:00:00', '2025-11-30 17:00:00', NULL),
(21, 4, 4, '2025-12-01', 'alpha', 'Tidak hadir tanpa izin', '2025-11-30 17:00:00', '2025-11-30 17:00:00', NULL),
(22, 5, 5, '2025-12-01', 'alpha', 'Hadir dengan sedikit keterlambatan', '2025-11-30 17:00:00', '2025-11-30 17:00:00', NULL),
(23, 6, 6, '2025-12-01', 'sakit', 'Izin pribadi', '2025-11-30 17:00:00', '2025-11-30 17:00:00', NULL),
(24, 7, 7, '2025-12-01', 'alpha', 'Hadir dengan sedikit keterlambatan', '2025-11-30 17:00:00', '2025-11-30 17:00:00', NULL),
(25, 8, 8, '2025-12-01', 'hadir', 'Tidak hadir tanpa izin', '2025-11-30 17:00:00', '2025-11-30 17:00:00', NULL),
(26, 9, 9, '2025-12-01', 'alpha', 'Izin karena acara keluarga', '2025-11-30 17:00:00', '2025-11-30 17:00:00', NULL),
(27, 10, 10, '2025-12-01', 'izin', 'Sakit berobat', '2025-11-30 17:00:00', '2025-11-30 17:00:00', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `kehadiran_gurus`
--

CREATE TABLE `kehadiran_gurus` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `jadwal_id` bigint(20) UNSIGNED NOT NULL,
  `guru_id` bigint(20) UNSIGNED NOT NULL,
  `tanggal` date NOT NULL,
  `status_kehadiran` enum('hadir','telat','tidak_hadir','izin','sakit') NOT NULL DEFAULT 'hadir',
  `waktu_datang` varchar(20) DEFAULT NULL,
  `durasi_keterlambatan` int(11) DEFAULT NULL COMMENT 'dalam menit',
  `keterangan` text DEFAULT NULL,
  `diinput_oleh` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `kehadiran_gurus`
--

INSERT INTO `kehadiran_gurus` (`id`, `jadwal_id`, `guru_id`, `tanggal`, `status_kehadiran`, `waktu_datang`, `durasi_keterlambatan`, `keterangan`, `diinput_oleh`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 10, 10, '2025-11-24', 'hadir', '07:00:00', NULL, 'Hadir tepat waktu', 1, '2025-11-29 00:07:19', '2025-11-29 00:07:19', NULL),
(2, 9, 4, '2025-11-25', 'telat', '07:45:00', 45, 'Terlambat karena kendala lalu lintas', 1, '2025-11-29 00:07:19', '2025-11-29 00:07:19', NULL),
(3, 8, 3, '2025-11-26', 'izin', NULL, NULL, 'Izin acara keluarga', 1, '2025-11-29 00:07:19', '2025-11-29 00:07:19', NULL),
(4, 10, 10, '2025-11-27', 'sakit', NULL, NULL, 'Sakit demam', 1, '2025-11-29 00:07:19', '2025-11-29 00:07:19', NULL),
(5, 7, 6, '2025-11-28', 'hadir', '06:55:00', NULL, 'Hadir lebih awal', 1, '2025-11-29 00:07:19', '2025-11-29 00:07:19', NULL),
(6, 3, 10, '2025-11-23', 'telat', '08:15:00', 75, 'Kendala transportasi', 1, '2025-11-29 00:07:19', '2025-11-29 00:07:19', NULL),
(7, 7, 6, '2025-11-22', 'hadir', '07:05:00', NULL, 'Hadir tepat waktu', 1, '2025-11-29 00:07:20', '2025-11-29 00:07:20', NULL),
(8, 2, 4, '2025-11-21', 'tidak_hadir', NULL, NULL, 'Tanpa keterangan', 1, '2025-11-29 00:07:20', '2025-11-29 00:07:20', NULL),
(9, 9, 6, '2025-11-20', 'hadir', '07:10:00', NULL, 'Hadir dengan sedikit keterlambatan', 1, '2025-11-29 00:07:20', '2025-11-29 00:07:20', NULL),
(10, 5, 8, '2025-11-19', 'sakit', NULL, NULL, 'Sakit berobat', 1, '2025-11-29 00:07:20', '2025-11-29 00:07:20', NULL),
(11, 1, 1, '2025-11-29', 'hadir', '07:30', NULL, 'Hadir tepat waktu', NULL, '2025-11-28 17:00:00', '2025-11-28 17:00:00', NULL),
(12, 1, 1, '2025-12-01', 'hadir', '12:30', NULL, 'Hadir dengan sedikit keterlambatan', NULL, '2025-11-30 17:00:00', '2025-11-30 17:00:00', NULL),
(13, 2, 2, '2025-12-01', 'hadir', '07:00', NULL, 'Izin karena acara keluarga', NULL, '2025-11-30 17:00:00', '2025-11-30 17:00:00', NULL),
(14, 3, 3, '2025-12-01', 'sakit', '12:00', NULL, 'Hadir tepat waktu', NULL, '2025-11-30 17:00:00', '2025-11-30 17:00:00', NULL),
(15, 4, 4, '2025-12-01', 'sakit', '08:15', NULL, 'Izin pribadi', NULL, '2025-11-30 17:00:00', '2025-11-30 17:00:00', NULL),
(16, 5, 5, '2025-12-01', 'tidak_hadir', '09:45', NULL, 'Izin karena acara keluarga', NULL, '2025-11-30 17:00:00', '2025-11-30 17:00:00', NULL),
(17, 6, 6, '2025-12-01', 'sakit', '11:00', NULL, 'Tidak hadir karena alasan pribadi', NULL, '2025-11-30 17:00:00', '2025-11-30 17:00:00', NULL),
(18, 7, 7, '2025-12-01', 'hadir', '09:00', NULL, 'Izin pribadi', NULL, '2025-11-30 17:00:00', '2025-11-30 17:00:00', NULL),
(19, 8, 8, '2025-12-01', 'sakit', '09:15', NULL, 'Hadir terlambat sedikit', NULL, '2025-11-30 17:00:00', '2025-11-30 17:00:00', NULL),
(20, 9, 9, '2025-12-01', 'izin', '11:15', NULL, 'Izin karena acara keluarga', NULL, '2025-11-30 17:00:00', '2025-11-30 17:00:00', NULL),
(21, 10, 10, '2025-12-01', 'izin', '12:30', NULL, 'Izin karena sakit', NULL, '2025-11-30 17:00:00', '2025-11-30 17:00:00', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `kelas`
--

CREATE TABLE `kelas` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nama` varchar(191) NOT NULL,
  `tingkat` int(11) NOT NULL,
  `jurusan` varchar(191) NOT NULL,
  `wali_kelas_id` bigint(20) UNSIGNED DEFAULT NULL,
  `kapasitas` int(11) NOT NULL DEFAULT 0,
  `jumlah_siswa` int(11) NOT NULL DEFAULT 0,
  `ruangan` varchar(191) DEFAULT NULL,
  `status` enum('aktif','nonaktif') NOT NULL DEFAULT 'aktif',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `kelas`
--

INSERT INTO `kelas` (`id`, `nama`, `tingkat`, `jurusan`, `wali_kelas_id`, `kapasitas`, `jumlah_siswa`, `ruangan`, `status`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'XII RPL 1', 12, 'Rekayasa Perangkat Lunak', NULL, 32, 2, 'Ruang 101', 'aktif', '2025-11-29 00:05:13', '2025-11-29 00:05:39', NULL),
(2, 'XII RPL 2', 12, 'Rekayasa Perangkat Lunak', NULL, 30, 2, 'Ruang 102', 'aktif', '2025-11-29 00:05:13', '2025-11-29 00:05:39', NULL),
(3, 'XII TKJ 1', 12, 'Teknik Komputer dan Jaringan', NULL, 28, 2, 'Ruang 201', 'aktif', '2025-11-29 00:05:13', '2025-11-29 00:05:39', NULL),
(4, 'XI RPL 1', 11, 'Rekayasa Perangkat Lunak', NULL, 32, 2, 'Ruang 301', 'aktif', '2025-11-29 00:05:13', '2025-11-29 00:05:39', NULL),
(5, 'X RPL 1', 10, 'Rekayasa Perangkat Lunak', NULL, 35, 2, 'Ruang 401', 'aktif', '2025-11-29 00:05:13', '2025-11-29 00:05:39', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `mata_pelajarans`
--

CREATE TABLE `mata_pelajarans` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `kode` varchar(191) NOT NULL,
  `nama` varchar(191) NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `sks` int(11) NOT NULL DEFAULT 0,
  `kategori` varchar(191) DEFAULT NULL,
  `status` enum('aktif','nonaktif') NOT NULL DEFAULT 'aktif',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `mata_pelajarans`
--

INSERT INTO `mata_pelajarans` (`id`, `kode`, `nama`, `deskripsi`, `sks`, `kategori`, `status`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'MTK', 'Matematika', 'Mata pelajaran Matematika Umum', 3, 'Normatif', 'aktif', '2025-11-29 00:05:56', '2025-11-29 00:05:56', NULL),
(2, 'BIN', 'Bahasa Indonesia', 'Mata pelajaran Bahasa Indonesia', 3, 'Normatif', 'aktif', '2025-11-29 00:05:57', '2025-11-29 00:05:57', NULL),
(3, 'BIG', 'Bahasa Inggris', 'Mata pelajaran Bahasa Inggris', 2, 'Normatif', 'aktif', '2025-11-29 00:05:57', '2025-11-29 00:05:57', NULL),
(4, 'PABP', 'Pendidikan Agama dan Budi Pekerti', 'Mata pelajaran Pendidikan Agama dan Budi Pekerti', 2, 'Normatif', 'aktif', '2025-11-29 00:05:57', '2025-11-29 00:05:57', NULL),
(5, 'PKN', 'Pendidikan Pancasila dan Kewarganegaraan', 'Mata pelajaran Pendidikan Pancasila dan Kewarganegaraan', 2, 'Normatif', 'aktif', '2025-11-29 00:05:57', '2025-11-29 00:05:57', NULL),
(6, 'PBO', 'Pemrograman Berorientasi Objek', 'Mata pelajaran Pemrograman Berorientasi Objek', 4, 'Adaptif', 'aktif', '2025-11-29 00:05:57', '2025-11-29 00:05:57', NULL),
(7, 'DB', 'Basis Data', 'Mata pelajaran Basis Data', 3, 'Adaptif', 'aktif', '2025-11-29 00:05:57', '2025-11-29 00:05:57', NULL),
(8, 'SK', 'Sistem Komputer', 'Mata pelajaran Sistem Komputer', 2, 'Adaptif', 'aktif', '2025-11-29 00:05:57', '2025-11-29 00:05:57', NULL),
(9, 'WEB', 'Pemrograman Web', 'Mata pelajaran Pemrograman Web', 4, 'Keahlian', 'aktif', '2025-11-29 00:05:57', '2025-11-29 00:05:57', NULL),
(10, 'MOB', 'Pemrograman Mobile', 'Mata pelajaran Pemrograman Mobile', 4, 'Keahlian', 'aktif', '2025-11-29 00:05:57', '2025-11-29 00:05:57', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(191) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2025_10_26_000002_create_gurus_table', 1),
(5, '2025_10_26_000003_create_siswas_table', 1),
(6, '2025_10_26_000004_create_mata_pelajarans_table', 1),
(7, '2025_10_26_000005_create_jadwals_table', 1),
(8, '2025_10_26_000007_create_kehadirans_table', 1),
(9, '2025_10_26_000008_create_kelas_table', 1),
(10, '2025_10_26_000009_add_foreign_keys', 1),
(11, '2025_10_26_005436_add_role_to_users_table', 1),
(12, '2025_10_29_000000_create_failed_import_rows_table', 1),
(13, '2025_10_29_042919_create_imports_table', 1),
(14, '2025_11_01_223813_create_personal_access_tokens_table', 1),
(15, '2025_11_15_093527_change_jam_columns_to_varchar_in_jadwals_table', 1),
(16, '2025_11_15_101831_add_user_id_to_gurus_table', 1),
(17, '2025_11_15_134300_create_guru_pengganties_table', 1),
(18, '2025_11_15_135932_create_kehadiran_gurus_table', 1),
(19, '2025_11_15_141517_remove_guru_mengajar_id_from_guru_pengganties_table', 1),
(20, '2025_11_26_032508_add_missing_columns_to_users_table', 1),
(21, '2025_11_26_033000_create_izin_gurus_table', 1),
(22, '2025_11_28_110727_add_successful_rows_to_imports_table', 1),
(23, '2025_11_28_111034_add_failed_rows_count_to_imports_table', 1),
(24, '2025_11_29_070345_add_user_id_to_siswas_table', 1),
(25, '2025_11_29_000000_add_performance_indexes', 2),
(26, '2025_12_03_110937_add_catatan_approval_to_izin_gurus_table', 2),
(27, '2025_12_03_110945_add_catatan_approval_to_guru_pengganties_table', 2),
(28, '2025_12_03_122142_add_pending_to_status_penggantian_enum', 3),
(29, '2025_12_03_125711_remove_alasan_penggantian_from_guru_pengganties_table', 4);

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(191) NOT NULL,
  `token` varchar(191) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tokenable_type` varchar(191) NOT NULL,
  `tokenable_id` bigint(20) UNSIGNED NOT NULL,
  `name` text NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `personal_access_tokens`
--

INSERT INTO `personal_access_tokens` (`id`, `tokenable_type`, `tokenable_id`, `name`, `token`, `abilities`, `last_used_at`, `expires_at`, `created_at`, `updated_at`) VALUES
(139, 'App\\Models\\User', 2, 'api_token', 'beae8c296963fbc260b599cc8221e682cf115b62d58a01a480921fa811fff8b8', '[\"*\"]', NULL, NULL, '2025-12-03 05:47:30', '2025-12-03 05:47:30'),
(37, 'App\\Models\\User', 4, 'api_token', 'c2a8fb615aa0d973c73aa8041bf9678639d5e46bc041cd99d2a36b74a551b715', '[\"*\"]', NULL, NULL, '2025-11-30 04:13:13', '2025-11-30 04:13:13'),
(140, 'App\\Models\\User', 3, 'api_token', '3256922130f3ebc2d9c03dc84fc4cc23dddd6843545e9c8f5133e208d90ff8a6', '[\"*\"]', NULL, NULL, '2025-12-03 06:34:43', '2025-12-03 06:34:43'),
(134, 'App\\Models\\User', 5, 'api_token', '55531b42c603c87a050f1a439285fa339c7d8297e384bf793d86fd9d7de6f4b5', '[\"*\"]', NULL, NULL, '2025-12-03 05:30:17', '2025-12-03 05:30:17');

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(191) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `siswas`
--

CREATE TABLE `siswas` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nis` varchar(191) NOT NULL,
  `nisn` varchar(191) NOT NULL,
  `nama` varchar(191) NOT NULL,
  `email` varchar(191) DEFAULT NULL,
  `no_telp` varchar(191) DEFAULT NULL,
  `alamat` text DEFAULT NULL,
  `jenis_kelamin` enum('L','P') NOT NULL,
  `tanggal_lahir` date DEFAULT NULL,
  `foto` varchar(191) DEFAULT NULL,
  `kelas_id` bigint(20) UNSIGNED NOT NULL,
  `nama_orang_tua` varchar(191) DEFAULT NULL,
  `no_telp_orang_tua` varchar(191) DEFAULT NULL,
  `status` enum('aktif','nonaktif','lulus','pindah') NOT NULL DEFAULT 'aktif',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `siswas`
--

INSERT INTO `siswas` (`id`, `nis`, `nisn`, `nama`, `email`, `no_telp`, `alamat`, `jenis_kelamin`, `tanggal_lahir`, `foto`, `kelas_id`, `nama_orang_tua`, `no_telp_orang_tua`, `status`, `created_at`, `updated_at`, `deleted_at`, `user_id`) VALUES
(1, '123456789001', '001123456789', 'Ahmad Fauzi', 'ahmad.fauzi@sekolah.com', '081312345601', 'Jl. Merdeka No. 1 Jakarta Pusat', 'L', '2005-05-15', NULL, 1, 'Bapak Fauzi', '081312345001', 'aktif', '2025-11-29 00:05:39', '2025-11-29 00:05:39', NULL, 4),
(2, '123456789002', '002123456789', 'Siti Aisyah', 'siti.aisyah@sekolah.com', '081312345602', 'Jl. Sudirman No. 5 Jakarta Pusat', 'P', '2005-08-22', NULL, 1, 'Ibu Aisyah', '081312345002', 'aktif', '2025-11-29 00:05:39', '2025-11-29 00:05:39', NULL, 6),
(3, '123456789003', '003123456789', 'Budi Prasetyo', 'budi.prasetyo@sekolah.com', '081312345603', 'Jl. Thamrin No. 10 Jakarta Selatan', 'L', '2005-03-10', NULL, 2, 'Bapak Prasetyo', '081312345003', 'aktif', '2025-11-29 00:05:39', '2025-11-29 00:05:39', NULL, NULL),
(4, '123456789004', '004123456789', 'Dewi Kartika', 'dewi.kartika@sekolah.com', '081312345604', 'Jl. Gatot Subroto No. 15 Jakarta Selatan', 'P', '2005-11-07', NULL, 2, 'Ibu Kartika', '081312345004', 'aktif', '2025-11-29 00:05:39', '2025-11-29 00:05:39', NULL, NULL),
(5, '123456789005', '005123456789', 'Eko Prasetyo', 'eko.prasetyo@sekolah.com', '081312345605', 'Jl. HR Rasuna Said No. 20 Jakarta Selatan', 'L', '2005-01-30', NULL, 3, 'Bapak Prasetyo', '081312345005', 'aktif', '2025-11-29 00:05:39', '2025-11-29 00:05:39', NULL, NULL),
(6, '123456789006', '006123456789', 'Fitri Lestari', 'fitri.lestari@sekolah.com', '081312345606', 'Jl. Asia Afrika No. 25 Bandung', 'P', '2005-06-18', NULL, 3, 'Ibu Lestari', '081312345006', 'aktif', '2025-11-29 00:05:39', '2025-11-29 00:05:39', NULL, NULL),
(7, '123456789007', '007123456789', 'Ganteng Pratama', 'ganteng.pratama@sekolah.com', '081312345607', 'Jl. Diponegoro No. 30 Bandung', 'L', '2005-09-05', NULL, 4, 'Bapak Pratama', '081312345007', 'aktif', '2025-11-29 00:05:39', '2025-11-29 00:05:39', NULL, NULL),
(8, '123456789008', '008123456789', 'Heni Marlina', 'heni.marlina@sekolah.com', '081312345608', 'Jl. Veteran No. 35 Surabaya', 'P', '2005-12-12', NULL, 4, 'Ibu Marlina', '081312345008', 'aktif', '2025-11-29 00:05:39', '2025-11-29 00:05:39', NULL, NULL),
(9, '123456789009', '009123456789', 'Indra Kusuma', 'indra.kusuma@sekolah.com', '081312345609', 'Jl. Gubernur Suryadarma No. 40 Surabaya', 'L', '2005-04-25', NULL, 5, 'Bapak Kusuma', '081312345009', 'aktif', '2025-11-29 00:05:39', '2025-11-29 00:05:39', NULL, NULL),
(10, '123456789010', '010123456789', 'Juli Ratnasari', 'juli.ratnasari@sekolah.com', '081312345610', 'Jl. Ahmad Yani No. 45 Medan', 'P', '2005-07-08', NULL, 5, 'Ibu Ratnasari', '081312345010', 'aktif', '2025-11-29 00:05:39', '2025-11-29 00:05:39', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(191) NOT NULL,
  `email` varchar(191) NOT NULL,
  `role` varchar(191) DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(191) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `guru_id` bigint(20) UNSIGNED DEFAULT NULL,
  `kelas_id` bigint(20) UNSIGNED DEFAULT NULL,
  `foto` varchar(191) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `role`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`, `guru_id`, `kelas_id`, `foto`) VALUES
(1, 'Administrator', 'admin@sekolah.com', 'admin', NULL, '$2y$12$pjVE58JAwrOfCV5t3vKn4O1oDIQ4ctY.ugLQqKCSJNvYmh4TkZ4qG', '162iP7CBXlwP9hia1G12fQQKdnyF8Kxr7cbK7CEiD7jeD0e9TIbKRp0lrwnt', '2025-11-29 00:05:04', '2025-11-29 00:05:04', NULL, NULL, NULL),
(2, 'Mariya Ernawati', 'kepsek@sekolah.com', 'kepsek', NULL, '$2y$12$Vyk23NpJgOpQPe89OvozjOwdQi/ACLuA6ry72sI6dzBgkqfm.eZmy', NULL, '2025-11-29 00:05:04', '2025-11-30 01:17:46', NULL, NULL, NULL),
(3, 'Guru Matematika', 'guru@sekolah.com', 'guru', NULL, '$2y$12$pHP02ziY0v8.5yBO.dN4jueqPWMOZA3mHQe7HAMC4uESAmWRySUPq', NULL, '2025-11-29 00:05:05', '2025-11-29 00:05:51', 1, NULL, NULL),
(4, 'Ahmad Siswa', 'siswa@sekolah.com', 'siswa', NULL, '$2y$12$om9GiuKedpATQf7iXrU5EemBGoNMla4zPtPdFodCz5lHMTQ.39r8m', NULL, '2025-11-29 00:05:05', '2025-11-29 00:05:39', NULL, 1, NULL),
(5, 'Budi Guru', 'budi.guru@sekolah.com', 'kurikulum', NULL, '$2y$12$zbT49AXElMf.BDPTJNE4OOLl4VLTzRZgw6XyDuhMfcCVqIiXJB/WG', NULL, '2025-11-29 00:05:05', '2025-11-29 00:05:05', NULL, NULL, NULL),
(6, 'Siti Siswa', 'siti.siswa@sekolah.com', 'siswa', NULL, '$2y$12$mFzXE9XJu.bPNFUZAsFM4e.0gIWi4RpvSLgvSGdNeOlOJ.R/iXNL6', NULL, '2025-11-29 00:05:06', '2025-11-29 00:05:39', NULL, 1, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `failed_import_rows`
--
ALTER TABLE `failed_import_rows`
  ADD PRIMARY KEY (`id`),
  ADD KEY `failed_import_rows_import_id_index` (`import_id`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `gurus`
--
ALTER TABLE `gurus`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `gurus_nip_unique` (`nip`),
  ADD UNIQUE KEY `gurus_email_unique` (`email`),
  ADD KEY `gurus_user_id_index` (`user_id`);

--
-- Indexes for table `guru_pengganties`
--
ALTER TABLE `guru_pengganties`
  ADD PRIMARY KEY (`id`),
  ADD KEY `guru_pengganties_jadwal_id_foreign` (`jadwal_id`),
  ADD KEY `guru_pengganties_guru_asli_id_foreign` (`guru_asli_id`),
  ADD KEY `guru_pengganties_guru_pengganti_id_foreign` (`guru_pengganti_id`),
  ADD KEY `guru_pengganties_dibuat_oleh_foreign` (`dibuat_oleh`),
  ADD KEY `idx_guru_pengganti_tanggal` (`tanggal`);

--
-- Indexes for table `imports`
--
ALTER TABLE `imports`
  ADD PRIMARY KEY (`id`),
  ADD KEY `imports_user_id_foreign` (`user_id`);

--
-- Indexes for table `izin_gurus`
--
ALTER TABLE `izin_gurus`
  ADD PRIMARY KEY (`id`),
  ADD KEY `izin_gurus_disetujui_oleh_foreign` (`disetujui_oleh`),
  ADD KEY `idx_izin_tanggal_range` (`tanggal_mulai`,`tanggal_selesai`),
  ADD KEY `idx_izin_guru_id` (`guru_id`);

--
-- Indexes for table `jadwals`
--
ALTER TABLE `jadwals`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jadwals_kelas_id_foreign` (`kelas_id`),
  ADD KEY `jadwals_mata_pelajaran_id_foreign` (`mata_pelajaran_id`),
  ADD KEY `jadwals_guru_id_foreign` (`guru_id`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indexes for table `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `kehadirans`
--
ALTER TABLE `kehadirans`
  ADD PRIMARY KEY (`id`),
  ADD KEY `kehadirans_siswa_id_foreign` (`siswa_id`),
  ADD KEY `idx_kehadiran_tanggal` (`tanggal`),
  ADD KEY `idx_kehadiran_jadwal` (`jadwal_id`);

--
-- Indexes for table `kehadiran_gurus`
--
ALTER TABLE `kehadiran_gurus`
  ADD PRIMARY KEY (`id`),
  ADD KEY `kehadiran_gurus_jadwal_id_foreign` (`jadwal_id`),
  ADD KEY `kehadiran_gurus_guru_id_foreign` (`guru_id`),
  ADD KEY `kehadiran_gurus_diinput_oleh_foreign` (`diinput_oleh`);

--
-- Indexes for table `kelas`
--
ALTER TABLE `kelas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `kelas_wali_kelas_id_foreign` (`wali_kelas_id`);

--
-- Indexes for table `mata_pelajarans`
--
ALTER TABLE `mata_pelajarans`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `mata_pelajarans_kode_unique` (`kode`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`),
  ADD KEY `personal_access_tokens_expires_at_index` (`expires_at`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `siswas`
--
ALTER TABLE `siswas`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `siswas_nis_unique` (`nis`),
  ADD UNIQUE KEY `siswas_nisn_unique` (`nisn`),
  ADD KEY `siswas_kelas_id_foreign` (`kelas_id`),
  ADD KEY `siswas_user_id_foreign` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `failed_import_rows`
--
ALTER TABLE `failed_import_rows`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `gurus`
--
ALTER TABLE `gurus`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `guru_pengganties`
--
ALTER TABLE `guru_pengganties`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `imports`
--
ALTER TABLE `imports`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `izin_gurus`
--
ALTER TABLE `izin_gurus`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `jadwals`
--
ALTER TABLE `jadwals`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `kehadirans`
--
ALTER TABLE `kehadirans`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `kehadiran_gurus`
--
ALTER TABLE `kehadiran_gurus`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `kelas`
--
ALTER TABLE `kelas`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `mata_pelajarans`
--
ALTER TABLE `mata_pelajarans`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=141;

--
-- AUTO_INCREMENT for table `siswas`
--
ALTER TABLE `siswas`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
