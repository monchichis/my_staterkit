-- phpMyAdmin SQL Dump
-- version 5.0.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Waktu pembuatan: 15 Jul 2022 pada 04.05
-- Versi server: 10.2.6-MariaDB-log
-- Versi PHP: 7.4.19

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_gusananta`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `mst_user`
--

CREATE TABLE `mst_user` (
  `id_user` int(11) NOT NULL,
  `nama` text NOT NULL,
  `nik` text NOT NULL,
  `email` text NOT NULL,
  `password` text NOT NULL,
  `level` text NOT NULL,
  `date_created` date NOT NULL,
  `image` text NOT NULL,
  `is_active` int(2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data untuk tabel `mst_user`
--

INSERT INTO `mst_user` (`id_user`, `nama`, `nik`, `email`, `password`, `level`, `date_created`, `image`, `is_active`) VALUES
(15, 'Wan Khodir', '0111844', 'admin@gmail.com', '$2y$10$VUEKgUN6JfiE.Ka7.yKXI.Arwpcvy2IP1JV.csFCQFMGYPfCbkayO', 'Admin', '2019-10-02', 'default3.png', 1),
(35, 'ngimcil1', '5555', 'ngimcil@gmail.com', '$2y$10$jKot/Kc/UjpdgDQfkGLFmemAzi9ghqjqV8HfXBz5N8MXuBXfCdnXW', 'Kadis', '2022-03-08', 'default.png', 1),
(37, 'budi', '57485748', 'budi@gmail.com', '$2y$10$6fFXJ7LJfIdYDw5aFa3IeOlEi8.q8prCmxnCGFY/07/5XWWUHfePi', 'User', '2022-04-23', 'default.png', 1),
(38, 'susilo', '384934839', 'susilo@gmail.com', '$2y$10$VYlxOEY7VyQDyVx3lyHTE.hgvWFhLhuKSlVd9ekeX2Yau9Lres.lO', 'User', '2022-04-23', 'default.png', 1),
(39, 'wakakakakak 555+', '12345678', 'wakaka@gmail.com', '$2y$10$VTS2fd4M/9sZK2haU0vqKuCtfWP.znXjpoPfEJ3JM8BWxpSQRREqy', 'User', '2022-07-15', 'default.png', 0);

-- --------------------------------------------------------

--
-- Struktur dari tabel `tbl_aplikasi`
--

CREATE TABLE `tbl_aplikasi` (
  `id` int(11) NOT NULL,
  `nama_aplikasi` varchar(250) NOT NULL,
  `alamat` varchar(250) NOT NULL,
  `telp` varchar(250) NOT NULL,
  `nama_developer` varchar(250) NOT NULL,
  `logo` varchar(250) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data untuk tabel `tbl_aplikasi`
--

INSERT INTO `tbl_aplikasi` (`id`, `nama_aplikasi`, `alamat`, `telp`, `nama_developer`, `logo`) VALUES
(1, 'Master Project CI 3', 'Jl. Planet Saturnus', '<*++++=<', 'Blegug Kasep Dev Team', 'Untitled1.png');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `mst_user`
--
ALTER TABLE `mst_user`
  ADD PRIMARY KEY (`id_user`);

--
-- Indeks untuk tabel `tbl_aplikasi`
--
ALTER TABLE `tbl_aplikasi`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `mst_user`
--
ALTER TABLE `mst_user`
  MODIFY `id_user` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- AUTO_INCREMENT untuk tabel `tbl_aplikasi`
--
ALTER TABLE `tbl_aplikasi`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
