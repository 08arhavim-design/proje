-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Anamakine: localhost:3306
-- Üretim Zamanı: 14 Şub 2026, 09:09:06
-- Sunucu sürümü: 10.3.39-MariaDB
-- PHP Sürümü: 8.4.16

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Veritabanı: `sporcu`
--

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `arenas`
--

CREATE TABLE `arenas` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(150) NOT NULL,
  `city` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Tablo döküm verisi `arenas`
--

INSERT INTO `arenas` (`id`, `name`, `city`) VALUES
(1, 'SARIGÖL', 'YUSUFELİ'),
(2, 'MELO', 'ARTVİN'),
(3, 'DEREKAPI', 'YUSUFELİ');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `bulls`
--

CREATE TABLE `bulls` (
  `id` int(10) UNSIGNED NOT NULL,
  `uid` int(11) DEFAULT NULL,
  `name` varchar(100) NOT NULL,
  `breed` varchar(100) DEFAULT NULL,
  `age` int(11) NOT NULL DEFAULT 0,
  `weight` varchar(50) DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `wins_text` text DEFAULT NULL,
  `mother` varchar(100) DEFAULT NULL,
  `father` varchar(100) DEFAULT NULL,
  `last_arena_text` varchar(150) DEFAULT NULL,
  `last_opponent_text` varchar(150) DEFAULT NULL,
  `owner_name` varchar(150) DEFAULT NULL,
  `category_id` int(10) UNSIGNED DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `created_by` int(10) UNSIGNED NOT NULL,
  `status` enum('pending','approved') NOT NULL DEFAULT 'pending',
  `reject_reason` text DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `won_titles` text DEFAULT NULL,
  `mother_name` varchar(150) DEFAULT NULL,
  `father_name` varchar(150) DEFAULT NULL,
  `last_arena` varchar(150) DEFAULT NULL,
  `last_opponent` varchar(150) DEFAULT NULL,
  `last_category` varchar(100) DEFAULT NULL,
  `photo_path` varchar(255) DEFAULT NULL,
  `owner_user_id` int(11) DEFAULT NULL,
  `original_owner` varchar(255) DEFAULT NULL,
  `district` varchar(100) DEFAULT NULL,
  `arenas` text DEFAULT NULL,
  `championships` text DEFAULT NULL,
  `price` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Tablo döküm verisi `bulls`
--

INSERT INTO `bulls` (`id`, `uid`, `name`, `breed`, `age`, `weight`, `city`, `wins_text`, `mother`, `father`, `last_arena_text`, `last_opponent_text`, `owner_name`, `category_id`, `image`, `created_by`, `status`, `reject_reason`, `created_at`, `won_titles`, `mother_name`, `father_name`, `last_arena`, `last_opponent`, `last_category`, `photo_path`, `owner_user_id`, `original_owner`, `district`, `arenas`, `championships`, `price`) VALUES
(10, 2, 'PİLOT', 'TOSUN', 3, '670', 'ARTVİN', NULL, NULL, NULL, NULL, NULL, 'FATİH İSPİRLİ', NULL, '1770662690_698a2b220864a2.98337489.jpg', 0, 'approved', NULL, '2026-02-08 22:23:22', NULL, 'ARKA', 'PİİKE', NULL, NULL, NULL, NULL, NULL, 'CEM TOSUN', 'MERKEZ', 'YUSUFELİ KAFKAFR MELO', 'YUSUFELİ KAFKAFR MELO  BİRİNİCİLİĞİ', '530000'),
(11, 7, 'ÇİLEKEŞ', 'SİMENTAL', 3, '574', 'ARTVİN', NULL, NULL, NULL, NULL, NULL, 'OSMAN EMRE', NULL, '1770704220_698acd5c5822a8.73318505.jpg', 0, 'approved', NULL, '2026-02-10 08:49:46', NULL, 'NADİGE', 'CESUR', NULL, NULL, NULL, NULL, NULL, 'FATİH CEM', 'YUSUFELİ', 'KAFKASÖR 2021  DEREKAPI 2018  SARIGÖL 2025', 'SARIGÖL ŞAMPİYONU', '15000'),
(12, 7, 'ahmet', 'tosun', 2, '444', 'muğla', NULL, NULL, NULL, NULL, NULL, 'cevdet', NULL, '1770968208_698ed490f0369.jpg', 0, 'approved', NULL, '2026-02-13 10:36:29', NULL, 'harman', 'civam', NULL, NULL, NULL, NULL, NULL, 'adem', 'milas', 'muğla birincisi', 'muğla birinsi', '500000'),
(13, 6, 'karabela', 'simental', 2, '333', 'artvin', NULL, NULL, NULL, NULL, NULL, 'adem gungor', NULL, '1770970407_698edd27193005.22024117.jpg', 6, 'approved', NULL, '2026-02-13 08:13:27', NULL, 'manısa', 'battal', NULL, NULL, NULL, '/boga/uploads/bulls/1770970407_698edd27193005.22024117.jpg', 6, 'fatma kesm', 'hopa', 'kafkasör', 'melo birincisi', '550000'),
(14, 2, 'PİLOT', 'TOSUN', 3, '670', 'ARTVİN', NULL, NULL, NULL, NULL, NULL, 'FATİH İSPİRLİ', NULL, '1770662690_698a2b220864a2.98337489.jpg', 0, 'approved', NULL, '2026-02-08 22:23:22', NULL, 'ARKA', 'PİİKE', NULL, NULL, NULL, NULL, NULL, 'CEM TOSUN', 'MERKEZ', 'YUSUFELİ KAFKAFR MELO', 'YUSUFELİ KAFKAFR MELO  BİRİNİCİLİĞİ', '530000'),
(15, 2, 'PİLOT', 'TOSUN', 3, '670', 'ARTVİN', NULL, NULL, NULL, NULL, NULL, 'FATİH İSPİRLİ', NULL, '1770662690_698a2b220864a2.98337489.jpg', 0, 'approved', NULL, '2026-02-08 22:23:22', NULL, 'ARKA', 'PİİKE', NULL, NULL, NULL, NULL, NULL, 'CEM TOSUN', 'MERKEZ', 'YUSUFELİ KAFKAFR MELO', 'YUSUFELİ KAFKAFR MELO  BİRİNİCİLİĞİ', '530000'),
(16, 7, 'ahmet', 'tosun', 2, '444', 'muğla', NULL, NULL, NULL, NULL, NULL, 'cevdet', NULL, '1770968208_698ed490f0369.jpg', 0, 'approved', NULL, '2026-02-13 10:36:29', NULL, 'harman', 'civam', NULL, NULL, NULL, NULL, NULL, 'adem', 'milas', 'muğla birincisi', 'muğla birinsi', '500000'),
(17, 6, 'karabela', 'simental', 2, '333', 'artvin', NULL, NULL, NULL, NULL, NULL, 'adem gungor', NULL, '1770970407_698edd27193005.22024117.jpg', 6, 'approved', NULL, '2026-02-13 08:13:27', NULL, 'manısa', 'battal', NULL, NULL, NULL, '/boga/uploads/bulls/1770970407_698edd27193005.22024117.jpg', 6, 'fatma kesm', 'hopa', 'kafkasör', 'melo birincisi', '550000'),
(18, 2, 'PİLOT', 'TOSUN', 3, '670', 'ARTVİN', NULL, NULL, NULL, NULL, NULL, 'FATİH İSPİRLİ', NULL, '1770662690_698a2b220864a2.98337489.jpg', 0, 'approved', NULL, '2026-02-08 22:23:22', NULL, 'ARKA', 'PİİKE', NULL, NULL, NULL, NULL, NULL, 'CEM TOSUN', 'MERKEZ', 'YUSUFELİ KAFKAFR MELO', 'YUSUFELİ KAFKAFR MELO  BİRİNİCİLİĞİ', '530000');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `bull_favorites`
--

CREATE TABLE `bull_favorites` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `bull_id` int(10) UNSIGNED NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `bull_matches`
--

CREATE TABLE `bull_matches` (
  `id` int(10) UNSIGNED NOT NULL,
  `bull_id` int(10) UNSIGNED NOT NULL,
  `arena_id` int(10) UNSIGNED NOT NULL,
  `match_date` date NOT NULL,
  `opponent_text` varchar(150) DEFAULT NULL,
  `result_text` varchar(80) DEFAULT NULL,
  `created_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `bull_views`
--

CREATE TABLE `bull_views` (
  `bull_id` int(10) UNSIGNED NOT NULL,
  `views` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Tablo döküm verisi `bull_views`
--

INSERT INTO `bull_views` (`bull_id`, `views`, `updated_at`) VALUES
(10, 38, '2026-02-13 11:48:14'),
(11, 182, '2026-02-14 06:00:11'),
(12, 4, '2026-02-13 18:57:52'),
(13, 16, '2026-02-13 16:34:14'),
(15, 2, '2026-02-13 11:42:21'),
(16, 2, '2026-02-13 11:29:41'),
(17, 10, '2026-02-13 18:32:23'),
(18, 15, '2026-02-13 20:22:30');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `categories`
--

CREATE TABLE `categories` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Tablo döküm verisi `categories`
--

INSERT INTO `categories` (`id`, `name`) VALUES
(3, 'Ayak'),
(1, 'Baş'),
(2, 'Orta');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `events`
--

CREATE TABLE `events` (
  `id` int(10) UNSIGNED NOT NULL,
  `festival` varchar(120) DEFAULT NULL,
  `title` varchar(160) NOT NULL,
  `city` varchar(60) DEFAULT NULL,
  `district` varchar(60) DEFAULT NULL,
  `location` varchar(160) DEFAULT NULL,
  `event_date` date NOT NULL,
  `start_time` time DEFAULT NULL,
  `categories` text DEFAULT NULL,
  `has_award` tinyint(1) NOT NULL DEFAULT 0,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Tablo döküm verisi `events`
--

INSERT INTO `events` (`id`, `festival`, `title`, `city`, `district`, `location`, `event_date`, `start_time`, `categories`, `has_award`, `notes`, `created_at`, `updated_at`) VALUES
(1, 'Boğa güreşleri', 'sarıgöl boğa güreşleri', 'artvin', 'yusufeli', 'sarıgöl arena', '2026-02-02', '13:00:00', 'ayak kateogiris baş ödülleri', 1, 'ayak kateogiris baş ödülleri', '2026-02-08 20:46:52', NULL),
(2, NULL, 'kafkasdör', NULL, NULL, 'kafkasör güreşleri', '2026-02-25', '15:25:00', NULL, 0, 'asdasdasdasdasdsadasda', '2026-02-10 06:10:30', NULL);

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `favorites`
--

CREATE TABLE `favorites` (
  `user_id` int(10) UNSIGNED NOT NULL,
  `bull_id` int(10) UNSIGNED NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Tablo döküm verisi `favorites`
--

INSERT INTO `favorites` (`user_id`, `bull_id`, `created_at`) VALUES
(7, 11, '2026-02-13 08:41:41');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `friends`
--

CREATE TABLE `friends` (
  `id` int(11) NOT NULL,
  `requester_id` int(11) NOT NULL,
  `addressee_id` int(11) NOT NULL,
  `status` enum('pending','accepted') NOT NULL DEFAULT 'pending',
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `messages`
--

CREATE TABLE `messages` (
  `id` int(11) NOT NULL,
  `sender_id` int(11) NOT NULL,
  `receiver_id` int(11) NOT NULL,
  `body` text NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `is_read` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Tablo döküm verisi `messages`
--

INSERT INTO `messages` (`id`, `sender_id`, `receiver_id`, `body`, `created_at`, `is_read`) VALUES
(1, 7, 10, 'selam', '2026-02-13 20:31:08', 1),
(2, 10, 7, 'dfs', '2026-02-13 23:12:54', 1),
(3, 10, 7, 'sdfsd', '2026-02-13 23:12:57', 1),
(4, 7, 10, 'sdfsdfsd', '2026-02-13 23:15:02', 1),
(5, 7, 10, 'selam', '2026-02-13 23:22:03', 1),
(6, 10, 7, 'selam', '2026-02-14 09:01:46', 1),
(7, 10, 7, 'selam', '2026-02-14 09:01:46', 1),
(8, 10, 7, 'asdsa', '2026-02-14 09:01:57', 1),
(9, 7, 10, 'asdfsdfa', '2026-02-14 09:03:06', 1),
(10, 7, 10, 'asdfsdfa', '2026-02-14 09:03:52', 1),
(11, 7, 10, 'sdfsdf', '2026-02-14 09:03:54', 1),
(12, 7, 10, 'selam', '2026-02-14 09:04:12', 1);

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `otp_codes`
--

CREATE TABLE `otp_codes` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `code_hash` varchar(255) NOT NULL,
  `expires_at` datetime NOT NULL,
  `attempts` int(11) NOT NULL DEFAULT 0,
  `last_sent_at` datetime DEFAULT NULL,
  `verified_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Tablo döküm verisi `otp_codes`
--

INSERT INTO `otp_codes` (`id`, `user_id`, `code_hash`, `expires_at`, `attempts`, `last_sent_at`, `verified_at`, `created_at`) VALUES
(2, 1, '$2y$10$paRlIVsXgdZ3oBgt66.89O6I4vUeURwftXKwYXiIlKJkb3kupAXGe', '2026-01-31 21:06:05', 0, '2026-01-31 21:03:05', '2026-01-31 21:03:18', '2026-01-31 21:03:05');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `rate_limits`
--

CREATE TABLE `rate_limits` (
  `key` varchar(190) NOT NULL,
  `attempts` int(11) NOT NULL DEFAULT 0,
  `first_ts` int(11) NOT NULL,
  `last_ts` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Tablo döküm verisi `rate_limits`
--

INSERT INTO `rate_limits` (`key`, `attempts`, `first_ts`, `last_ts`) VALUES
('login:905324643708', 1, 1769882585, 1769882585),
('otp:905324643708', 1, 1769882598, 1769882598);

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `users`
--

CREATE TABLE `users` (
  `id` int(10) UNSIGNED NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `role` enum('user','admin','superadmin') NOT NULL DEFAULT 'user',
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL,
  `full_name` varchar(150) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Tablo döküm verisi `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `phone`, `role`, `is_active`, `created_at`, `full_name`) VALUES
(6, 'superadmin', '$2y$10$LQCVImhkRX3sNGmRtBZfOe611lok2rovyNWrjFS4m69YCZU7bR542', '905324643708', 'superadmin', 1, '0000-00-00 00:00:00', NULL),
(7, 'arzu', '$2y$10$LQCVImhkRX3sNGmRtBZfOe611lok2rovyNWrjFS4m69YCZU7bR542', '905324643708', 'user', 1, '0000-00-00 00:00:00', NULL),
(8, 'mesmes', '$2y$10$8b0HHIYAJtSe2BpkGShkW.jUzv.a4cfPpJ9TF/pP9BCqTubCXWMgi', '905426260303', 'user', 1, '0000-00-00 00:00:00', NULL),
(9, 'Kadirkaratas08@gmail.com', '$2y$10$9SVZBoWJPNV/a2tUGIJGSeOLQjOaN/jPECb7AsdlHuFzRyhVaZyiW', '905316570208', 'admin', 1, '0000-00-00 00:00:00', NULL),
(10, 'arzu2', '$2y$10$LQCVImhkRX3sNGmRtBZfOe611lok2rovyNWrjFS4m69YCZU7bR542', '905324643708', 'user', 1, '0000-00-00 00:00:00', NULL);

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `user_presence`
--

CREATE TABLE `user_presence` (
  `user_id` int(11) NOT NULL,
  `last_seen` datetime NOT NULL,
  `ip` varchar(64) DEFAULT NULL,
  `user_agent` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Tablo döküm verisi `user_presence`
--

INSERT INTO `user_presence` (`user_id`, `last_seen`, `ip`, `user_agent`) VALUES
(6, '2026-02-13 20:21:53', '95.5.118.122', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36 Edg/144.0.0.0'),
(7, '2026-02-14 09:08:47', '109.228.250.245', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36 Edg/144.0.0.0'),
(10, '2026-02-14 09:08:52', '109.228.250.245', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36 Edg/144.0.0.0');

--
-- Dökümü yapılmış tablolar için indeksler
--

--
-- Tablo için indeksler `arenas`
--
ALTER TABLE `arenas`
  ADD PRIMARY KEY (`id`);

--
-- Tablo için indeksler `bulls`
--
ALTER TABLE `bulls`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_bulls_owner_user_id` (`owner_user_id`);

--
-- Tablo için indeksler `bull_favorites`
--
ALTER TABLE `bull_favorites`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_user_bull` (`user_id`,`bull_id`),
  ADD KEY `idx_user` (`user_id`),
  ADD KEY `idx_bull` (`bull_id`);

--
-- Tablo için indeksler `bull_matches`
--
ALTER TABLE `bull_matches`
  ADD PRIMARY KEY (`id`);

--
-- Tablo için indeksler `bull_views`
--
ALTER TABLE `bull_views`
  ADD PRIMARY KEY (`bull_id`);

--
-- Tablo için indeksler `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Tablo için indeksler `events`
--
ALTER TABLE `events`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_event_date` (`event_date`),
  ADD KEY `idx_festival` (`festival`);

--
-- Tablo için indeksler `favorites`
--
ALTER TABLE `favorites`
  ADD PRIMARY KEY (`user_id`,`bull_id`),
  ADD KEY `idx_bull` (`bull_id`);

--
-- Tablo için indeksler `friends`
--
ALTER TABLE `friends`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_pair` (`requester_id`,`addressee_id`),
  ADD KEY `addressee_id` (`addressee_id`,`status`),
  ADD KEY `requester_id` (`requester_id`,`status`);

--
-- Tablo için indeksler `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `receiver_id` (`receiver_id`,`created_at`),
  ADD KEY `sender_id` (`sender_id`,`created_at`);

--
-- Tablo için indeksler `otp_codes`
--
ALTER TABLE `otp_codes`
  ADD PRIMARY KEY (`id`);

--
-- Tablo için indeksler `rate_limits`
--
ALTER TABLE `rate_limits`
  ADD PRIMARY KEY (`key`);

--
-- Tablo için indeksler `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Tablo için indeksler `user_presence`
--
ALTER TABLE `user_presence`
  ADD PRIMARY KEY (`user_id`),
  ADD KEY `last_seen` (`last_seen`);

--
-- Dökümü yapılmış tablolar için AUTO_INCREMENT değeri
--

--
-- Tablo için AUTO_INCREMENT değeri `arenas`
--
ALTER TABLE `arenas`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Tablo için AUTO_INCREMENT değeri `bulls`
--
ALTER TABLE `bulls`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- Tablo için AUTO_INCREMENT değeri `bull_favorites`
--
ALTER TABLE `bull_favorites`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;

--
-- Tablo için AUTO_INCREMENT değeri `bull_matches`
--
ALTER TABLE `bull_matches`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Tablo için AUTO_INCREMENT değeri `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Tablo için AUTO_INCREMENT değeri `events`
--
ALTER TABLE `events`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Tablo için AUTO_INCREMENT değeri `friends`
--
ALTER TABLE `friends`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Tablo için AUTO_INCREMENT değeri `messages`
--
ALTER TABLE `messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- Tablo için AUTO_INCREMENT değeri `otp_codes`
--
ALTER TABLE `otp_codes`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Tablo için AUTO_INCREMENT değeri `users`
--
ALTER TABLE `users`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
