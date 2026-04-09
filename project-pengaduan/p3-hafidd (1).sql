-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 07, 2026 at 09:40 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `p3-hafidd`
--

-- --------------------------------------------------------

--
-- Table structure for table `aspirasi`
--

CREATE TABLE `aspirasi` (
  `id_aspirasi` int(11) NOT NULL,
  `nama` varchar(255) NOT NULL,
  `judul` varchar(255) NOT NULL,
  `isi` text NOT NULL,
  `kategori` varchar(100) NOT NULL,
  `tanggal` datetime NOT NULL,
  `status` enum('menunggu','diproses','selesai') NOT NULL DEFAULT 'menunggu'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `aspirasi`
--

INSERT INTO `aspirasi` (`id_aspirasi`, `nama`, `judul`, `isi`, `kategori`, `tanggal`, `status`) VALUES
(1, 'hafiddd', 'Lapangan rasa mesirr', 'kararebullllll', 'Lapangan Sekolah', '2026-04-02 02:19:59', 'menunggu');

-- --------------------------------------------------------

--
-- Table structure for table `data_aspirasi`
--

CREATE TABLE `data_aspirasi` (
  `id_aspirasi` int(11) NOT NULL,
  `sarana` varchar(100) NOT NULL,
  `deskripsi` text NOT NULL,
  `status` enum('direncanakan','diproses','selesai') DEFAULT 'direncanakan',
  `progress_persen` int(11) DEFAULT 0,
  `tanggal_mulai` date NOT NULL,
  `tanggal_selesai` date DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `data_aspirasi`
--

INSERT INTO `data_aspirasi` (`id_aspirasi`, `sarana`, `deskripsi`, `status`, `progress_persen`, `tanggal_mulai`, `tanggal_selesai`, `created_at`, `updated_at`) VALUES
(2, 'Lapangan Sekolah', 'Perbaikan lapangan basket dan pengecatan', 'direncanakan', 100, '2026-02-20', '2026-03-30', '2026-02-11 00:43:43', '2026-04-07 07:10:08'),
(3, 'Masjid', 'Perbaikan atap dan sistem ventilasi masjid', 'selesai', 100, '2025-12-01', '2026-01-31', '2026-02-11 00:43:43', '2026-02-11 00:43:43'),
(4, 'Laboratorium', 'Penggantian peralatan laboratorium komputer', 'diproses', 45, '2026-01-20', '2026-03-15', '2026-02-11 00:43:43', '2026-02-11 00:43:43'),
(5, 'Masjid', 'Pembangunan kolam pencucian kaki sebelum area suci masjid', 'direncanakan', 57, '2027-10-02', '2028-11-02', '2026-02-11 00:47:03', '2026-02-24 01:20:39'),
(6, 'Lapangan Sekolah', 'Penambahan material bata untuk pembatas', 'diproses', 100, '2026-02-02', '2026-05-02', '2026-02-24 01:20:11', '2026-04-07 07:10:34'),
(7, 'Masjid', 'Pengecoran dan pengecatan dinding bagian atas', 'selesai', 100, '2027-11-02', '2028-08-01', '2026-03-12 02:14:09', '2026-04-07 07:09:48');

-- --------------------------------------------------------

--
-- Table structure for table `kategori`
--

CREATE TABLE `kategori` (
  `id_kategori` int(11) NOT NULL,
  `nama_kategori` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `kategori`
--

INSERT INTO `kategori` (`id_kategori`, `nama_kategori`) VALUES
(1, 'Masjid'),
(2, 'Lapangan Sekolah'),
(3, 'Kantin'),
(4, 'Kamar mandi'),
(5, 'Laboratorium'),
(6, 'Ruang kelass');

-- --------------------------------------------------------

--
-- Table structure for table `log_aktivitas`
--

CREATE TABLE `log_aktivitas` (
  `id` int(11) NOT NULL,
  `aksi` varchar(100) DEFAULT NULL,
  `deskripsi` text DEFAULT NULL,
  `user` varchar(100) DEFAULT NULL,
  `waktu` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `log_aktivitas`
--

INSERT INTO `log_aktivitas` (`id`, `aksi`, `deskripsi`, `user`, `waktu`) VALUES
(1, 'jsbdbdf', 'fwfefe', 'fefefe', '2026-04-07 05:38:34');

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `id` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `sender` varchar(10) NOT NULL,
  `message` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `messages`
--

INSERT INTO `messages` (`id`, `id_user`, `sender`, `message`, `created_at`) VALUES
(11, 2, 'siswa', 'assalamualikum ya habibbbb', '2026-02-10 06:11:05'),
(14, 2, 'admin', 'waalaikumussalam ya ukhtiii', '2026-02-10 06:12:04'),
(15, 2, 'admin', 'ramadhan kareemmmm', '2026-02-24 04:49:05'),
(17, 2, 'siswa', 'maass maff', '2026-02-24 05:04:22'),
(18, 2, 'siswa', 'kebalik jadi kata kata nya maf', '2026-03-12 02:16:15'),
(19, 2, 'admin', 'shapppp', '2026-03-30 08:10:21'),
(20, 2, 'siswa', 'yesss', '2026-03-30 08:10:35'),
(21, 2, 'admin', 'ikiiii ikiiii', '2026-04-02 01:57:39'),
(22, 0, 'siswa', 'haiii bosss', '2026-04-06 01:57:12'),
(23, 0, 'siswa', 'boss sehatttt?????', '2026-04-06 04:08:18');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id_user` int(255) NOT NULL,
  `email` text NOT NULL,
  `nama` varchar(255) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` int(255) NOT NULL,
  `role` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id_user`, `email`, `nama`, `username`, `password`, `role`) VALUES
(0, 'masiki@gmail.com', 'Mas ikiii', 'kate kateline', 54321, 'siswa'),
(1, 'hafid@gmail.com', 'pa hafiddd', 'Pa 4fidd', 123123, 'admin'),
(2, 'ikidasep@gmail.com', 'ikii', 'dasepp', 12345, 'siswa');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `aspirasi`
--
ALTER TABLE `aspirasi`
  ADD PRIMARY KEY (`id_aspirasi`);

--
-- Indexes for table `data_aspirasi`
--
ALTER TABLE `data_aspirasi`
  ADD PRIMARY KEY (`id_aspirasi`);

--
-- Indexes for table `kategori`
--
ALTER TABLE `kategori`
  ADD PRIMARY KEY (`id_kategori`);

--
-- Indexes for table `log_aktivitas`
--
ALTER TABLE `log_aktivitas`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id_user`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `aspirasi`
--
ALTER TABLE `aspirasi`
  MODIFY `id_aspirasi` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `data_aspirasi`
--
ALTER TABLE `data_aspirasi`
  MODIFY `id_aspirasi` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `kategori`
--
ALTER TABLE `kategori`
  MODIFY `id_kategori` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `log_aktivitas`
--
ALTER TABLE `log_aktivitas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
