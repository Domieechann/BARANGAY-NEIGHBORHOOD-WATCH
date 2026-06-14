-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 14, 2026 at 10:31 AM
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
(1, 'admin_luis', 'lowest', 'Luis Miguel Asuncion', 'Active'),
(2, 'admin_steven', 'pogiako123', 'Steven Angelo Martinez', 'Inactive'),
(3, 'admin_dominic', 'pogipogi', 'Dominic Aquino', 'Active'),
(4, 'admin_julian', 'joshuag', 'Julian Magrata', 'Inactive'),
(5, 'admin_leo', 'mcdomcdo', 'Leo Almazar', 'Active');

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
  `approved_by` int(11) DEFAULT NULL,
  `requested_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `password_reset_requests`
--

INSERT INTO `password_reset_requests` (`id`, `user_id`, `username`, `new_password`, `status`, `approved_by`, `requested_at`) VALUES
(9, 5, 'HEYBAY', '$2y$10$111Q/bK/UDePeribEI9BCu2JQkCWVxZP4fbd4Im2gqXjgXLyTDtJu', 'Approved', 1, '2026-06-14 15:49:42');

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
  `handled_by` int(11) DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reports`
--

INSERT INTO `reports` (`id`, `user`, `report_type`, `priority`, `description`, `location`, `status`, `handled_by`, `updated_at`, `created_at`) VALUES
(1, 1, 'Barangay Issue', 'High', 'Ewan ko ba, may butas sa sidewalk.', 'Sa may Dao St. Kanto sa may dali', 'Under Review', 5, '2026-06-14 16:29:24', '2026-06-12 10:44:12'),
(2, 5, 'Barangay Issue', 'Urgent', 'MAY NAHULOG NA PUSA SA CREEK', 'Barangka. Malapit sa rb', 'Under Review', 5, '2026-06-14 16:30:24', '2026-06-12 10:51:11'),
(3, 5, 'Kapitbahay', 'Medium', 'Subject: Julian Magrata | ANG INGAY INGAY NG BAHAY NILA', 'BAHAY TORO', 'In Progress', 5, '2026-06-14 16:29:39', '2026-06-12 21:40:51'),
(4, 5, 'Official', 'Normal', 'Officials: Barangay Official: kagawad_2 | Complaint: misconduct | Incident Date: 2026-06-04 | Details: Nagtatanong lang ako tas minura ako amputa', '', 'In Progress', 5, '2026-06-14 16:29:50', '2026-06-12 21:42:02'),
(5, 5, 'Official', 'Normal', 'Officials: Tanod: tanod_2 | Complaint: abuse | Incident Date: 2026-06-06 | Details: NANANAKIT NG BATA. NAKAUPO LANG SA TINDAHAN TAS BINATUKAN', '', 'In Progress', 5, '2026-06-14 16:29:55', '2026-06-12 21:43:23'),
(6, 5, 'Barangay Issue', 'Urgent', 'May pumutok na pipe sa ilalim ng kalsada!!', 'Sa lilac, malapit sa Kapitan Moy', 'In Progress', 5, '2026-06-14 16:29:16', '2026-06-12 21:47:06'),
(7, 2, 'Official', 'Normal', 'Officials: SK Official: sk_kagawad_1 | Complaint: harassment | Incident Date: 2026-06-05 | Details: BASTOS. NANGHAHAWAK NG BURAT', '', 'In Progress', 5, '2026-06-14 16:29:52', '2026-06-12 21:55:00'),
(8, 3, 'Official', 'Normal', 'Officials: SK Official: sk_kagawad_6 | Complaint: abuse | Incident Date: 2026-06-04 | Details: Akala mo siya may are ng kalsada', '', 'Under Review', 1, '2026-06-14 16:10:49', '2026-06-12 22:06:46'),
(9, 3, 'Barangay Issue', 'Normal', 'JGKAJGHAUHGAGAGJAGFAFAF', 'Sa lilac, malapit sa Kapitan Moy', 'In Progress', 5, '2026-06-14 16:29:10', '2026-06-12 23:42:05');

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
  `verified_by` int(11) DEFAULT NULL,
  `verified_at` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_hidden` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `name`, `address`, `phone`, `email`, `occupation`, `age`, `birthday`, `gender`, `civil_status`, `verification_id`, `status`, `verified_by`, `verified_at`, `created_at`, `is_hidden`) VALUES
(1, 'luispogi', '$2y$10$pStl6ylY6D7a0d.sK4jup.k7THPKhqj5V4DO7wQPdd4I0vvBM/4uq', 'Asuncion, Luis Miguel', '123 Katipunan St. Marikina Heights, Marikina City', '09276461983', 'angelomartinezsteven@gmail.com', 'Student', 17, '2008-07-26', 'male', 'single', 'uploads/verification_ids/verif_1781270932_708ac182.png', 'Rejected', 1, '2026-06-14 16:11:18', '2026-06-12 13:28:52', 0),
(2, 'stevenpogi', '$2y$10$RzVMaKuRjMMKWrjPtIdXNOpzwja47QOn8Ee3cqjzV3Vm6pzNUoTPq', 'Martinez, Steven Angelo G.', '123 Dao St., Marikina Heights', '09124781732', 'charlieangelieroan@gmail.com', 'Student', 20, '2005-08-19', 'male', 'single', 'uploads/verification_ids/verif_1781273802_c1b7b366.jpg', 'Verified', 1, '2026-06-14 16:11:46', '2026-06-12 14:16:42', 0),
(3, 'julianpogi', '$2y$10$PSczRa89gid5qgBhjaE5r.a3pyxTzYeY4Si6VH4GH9kLGfrUMNYnS', 'BENiE, UMAG NOA', '123 Katipunan St. Marikina Heights, Marikina City', '09276461983', NULL, NULL, 22, NULL, 'male', NULL, 'uploads/verification_ids/verif_1781325502_3abdb97a.jpg', 'Rejected', 1, '2026-06-14 16:11:44', '2026-06-13 04:38:23', 0),
(4, 'BENBEN', '$2y$10$vGX8i5YoKgpg9goLPNTkaOdDYG.YZtzfMniDVduAJYE5Atyh/PLf.', 'James ,Ace Smith', '123 Dao St. ,Marikina Heights', '09124781732', NULL, NULL, 42, NULL, 'prefer_not', NULL, 'uploads/verification_ids/verif_1781325996_96772865.png', 'Verified', 1, '2026-06-14 16:12:20', '2026-06-13 04:46:36', 0),
(5, 'HEYBAY', '$2y$10$111Q/bK/UDePeribEI9BCu2JQkCWVxZP4fbd4Im2gqXjgXLyTDtJu', 'Heybay, Magkatil Bang H.', '834 Proj 6, Quezon City', '09218472442', NULL, NULL, 16, NULL, 'female', NULL, 'uploads/verification_ids/verif_1781335649_3d3a9c71.png', 'Verified', 1, '2026-06-14 16:12:19', '2026-06-13 07:27:29', 0);

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
-- Indexes for table `password_reset_requests`
--
ALTER TABLE `password_reset_requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_prr_approved_by` (`approved_by`);

--
-- Indexes for table `reports`
--
ALTER TABLE `reports`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user`),
  ADD KEY `fk_reports_handled_by` (`handled_by`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD KEY `fk_users_verified_by` (`verified_by`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `password_reset_requests`
--
ALTER TABLE `password_reset_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `reports`
--
ALTER TABLE `reports`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `password_reset_requests`
--
ALTER TABLE `password_reset_requests`
  ADD CONSTRAINT `fk_prr_approved_by` FOREIGN KEY (`approved_by`) REFERENCES `admins` (`id`);

--
-- Constraints for table `reports`
--
ALTER TABLE `reports`
  ADD CONSTRAINT `fk_reports_handled_by` FOREIGN KEY (`handled_by`) REFERENCES `admins` (`id`),
  ADD CONSTRAINT `reports_ibfk_1` FOREIGN KEY (`user`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `fk_users_verified_by` FOREIGN KEY (`verified_by`) REFERENCES `admins` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
