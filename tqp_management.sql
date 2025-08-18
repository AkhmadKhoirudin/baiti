-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 17 Agu 2025 pada 12.22
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
-- Database: `tqp_management`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `guru`
--

CREATE TABLE `guru` (
  `id` int(11) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `mapel` varchar(50) NOT NULL,
  `no_hp` varchar(15) DEFAULT NULL,
  `alamat` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `kelas`
--

CREATE TABLE `kelas` (
  `id` int(11) NOT NULL,
  `nama_kelas` varchar(50) NOT NULL,
  `spp` int(11) NOT NULL,
  `wali_kelas` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `kelas`
--

INSERT INTO `kelas` (`id`, `nama_kelas`, `spp`, `wali_kelas`) VALUES
(7, ' TPQ A ALMUQOYYIM', 90000, NULL),
(9, ' TPQ B1 ALMUQOYYIM', 90000, NULL),
(10, 'TPQ B2 ALMUQOYYIM', 90000, NULL),
(11, 'TPQ C ALMUQOYYIM', 90000, NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `siswa`
--

CREATE TABLE `siswa` (
  `NIK` int(20) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `kelas` int(10) NOT NULL,
  `status` text DEFAULT NULL,
  `jenis_kelamin` enum('L','P') DEFAULT NULL,
  `alamat` text DEFAULT NULL,
  `no_hp` varchar(15) DEFAULT NULL,
  `nama_ayah` varchar(100) DEFAULT NULL,
  `nama_ibu` varchar(100) DEFAULT NULL,
  `pekerjaan_ayah` varchar(100) DEFAULT NULL,
  `pekerjaan_ibu` varchar(100) DEFAULT NULL,
  `tahun_masuk` year(4) DEFAULT NULL,
  `kelas_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `siswa`
--

INSERT INTO `siswa` (`NIK`, `nama`, `kelas`, `status`, `jenis_kelamin`, `alamat`, `no_hp`, `nama_ayah`, `nama_ibu`, `pekerjaan_ayah`, `pekerjaan_ibu`, `tahun_masuk`, `kelas_id`) VALUES
(3, 'auliyah', 7, 'Aktif', 'L', '', '', '', '', '', '', '2024', NULL),
(4, 'Bima', 7, 'Aktif', 'L', '', '', '', '', '', '', '2024', NULL),
(5, 'Cikal Rahmat', 7, 'aktif', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(6, 'Fidan Hidayatullah', 7, 'aktif', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(7, 'Fahri Ramadhan', 7, 'aktif', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(8, 'Fauziyah Melani', 7, 'aktif', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(9, 'M. Arka Ramadhan', 7, 'aktif', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(10, 'M. Azka Safaraz', 7, 'aktif', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(11, 'Mudzalifah Rizqiyah', 7, 'aktif', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(12, 'Moza Madina', 7, 'aktif', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(13, 'Rafa Fauzan', 7, 'aktif', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(14, 'Reva Januari', 7, 'aktif', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(15, 'Ibtisama Rajwa Karim', 7, 'aktif', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(16, 'M. Haidar Karim', 7, 'aktif', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(17, 'M. Rajib', 7, 'aktif', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(18, 'A. Naufal Bahauddin', 7, 'aktif', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(19, 'Tiya Ramadhani', 7, 'aktif', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(20, 'Kayla Nadhifa Almayra', 7, 'aktif', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(21, 'Atikah Mulyadi', 7, 'aktif', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(22, 'Ezhar Daffah Assabik', 7, 'aktif', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(23, 'Fahmi Alfatah', 7, 'aktif', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(24, 'M. Alki', 7, 'aktif', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(25, 'Nathan', 7, 'aktif', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(26, 'Alan', 7, 'aktif', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(27, 'Sekar', 7, 'aktif', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(28, 'Aralin', 7, 'aktif', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(29, 'Elena', 7, 'aktif', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(30, 'Kanaya', 7, 'aktif', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(31, 'Zahra', 7, 'aktif', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(32, 'Putra', 7, 'aktif', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(33, 'Rizki Ramadhan', 7, 'aktif', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(34, 'Kayla Chifa', 7, 'aktif', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(35, 'Alfiyan', 9, 'aktif', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(36, 'Alfarizki', 9, 'aktif', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(37, 'David Franky.M', 9, 'aktif', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(38, 'Ceisya Putri. R', 9, 'aktif', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(39, 'M. Ulil Absor.A', 9, 'aktif', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(40, 'M. Rezfan Syauqi', 9, 'aktif', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(41, 'Nabila Andriana', 9, 'aktif', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(42, 'Niki Apriliza A.', 9, 'aktif', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(43, 'Ningrum Sari', 9, 'aktif', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(44, 'Qinda Agustin', 9, 'aktif', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(45, 'Quanesya Nasywa', 9, 'aktif', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(46, 'Tito Aliuddin', 9, 'aktif', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(47, 'Yumna Malikulana.R.J', 9, 'aktif', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(48, 'Khodijah Adzakira', 9, 'aktif', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(49, 'Sodik Yunus', 9, 'aktif', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(50, 'Violla', 9, 'aktif', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(51, 'Arsen Yazen', 10, 'aktif', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(52, 'Aula Najlinda R', 10, 'aktif', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(53, 'Alya Qifara', 10, 'aktif', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(54, 'Afif Akhwafi S', 10, 'aktif', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(55, 'Dhea Azzahra S', 10, 'aktif', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(56, 'Fairuz Zahada A', 10, 'aktif', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(57, 'Kenan Putri Nellan', 10, 'aktif', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(58, 'Faylan', 10, 'aktif', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(59, 'Leela', 10, 'aktif', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(60, 'M. Fadlu', 10, 'aktif', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(61, 'M. Maulana Khalid', 10, 'aktif', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(62, 'Rahman Dzakira', 10, 'aktif', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(63, 'Rizqi Sugiono', 10, 'aktif', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(64, 'Qutbi Alnaf', 10, 'aktif', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(65, 'Lisanamil Arqani', 10, 'aktif', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(66, 'M. Fikri Hairin A', 10, 'aktif', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(67, 'M. Aftar Zainal. A', 11, 'aktif', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(68, 'M. Nur Fariin', 11, 'aktif', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(69, 'Ahmad Bilal Afqian', 11, 'aktif', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(70, 'Assyifa Nurita T', 11, 'aktif', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(71, 'Abdbar', 11, 'aktif', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(72, 'Daniel Aditya. A', 11, 'aktif', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(73, 'M. Hafiz Alfarizi', 11, 'aktif', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(74, 'Al Rayy Akbar', 11, 'aktif', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(75, 'Rasya Ahafiz', 11, 'aktif', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(76, 'Relin', 11, 'aktif', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(77, 'Saeful Arifin', 11, 'aktif', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(78, 'Putri Wulan', 11, 'aktif', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `spp`
--

CREATE TABLE `spp` (
  `id` int(11) NOT NULL,
  `siswa_nik` int(11) DEFAULT NULL,
  `tahun_ajaran` varchar(50) DEFAULT NULL,
  `biaya_spp` int(15) DEFAULT NULL,
  `date` date NOT NULL,
  `status` enum('pemasukan','pengeluaran') NOT NULL,
  `ket` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `spp`
--

INSERT INTO `spp` (`id`, `siswa_nik`, `tahun_ajaran`, `biaya_spp`, `date`, `status`, `ket`) VALUES
(2, 3, '2024', 90000, '0000-00-00', 'pemasukan', ''),
(3, 4, '2024', 90000, '0000-00-00', 'pemasukan', ''),
(4, 4, '2024', 90000, '2025-08-13', 'pemasukan', '');

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','operator') NOT NULL DEFAULT 'operator'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `guru`
--
ALTER TABLE `guru`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `kelas`
--
ALTER TABLE `kelas`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `siswa`
--
ALTER TABLE `siswa`
  ADD PRIMARY KEY (`NIK`),
  ADD KEY `kelas` (`kelas`),
  ADD KEY `kelas_id` (`kelas_id`);

--
-- Indeks untuk tabel `spp`
--
ALTER TABLE `spp`
  ADD PRIMARY KEY (`id`),
  ADD KEY `siswa_nik` (`siswa_nik`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `guru`
--
ALTER TABLE `guru`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `kelas`
--
ALTER TABLE `kelas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT untuk tabel `siswa`
--
ALTER TABLE `siswa`
  MODIFY `NIK` int(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=124;

--
-- AUTO_INCREMENT untuk tabel `spp`
--
ALTER TABLE `spp`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `siswa`
--
ALTER TABLE `siswa`
  ADD CONSTRAINT `fk_siswa_kelas` FOREIGN KEY (`kelas_id`) REFERENCES `kelas` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `siswa_ibfk_1` FOREIGN KEY (`kelas_id`) REFERENCES `kelas` (`id`);

--
-- Ketidakleluasaan untuk tabel `spp`
--
ALTER TABLE `spp`
  ADD CONSTRAINT `spp_ibfk_1` FOREIGN KEY (`siswa_nik`) REFERENCES `siswa` (`NIK`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
