-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 11, 2025 at 03:35 AM
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
-- Database: `comshop`
--

-- --------------------------------------------------------

--
-- Table structure for table `hourly_rates`
--

CREATE TABLE `hourly_rates` (
  `id` int(11) NOT NULL,
  `rate` decimal(10,2) NOT NULL,
  `time` decimal(5,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `hourly_rates`
--

INSERT INTO `hourly_rates` (`id`, `rate`, `time`, `created_at`) VALUES
(13, 5.00, 30.00, '2025-05-10 22:41:38');

-- --------------------------------------------------------

--
-- Table structure for table `session_logs`
--

CREATE TABLE `session_logs` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `login_time` datetime DEFAULT NULL,
  `logout_time` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `session_logs`
--

INSERT INTO `session_logs` (`id`, `user_id`, `login_time`, `logout_time`) VALUES
(57, 3, '2025-05-11 06:38:02', '2025-05-11 06:42:46'),
(58, 6, '2025-05-11 06:38:32', '2025-05-11 06:42:00'),
(59, 3, '2025-05-11 06:43:53', NULL),
(60, 5, '2025-05-11 06:50:43', '2025-05-11 06:53:21'),
(63, 5, '2025-05-11 08:49:43', '2025-05-11 08:53:27'),
(64, 5, '2025-05-11 09:00:53', '2025-05-11 09:02:05'),
(65, 5, '2025-05-11 09:16:44', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `firstname` varchar(100) DEFAULT NULL,
  `lastname` varchar(50) NOT NULL,
  `contactnumber` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) DEFAULT NULL,
  `role` enum('admin','user') DEFAULT 'user',
  `reset_token` varchar(255) DEFAULT NULL,
  `reset_token_expiry` datetime DEFAULT NULL,
  `profile_picture` varchar(255) DEFAULT 'default.png'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `firstname`, `lastname`, `contactnumber`, `address`, `username`, `password`, `role`, `reset_token`, `reset_token_expiry`, `profile_picture`) VALUES
(3, 'Marlon  ', 'Villena', '09368808548', 'Purok 5 Brgy. Amsic', 'mjdivs08', '$2y$10$HzfQ4k2Ofe65ufZdv96H8OjcGMxXq6oqeCE71t/asc/HGnibQZYQm', 'admin', NULL, NULL, '1746924101_eye.jpg'),
(4, 'Gon', 'Freeks', '09123456789', 'Lele Tabe', 'gonfreeks1', '$2y$10$b4aF5OX46i7KldWwK4o2keoRH.nef1GCGjdBASkq5j8u7BD/tVyzC', 'user', NULL, NULL, 'default.png'),
(5, 'Kuya ', 'Wil', '09888888888', 'GMA', 'kuyawil', '$2y$10$XAmZEOiyVsnDyCUX8u2J6.2YGTOJxRd.Ba04EjA98of9C5VhizGA.', 'user', NULL, NULL, '1746924537_kuyawil.jpg'),
(6, 'Jema', 'Santillan', '09478079003', 'Purok 5 Brgy. Amsic', 'jemaganda', '$2y$10$12.fUvZJl7RnvtqqgxUCX.n1zzQtdwSbEvqxBzBvg20IxA2jXx1F6', 'user', NULL, NULL, 'default.png');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `hourly_rates`
--
ALTER TABLE `hourly_rates`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `session_logs`
--
ALTER TABLE `session_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `username_2` (`username`),
  ADD UNIQUE KEY `username_3` (`username`),
  ADD UNIQUE KEY `username_4` (`username`),
  ADD UNIQUE KEY `username_5` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `hourly_rates`
--
ALTER TABLE `hourly_rates`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `session_logs`
--
ALTER TABLE `session_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=66;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `session_logs`
--
ALTER TABLE `session_logs`
  ADD CONSTRAINT `session_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
