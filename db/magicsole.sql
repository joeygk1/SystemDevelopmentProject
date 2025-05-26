-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 25, 2025 at 03:46 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `magicsole`
--

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `booking_id` int(5) NOT NULL,
  `client_id` int(4) NOT NULL,
  `dropoff_date` datetime NOT NULL,
  `pickup_date` datetime DEFAULT NULL,
  `shoes_quantity` int(2) NOT NULL,
  `status` varchar(30) NOT NULL,
  `total_Price` decimal(5,0) NOT NULL,
  `name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`booking_id`, `client_id`, `dropoff_date`, `pickup_date`, `shoes_quantity`, `status`, `total_Price`, `name`) VALUES
(5, 5, '2025-05-13 10:00:00', NULL, 1, 'Pending', 0, ''),
(6, 5, '2025-05-17 13:00:00', NULL, 1, 'Pending', 100, ''),
(7, 1, '2025-05-31 18:00:00', NULL, 3, 'Completed', 130, 'Gordon'),
(8, 1, '2025-05-20 19:00:00', NULL, 2, 'Pending', 230, 'John Porc'),
(9, 1, '2025-05-20 18:00:00', NULL, 5, 'Pending', 100, 'Joh'),
(22, 7, '2025-05-20 02:00:00', NULL, 2, 'Completed', 230, 'Chris P Bacon'),
(25, 7, '2025-05-19 12:00:00', NULL, 1, 'Pending', 50, 'Joey Ayoub'),
(26, 1, '2025-05-21 19:00:00', NULL, 3, 'Pending', 130, 'Josh'),
(27, 7, '2025-05-22 01:00:00', NULL, 2, 'Pending', 50, 'Josh'),
(28, 7, '2025-05-25 06:00:00', NULL, 1, 'Pending', 70, 'Luca'),
(29, 7, '2025-05-22 05:00:00', NULL, 1, 'Pending', 80, 'Chris'),
(31, 7, '2025-05-26 05:00:00', NULL, 2, 'Completed', 80, 'Gordon'),
(32, 7, '2025-05-22 15:00:00', NULL, 1, 'Cancelled', 150, 'Cooper'),
(33, 7, '2025-05-29 18:00:00', NULL, 1, 'Completed', 130, 'Harry P'),
(34, 8, '2025-05-24 18:00:00', NULL, 2, 'Completed', 70, 'Gianny'),
(35, 7, '2025-05-22 13:00:00', NULL, 3, 'Completed', 150, 'Thomas'),
(36, 1, '2025-05-22 17:00:00', NULL, 4, 'Completed', 150, 'Thomas'),
(37, 7, '2025-05-24 10:00:00', NULL, 2, 'Pending', 150, 'Chris');

-- --------------------------------------------------------

--
-- Table structure for table `booking_service`
--

CREATE TABLE `booking_service` (
  `shoe_id` int(5) NOT NULL,
  `service_id` int(5) NOT NULL,
  `booking_id` int(5) NOT NULL,
  `shoe_name` varchar(64) NOT NULL,
  `shoe_image` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `booking_service`
--

INSERT INTO `booking_service` (`shoe_id`, `service_id`, `booking_id`, `shoe_name`, `shoe_image`) VALUES
(1, 1, 33, '', ''),
(2, 2, 33, '', ''),
(3, 1, 32, '', ''),
(4, 3, 32, '', ''),
(5, 4, 32, '', ''),
(6, 4, 31, '', ''),
(8, 1, 28, '', ''),
(9, 3, 28, '', ''),
(10, 1, 27, '', ''),
(11, 5, 35, '', ''),
(12, 6, 35, '', ''),
(13, 7, 35, '', ''),
(14, 1, 25, '', ''),
(15, 1, 22, '', ''),
(16, 2, 22, '', ''),
(17, 3, 22, '', ''),
(18, 4, 22, '', ''),
(19, 2, 9, '', ''),
(20, 3, 9, '', ''),
(21, 1, 7, '', ''),
(22, 4, 7, '', ''),
(23, 1, 26, '', ''),
(24, 2, 26, '', ''),
(33, 1, 36, '', ''),
(34, 3, 36, '', ''),
(35, 4, 36, '', ''),
(46, 2, 29, '', ''),
(47, 1, 37, '', ''),
(48, 2, 37, '', ''),
(49, 3, 37, '', '');

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `id` int(11) NOT NULL,
  `user_id` int(5) NOT NULL,
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `expires_at` datetime NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `password_resets`
--

INSERT INTO `password_resets` (`id`, `user_id`, `email`, `token`, `expires_at`, `created_at`) VALUES
(1, 0, 'joey.ayoubdisalvo@icloud.com', 'b8aad99939c0ad478d9bd5e0cedb0b562dd8f74a95d3234fb605afc518e7dc5a', '2025-05-02 18:18:27', '2025-05-02 15:18:27'),
(2, 0, 'joey.ayoubdisalvo@icloud.com', 'b61c86248d0275fd16766acd363fe4470da77bb83b5187ce0401321f2bd8d5b4', '2025-05-02 18:18:29', '2025-05-02 15:18:29'),
(4, 0, 'xxchris.yk71@gmail.com', '3f17cb4bd9f9f27651d0345f141670bb8c237fbc156c1aa175e69aae00e0265a', '2025-05-19 04:35:56', '2025-05-19 01:35:56'),
(5, 0, 'xxchris.yk71@gmail.com', 'afca43c4f28aa0d982de188a2ff842bc7409ccea5c41ae75bd02aa8d917327d6', '2025-05-19 04:35:57', '2025-05-19 01:35:57'),
(6, 0, 'xxchris.yk71@gmail.com', 'ada40983e6820fdc6dc02b32ca77058d030172e738c03a0bad150f228ddb549f', '2025-05-19 04:35:59', '2025-05-19 01:35:59'),
(7, 0, 'xxchris.yk71@gmail.com', 'dead5d360949526a1b0b6a1e0881d1ab1e00dbefbac4281044112918d00b4d8d', '2025-05-19 04:36:00', '2025-05-19 01:36:00'),
(8, 0, 'xxchris.yk71@gmail.com', '480d605ba2bc448f3bc7733f7836570a78ca540b8270b313a322346f72f756b7', '2025-05-19 04:36:02', '2025-05-19 01:36:02'),
(9, 0, 'xxchris.yk71@gmail.com', '1242ada31140a17c537421ea3f504ac524bee79c2d4d1e29e190b587b4af37ec', '2025-05-19 04:39:55', '2025-05-19 01:39:55'),
(11, 0, 'xxchris.yk71@gmail.com', 'f63414e282e126afb1e02826676b96be243616f5c2a023c6b149e0dea90a8676', '2025-05-19 04:50:17', '2025-05-19 01:50:17'),
(13, 0, 'xxchris.yk71@gmail.com', '4186a65a109f774e63f531c54ebdef35dde29446c6ffc689f8c51154c3f28f99', '2025-05-19 04:59:59', '2025-05-19 01:59:59'),
(16, 0, 'xxchris.yk71@gmail.com', '6954d85c0e959b5bfb2a5b37bc3e178772589300bf0469d99190d8c3da2e8fd7', '2025-05-19 05:13:32', '2025-05-19 02:13:32');

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `payment_id` int(5) NOT NULL,
  `booking_id` int(5) NOT NULL,
  `payment_method` varchar(25) NOT NULL,
  `is_payed` tinyint(1) NOT NULL DEFAULT 0,
  `total_price` double(10,2) NOT NULL,
  `payment_date` date NOT NULL,
  `deposit_amount` double(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`payment_id`, `booking_id`, `payment_method`, `is_payed`, `total_price`, `payment_date`, `deposit_amount`) VALUES
(1, 13, '', 0, 0.99, '0000-00-00', 0.00),
(2, 22, 'etransfer', 0, 230.00, '2025-05-16', 0.00),
(3, 23, 'cash', 0, 50.00, '2025-05-16', 0.00),
(4, 24, 'cash', 0, 130.00, '2025-05-16', 0.00),
(5, 25, 'cash', 0, 50.00, '2025-05-17', 0.00),
(6, 26, 'cash', 0, 130.00, '2025-05-19', 0.00),
(7, 27, 'etransfer', 0, 50.00, '2025-05-19', 0.00),
(8, 28, 'cash', 0, 70.00, '2025-05-19', 0.00),
(9, 29, 'etransfer', 0, 80.00, '2025-05-19', 0.00),
(10, 30, 'cash', 0, 50.00, '2025-05-19', 0.00),
(11, 31, 'cash', 0, 80.00, '2025-05-19', 0.00),
(12, 32, 'cash', 0, 150.00, '2025-05-19', 0.00),
(13, 33, 'cash', 0, 130.00, '2025-05-19', 0.00),
(14, 34, 'cash', 0, 70.00, '2025-05-19', 0.00),
(15, 35, 'cash', 0, 150.00, '2025-05-20', 0.00),
(16, 36, 'cash', 0, 150.00, '2025-05-20', 0.00),
(17, 37, 'etransfer', 0, 150.00, '2025-05-22', 0.00);

-- --------------------------------------------------------

--
-- Table structure for table `services`
--

CREATE TABLE `services` (
  `service_id` int(5) NOT NULL,
  `service_name` varchar(25) NOT NULL,
  `price` double(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `services`
--

INSERT INTO `services` (`service_id`, `service_name`, `price`) VALUES
(1, 'cleaning', 50.00),
(2, 'repaint', 80.00),
(3, 'icysole', 20.00),
(4, 'redye', 80.00),
(5, 'cleaning', 50.00),
(6, 'repaint', 80.00),
(7, 'icysole', 20.00),
(8, 'cleaning', 50.00),
(9, 'icysole', 20.00),
(10, 'cleaning', 50.00),
(11, 'repaint', 80.00),
(12, 'repaint', 80.00),
(13, 'repaint', 80.00),
(14, 'icysole', 20.00),
(15, 'redye', 80.00),
(16, 'cleaning', 50.00),
(17, 'repaint', 80.00),
(18, 'repaint', 80.00),
(19, 'icysole', 20.00);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `admin_id` int(5) NOT NULL,
  `username` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('client','admin') DEFAULT 'client',
  `verification_code` varchar(6) DEFAULT NULL,
  `otp` varchar(6) DEFAULT NULL,
  `otp_expiry` datetime DEFAULT NULL,
  `phone` varchar(15) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `admin_id`, `username`, `email`, `password`, `role`, `verification_code`, `otp`, `otp_expiry`, `phone`) VALUES
(1, 0, 'Joey', 'joeyayoubdisalvo@gmail.com', '$2y$10$sSke6AdEMyZEQ/w7yKHhYu56gead1mft2.U0iAi7/g9yL59Q9SCpG', 'client', NULL, '094147', '2025-05-15 19:00:57', '5146897641'),
(2, 0, 'Admin', 'joey.ayoubdisalvo@icloud.com', '$2y$10$GeYBdgM.BLA9rOVN/05j3.OEdkVGMHzQPFh2.mO95cmOpu4OoSKZC', 'admin', NULL, NULL, NULL, NULL),
(3, 0, 'Kishaan', 'kishaan2006@gmail.com', '$2y$10$w/JwTBREuJtTy.KCXPyqTuC1rCYhFGDa4P/kefab1d3Bek6jhHE8C', 'admin', NULL, NULL, NULL, NULL),
(4, 0, 'jaimejoe', 'billonesjaimejose@gmail.com', '$2y$10$zvjBpoYi1lqWPQ15tk1rPeJH8YwXDZ5l2w6IoW2oMUyXR.3rtIjcW', 'admin', NULL, NULL, NULL, NULL),
(5, 0, 'clientKishaan', 'weirdgrimreaper13@gmail.com', '$2y$10$JmWa3SLEe9.QP2LE1dRrweJkHbL8URZEsURaQpmEst4nu8R23w9XG', 'client', NULL, NULL, NULL, NULL),
(6, 1, 'testuser', 'test@example.com', '$2y$10$2xKzN8kZ5b2qYJ5z7vW2Z.4cP9zL3qT6uX9vW2kM5nP8qR3tY6uI.', 'client', NULL, NULL, NULL, NULL),
(7, 0, 'edp', 'xxchris.yk71@gmail.com', '$2y$10$5OeBDSix5HEzN9xEIZz/aud5WcA/eCgqf8nbKdD.cjHKbMUYkTMim', 'client', NULL, NULL, NULL, '5146499374'),
(8, 0, 'edp', '', '', 'client', NULL, NULL, NULL, '5146897641'),
(11, 0, 'llecoPower', 'llecopower@gmail.com', '$2y$10$z3X8mJ9Xz5Qz3z3z3z3z3uJ9Xz5Qz3z3z3z3z3z3z3z3z3z3z3z3', 'admin', NULL, NULL, NULL, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`booking_id`),
  ADD KEY `customer_id` (`client_id`);

--
-- Indexes for table `booking_service`
--
ALTER TABLE `booking_service`
  ADD PRIMARY KEY (`shoe_id`),
  ADD KEY `booking_id` (`booking_id`),
  ADD KEY `service_id` (`service_id`),
  ADD KEY `service_id_2` (`service_id`,`booking_id`) USING BTREE;

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_email` (`email`),
  ADD KEY `idx_token` (`token`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`payment_id`),
  ADD KEY `booking_id` (`booking_id`);

--
-- Indexes for table `services`
--
ALTER TABLE `services`
  ADD PRIMARY KEY (`service_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `booking_id` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- AUTO_INCREMENT for table `booking_service`
--
ALTER TABLE `booking_service`
  MODIFY `shoe_id` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=50;

--
-- AUTO_INCREMENT for table `password_resets`
--
ALTER TABLE `password_resets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `payment_id` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `services`
--
ALTER TABLE `services`
  MODIFY `service_id` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bookings`
--
ALTER TABLE `bookings`
  ADD CONSTRAINT `bookings_ibfk_1` FOREIGN KEY (`client_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `booking_service`
--
ALTER TABLE `booking_service`
  ADD CONSTRAINT `booking_service_ibfk_1` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`booking_id`),
  ADD CONSTRAINT `booking_service_ibfk_2` FOREIGN KEY (`service_id`) REFERENCES `services` (`service_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
