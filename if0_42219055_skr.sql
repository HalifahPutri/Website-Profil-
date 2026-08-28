-- phpMyAdmin SQL Dump
-- version 4.9.0.1
-- https://www.phpmyadmin.net/
--
-- Host: sql101.infinityfree.com
-- Waktu pembuatan: 22 Jun 2026 pada 06.44
-- Versi server: 11.4.12-MariaDB
-- Versi PHP: 7.2.22

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `if0_42219055_skr`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `admin`
--

CREATE TABLE `admin` (
  `id` int(11) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `foto` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `admin`
--

INSERT INTO `admin` (`id`, `nama`, `username`, `password`, `foto`, `created_at`) VALUES
(1, '', 'admin', '$2y$10$WoM7IT/9lpuxMIy.5i77burzh.AGvDE5DvZaxMnqYM1VovAZgVWY2', '', '2026-02-10 16:04:10');

-- --------------------------------------------------------

--
-- Struktur dari tabel `berita`
--

CREATE TABLE `berita` (
  `id_berita` int(11) NOT NULL,
  `judul` varchar(200) NOT NULL,
  `tanggal_kegiatan` date DEFAULT NULL,
  `lokasi` varchar(255) DEFAULT NULL,
  `isi` text NOT NULL,
  `id_kategori` int(11) NOT NULL,
  `gambar` varchar(255) DEFAULT NULL,
  `deskripsi` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `berita`
--

INSERT INTO `berita` (`id_berita`, `judul`, `tanggal_kegiatan`, `lokasi`, `isi`, `id_kategori`, `gambar`, `deskripsi`) VALUES
(7, 'Peringatan Hari Sumpah Pemuda', '2025-10-26', 'Gedung Pandhawa Rejosari', '', 4, '1779098023_sumpahpemuda.jpg', 'Peringatan hari sumpah peuda dan Donor Darah Rutin'),
(16, 'Lomba Anak-Anak ', '2026-08-02', 'Lapangan Gedung Pandhawa', '', 5, NULL, 'Lomba Anak-anak'),
(17, 'Lomba Ibu dan Bapak', '2026-08-09', 'Lapangan Gedung Pandhawa', '', 5, NULL, 'Lomba ibu dan Bapak'),
(18, 'Karnaval Dukuh Rejosari', '2026-08-23', 'Lapangan Gedung Pandhawa', '', 5, NULL, 'Karnaval'),
(19, 'Donor Darah Rutin', '2026-08-23', 'Gedung Pandhawa Rejosari', '', 5, NULL, 'Donor Darah'),
(22, 'Peringatan Hari Sumpah Pemuda', '2026-10-31', 'Lapangan Gedung Pandhawa', '', 5, NULL, 'Hari Sumpah Pemuda');

-- --------------------------------------------------------

--
-- Struktur dari tabel `chat`
--

CREATE TABLE `chat` (
  `id_chat` int(11) NOT NULL,
  `kode_chat` varchar(20) NOT NULL,
  `pengirim` enum('user','admin') NOT NULL,
  `pesan` text NOT NULL,
  `waktu` datetime NOT NULL,
  `dibaca` tinyint(1) NOT NULL,
  `tipe` enum('text','file') NOT NULL DEFAULT 'text',
  `status` enum('belum','sudah') DEFAULT 'belum'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `chat`
--

INSERT INTO `chat` (`id_chat`, `kode_chat`, `pengirim`, `pesan`, `waktu`, `dibaca`, `tipe`, `status`) VALUES
(1, 'USR1778347931', 'user', 'haloo', '2026-05-10 00:32:11', 1, 'text', 'sudah'),
(2, 'USR1778348146', 'user', 'halooo', '2026-05-10 00:35:46', 1, 'text', 'sudah'),
(3, 'USR1778347931', 'admin', 'trgyhjkh', '2026-05-11 16:34:28', 0, 'text', 'belum'),
(4, 'USR1778347931', 'admin', '💯', '2026-05-11 16:34:39', 0, 'text', 'belum'),
(5, 'USR1778347931', 'admin', 'haloo\r\n', '2026-06-22 00:38:18', 0, 'text', 'belum'),
(6, 'USR1782113941', 'user', 'owhshs', '2026-06-22 00:39:01', 1, 'text', 'sudah'),
(7, 'USR1782113941', 'admin', 'lapsh\r\n', '2026-06-22 00:39:17', 0, 'text', 'belum'),
(8, 'USR1782113941', 'admin', 'oweowiefj\r\n', '2026-06-22 00:47:50', 0, 'text', 'belum'),
(9, 'USR1782113941', 'admin', 'wehjrer\r\n', '2026-06-22 00:51:30', 0, 'text', 'belum'),
(10, 'USR1782113941', 'admin', 'iewyfgherjf\r\n', '2026-06-22 01:03:30', 0, 'text', 'belum'),
(11, 'USR1782113941', 'admin', 'shjferdf', '2026-06-22 01:08:34', 0, 'text', 'belum'),
(12, 'USR1782113941', 'admin', 'ekewhrgfrdf\r\n', '2026-06-22 01:21:08', 0, 'text', 'belum'),
(13, 'USR1782113941', 'admin', 'ahjhfer', '2026-06-22 01:40:08', 0, 'text', 'belum');

-- --------------------------------------------------------

--
-- Struktur dari tabel `galeri`
--

CREATE TABLE `galeri` (
  `id_galeri` int(11) NOT NULL,
  `judul` varchar(150) NOT NULL,
  `gambar` varchar(255) NOT NULL,
  `kategori` varchar(100) NOT NULL,
  `tanggal_upload` datetime NOT NULL,
  `link` varchar(255) DEFAULT NULL,
  `tanggal_kegiatan` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `galeri`
--

INSERT INTO `galeri` (`id_galeri`, `judul`, `gambar`, `kategori`, `tanggal_upload`, `link`, `tanggal_kegiatan`) VALUES
(3, 'Lomba Anak-Anak', '1778134073_lombaanak.jpeg', '', '2026-05-07 13:07:53', 'https://drive.google.com/drive/folders/1DsFy5LFzPE3B8oNNeC8IyRl3mj8JxjIn', '2024-08-04'),
(4, 'Lomba Ibu dan Bapak', '1778134216_bpkibu.jpg', '', '2026-05-07 13:10:16', 'https://drive.google.com/drive/folders/18CR2DKoLkQwZRCO1kEHL-AN2ghyegcp4', '2024-08-11'),
(5, 'Gerak Jalan Sehat Dukuh Rejosari', '1778134281_IMG_7413.jpg', '', '2026-05-07 13:11:21', 'https://drive.google.com/drive/folders/1-KKvSVIUbD4C1N-vp4w6sS8-nHAi-oYz', '2024-08-18'),
(6, 'Lomba Anak-Anak ', '1778134368_IMG_79562.jpg', '', '2026-05-07 13:12:48', 'https://drive.google.com/drive/folders/17DA6JACFFqAxOCwMe-roG300uHoyurmP', '2025-08-03'),
(7, 'Lomba Ibu-Ibu', '1778134427_IMG_8336.jpg', '', '2026-05-07 13:13:47', 'https://drive.google.com/drive/folders/1Nuj0RbgJuWmXdMe16b6xFbm9fesgQJSF', '2025-08-10'),
(8, 'Lomba Bapak-Bapak', '1778134482_IMG_8773.jpg', '', '2026-05-07 13:14:42', 'https://drive.google.com/drive/folders/1l670bKZDa9lS3RHgubdRTLjMjRnv6YSY', '2025-08-17'),
(9, 'Karnaval Dukuh Rejosari', '1778134542_IMG_8933.jpg', '', '2026-05-07 13:15:42', 'https://drive.google.com/drive/folders/1rgpC2ZNnotll8scBdNyyGoHeyw8_gnW3', '2025-08-24');

-- --------------------------------------------------------

--
-- Struktur dari tabel `kategori`
--

CREATE TABLE `kategori` (
  `id_kategori` int(11) NOT NULL,
  `nama_kategori` varchar(100) NOT NULL,
  `slug` varchar(120) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `kategori`
--

INSERT INTO `kategori` (`id_kategori`, `nama_kategori`, `slug`, `created_at`) VALUES
(4, 'Terlaksana', 'terlaksana', '2026-05-07 16:21:38'),
(5, 'Rencana', 'rencana', '2026-05-07 16:21:44');

-- --------------------------------------------------------

--
-- Struktur dari tabel `pengurus`
--

CREATE TABLE `pengurus` (
  `id_pengurus` int(11) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `jabatan` varchar(100) NOT NULL,
  `foto` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `pengurus`
--

INSERT INTO `pengurus` (`id_pengurus`, `nama`, `jabatan`, `foto`) VALUES
(2, 'M. Tegar Yanu S', 'Wakil Ketua', '1777982651_wakil.png'),
(3, 'Halifah Putri R', 'Sekretaris', '1781764095_ChatGPT Image 18 Jun 2026, 13.27.23.png'),
(4, 'Febri Cahyo P', 'Sekretaris', '1781764070_ChatGPT Image 18 Jun 2026, 13.27.16.png'),
(5, 'Linda Elsa ', 'Bendahara', '1777982685_bendahara1.png'),
(6, 'Eka Putri F', 'Bendahara', '1777982726_bendahara2.png'),
(8, 'Prayoga', 'Humas', '1777982751_humas1.png'),
(9, 'Wisnu Matara D', 'Ketua', '1777981317_ketua.png'),
(10, 'Ryan R', 'Humas', '1777982824_hms2.png'),
(11, 'Andreyan A', 'Humas', '1777982891_hms3.png');

-- --------------------------------------------------------

--
-- Struktur dari tabel `pesan`
--

CREATE TABLE `pesan` (
  `id_pesan` int(11) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `subjek` varchar(150) NOT NULL,
  `pesan` text NOT NULL,
  `tanggal` datetime NOT NULL,
  `status` enum('baru','dibalas') NOT NULL,
  `telepon` varchar(20) DEFAULT NULL,
  `topik` varchar(100) DEFAULT NULL,
  `kode_chat` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `pesan`
--

INSERT INTO `pesan` (`id_pesan`, `nama`, `email`, `subjek`, `pesan`, `tanggal`, `status`, `telepon`, `topik`, `kode_chat`) VALUES
(1, 'Halifah Putri Riyanto', 'halifahputri19@gmail.com', '', 'haloo', '2026-05-10 00:32:11', 'baru', '085876477615', 'Informasi', 'USR1778347931'),
(2, 'Halifah Putri Riyanto', 'halifahputri19@gmail.com', '', 'halooo', '2026-05-10 00:35:46', 'baru', '085876477615', 'Informasi', 'USR1778348146'),
(3, 'hanif', 'halifahputri19@gmail.com', '', 'owhshs', '2026-06-22 00:39:01', 'baru', '085876477615', 'Saran', 'USR1782113941');

-- --------------------------------------------------------

--
-- Struktur dari tabel `visitor`
--

CREATE TABLE `visitor` (
  `id` int(11) NOT NULL,
  `ip_addres` varchar(45) NOT NULL,
  `halaman` varchar(150) NOT NULL,
  `visit_time` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `visitor`
--

INSERT INTO `visitor` (`id`, `ip_addres`, `halaman`, `visit_time`) VALUES
(1, '::1', '/komunikasi/index.php', '2026-06-02 16:25:15'),
(2, '::1', '/komunikasi/index.php', '2026-06-02 16:25:31'),
(3, '::1', '/komunikasi/index.php', '2026-06-02 16:25:36'),
(4, '::1', '/komunikasi/index.php', '2026-06-02 16:51:41'),
(5, '::1', '/komunikasi/index.php', '2026-06-02 16:52:04'),
(6, '::1', '/komunikasi/index.php', '2026-06-02 16:52:54'),
(7, '::1', '/komunikasi/index.php', '2026-06-02 17:08:34'),
(8, '::1', '/komunikasi/index.php', '2026-06-02 17:08:51'),
(9, '::1', '/komunikasi/index.php', '2026-06-02 17:09:41'),
(10, '::1', '/komunikasi/index.php', '2026-06-02 17:09:56'),
(11, '::1', '/komunikasi/index.php', '2026-06-04 13:02:35'),
(12, '::1', '/komunikasi/index.php', '2026-06-04 16:08:06'),
(13, '::1', '/komunikasi/index.php', '2026-06-04 16:11:01'),
(14, '::1', '/komunikasi/index.php', '2026-06-04 16:11:11'),
(15, '::1', '/komunikasi/index.php', '2026-06-04 16:11:18'),
(16, '::1', '/komunikasi/index.php', '2026-06-04 16:11:26'),
(17, '::1', '/komunikasi/index.php', '2026-06-04 16:11:32'),
(18, '::1', '/komunikasi/index.php', '2026-06-04 16:11:34'),
(19, '::1', '/komunikasi/index.php', '2026-06-04 16:11:43'),
(20, '::1', '/komunikasi/index.php', '2026-06-04 16:12:03'),
(21, '::1', '/komunikasi/index.php', '2026-06-04 16:21:42'),
(22, '::1', '/komunikasi/index.php', '2026-06-04 16:33:25'),
(23, '::1', '/komunikasi/index.php', '2026-06-09 12:29:52'),
(24, '::1', '/komunikasi/index.php', '2026-06-09 15:01:19'),
(25, '::1', '/komunikasi/index.php', '2026-06-10 13:24:26'),
(26, '::1', '/komunikasi/index.php', '2026-06-10 13:36:40'),
(27, '::1', '/komunikasi/index.php', '2026-06-10 13:37:21'),
(28, '::1', '/komunikasi/index.php', '2026-06-10 14:44:40'),
(29, '::1', '/komunikasi/index.php', '2026-06-10 14:45:34'),
(30, '::1', '/komunikasi/index.php', '2026-06-10 14:52:49'),
(31, '::1', '/komunikasi/index.php', '2026-06-10 14:53:15'),
(32, '::1', '/komunikasi/index.php', '2026-06-10 14:53:29'),
(33, '::1', '/komunikasi/index.php', '2026-06-10 14:53:39'),
(34, '::1', '/komunikasi/index.php', '2026-06-10 14:53:51'),
(35, '::1', '/komunikasi/index.php', '2026-06-10 14:54:01'),
(36, '::1', '/komunikasi/index.php', '2026-06-10 14:55:27'),
(37, '::1', '/komunikasi/index.php', '2026-06-10 14:55:39'),
(38, '::1', '/komunikasi/index.php', '2026-06-10 14:59:03'),
(39, '::1', '/komunikasi/index.php', '2026-06-10 15:09:50'),
(40, '::1', '/komunikasi/index.php', '2026-06-10 15:13:33'),
(41, '::1', '/komunikasi/index.php', '2026-06-10 16:52:29'),
(42, '::1', '/komunikasi/index.php', '2026-06-10 16:52:43'),
(43, '::1', '/komunikasi/index.php', '2026-06-10 17:29:33'),
(44, '::1', '/komunikasi/index.php', '2026-06-10 17:31:04'),
(45, '::1', '/komunikasi/index.php', '2026-06-10 17:31:35'),
(46, '::1', '/komunikasi/index.php', '2026-06-11 13:50:54'),
(47, '::1', '/komunikasi/index.php', '2026-06-11 14:18:43'),
(48, '::1', '/komunikasi/index.php', '2026-06-11 14:24:46'),
(49, '::1', '/komunikasi/index.php', '2026-06-11 14:26:14'),
(50, '::1', '/komunikasi/index.php', '2026-06-11 14:26:19'),
(51, '::1', '/komunikasi/index.php', '2026-06-11 14:27:06'),
(52, '::1', '/komunikasi/index.php', '2026-06-11 14:27:30'),
(53, '::1', '/komunikasi/index.php', '2026-06-11 14:27:55'),
(54, '::1', '/komunikasi/index.php', '2026-06-11 14:28:59'),
(55, '::1', '/komunikasi/index.php', '2026-06-16 11:51:20'),
(56, '::1', '/komunikasi/index.php', '2026-06-16 11:52:55'),
(57, '::1', '/komunikasi/index.php', '2026-06-16 11:57:10'),
(58, '::1', '/komunikasi/index.php', '2026-06-16 11:58:48'),
(59, '::1', '/komunikasi/index.php', '2026-06-16 12:24:04'),
(60, '::1', '/komunikasi/index.php', '2026-06-16 12:24:10'),
(61, '::1', '/komunikasi/index.php', '2026-06-16 14:27:17'),
(62, '::1', '/komunikasi/index.php', '2026-06-16 14:47:40'),
(63, '::1', '/komunikasi/index.php', '2026-06-16 14:47:41'),
(64, '::1', '/komunikasi/index.php', '2026-06-18 13:15:37'),
(65, '::1', '/komunikasi/index.php', '2026-06-18 13:38:44'),
(66, '::1', '/komunikasi/index.php', '2026-06-18 13:44:12'),
(67, '::1', '/komunikasi/index.php', '2026-06-18 13:44:19'),
(68, '::1', '/komunikasi/index.php', '2026-06-18 13:45:18'),
(69, '::1', '/komunikasi/index.php', '2026-06-18 13:45:37'),
(70, '::1', '/komunikasi/index.php', '2026-06-18 13:45:46'),
(71, '::1', '/komunikasi/index.php', '2026-06-18 13:45:48'),
(72, '::1', '/komunikasi/index.php', '2026-06-18 13:46:08'),
(73, '::1', '/komunikasi/index.php', '2026-06-18 13:47:08'),
(74, '::1', '/komunikasi/index.php', '2026-06-18 13:48:35'),
(75, '::1', '/komunikasi/index.php', '2026-06-18 13:48:48'),
(76, '::1', '/komunikasi/index.php', '2026-06-18 13:48:56'),
(77, '::1', '/komunikasi/index.php', '2026-06-18 13:49:00'),
(78, '::1', '/komunikasi/index.php', '2026-06-18 13:49:09'),
(79, '::1', '/komunikasi/index.php', '2026-06-18 13:49:19'),
(80, '::1', '/komunikasi/index.php', '2026-06-18 13:50:13'),
(81, '114.141.56.230', '/?i=1', '2026-06-19 06:26:30'),
(82, '114.141.56.230', '/index.php', '2026-06-19 06:27:16'),
(83, '114.141.56.230', '/index.php', '2026-06-19 06:27:24'),
(84, '114.141.56.230', '/index.php', '2026-06-19 06:27:33'),
(85, '125.163.215.234', '/?i=1', '2026-06-19 07:23:23'),
(86, '125.163.215.234', '/index.php', '2026-06-19 07:27:13'),
(87, '125.163.215.234', '/index.php', '2026-06-19 07:34:49'),
(88, '125.163.215.234', '/index.php', '2026-06-19 07:55:34'),
(89, '125.166.1.65', '/?i=1', '2026-06-19 08:12:10'),
(90, '125.166.1.65', '/?i=1', '2026-06-19 08:12:22'),
(91, '125.163.215.234', '/', '2026-06-19 08:17:43'),
(92, '125.163.215.234', '/index.php', '2026-06-19 08:20:31'),
(93, '125.163.215.234', '/', '2026-06-19 08:21:06'),
(94, '125.166.1.65', '/', '2026-06-19 08:23:28'),
(95, '125.163.215.234', '/index.php', '2026-06-19 08:29:36'),
(96, '125.166.1.65', '/', '2026-06-19 08:30:21'),
(97, '125.166.1.65', '/index.php', '2026-06-19 08:31:03'),
(98, '125.163.215.234', '/', '2026-06-19 08:34:41'),
(99, '125.166.1.65', '/', '2026-06-19 08:34:48'),
(100, '125.163.215.234', '/index.php', '2026-06-19 09:27:09'),
(101, '125.163.215.234', '/index.php', '2026-06-19 09:33:21'),
(102, '125.163.215.234', '/index.php', '2026-06-19 09:43:12'),
(103, '52.16.245.145', '/?i=1', '2026-06-19 10:06:27'),
(104, '125.163.215.234', '/index.php', '2026-06-19 10:33:13'),
(105, '125.163.215.234', '/index.php', '2026-06-19 11:40:10'),
(106, '125.163.215.234', '/index.php', '2026-06-19 11:44:07'),
(107, '125.163.215.234', '/index.php', '2026-06-19 11:44:22'),
(108, '125.163.215.234', '/index.php', '2026-06-19 11:44:34'),
(109, '125.163.215.234', '/index.php', '2026-06-19 11:49:07'),
(110, '125.163.215.234', '/', '2026-06-19 12:12:59'),
(111, '125.163.215.234', '/', '2026-06-19 12:13:38'),
(112, '125.163.215.234', '/', '2026-06-19 12:13:51'),
(113, '125.163.215.234', '/index.php?i=1', '2026-06-19 12:15:37'),
(114, '125.163.215.234', '/index.php', '2026-06-19 12:35:54'),
(115, '125.163.215.234', '/index.php', '2026-06-19 12:37:39'),
(116, '125.163.215.234', '/index.php', '2026-06-19 12:40:43'),
(117, '125.166.1.65', '/', '2026-06-19 12:46:08'),
(118, '125.166.1.65', '/', '2026-06-19 12:48:29'),
(119, '125.163.215.234', '/index.php', '2026-06-19 12:49:41'),
(120, '125.163.215.234', '/index.php', '2026-06-19 12:51:14'),
(121, '125.163.215.234', '/', '2026-06-19 12:52:58'),
(122, '125.163.215.234', '/', '2026-06-19 13:13:28'),
(123, '125.163.215.234', '/', '2026-06-19 13:30:24'),
(124, '125.163.215.234', '/', '2026-06-19 13:38:46'),
(125, '125.163.215.234', '/', '2026-06-19 13:40:24'),
(126, '125.163.215.234', '/', '2026-06-19 13:44:10'),
(127, '125.163.215.234', '/', '2026-06-19 13:44:44'),
(128, '125.163.215.234', '/', '2026-06-19 13:50:37'),
(129, '125.163.215.234', '/?i=1', '2026-06-19 13:51:04'),
(130, '125.163.215.234', '/?i=1', '2026-06-19 13:51:32'),
(131, '125.163.215.234', '/index.php', '2026-06-19 13:54:00'),
(132, '125.166.1.65', '/', '2026-06-19 13:57:16'),
(133, '34.87.79.226', '/', '2026-06-19 18:10:16'),
(134, '54.211.153.168', '/?i=1', '2026-06-20 05:37:07'),
(135, '3.95.149.78', '/?i=1', '2026-06-20 05:37:16'),
(136, '17.246.15.202', '/?i=1', '2026-06-20 12:09:05'),
(137, '17.241.219.119', '/?i=1', '2026-06-20 12:20:24'),
(138, '17.241.75.189', '/?i=1', '2026-06-20 12:34:18'),
(139, '66.249.71.102', '/', '2026-06-21 10:38:35'),
(140, '192.178.6.9', '/', '2026-06-21 10:38:38'),
(141, '45.126.58.149', '/?i=1', '2026-06-21 12:55:35'),
(142, '125.166.150.216', '/?i=1', '2026-06-22 04:43:25'),
(143, '36.50.157.21', '/?i=1', '2026-06-22 04:43:30'),
(144, '125.166.150.216', '/', '2026-06-22 04:47:01'),
(145, '125.166.150.216', '/', '2026-06-22 04:50:59'),
(146, '125.166.150.216', '/', '2026-06-22 04:51:02'),
(147, '125.166.150.216', '/', '2026-06-22 04:51:03'),
(148, '125.166.150.216', '/', '2026-06-22 04:56:05'),
(149, '125.166.150.216', '/', '2026-06-22 04:56:06'),
(150, '125.166.150.216', '/index.php', '2026-06-22 04:57:42'),
(151, '125.166.150.216', '/index.php', '2026-06-22 04:57:44'),
(152, '125.166.150.216', '/index.php', '2026-06-22 05:04:01'),
(153, '125.166.150.216', '/index.php', '2026-06-22 05:04:02'),
(154, '125.166.12.187', '/?i=1', '2026-06-22 05:06:34'),
(155, '125.166.150.216', '/', '2026-06-22 05:12:28'),
(156, '116.12.47.210', '/?i=1', '2026-06-22 05:15:42'),
(157, '125.166.12.187', '/', '2026-06-22 05:16:36'),
(158, '125.166.12.187', '/', '2026-06-22 05:18:56'),
(159, '125.166.150.216', '/index.php', '2026-06-22 06:26:45'),
(160, '125.166.150.216', '/index.php', '2026-06-22 06:26:48'),
(161, '103.116.13.229', '/index.php?i=1', '2026-06-22 06:26:54'),
(162, '125.166.150.216', '/index.php', '2026-06-22 09:16:49'),
(163, '180.242.96.113', '/?i=1', '2026-06-22 09:19:50'),
(164, '180.242.96.113', '/', '2026-06-22 09:33:52');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `berita`
--
ALTER TABLE `berita`
  ADD PRIMARY KEY (`id_berita`),
  ADD KEY `fk_kategori_berita` (`id_kategori`);

--
-- Indeks untuk tabel `chat`
--
ALTER TABLE `chat`
  ADD PRIMARY KEY (`id_chat`);

--
-- Indeks untuk tabel `galeri`
--
ALTER TABLE `galeri`
  ADD PRIMARY KEY (`id_galeri`);

--
-- Indeks untuk tabel `kategori`
--
ALTER TABLE `kategori`
  ADD PRIMARY KEY (`id_kategori`);

--
-- Indeks untuk tabel `pengurus`
--
ALTER TABLE `pengurus`
  ADD PRIMARY KEY (`id_pengurus`);

--
-- Indeks untuk tabel `pesan`
--
ALTER TABLE `pesan`
  ADD PRIMARY KEY (`id_pesan`);

--
-- Indeks untuk tabel `visitor`
--
ALTER TABLE `visitor`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `berita`
--
ALTER TABLE `berita`
  MODIFY `id_berita` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT untuk tabel `chat`
--
ALTER TABLE `chat`
  MODIFY `id_chat` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT untuk tabel `galeri`
--
ALTER TABLE `galeri`
  MODIFY `id_galeri` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT untuk tabel `kategori`
--
ALTER TABLE `kategori`
  MODIFY `id_kategori` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT untuk tabel `pengurus`
--
ALTER TABLE `pengurus`
  MODIFY `id_pengurus` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT untuk tabel `pesan`
--
ALTER TABLE `pesan`
  MODIFY `id_pesan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT untuk tabel `visitor`
--
ALTER TABLE `visitor`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=165;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `berita`
--
ALTER TABLE `berita`
  ADD CONSTRAINT `fk_kategori_berita` FOREIGN KEY (`id_kategori`) REFERENCES `kategori` (`id_kategori`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
