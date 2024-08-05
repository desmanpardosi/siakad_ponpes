-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Waktu pembuatan: 05 Agu 2024 pada 13.52
-- Versi server: 8.0.39-0ubuntu0.24.04.1
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
  `ruangan_id` bigint NOT NULL,
  `nama_asset` varchar(50) NOT NULL,
  `tgl_buat` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `user_buat` varchar(50) NOT NULL,
  `NA` enum('Y','N') CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT 'N'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Dumping data untuk tabel `assets`
--

INSERT INTO `assets` (`asset_id`, `ruangan_id`, `nama_asset`, `tgl_buat`, `user_buat`, `NA`) VALUES
(9, 1, 'Kursi', '2024-08-01 10:40:20', 'superadmin', 'N'),
(10, 1, 'Meja', '2024-08-01 10:40:28', 'superadmin', 'N'),
(11, 1, 'TV', '2024-08-01 10:40:32', 'superadmin', 'N'),
(12, 2, 'Kursi', '2024-08-01 10:41:01', 'superadmin', 'N'),
(13, 2, 'Meja', '2024-08-01 10:41:05', 'superadmin', 'N'),
(14, 1, 'Laptop', '2024-08-01 10:41:13', 'superadmin', 'N'),
(15, 2, 'Laptop', '2024-08-01 10:41:19', 'superadmin', 'N');

-- --------------------------------------------------------

--
-- Struktur dari tabel `jadwal_pelajaran`
--

