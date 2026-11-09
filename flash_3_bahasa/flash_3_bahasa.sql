-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Waktu pembuatan: 09 Nov 2026 pada 12.43
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
-- Database: `flash_3_bahasa`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `words`
--

CREATE TABLE `words` (
  `id` int(11) NOT NULL,
  `indonesia` varchar(255) NOT NULL,
  `inggris` varchar(255) NOT NULL,
  `jawa` varchar(255) NOT NULL,
  `kategori` varchar(100) DEFAULT 'Umum',
  `contoh_kalimat` text DEFAULT NULL,
  `gambar_url` text DEFAULT NULL,
  `is_memorized` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `words`
--

INSERT INTO `words` (`id`, `indonesia`, `inggris`, `jawa`, `kategori`, `contoh_kalimat`, `gambar_url`, `is_memorized`, `created_at`, `updated_at`) VALUES
(2, 'Makan', 'Eat', 'Mangan', 'Kegiatan', NULL, '', 1, '2026-09-29 12:39:47', '2026-09-29 13:26:58'),
(4, 'ibu', 'mother', 'emak', 'umum', '', '', 1, '2026-09-29 13:29:19', '2026-09-29 13:29:28'),
(5, 'HP', 'HANDPHONE', 'hp terosssss', 'technologi', '', 'https://www.reddit.com/r/sonic/comments/1htwdae/what_is_the_difference_between_neo_metal_sonics/?tl=id', 0, '2026-10-06 11:40:03', '2026-10-06 12:03:52');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `words`
--
ALTER TABLE `words`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `words`
--
ALTER TABLE `words`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
