-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 27, 2025 at 03:10 PM
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
-- Database: `covid_system`
--

-- --------------------------------------------------------

--
-- Table structure for table `appointments`
--

CREATE TABLE `appointments` (
  `id` int(11) NOT NULL,
  `patient_id` int(11) NOT NULL,
  `hospital_id` int(11) NOT NULL,
  `vaccine_id` int(11) DEFAULT NULL,
  `type` enum('test','vaccination') NOT NULL,
  `appointment_date` date NOT NULL,
  `status` enum('pending','approved','rejected','completed') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `appointments`
--

INSERT INTO `appointments` (`id`, `patient_id`, `hospital_id`, `vaccine_id`, `type`, `appointment_date`, `status`, `created_at`) VALUES
(3, 3, 4, NULL, 'test', '2025-09-23', 'pending', '2025-09-23 12:09:48'),
(4, 5, 4, NULL, 'test', '2025-09-25', 'approved', '2025-09-24 16:43:10'),
(5, 5, 4, NULL, 'vaccination', '2025-09-26', 'approved', '2025-09-25 08:57:33'),
(6, 6, 4, NULL, 'vaccination', '2025-09-27', 'approved', '2025-09-25 10:26:21'),
(7, 7, 4, NULL, 'test', '2025-09-27', 'rejected', '2025-09-26 19:40:49'),
(8, 7, 8, NULL, 'test', '2025-09-27', 'approved', '2025-09-26 20:03:53'),
(9, 7, 8, NULL, 'test', '2025-09-27', 'pending', '2025-09-27 13:06:46');

-- --------------------------------------------------------

--
-- Table structure for table `hospitals`
--

CREATE TABLE `hospitals` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `name` varchar(150) NOT NULL,
  `location` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `results`
--

CREATE TABLE `results` (
  `id` int(11) NOT NULL,
  `appointment_id` int(11) NOT NULL,
  `test_result` enum('positive','negative','not_applicable') DEFAULT 'not_applicable',
  `vaccination_status` enum('not_done','1st_dose','2nd_dose','completed') DEFAULT 'not_done',
  `remarks` text DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `results`
--

INSERT INTO `results` (`id`, `appointment_id`, `test_result`, `vaccination_status`, `remarks`, `updated_at`) VALUES
(1, 4, 'not_applicable', '1st_dose', '', '2025-09-24 17:19:25'),
(2, 4, 'positive', 'not_done', NULL, '2025-09-24 18:08:39'),
(3, 5, 'not_applicable', 'completed', '', '2025-09-25 09:12:46'),
(4, 6, 'positive', 'not_done', '', '2025-09-25 10:27:00'),
(5, 7, 'not_applicable', 'not_done', '', '2025-09-26 19:42:08');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `role` enum('admin','hospital','patient') NOT NULL DEFAULT 'patient',
  `status` enum('pending','approved','rejected') DEFAULT 'approved',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `phone`, `address`, `role`, `status`, `created_at`) VALUES
(1, 'System Admin', 'admin@covid.com', '0e7517141fb53f21ee439b355b5a1d0a', NULL, NULL, 'admin', 'approved', '2025-09-23 11:26:18'),
(2, 'Usama Habib', 'usamahabib1011@gmail.com', '$2y$10$d0zvcTRLNNrj5mWYaPpAQuDkO/PD0kXak/da5QmmwqPrpMzeBnKNG', '0333-3361120', 'Savana City Flat No 107 Karachi', 'patient', 'approved', '2025-09-23 11:40:10'),
(3, 'fahad', 'fahad@gmail.com', '$2y$10$/iIHxH9rYjHxH11ZeAnmdOoJ8fXYPqSPss.oShJutb9Ph/an3ojdG', '123456789', 'Savana City Flat No 107 Karachi', 'patient', 'approved', '2025-09-23 11:45:29'),
(4, 'osmania', 'osmania@gmail.com', '$2y$10$MzGpRfWhAc0GbAiRpKRQN.Kw6cW22ynvtZkDgtds8oGoxYVXC4eZe', '123456789', 'gulshan', 'hospital', 'approved', '2025-09-23 11:50:08'),
(5, 'ali', 'ali@gmail.com', '$2y$10$swTRDfR/XZvC0Vx/dPLNV.liqWFT0ZunenEfUtBEdoxueWiZ6Rtme', '12345678910', 'Savana City Flat No 107 Karachi', 'patient', 'approved', '2025-09-24 16:36:48'),
(6, 'ali', 'ali2@gmail.com', '$2y$10$Tdh8e3O091Z50F1Al5P2qu9DbeVRbcxFfSbKYzwqYH78dSI7t43CS', '123456789', 'gulshan', 'patient', 'approved', '2025-09-25 10:19:36'),
(7, 'fahadhabib', 'fahadhabib246810@gmail.com', '$2y$10$XBjV.kDYeWLcxsQtxeQeZuUu25eoS8utFsFUJxGzpmRYQ6DqX1JjK', '319885406', 'savana', 'patient', 'approved', '2025-09-26 19:39:36'),
(8, 'liaqat ', 'liaqat@gmail.com', '$2y$10$sJBjQvTh2wXx8BmZBZDXge1j/NKweSVccJBU5ok217oy8uzvcvN22', '234567890', 'stadium road', 'hospital', 'approved', '2025-09-26 20:00:08');

-- --------------------------------------------------------

--
-- Table structure for table `vaccines`
--

CREATE TABLE `vaccines` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `stock` int(11) DEFAULT 0,
  `status` enum('available','unavailable') DEFAULT 'available'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `vaccines`
--

INSERT INTO `vaccines` (`id`, `name`, `stock`, `status`) VALUES
(1, 'Pfizer', 100, 'available'),
(2, 'Moderna', 50, 'available'),
(3, 'Sinopharm', 3, 'available');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `appointments`
--
ALTER TABLE `appointments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `patient_id` (`patient_id`),
  ADD KEY `hospital_id` (`hospital_id`),
  ADD KEY `vaccine_id` (`vaccine_id`);

--
-- Indexes for table `hospitals`
--
ALTER TABLE `hospitals`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `results`
--
ALTER TABLE `results`
  ADD PRIMARY KEY (`id`),
  ADD KEY `appointment_id` (`appointment_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `vaccines`
--
ALTER TABLE `vaccines`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `appointments`
--
ALTER TABLE `appointments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `hospitals`
--
ALTER TABLE `hospitals`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `results`
--
ALTER TABLE `results`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `vaccines`
--
ALTER TABLE `vaccines`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `appointments`
--
ALTER TABLE `appointments`
  ADD CONSTRAINT `appointments_ibfk_1` FOREIGN KEY (`patient_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `appointments_ibfk_3` FOREIGN KEY (`vaccine_id`) REFERENCES `vaccines` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `hospitals`
--
ALTER TABLE `hospitals`
  ADD CONSTRAINT `hospitals_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `results`
--
ALTER TABLE `results`
  ADD CONSTRAINT `results_ibfk_1` FOREIGN KEY (`appointment_id`) REFERENCES `appointments` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