CREATE TABLE `jadwal_pelajaran` (
  `jadwal_id` bigint NOT NULL,
  `tahun_id` bigint NOT NULL,
  `jp_id` bigint NOT NULL,
  `mapel_id` bigint NOT NULL,
  `NA` enum('Y','N') NOT NULL DEFAULT 'N'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Dumping data untuk tabel `jadwal_pelajaran`
--

INSERT INTO `jadwal_pelajaran` (`jadwal_id`, `tahun_id`, `jp_id`, `mapel_id`, `NA`) VALUES
(1, 1, 1, 1, 'N'),
(2, 1, 6, 1, 'N'),
(3, 1, 2, 2, 'N'),
(4, 1, 7, 3, 'N');

-- --------------------------------------------------------

--
-- Struktur dari tabel `jam_pelajaran`
--

CREATE TABLE `jam_pelajaran` (
  `jp_id` bigint NOT NULL,
  `hari` int NOT NULL COMMENT '0 = Senin s/d 6 = Minggu',
  `jam` varchar(50) NOT NULL,
  `NA` enum('Y','N') NOT NULL DEFAULT 'N'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Dumping data untuk tabel `jam_pelajaran`
--

INSERT INTO `jam_pelajaran` (`jp_id`, `hari`, `jam`, `NA`) VALUES
(1, 0, '07.00-08.25', 'N'),
(2, 0, '08.25-09.45', 'N'),
(3, 0, '10.15-11.25', 'N'),
(4, 0, '11.25-12.30', 'N'),
(5, 0, '08.00-08.15', 'Y'),
(6, 1, '08.00-08.15', 'Y'),
(7, 0, '11.25-12.30', 'N');

-- --------------------------------------------------------

--
-- Struktur dari tabel `kelas`
--

CREATE TABLE `kelas` (
  `kelas_id` bigint NOT NULL,
  `kelas_semester` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `tgl_buat` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `user_buat` varchar(50) NOT NULL,
  `NA` enum('Y','N') CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT 'N'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Dumping data untuk tabel `kelas`
--

INSERT INTO `kelas` (`kelas_id`, `kelas_semester`, `tgl_buat`, `user_buat`, `NA`) VALUES
(1, 'X / II', '2024-08-01 19:38:51', 'admin', 'N'),
(2, 'XI / II', '2024-08-05 01:17:57', 'admin', 'N');

-- --------------------------------------------------------

--
-- Struktur dari tabel `keuangan`
--

CREATE TABLE `keuangan` (
  `keuangan_id` bigint NOT NULL,
  `jenis` int NOT NULL DEFAULT '0' COMMENT '0 = Pemasukan; 1 = Pengeluaran;',
  `tanggal` date NOT NULL,
  `kategori_id` bigint NOT NULL,
  `nominal` bigint NOT NULL,
  `tgl_buat` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `user_buat` bigint NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Dumping data untuk tabel `keuangan`
--

INSERT INTO `keuangan` (`keuangan_id`, `jenis`, `tanggal`, `kategori_id`, `nominal`, `tgl_buat`, `user_buat`) VALUES
(1, 0, '2024-08-01', 6, 1500000, '2024-08-01 15:10:21', 0),
(2, 1, '2024-08-01', 3, 500000, '2024-08-01 15:25:46', 0);

-- --------------------------------------------------------

--
-- Struktur dari tabel `mapel`
--

CREATE TABLE `mapel` (
  `mapel_id` bigint NOT NULL,
  `mapel` varchar(50) NOT NULL,
  `kelas_id` bigint NOT NULL,
  `guru` bigint NOT NULL COMMENT 'Guru',
  `tgl_buat` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `user_buat` varchar(50) NOT NULL,
  `NA` enum('Y','N') NOT NULL DEFAULT 'N'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Dumping data untuk tabel `mapel`
--

INSERT INTO `mapel` (`mapel_id`, `mapel`, `kelas_id`, `guru`, `tgl_buat`, `user_buat`, `NA`) VALUES
(1, 'Bahasa Indonesia', 1, 7, '2024-08-01 20:56:33', 'admin', 'N'),
(2, 'Bahasa Indonesia', 2, 7, '2024-08-05 01:18:13', 'admin', 'N'),
(3, 'Matematika', 1, 8, '2024-08-05 19:50:02', 'admin', 'N');

-- --------------------------------------------------------

--
-- Struktur dari tabel `nilai`
--

CREATE TABLE `nilai` (
  `nilai_id` bigint NOT NULL,
  `mapel_id` bigint NOT NULL,
  `santri_id` bigint NOT NULL,
  `nilai` int NOT NULL DEFAULT '0',
  `tgl_buat` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Dumping data untuk tabel `nilai`
--

INSERT INTO `nilai` (`nilai_id`, `mapel_id`, `santri_id`, `nilai`, `tgl_buat`) VALUES
(4, 1, 3, 80, '2024-08-05 19:44:44'),
(7, 3, 3, 65, '2024-08-05 19:59:30');

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
-- Struktur dari tabel `pengumuman`
--

CREATE TABLE `pengumuman` (
  `pengumuman_id` bigint NOT NULL,
  `judul` varchar(100) NOT NULL,
  `deskripsi` text NOT NULL,
  `tgl_buat` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `user_buat` varchar(50) DEFAULT NULL,
  `NA` enum('Y','N') NOT NULL DEFAULT 'N'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Struktur dari tabel `presensi`
--

CREATE TABLE `presensi` (
  `presensi_id` bigint NOT NULL,
  `jadwal_id` bigint NOT NULL,
  `santri_id` bigint NOT NULL,
  `tgl_presensi` date NOT NULL,
  `tgl_buat` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Dumping data untuk tabel `presensi`
--

INSERT INTO `presensi` (`presensi_id`, `jadwal_id`, `santri_id`, `tgl_presensi`, `tgl_buat`) VALUES
(7, 1, 3, '2024-08-04', '2024-08-05 01:11:37');

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
-- Struktur dari tabel `ruangan`
--

CREATE TABLE `ruangan` (
  `ruangan_id` bigint NOT NULL,
  `nama_ruangan` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `tgl_buat` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `user_buat` varchar(50) NOT NULL,
  `NA` enum('Y','N') CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT 'N'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Dumping data untuk tabel `ruangan`
--

INSERT INTO `ruangan` (`ruangan_id`, `nama_ruangan`, `tgl_buat`, `user_buat`, `NA`) VALUES
(1, 'R. Kepala Sekolah', '2024-08-01 10:32:48', 'superadmin', 'N'),
(2, 'R. Wakil Kepala Sekolah', '2024-08-01 10:40:52', 'superadmin', 'N');

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
  `kelas_id` bigint NOT NULL,
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

INSERT INTO `santri` (`santri_id`, `nis`, `nama_lengkap`, `nik`, `no_kk`, `tempat_lahir`, `tgl_lahir`, `alamat`, `no_hp`, `pendidikan_formal`, `kelas_id`, `nisn`, `program_ponpes`, `riwayat_mondok`, `nama_ayah`, `nama_ibu`, `nohp_ortu`, `alamat_ortu`) VALUES
(3, '121112066', 'DHP', '1971051512940001', '1971051512940001', 'Pangkalpinang', '1994-12-15', NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `staff`
--

CREATE TABLE `staff` (
  `staff_id` bigint NOT NULL,
  `staff_type` int NOT NULL DEFAULT '0' COMMENT '0 = Guru; 1 = Staff;',
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

--
-- Dumping data untuk tabel `staff`
--

INSERT INTO `staff` (`staff_id`, `staff_type`, `nama_lengkap`, `nik`, `tempat_lahir`, `tgl_lahir`, `alamat`, `no_hp`, `pendidikan_terakhir`, `bidang_mengajar`, `no_sk`, `mulai_mengajar`, `status`) VALUES
(9, 0, 'Desman Harianto Pardosi', '1971051512940001', 'Pangkalpinang', NULL, 'Perumahan Cahaya Indah Residence 17 No. 08, Jl. Anggrek, Kel. Tua Tunu Indah, Kec. Gerunggang', '0811666824', NULL, NULL, NULL, NULL, 0);

-- --------------------------------------------------------

--
-- Struktur dari tabel `tahun_pelajaran`
--

CREATE TABLE `tahun_pelajaran` (
  `tahun_id` bigint NOT NULL,
  `tahun_pelajaran` varchar(50) NOT NULL,
  `NA` enum('Y','N') NOT NULL DEFAULT 'N'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Dumping data untuk tabel `tahun_pelajaran`
--

INSERT INTO `tahun_pelajaran` (`tahun_id`, `tahun_pelajaran`, `NA`) VALUES
(1, '2024/2025', 'N'),
(2, '2025/2026', 'N');

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
(1, 'superadmin', 'Super Admin', 0, '$2a$12$y6VnBvFXAVxp207MT6oMDOlxP1bXR8/dCWLgtjhl3vunC.791IAQG', 'Wb61ogoi9QyPvaQRiL6QofLdzdqn0jXbqUwee6cxIopKoVFfHdCb2W6ZCGK2', NULL, NULL),
(5, 'admin', 'Admin', 1, '$2y$10$5fVRQuVavslEwTTf0e4oZuwhb53WmqhQ4fV.FZpYlHbWshQnOszdW', NULL, NULL, NULL),
(6, '121112066', 'DHP', 3, '$2y$10$dbEmNnOnhD66onDUzHZ1d.U.iXjFnNm6D5suECJIb8BDsF2Ow43WO', NULL, NULL, NULL),
(7, 'guru', 'Guru Bahasa Indonesia, S.Pd.', 2, '$2y$10$Yj2o7H7jU3Ele2k3jo6adOIleCZPDymJAlF9u0QfeDcVTAmaHCZ0.', NULL, NULL, NULL),
(8, 'guru_mtk', 'Guru MTK, S.Pd', 2, '$2y$10$bcK07sW83K5U9owLnhVmo.G9hmGOG3CXWjlzuh8l43dOOaAviPYpO', NULL, NULL, NULL);

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `assets`
--
ALTER TABLE `assets`
  ADD PRIMARY KEY (`asset_id`);

--
-- Indeks untuk tabel `jadwal_pelajaran`
--
ALTER TABLE `jadwal_pelajaran`
  ADD PRIMARY KEY (`jadwal_id`);

--
-- Indeks untuk tabel `jam_pelajaran`
--
ALTER TABLE `jam_pelajaran`
  ADD PRIMARY KEY (`jp_id`);

--
-- Indeks untuk tabel `kelas`
--
ALTER TABLE `kelas`
  ADD PRIMARY KEY (`kelas_id`);

--
-- Indeks untuk tabel `keuangan`
--
ALTER TABLE `keuangan`
  ADD PRIMARY KEY (`keuangan_id`);

--
-- Indeks untuk tabel `mapel`
--
ALTER TABLE `mapel`
  ADD PRIMARY KEY (`mapel_id`);

--
-- Indeks untuk tabel `nilai`
--
ALTER TABLE `nilai`
  ADD PRIMARY KEY (`nilai_id`);

--
-- Indeks untuk tabel `pemasukan_kategori`
--
ALTER TABLE `pemasukan_kategori`
  ADD PRIMARY KEY (`kategori_id`);

--
-- Indeks untuk tabel `pengeluaran_kategori`
--
ALTER TABLE `pengeluaran_kategori`
  ADD PRIMARY KEY (`kategori_id`);

--
-- Indeks untuk tabel `pengumuman`
--
ALTER TABLE `pengumuman`
  ADD PRIMARY KEY (`pengumuman_id`);

--
-- Indeks untuk tabel `presensi`
--
ALTER TABLE `presensi`
  ADD PRIMARY KEY (`presensi_id`);

--
-- Indeks untuk tabel `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`role_id`);

--
-- Indeks untuk tabel `ruangan`
--
ALTER TABLE `ruangan`
  ADD PRIMARY KEY (`ruangan_id`);

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
-- Indeks untuk tabel `tahun_pelajaran`
--
ALTER TABLE `tahun_pelajaran`
  ADD PRIMARY KEY (`tahun_id`);

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
  MODIFY `asset_id` bigint NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT untuk tabel `jadwal_pelajaran`
--
ALTER TABLE `jadwal_pelajaran`
  MODIFY `jadwal_id` bigint NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT untuk tabel `jam_pelajaran`
--
ALTER TABLE `jam_pelajaran`
  MODIFY `jp_id` bigint NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT untuk tabel `kelas`
--
ALTER TABLE `kelas`
  MODIFY `kelas_id` bigint NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `keuangan`
--
ALTER TABLE `keuangan`
  MODIFY `keuangan_id` bigint NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `mapel`
--
ALTER TABLE `mapel`
  MODIFY `mapel_id` bigint NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT untuk tabel `nilai`
--
ALTER TABLE `nilai`
  MODIFY `nilai_id` bigint NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT untuk tabel `pemasukan_kategori`
--
ALTER TABLE `pemasukan_kategori`
  MODIFY `kategori_id` bigint NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT untuk tabel `pengeluaran_kategori`
--
ALTER TABLE `pengeluaran_kategori`
  MODIFY `kategori_id` bigint NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT untuk tabel `pengumuman`
--
ALTER TABLE `pengumuman`
  MODIFY `pengumuman_id` bigint NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `presensi`
--
ALTER TABLE `presensi`
  MODIFY `presensi_id` bigint NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT untuk tabel `roles`
--
ALTER TABLE `roles`
  MODIFY `role_id` bigint NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT untuk tabel `ruangan`
--
ALTER TABLE `ruangan`
  MODIFY `ruangan_id` bigint NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `santri`
--
ALTER TABLE `santri`
  MODIFY `santri_id` bigint NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT untuk tabel `staff`
--
ALTER TABLE `staff`
  MODIFY `staff_id` bigint NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT untuk tabel `tahun_pelajaran`
--
ALTER TABLE `tahun_pelajaran`
  MODIFY `tahun_id` bigint NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
