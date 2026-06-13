-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 13, 2026 at 09:38 AM
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
-- Database: `barangay_watch`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

DROP TABLE IF EXISTS `admins`;
CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'Active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `username`, `password`, `full_name`, `status`) VALUES
(1, 'admin_luis', 'lowest', 'Luis Miguel Asuncion', 'Inactive'),
(2, 'admin_steven', 'pogiako123', 'Steven Angelo Martinez', 'Inactive'),
(3, 'admin_dominic', 'pogipogi', 'Dominic Aquino', 'Inactive'),
(4, 'admin_julian', 'joshuag', 'Julian Magrata', 'Inactive'),
(5, 'admin_leo', 'mcdomcdo', 'Leo Almazar', 'Inactive');

-- --------------------------------------------------------

--
-- Table structure for table `audit_logs`
--

DROP TABLE IF EXISTS `audit_logs`;
CREATE TABLE `audit_logs` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `admin_id` int(11) DEFAULT NULL,
  `action` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_requests`
--

DROP TABLE IF EXISTS `password_reset_requests`;
CREATE TABLE `password_reset_requests` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `new_password` varchar(255) NOT NULL,
  `status` enum('Pending','Approved','Rejected') DEFAULT 'Pending',
  `requested_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `password_reset_requests`
--

INSERT INTO `password_reset_requests` (`id`, `user_id`, `username`, `new_password`, `status`, `requested_at`) VALUES
(1, 7, 'julianpogi', '$2y$10$itOZP.CKBdLcYqiyaKQpVeoBQKzWxzO41zRVyk5luK.0vPBSyyRz.', 'Approved', '2026-06-13 14:41:05'),
(2, 6, 'stevenpogi', '$2y$10$zkdBmMHAGtjpbCC7rkoQJ.56fyIbjrWxlPoZbGaD3RtYlbTr5RI4O', 'Rejected', '2026-06-13 14:49:08');

-- --------------------------------------------------------

--
-- Table structure for table `reports`
--

