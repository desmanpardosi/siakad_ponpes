-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Waktu pembuatan: 29 Jul 2024 pada 11.47
-- Versi server: 8.0.37-0ubuntu0.24.04.1
-- Versi PHP: 8.2.21

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `pesantren`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `assets`
--

CREATE TABLE `assets` (
  `asset_id` bigint NOT NULL,
  `nama_asset` varchar(50) NOT NULL,
  `tgl_buat` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `user_buat` varchar(50) NOT NULL,
  `NA` enum('Y','N') CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT 'N'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Dumping data untuk tabel `assets`
--

INSERT INTO `assets` (`asset_id`, `nama_asset`, `tgl_buat`, `user_buat`, `NA`) VALUES
(6, 'Kursi', '2024-07-28 14:15:43', 'admin', 'N'),
(7, 'Meja', '2024-07-28 14:15:46', 'admin', 'N'),
(8, 'Meubelair', '2024-07-28 14:15:50', 'admin', 'N');

-- --------------------------------------------------------

--
-- Struktur dari tabel `pemasukan`
--

CREATE TABLE `pemasukan` (
  `pemasukan_id` bigint NOT NULL,
  `tanggal` date NOT NULL,
  `kategori_id` bigint NOT NULL,
  `jumlah` bigint NOT NULL,
  `tgl_buat` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `user_buat` bigint NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Dumping data untuk tabel `pemasukan`
--

INSERT INTO `pemasukan` (`pemasukan_id`, `tanggal`, `kategori_id`, `jumlah`, `tgl_buat`, `user_buat`) VALUES
(1, '2024-07-29', 6, 1500000, '2024-07-29 14:23:17', 1),
(2, '2024-07-29', 7, 500000, '2024-07-29 14:57:15', 0),
(3, '2024-07-28', 6, 500000, '2024-07-29 14:58:57', 0);

-- --------------------------------------------------------

--
-- Struktur dari tabel `pemasukan_kategori`
--

CREATE TABLE `pemasukan_kategori` (
  `kategori_id` bigint NOT NULL,
  `kategori` varchar(50) NOT NULL,
  `tgl_buat` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `user_buat` varchar(50) NOT NULL,
  `NA` enum('Y','N') CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT 'N'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Dumping data untuk tabel `pemasukan_kategori`
--

INSERT INTO `pemasukan_kategori` (`kategori_id`, `kategori`, `tgl_buat`, `user_buat`, `NA`) VALUES
(6, 'Uang Makan', '2024-07-28 14:16:11', 'superadmin', 'N'),
(7, 'Uang Listrik', '2024-07-28 14:16:13', 'superadmin', 'N');

-- --------------------------------------------------------

--
-- Struktur dari tabel `pengeluaran`
--

CREATE TABLE `pengeluaran` (
  `pengeluaran_id` bigint NOT NULL,
  `tanggal` date NOT NULL,
  `kategori_id` bigint NOT NULL,
  `jumlah` bigint NOT NULL,
  `tgl_buat` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `user_buat` bigint NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Dumping data untuk tabel `pengeluaran`
--

INSERT INTO `pengeluaran` (`pengeluaran_id`, `tanggal`, `kategori_id`, `jumlah`, `tgl_buat`, `user_buat`) VALUES
(1, '2024-07-29', 1, 500000, '2024-07-29 15:55:27', 0),
(2, '2024-07-29', 3, 700000, '2024-07-29 15:59:28', 0);

-- --------------------------------------------------------

--
-- Struktur dari tabel `pengeluaran_kategori`
--

CREATE TABLE `pengeluaran_kategori` (
  `kategori_id` bigint NOT NULL,
  `jenis` int NOT NULL COMMENT '0 = Dana Guru; 1 = Dana Operasional;',
  `kategori` varchar(50) NOT NULL,
  `tgl_buat` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `user_buat` varchar(50) NOT NULL,
  `NA` enum('Y','N') CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT 'N'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Dumping data untuk tabel `pengeluaran_kategori`
--

INSERT INTO `pengeluaran_kategori` (`kategori_id`, `jenis`, `kategori`, `tgl_buat`, `user_buat`, `NA`) VALUES
(1, 0, 'Honor', '2024-07-29 15:37:49', 'superadmin', 'N'),
(2, 0, 'Insentif', '2024-07-29 15:38:04', 'superadmin', 'N'),
(3, 1, 'Listrik', '2024-07-29 15:38:14', 'superadmin', 'N'),
(4, 1, 'Dapur', '2024-07-29 15:39:17', 'superadmin', 'N');

-- --------------------------------------------------------

--
-- Struktur dari tabel `roles`
--

CREATE TABLE `roles` (
  `role_id` bigint NOT NULL,
  `name` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Dumping data untuk tabel `roles`
--

INSERT INTO `roles` (`role_id`, `name`) VALUES
(0, 'Super Admin'),
(1, 'Admin'),
(2, 'Guru'),
(3, 'Santri');

-- --------------------------------------------------------

--
-- Struktur dari tabel `santri`
--

CREATE TABLE `santri` (
  `santri_id` bigint NOT NULL,
  `nis` varchar(25) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `nama_lengkap` varchar(50) NOT NULL,
  `nik` varchar(16) DEFAULT NULL,
  `no_kk` varchar(16) DEFAULT NULL,
  `tempat_lahir` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `tgl_lahir` date DEFAULT NULL,
  `alamat` text CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci,
  `no_hp` varchar(20) DEFAULT NULL,
  `pendidikan_formal` int DEFAULT NULL COMMENT '0 = PAUD; 1 = MI; 2 = MTS; 3 = SMK;',
  `kelas_semester` varchar(25) DEFAULT NULL,
  `nisn` varchar(10) DEFAULT NULL,
  `program_ponpes` int DEFAULT NULL COMMENT '0 = Pondok; 1 = Kursus;',
  `riwayat_mondok` varchar(100) DEFAULT NULL,
  `nama_ayah` varchar(50) DEFAULT NULL,
  `nama_ibu` varchar(50) DEFAULT NULL,
  `nohp_ortu` varchar(20) DEFAULT NULL,
  `alamat_ortu` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Dumping data untuk tabel `santri`
--

INSERT INTO `santri` (`santri_id`, `nis`, `nama_lengkap`, `nik`, `no_kk`, `tempat_lahir`, `tgl_lahir`, `alamat`, `no_hp`, `pendidikan_formal`, `kelas_semester`, `nisn`, `program_ponpes`, `riwayat_mondok`, `nama_ayah`, `nama_ibu`, `nohp_ortu`, `alamat_ortu`) VALUES
(3, '121112066', 'DHP', '1971051512940001', '1971051512940001', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `staff`
--

CREATE TABLE `staff` (
  `staff_id` bigint NOT NULL,
  `staff_type` int DEFAULT '0' COMMENT '0 = Guru; 1 = Staff;',
  `nama_lengkap` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `nik` varchar(16) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `tempat_lahir` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `tgl_lahir` date DEFAULT NULL,
  `alamat` text,
  `no_hp` varchar(20) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `pendidikan_terakhir` varchar(25) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `bidang_mengajar` varchar(100) DEFAULT NULL,
  `no_sk` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `mulai_mengajar` varchar(4) DEFAULT NULL,
  `status` int DEFAULT '2' COMMENT '0 = Sertifikasi; 1 = Honorer; 2 = Lainnya;'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id` bigint UNSIGNED NOT NULL,
  `username` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'User',
  `role` int NOT NULL COMMENT '0 = Super Admin; 1 = Admin; 2 = Guru; 3 = Santri;',
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id`, `username`, `name`, `role`, `password`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'superadmin', 'Super Admin', 0, '$2a$12$y6VnBvFXAVxp207MT6oMDOlxP1bXR8/dCWLgtjhl3vunC.791IAQG', 'EHUwa4jykJWu6YO47lKmxSs8B9j6H4WkrVs65up4sez1A06H5fJpmcQ0QEYZ', NULL, NULL),
(5, 'admin', 'Admin', 1, '$2y$10$5fVRQuVavslEwTTf0e4oZuwhb53WmqhQ4fV.FZpYlHbWshQnOszdW', NULL, NULL, NULL),
(6, '121112066', 'DHP', 3, '$2y$10$dbEmNnOnhD66onDUzHZ1d.U.iXjFnNm6D5suECJIb8BDsF2Ow43WO', NULL, NULL, NULL);

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `assets`
--
ALTER TABLE `assets`
  ADD PRIMARY KEY (`asset_id`);

--
-- Indeks untuk tabel `pemasukan`
--
ALTER TABLE `pemasukan`
  ADD PRIMARY KEY (`pemasukan_id`);

--
-- Indeks untuk tabel `pemasukan_kategori`
--
ALTER TABLE `pemasukan_kategori`
  ADD PRIMARY KEY (`kategori_id`);

--
-- Indeks untuk tabel `pengeluaran`
--
ALTER TABLE `pengeluaran`
  ADD PRIMARY KEY (`pengeluaran_id`);

--
-- Indeks untuk tabel `pengeluaran_kategori`
--
ALTER TABLE `pengeluaran_kategori`
  ADD PRIMARY KEY (`kategori_id`);

--
-- Indeks untuk tabel `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`role_id`);

--
-- Indeks untuk tabel `santri`
--
ALTER TABLE `santri`
  ADD PRIMARY KEY (`santri_id`);

--
-- Indeks untuk tabel `staff`
--
ALTER TABLE `staff`
  ADD PRIMARY KEY (`staff_id`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD KEY `username` (`username`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `assets`
--
ALTER TABLE `assets`
  MODIFY `asset_id` bigint NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT untuk tabel `pemasukan`
--
ALTER TABLE `pemasukan`
  MODIFY `pemasukan_id` bigint NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT untuk tabel `pemasukan_kategori`
--
ALTER TABLE `pemasukan_kategori`
  MODIFY `kategori_id` bigint NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT untuk tabel `pengeluaran`
--
ALTER TABLE `pengeluaran`
  MODIFY `pengeluaran_id` bigint NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `pengeluaran_kategori`
--
ALTER TABLE `pengeluaran_kategori`
  MODIFY `kategori_id` bigint NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT untuk tabel `roles`
--
ALTER TABLE `roles`
  MODIFY `role_id` bigint NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT untuk tabel `santri`
--
ALTER TABLE `santri`
  MODIFY `santri_id` bigint NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT untuk tabel `staff`
--
ALTER TABLE `staff`
  MODIFY `staff_id` bigint NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
