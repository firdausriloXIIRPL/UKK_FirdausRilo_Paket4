-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Feb 10, 2026 at 04:21 AM
-- Server version: 8.0.30
-- PHP Version: 8.1.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `perpusrlo`
--

-- --------------------------------------------------------

--
-- Table structure for table `authors`
--

CREATE TABLE `authors` (
  `author_id` int NOT NULL,
  `nama_penulis` varchar(100) NOT NULL,
  `biografi` text,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `books`
--

CREATE TABLE `books` (
  `book_id` int NOT NULL,
  `kode_buku` varchar(20) NOT NULL,
  `judul` varchar(200) NOT NULL,
  `author_id` int DEFAULT NULL,
  `publisher_id` int DEFAULT NULL,
  `category_id` int DEFAULT NULL,
  `tahun_terbit` year DEFAULT NULL,
  `isbn` varchar(20) DEFAULT NULL,
  `jumlah_halaman` int DEFAULT NULL,
  `stok_tersedia` int DEFAULT '0',
  `stok_total` int DEFAULT '0',
  `rak_lokasi` varchar(20) DEFAULT NULL,
  `deskripsi` text,
  `cover_image` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `books`
--

INSERT INTO `books` (`book_id`, `kode_buku`, `judul`, `author_id`, `publisher_id`, `category_id`, `tahun_terbit`, `isbn`, `jumlah_halaman`, `stok_tersedia`, `stok_total`, `rak_lokasi`, `deskripsi`, `cover_image`, `created_at`, `updated_at`) VALUES
(1, '55672', 'HYDUP JOKOWY', NULL, NULL, 5, 1999, '112-334-556', 271, 3, 3, '001', 'HIDUP JOKOWY', '698a8a540c67a_1770687060.jpg', '2026-02-10 08:31:00', '2026-02-10 08:51:11'),
(2, '1', 'abc', NULL, NULL, 2, 1993, '1', 234, 3, 5, '002', 'watsap', '698a912769a7c_1770688807.png', '2026-02-10 09:00:07', '2026-02-10 10:30:39');

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `category_id` int NOT NULL,
  `nama_kategori` varchar(50) NOT NULL,
  `deskripsi` text,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`category_id`, `nama_kategori`, `deskripsi`, `created_at`) VALUES
(1, 'Fiksi', 'Buku fiksi dan novel', '2026-02-10 07:19:03'),
(2, 'Non-Fiksi', 'Buku non-fiksi', '2026-02-10 07:19:03'),
(3, 'Teknologi', 'Buku tentang teknologi dan komputer', '2026-02-10 07:19:03'),
(4, 'Pendidikan', 'Buku pendidikan dan pembelajaran', '2026-02-10 07:19:03'),
(5, 'Sejarah', 'Buku sejarah', '2026-02-10 07:19:03');

-- --------------------------------------------------------

--
-- Table structure for table `loans`
--

CREATE TABLE `loans` (
  `loan_id` int NOT NULL,
  `user_id` int NOT NULL,
  `book_id` int NOT NULL,
  `tanggal_pinjam` date NOT NULL,
  `tanggal_kembali` date NOT NULL,
  `tanggal_pengembalian` date DEFAULT NULL,
  `status` enum('dipinjam','dikembalikan','terlambat') COLLATE utf8mb4_unicode_ci DEFAULT 'dipinjam',
  `denda` decimal(10,2) DEFAULT '0.00',
  `keterangan` text COLLATE utf8mb4_unicode_ci,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `loans`
--

INSERT INTO `loans` (`loan_id`, `user_id`, `book_id`, `tanggal_pinjam`, `tanggal_kembali`, `tanggal_pengembalian`, `status`, `denda`, `keterangan`, `created_at`, `updated_at`) VALUES
(1, 2, 2, '2026-02-10', '2026-02-17', '2026-02-10', 'dikembalikan', '0.00', 'Disetujui dari request #2', '2026-02-10 10:03:10', '2026-02-10 10:30:39'),
(2, 2, 2, '2026-02-10', '2026-02-17', NULL, 'dipinjam', '0.00', 'DD', '2026-02-10 10:24:40', '2026-02-10 10:24:40');

-- --------------------------------------------------------

--
-- Table structure for table `loans_backup`
--

CREATE TABLE `loans_backup` (
  `loan_id` int NOT NULL DEFAULT '0',
  `user_id` int NOT NULL,
  `book_id` int NOT NULL,
  `tanggal_pinjam` date NOT NULL,
  `tanggal_kembali_rencana` date NOT NULL,
  `tanggal_kembali_aktual` date DEFAULT NULL,
  `status` enum('dipinjam','dikembalikan','terlambat') DEFAULT 'dipinjam',
  `denda` decimal(10,2) DEFAULT '0.00',
  `keterangan` text,
  `admin_pinjam` int NOT NULL,
  `admin_kembali` int DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `loans_backup`
--

INSERT INTO `loans_backup` (`loan_id`, `user_id`, `book_id`, `tanggal_pinjam`, `tanggal_kembali_rencana`, `tanggal_kembali_aktual`, `status`, `denda`, `keterangan`, `admin_pinjam`, `admin_kembali`, `created_at`, `updated_at`) VALUES
(1, 2, 1, '2026-02-10', '2026-02-17', '2026-02-10', 'dikembalikan', '0.00', 'minjem', 1, 1, '2026-02-10 08:44:01', '2026-02-10 08:51:11');

-- --------------------------------------------------------

--
-- Table structure for table `loan_requests`
--

CREATE TABLE `loan_requests` (
  `request_id` int NOT NULL,
  `user_id` int NOT NULL,
  `book_id` int NOT NULL,
  `request_date` datetime DEFAULT CURRENT_TIMESTAMP,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `approved_date` datetime DEFAULT NULL,
  `keterangan` text,
  `admin_response` int DEFAULT NULL,
  `response_note` text,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `loan_requests`
--

INSERT INTO `loan_requests` (`request_id`, `user_id`, `book_id`, `request_date`, `status`, `approved_date`, `keterangan`, `admin_response`, `response_note`, `created_at`, `updated_at`) VALUES
(1, 2, 1, '2026-02-10 08:58:22', 'rejected', NULL, '', 1, 'gmw aj', '2026-02-10 08:58:22', '2026-02-10 10:25:00'),
(2, 2, 2, '2026-02-10 09:46:09', 'approved', '2026-02-10 10:03:10', '', 1, NULL, '2026-02-10 09:46:09', '2026-02-10 10:03:10');

-- --------------------------------------------------------

--
-- Table structure for table `publishers`
--

CREATE TABLE `publishers` (
  `publisher_id` int NOT NULL,
  `nama_penerbit` varchar(100) NOT NULL,
  `alamat` text,
  `telepon` varchar(15) DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `setting_id` int NOT NULL,
  `nama_setting` varchar(50) NOT NULL,
  `nilai` varchar(100) NOT NULL,
  `deskripsi` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`setting_id`, `nama_setting`, `nilai`, `deskripsi`) VALUES
(1, 'lama_peminjaman', '7', 'Lama peminjaman dalam hari'),
(2, 'denda_per_hari', '1000', 'Denda keterlambatan per hari (Rp)'),
(3, 'max_buku_pinjam', '3', 'Maksimal buku yang bisa dipinjam per anggota'),
(4, 'nama_perpustakaan', 'Perpustakaan RLO', 'Nama perpustakaan');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `nama_lengkap` varchar(100) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `no_telepon` varchar(15) DEFAULT NULL,
  `alamat` text,
  `role` enum('admin','anggota') NOT NULL,
  `status` enum('aktif','nonaktif') DEFAULT 'aktif',
  `foto` varchar(255) DEFAULT NULL,
  `tanggal_daftar` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `username`, `password`, `nama_lengkap`, `email`, `no_telepon`, `alamat`, `role`, `status`, `foto`, `tanggal_daftar`, `updated_at`) VALUES
(1, 'admin', '$2y$10$HsPNLaX97kueousM4xWVHOGBV/erXAb0o6XX.s9.e.7YdfSGL5td.', 'Administrator', 'admin@perpusrlo.com', NULL, NULL, 'admin', 'aktif', NULL, '2026-02-10 07:19:03', '2026-02-10 08:22:59'),
(2, 'user1', '$2y$10$3jqGqVTyY2KVnrdjQkAy5ezf4eMP4EJu5mnJN4qm/eLwMD6DbsYJK', 'user1', 'user12@gmail.com', '098765432', 'manaja', 'anggota', 'aktif', NULL, '2026-02-10 08:26:43', '2026-02-10 09:47:38');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `authors`
--
ALTER TABLE `authors`
  ADD PRIMARY KEY (`author_id`);

--
-- Indexes for table `books`
--
ALTER TABLE `books`
  ADD PRIMARY KEY (`book_id`),
  ADD UNIQUE KEY `kode_buku` (`kode_buku`),
  ADD KEY `author_id` (`author_id`),
  ADD KEY `publisher_id` (`publisher_id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`category_id`);

--
-- Indexes for table `loans`
--
ALTER TABLE `loans`
  ADD PRIMARY KEY (`loan_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `book_id` (`book_id`);

--
-- Indexes for table `loan_requests`
--
ALTER TABLE `loan_requests`
  ADD PRIMARY KEY (`request_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `book_id` (`book_id`),
  ADD KEY `admin_response` (`admin_response`);

--
-- Indexes for table `publishers`
--
ALTER TABLE `publishers`
  ADD PRIMARY KEY (`publisher_id`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`setting_id`),
  ADD UNIQUE KEY `nama_setting` (`nama_setting`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `authors`
--
ALTER TABLE `authors`
  MODIFY `author_id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `books`
--
ALTER TABLE `books`
  MODIFY `book_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `category_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `loans`
--
ALTER TABLE `loans`
  MODIFY `loan_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `loan_requests`
--
ALTER TABLE `loan_requests`
  MODIFY `request_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `publishers`
--
ALTER TABLE `publishers`
  MODIFY `publisher_id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `setting_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `books`
--
ALTER TABLE `books`
  ADD CONSTRAINT `books_ibfk_1` FOREIGN KEY (`author_id`) REFERENCES `authors` (`author_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `books_ibfk_2` FOREIGN KEY (`publisher_id`) REFERENCES `publishers` (`publisher_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `books_ibfk_3` FOREIGN KEY (`category_id`) REFERENCES `categories` (`category_id`) ON DELETE SET NULL;

--
-- Constraints for table `loans`
--
ALTER TABLE `loans`
  ADD CONSTRAINT `loans_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `loans_ibfk_2` FOREIGN KEY (`book_id`) REFERENCES `books` (`book_id`) ON DELETE CASCADE;

--
-- Constraints for table `loan_requests`
--
ALTER TABLE `loan_requests`
  ADD CONSTRAINT `loan_requests_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `loan_requests_ibfk_2` FOREIGN KEY (`book_id`) REFERENCES `books` (`book_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `loan_requests_ibfk_3` FOREIGN KEY (`admin_response`) REFERENCES `users` (`user_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