DROP TABLE IF EXISTS `reports`;
CREATE TABLE `reports` (
  `id` int(11) NOT NULL,
  `user` int(11) NOT NULL,
  `report_type` varchar(50) NOT NULL,
  `priority` varchar(20) DEFAULT NULL,
  `description` text NOT NULL,
  `location` varchar(255) NOT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'Pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reports`
--

INSERT INTO `reports` (`id`, `user`, `report_type`, `priority`, `description`, `location`, `status`, `created_at`) VALUES
(1, 5, 'Barangay Issue', 'Urgent', 'may bumagsak na puno sa kalsada', 'Gil Fernando St. Sa kanto po', 'Pending', '2026-06-12 10:23:02'),
(2, 5, 'Barangay Issue', 'Urgent', 'may bumagsak na puno sa kalsada', 'Gil Fernando St. Sa kanto po', 'Pending', '2026-06-12 10:41:21'),
(3, 5, 'Barangay Issue', 'Urgent', 'may bumagsak na puno sa kalsada', 'Gil Fernando St. Sa kanto po', 'Pending', '2026-06-12 10:41:26'),
(4, 6, 'Barangay Issue', 'High', 'Ewan ko ba, may butas sa sidewalk.', 'Sa may Dao St. Kanto sa may dali', 'Pending', '2026-06-12 10:44:12'),
(5, 6, 'Barangay Issue', 'Urgent', 'MAY NAHULOG NA PUSA SA CREEK', 'Barangka. Malapit sa rb', 'Pending', '2026-06-12 10:51:11'),
(6, 5, 'Kapitbahay', 'Medium', 'Subject: Julian Magrata | ANG INGAY INGAY NG BAHAY NILA', 'BAHAY TORO', 'In Progress', '2026-06-12 21:40:51'),
(7, 5, 'Official', 'Normal', 'Officials: Barangay Official: kagawad_2 | Complaint: misconduct | Incident Date: 2026-06-04 | Details: Nagtatanong lang ako tas minura ako amputa', '', 'Pending', '2026-06-12 21:42:02'),
(8, 5, 'Official', 'Normal', 'Officials: Tanod: tanod_2 | Complaint: abuse | Incident Date: 2026-06-06 | Details: NANANAKIT NG BATA. NAKAUPO LANG SA TINDAHAN TAS BINATUKAN', '', 'Under Review', '2026-06-12 21:43:23'),
(9, 5, 'Barangay Issue', 'Urgent', 'May pumutok na pipe sa ilalim ng kalsada!!', 'Sa lilac, malapit sa Kapitan Moy', 'Under Review', '2026-06-12 21:47:06'),
(10, 6, 'Official', 'Normal', 'Officials: SK Official: sk_kagawad_1 | Complaint: harassment | Incident Date: 2026-06-05 | Details: BASTOS. NANGHAHAWAK NG BURAT', '', 'Resolved', '2026-06-12 21:55:00'),
(11, 6, 'Official', 'Normal', 'Officials: SK Official: sk_kagawad_6 | Complaint: abuse | Incident Date: 2026-06-04 | Details: Akala mo siya may are ng kalsada', '', 'Under Review', '2026-06-12 22:06:46'),
(12, 7, 'Barangay Issue', 'Normal', 'JGKAJGHAUHGAGAGJAGFAFAF', 'Sa lilac, malapit sa Kapitan Moy', 'Resolved', '2026-06-12 23:42:05');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `name` varchar(100) NOT NULL,
  `address` varchar(255) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `occupation` varchar(100) DEFAULT NULL,
  `age` int(11) NOT NULL,
  `birthday` date DEFAULT NULL,
  `gender` varchar(20) NOT NULL,
  `civil_status` varchar(50) DEFAULT NULL,
  `verification_id` varchar(255) NOT NULL,
  `status` varchar(50) NOT NULL DEFAULT '',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_hidden` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `name`, `address`, `phone`, `email`, `occupation`, `age`, `birthday`, `gender`, `civil_status`, `verification_id`, `status`, `created_at`, `is_hidden`) VALUES
(5, 'luispogi', '$2y$10$SNGztsmAgMDK.NL8ThVTr.PRY4DnXUCtBAEvGzblAsliEzMEepjI6', 'Asuncion, Luis Miguel', '123 Katipunan St. Marikina Heights, Marikina City', '09276461983', 'angelomartinezsteven@gmail.com', 'Student', 17, '2008-07-26', 'male', 'single', 'uploads/verification_ids/verif_1781270932_708ac182.png', 'Verified', '2026-06-12 13:28:52', 0),
(6, 'stevenpogi', '$2y$10$szFVPwCdymhZ5B2dTSaVGekUX.zS2oWrQxXg2yqovYe.0x3oRek0u', 'Martinez, Steven Angelo G.', '123 Dao St., Marikina Heights', '09124781732', 'charlieangelieroan@gmail.com', 'Student', 20, '2005-08-19', 'male', 'single', 'uploads/verification_ids/verif_1781273802_c1b7b366.jpg', 'Verified', '2026-06-12 14:16:42', 0),
(7, 'julianpogi', '$2y$10$itOZP.CKBdLcYqiyaKQpVeoBQKzWxzO41zRVyk5luK.0vPBSyyRz.', 'BENiE, UMAG NOA', '123 Katipunan St. Marikina Heights, Marikina City', '09276461983', NULL, NULL, 22, NULL, 'male', NULL, 'uploads/verification_ids/verif_1781325502_3abdb97a.jpg', 'Verified', '2026-06-13 04:38:23', 0),
(8, 'BENBEN', '$2y$10$vGX8i5YoKgpg9goLPNTkaOdDYG.YZtzfMniDVduAJYE5Atyh/PLf.', 'James ,Ace Smith', '123 Dao St. ,Marikina Heights', '09124781732', NULL, NULL, 42, NULL, 'prefer_not', NULL, 'uploads/verification_ids/verif_1781325996_96772865.png', 'Not Verified', '2026-06-13 04:46:36', 0),
(9, 'HEYBAY', '$2y$10$y2Y6uUUd/dvET/Yvl2PXOOQtr8X7nY4owyVIVfxS4IOncSwpv5q1y', 'Heybay, Magkatil Bang H.', '834 Proj 6, Quezon City', '09218472442', NULL, NULL, 16, NULL, 'female', NULL, 'uploads/verification_ids/verif_1781335649_3d3a9c71.png', 'Verified', '2026-06-13 07:27:29', 0);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `admin_id` (`admin_id`);

--
-- Indexes for table `password_reset_requests`
--
ALTER TABLE `password_reset_requests`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `reports`
--
ALTER TABLE `reports`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `audit_logs`
--
ALTER TABLE `audit_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `password_reset_requests`
--
ALTER TABLE `password_reset_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `reports`
--
ALTER TABLE `reports`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD CONSTRAINT `audit_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `audit_logs_ibfk_2` FOREIGN KEY (`admin_id`) REFERENCES `admins` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `reports`
--
ALTER TABLE `reports`
  ADD CONSTRAINT `reports_ibfk_1` FOREIGN KEY (`user`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
