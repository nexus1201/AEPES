-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 18, 2026 at 11:38 AM
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
-- Database: `aepes-db`
--

-- --------------------------------------------------------

--
-- Table structure for table `audit_logs`
--

CREATE TABLE `audit_logs` (
  `audit_id` int(11) NOT NULL,
  `ipcrf_id` int(11) NOT NULL,
  `action` varchar(255) NOT NULL,
  `remarks` text DEFAULT NULL,
  `performed_by` int(11) NOT NULL,
  `role` varchar(50) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `audit_trail`
--

CREATE TABLE `audit_trail` (
  `audit_id` int(11) NOT NULL,
  `ipcrf_id` int(11) NOT NULL,
  `actor_id` int(11) NOT NULL,
  `actor_role` varchar(30) NOT NULL,
  `action` varchar(50) NOT NULL,
  `remarks` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `audit_trail`
--

INSERT INTO `audit_trail` (`audit_id`, `ipcrf_id`, `actor_id`, `actor_role`, `action`, `remarks`, `created_at`) VALUES
(1, 23, 8, 'Supervisor', 'Supervisor approved Core', 'asd', '2026-01-26 01:13:16'),
(2, 23, 8, 'Supervisor', 'Supervisor approved Strategic', 'asd', '2026-01-26 01:13:20'),
(3, 23, 8, 'Supervisor', 'Supervisor approved Support', 'asd', '2026-01-26 01:13:26'),
(4, 23, 8, 'Supervisor', 'Supervisor finalized IPCRF', 'Sent to Auditor', '2026-01-26 01:13:28'),
(5, 1, 1, 'System', 'TEST LOG', 'Manual test entry', '2026-01-26 10:47:26'),
(6, 24, 3, 'Supervisor', 'Supervisor approved Core', 'asd', '2026-01-26 10:49:49'),
(7, 24, 3, 'Supervisor', 'Supervisor approved Strategic', 'asd', '2026-01-26 10:49:55'),
(8, 24, 3, 'Supervisor', 'Supervisor approved Support', 'asd', '2026-01-26 10:50:03'),
(9, 24, 4, 'Auditor', 'Auditor approved IPCRF', 'approved', '2026-01-26 10:53:52'),
(10, 23, 4, 'Auditor', 'Auditor approved IPCRF', 'passed', '2026-01-26 11:20:09'),
(11, 25, 8, 'Supervisor', 'Supervisor approved Core', 'asd', '2026-01-26 11:22:47'),
(12, 25, 8, 'Supervisor', 'Supervisor approved Strategic', 'asd', '2026-01-26 11:22:53'),
(13, 25, 8, 'Supervisor', 'Supervisor approved Support', 'asd', '2026-01-26 11:22:58'),
(14, 25, 4, 'Auditor', 'Auditor approved IPCRF', 'passed', '2026-01-26 11:23:20'),
(15, 22, 4, 'Auditor', 'Auditor approved IPCRF', 'approve', '2026-01-26 11:27:49'),
(16, 26, 3, 'Supervisor', 'Supervisor approved Core', 'asd', '2026-01-26 11:29:29'),
(17, 26, 3, 'Supervisor', 'Supervisor approved Strategic', 'asd', '2026-01-26 11:29:33'),
(18, 26, 3, 'Supervisor', 'Supervisor approved Support', 'asd', '2026-01-26 11:29:38'),
(19, 26, 4, 'Auditor', 'Auditor approved IPCRF', 'passed and approved', '2026-01-26 11:29:58'),
(20, 16, 4, 'Auditor', 'Auditor approved IPCRF', 'good', '2026-01-26 11:46:58'),
(21, 27, 3, 'Supervisor', 'Supervisor approved Core', 'asd', '2026-01-26 12:14:46'),
(22, 27, 3, 'Supervisor', 'Supervisor approved Strategic', 'asd', '2026-01-26 12:14:51'),
(23, 27, 3, 'Supervisor', 'Supervisor approved Support', 'asd', '2026-01-26 12:14:58'),
(24, 27, 3, 'Supervisor', 'Supervisor finalized IPCRF', 'Sent to Auditor', '2026-01-26 12:15:00'),
(25, 27, 4, 'Auditor', 'Auditor approved IPCRF', 'good', '2026-01-26 12:17:38'),
(26, 11, 4, 'Auditor', 'Auditor approved IPCRF', 'good', '2026-01-26 12:25:14'),
(27, 28, 8, 'Supervisor', 'Supervisor approved Core', 'asd', '2026-01-26 12:41:00'),
(28, 28, 8, 'Supervisor', 'Supervisor approved Strategic', 'asd', '2026-01-26 12:41:04'),
(29, 28, 8, 'Supervisor', 'Supervisor approved Support', 'asd', '2026-01-26 12:41:09'),
(30, 28, 8, 'Supervisor', 'Supervisor finalized IPCRF', 'Sent to Auditor', '2026-01-26 12:41:10'),
(31, 28, 4, 'Auditor', 'Auditor approved IPCRF', 'good', '2026-01-26 12:41:23'),
(32, 28, 2, 'HR', 'HR approved IPCRF', 'passed everything', '2026-01-26 13:30:58'),
(33, 29, 3, 'Supervisor', 'Supervisor approved Core', 'asd', '2026-01-26 14:10:03'),
(34, 29, 3, 'Supervisor', 'Supervisor approved Strategic', 'asd', '2026-01-26 14:10:10'),
(35, 29, 3, 'Supervisor', 'Supervisor approved Support', 'asd', '2026-01-26 14:10:15'),
(36, 27, 2, 'HR', 'HR certified IPCRF', 'certified and approved', '2026-01-26 15:05:58'),
(37, 30, 3, 'Supervisor', 'Supervisor approved Core', 'asd', '2026-01-26 16:07:29'),
(38, 30, 3, 'Supervisor', 'Supervisor approved Strategic', 'asd', '2026-01-26 16:07:33'),
(39, 30, 3, 'Supervisor', 'Supervisor approved Support', 'asd', '2026-01-26 16:07:38'),
(40, 30, 4, 'Auditor', 'Auditor approved → Sent to HR', 'very good', '2026-01-26 16:08:33'),
(41, 30, 2, 'HR', 'HR certified IPCRF', 'fully approved', '2026-01-26 16:08:58'),
(42, 11, 2, 'HR', 'HR certified IPCRF', 'asd', '2026-01-26 16:15:23'),
(43, 31, 3, 'Supervisor', 'Supervisor approved Core', 'asd', '2026-01-26 16:18:40'),
(44, 31, 3, 'Supervisor', 'Supervisor approved Strategic', 'asd', '2026-01-26 16:18:45'),
(45, 31, 3, 'Supervisor', 'Supervisor approved Support', 'asd', '2026-01-26 16:18:53'),
(46, 31, 4, 'Auditor', 'Auditor approved → Sent to HR', 'nice', '2026-01-26 16:19:19'),
(47, 31, 2, 'HR', 'HR certified IPCRF', 'passed', '2026-01-26 16:19:38'),
(48, 32, 3, 'Supervisor', 'Supervisor approved Core', 'asd', '2026-01-26 16:30:17'),
(49, 32, 3, 'Supervisor', 'Supervisor approved Strategic', 'zxc', '2026-01-26 16:30:22'),
(50, 32, 3, 'Supervisor', 'Supervisor approved Support', 'sda', '2026-01-26 16:30:27'),
(51, 32, 4, 'Auditor', 'Auditor approved → Sent to HR', 'good', '2026-01-26 16:30:39'),
(52, 32, 2, 'HR', 'HR certified IPCRF', NULL, '2026-01-26 16:34:37'),
(53, 33, 3, 'Supervisor', 'Supervisor approved Core', 'asd', '2026-01-26 16:38:02'),
(54, 33, 3, 'Supervisor', 'Supervisor approved Strategic', 'asd', '2026-01-26 16:38:05'),
(55, 33, 3, 'Supervisor', 'Supervisor approved Support', 'sda', '2026-01-26 16:38:10'),
(56, 33, 4, 'Auditor', 'Auditor approved → Sent to HR', 'yes', '2026-01-26 16:38:21'),
(57, 33, 2, 'HR', 'HR certified IPCRF', NULL, '2026-01-26 16:38:43'),
(58, 34, 3, 'Supervisor', 'Supervisor approved Core', 'asd', '2026-01-27 15:46:39'),
(59, 34, 3, 'Supervisor', 'Supervisor approved Strategic', 'asd', '2026-01-27 15:46:51'),
(60, 34, 3, 'Supervisor', 'Supervisor approved Support', 'asd', '2026-01-27 15:47:13'),
(61, 34, 4, 'Auditor', 'Auditor approved → Sent to HR', 'asd', '2026-01-27 15:48:01'),
(62, 34, 2, 'HR', 'HR certified IPCRF', NULL, '2026-01-27 15:48:36'),
(63, 35, 3, 'Supervisor', 'Supervisor approved Core', 'asd', '2026-01-28 09:39:32'),
(64, 35, 3, 'Supervisor', 'Supervisor approved Strategic', 'asd', '2026-01-28 09:39:40'),
(65, 35, 3, 'Supervisor', 'Supervisor approved Support', 'asd', '2026-01-28 09:39:51'),
(66, 35, 4, 'Auditor', 'Auditor approved → Sent to HR', 'asd', '2026-01-28 09:40:13'),
(67, 35, 2, 'HR', 'HR certified IPCRF', NULL, '2026-01-28 09:40:44'),
(68, 36, 3, 'Supervisor', 'Supervisor approved Core', 'asd', '2026-01-28 09:42:16'),
(69, 36, 3, 'Supervisor', 'Supervisor approved Strategic', 'assd', '2026-01-28 09:42:20'),
(70, 36, 3, 'Supervisor', 'Supervisor approved Support', 'asd', '2026-01-28 09:42:26'),
(71, 36, 4, 'Auditor', 'Auditor approved → Sent to HR', 'asd', '2026-01-28 09:42:39'),
(72, 36, 2, 'HR', 'HR certified IPCRF', NULL, '2026-01-28 09:52:54'),
(73, 37, 3, 'Supervisor', 'Supervisor approved Core', 'sda', '2026-01-28 10:17:13'),
(74, 37, 3, 'Supervisor', 'Supervisor approved Strategic', 'asd', '2026-01-28 10:17:18'),
(75, 37, 3, 'Supervisor', 'Supervisor approved Support', 'asd', '2026-01-28 10:17:22'),
(76, 37, 4, 'Auditor', 'Auditor approved → Sent to HR', '4', '2026-01-28 10:18:38'),
(77, 37, 2, 'HR', 'HR certified IPCRF', NULL, '2026-01-28 10:19:36'),
(78, 38, 3, 'Supervisor', 'Supervisor returned Core', 'need to revise', '2026-01-29 12:05:40'),
(79, 39, 3, 'Supervisor', 'Supervisor approved Core', 'asd', '2026-01-29 13:01:44'),
(80, 39, 3, 'Supervisor', 'Supervisor returned Strategic', 'revised', '2026-01-29 14:20:19'),
(81, 39, 3, 'Supervisor', 'Supervisor approved Support', 'passed', '2026-01-29 14:20:34'),
(82, 39, 3, 'Supervisor', 'Supervisor approved Strategic', 'asd', '2026-01-29 15:56:32'),
(83, 39, 4, 'Auditor', 'Auditor approved → Sent to HR', 'approved', '2026-01-29 15:59:07'),
(84, 39, 2, 'HR', 'HR certified IPCRF', NULL, '2026-01-29 15:59:37'),
(85, 38, 3, 'Supervisor', 'Supervisor approved Core', 'asd', '2026-01-29 16:00:06'),
(86, 38, 3, 'Supervisor', 'Supervisor approved Strategic', 'asd', '2026-01-29 16:00:13'),
(87, 40, 3, 'Supervisor', 'Supervisor approved Core', 'asd', '2026-01-29 16:49:01'),
(88, 40, 3, 'Supervisor', 'Supervisor returned Strategic', 'needs to revise', '2026-01-29 16:49:10'),
(89, 40, 3, 'Supervisor', 'Supervisor returned Strategic', 'revised please', '2026-01-29 17:01:51'),
(90, 40, 3, 'Supervisor', 'Supervisor approved Strategic', 'asd', '2026-01-29 17:02:58'),
(91, 40, 3, 'Supervisor', 'Supervisor returned Support', 'asd', '2026-01-29 17:03:06'),
(92, 40, 3, 'Supervisor', 'Supervisor approved Support', 'asd', '2026-01-29 17:36:50'),
(93, 40, 4, 'Auditor', 'Auditor approved → Sent to HR', 'approved', '2026-01-29 17:37:03'),
(94, 40, 2, 'HR', 'HR certified IPCRF', NULL, '2026-01-29 17:44:05'),
(95, 41, 3, 'Supervisor', 'Supervisor approved Core', 'asd', '2026-01-30 10:29:28'),
(96, 41, 3, 'Supervisor', 'Supervisor approved Strategic', 'asd', '2026-01-30 10:29:38'),
(97, 41, 3, 'Supervisor', 'Supervisor approved Support', 'asd', '2026-01-30 10:29:50'),
(98, 41, 4, 'Auditor', 'Auditor approved → Sent to HR', 'approved ready for HR to review', '2026-01-30 10:30:24'),
(99, 41, 2, 'HR', 'HR certified IPCRF', NULL, '2026-01-30 10:30:50'),
(100, 46, 3, 'Supervisor', 'Supervisor returned Core', 'revised', '2026-01-30 18:58:36'),
(101, 46, 3, 'Supervisor', 'Supervisor returned Strategic', 'revised', '2026-01-30 18:58:42'),
(102, 46, 3, 'Supervisor', 'Supervisor returned Core', 'asd', '2026-01-30 19:34:26'),
(103, 46, 3, 'Supervisor', 'Supervisor returned Core', 'return', '2026-01-30 23:23:29'),
(104, 46, 3, 'Supervisor', 'Supervisor returned Strategic', 'asd', '2026-01-30 23:42:19'),
(105, 46, 3, 'Supervisor', 'Supervisor returned Strategic', 'return', '2026-01-30 23:43:44'),
(106, 46, 3, 'Supervisor', 'Supervisor returned Core', 'asd', '2026-01-31 01:34:34'),
(107, 46, 3, 'Supervisor', 'Supervisor returned Support', 'asd', '2026-01-31 01:38:03'),
(108, 46, 3, 'Supervisor', 'Supervisor approved Core', 'asd', '2026-01-31 01:40:21'),
(109, 46, 3, 'Supervisor', 'Supervisor approved Strategic', 'asd', '2026-01-31 01:40:27'),
(110, 46, 3, 'Supervisor', 'Supervisor approved Support', 'asd', '2026-01-31 01:41:14'),
(111, 46, 4, 'Auditor', 'Auditor approved → Sent to HR', 'asd', '2026-01-31 02:07:22'),
(112, 49, 3, 'Supervisor', 'Supervisor returned Core', 'asd', '2026-01-31 10:27:47'),
(113, 42, 3, 'Supervisor', 'Supervisor approved Core', 'asd', '2026-01-31 16:48:45'),
(114, 42, 3, 'Supervisor', 'Supervisor approved Strategic', 'asd', '2026-01-31 16:48:49'),
(115, 42, 3, 'Supervisor', 'Supervisor returned Support', 'revised', '2026-01-31 16:48:58'),
(116, 50, 3, 'Supervisor', 'Supervisor returned Core', 'asd', '2026-01-31 16:53:25'),
(117, 50, 3, 'Supervisor', 'Supervisor returned Strategic', 'asd', '2026-02-01 02:38:16'),
(118, 50, 8, 'Supervisor', 'Supervisor returned Strategic', 'return', '2026-02-01 02:39:16'),
(119, 50, 8, 'Supervisor', 'Supervisor approved Core', 'asd', '2026-02-01 03:01:40'),
(120, 50, 8, 'Supervisor', 'Supervisor approved Strategic', 'asd', '2026-02-01 03:01:48'),
(121, 50, 8, 'Supervisor', 'Supervisor approved Support', 'asd', '2026-02-01 03:01:56'),
(122, 50, 9, 'Auditor', 'Auditor approved → Sent to HR', 'asd', '2026-02-01 03:02:38'),
(123, 50, 39, 'HR', 'HR certified IPCRF', NULL, '2026-02-01 03:03:10'),
(124, 51, 3, 'Supervisor', 'Supervisor approved Core', 'test', '2026-02-01 20:40:22'),
(125, 51, 3, 'Supervisor', 'Supervisor approved Strategic', 'test', '2026-02-01 20:40:28'),
(126, 51, 3, 'Supervisor', 'Supervisor approved Support', 'test', '2026-02-01 20:40:38'),
(127, 46, 2, 'HR', 'HR certified IPCRF', NULL, '2026-03-08 14:41:44'),
(128, 53, 3, 'Supervisor', 'Supervisor approved Core', 'test', '2026-03-19 11:12:58'),
(129, 53, 3, 'Supervisor', 'Supervisor approved Strategic', 'test', '2026-03-19 11:13:54'),
(130, 53, 3, 'Supervisor', 'Supervisor approved Support', 'test', '2026-03-19 11:14:01'),
(131, 53, 4, 'Auditor', 'Auditor approved → Sent to HR', 'test', '2026-03-19 11:21:36'),
(132, 53, 2, 'HR', 'HR certified IPCRF', NULL, '2026-03-19 11:24:14');

-- --------------------------------------------------------

--
-- Table structure for table `ipcrf`
--

CREATE TABLE `ipcrf` (
  `ipcrf_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `evaluation_period` varchar(20) NOT NULL,
  `status` varchar(20) DEFAULT 'Closed',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `core_rating` decimal(5,2) DEFAULT NULL,
  `strategic_rating` decimal(5,2) DEFAULT NULL,
  `support_rating` decimal(5,2) DEFAULT NULL,
  `overall_rating` decimal(5,2) DEFAULT NULL,
  `core_q1` decimal(5,2) DEFAULT NULL,
  `core_qn2` decimal(5,2) DEFAULT NULL,
  `core_t3` decimal(5,2) DEFAULT NULL,
  `core_a4` decimal(5,2) DEFAULT NULL,
  `strategic_q1` decimal(5,2) DEFAULT NULL,
  `strategic_qn2` decimal(5,2) DEFAULT NULL,
  `strategic_t3` decimal(5,2) DEFAULT NULL,
  `strategic_a4` decimal(5,2) DEFAULT NULL,
  `support_q1` decimal(5,2) DEFAULT NULL,
  `support_qn2` decimal(5,2) DEFAULT NULL,
  `support_t3` decimal(5,2) DEFAULT NULL,
  `support_a4` decimal(5,2) DEFAULT NULL,
  `evaluated_by` int(11) DEFAULT NULL,
  `evaluated_at` datetime DEFAULT NULL,
  `core_remarks` text DEFAULT NULL,
  `strategic_remarks` text DEFAULT NULL,
  `support_remarks` text DEFAULT NULL,
  `core_status` enum('Pending','Reviewed','Returned') DEFAULT 'Pending',
  `strategic_status` enum('Pending','Reviewed','Returned') DEFAULT 'Pending',
  `support_status` enum('Pending','Reviewed','Returned') DEFAULT 'Pending',
  `audited_by` int(11) DEFAULT NULL,
  `audited_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ipcrf`
--

INSERT INTO `ipcrf` (`ipcrf_id`, `user_id`, `evaluation_period`, `status`, `created_at`, `core_rating`, `strategic_rating`, `support_rating`, `overall_rating`, `core_q1`, `core_qn2`, `core_t3`, `core_a4`, `strategic_q1`, `strategic_qn2`, `strategic_t3`, `strategic_a4`, `support_q1`, `support_qn2`, `support_t3`, `support_a4`, `evaluated_by`, `evaluated_at`, `core_remarks`, `strategic_remarks`, `support_remarks`, `core_status`, `strategic_status`, `support_status`, `audited_by`, `audited_at`) VALUES
(11, 1, '2026', '', '2026-01-08 04:39:02', 4.00, 4.00, 4.00, 4.17, 4.00, 4.00, 4.00, 4.00, 4.00, 4.00, 4.00, 4.00, 4.00, 4.00, 4.00, 4.00, 3, '2026-01-24 11:48:17', NULL, NULL, NULL, 'Reviewed', 'Reviewed', 'Reviewed', NULL, NULL),
(12, 5, '2025', '', '2026-01-15 06:14:57', 5.00, 5.00, 5.00, NULL, 5.00, 5.00, 5.00, 5.00, 5.00, 5.00, 5.00, 5.00, 5.00, 5.00, 5.00, 5.00, 3, '2026-01-24 08:43:38', NULL, NULL, NULL, 'Reviewed', 'Reviewed', 'Reviewed', NULL, NULL),
(13, 6, '2025', '', '2026-01-16 22:46:56', 4.00, 4.00, 4.00, NULL, 4.00, 4.00, 4.00, 4.00, 4.00, 4.00, 4.00, 4.00, 4.00, 4.00, 4.00, 4.00, 3, '2026-01-24 08:45:21', NULL, NULL, NULL, 'Reviewed', 'Reviewed', 'Reviewed', NULL, NULL),
(14, 7, '2025', '', '2026-01-17 02:15:08', 5.00, 5.00, 5.00, NULL, 5.00, 5.00, 5.00, 5.00, 5.00, 5.00, 5.00, 5.00, 5.00, 5.00, 5.00, 5.00, 3, '2026-01-24 10:19:56', NULL, NULL, NULL, 'Reviewed', 'Reviewed', 'Reviewed', NULL, NULL),
(15, 10, '2025', '', '2026-01-20 02:21:49', 4.25, 5.00, 4.75, NULL, 5.00, 4.00, 4.00, 4.00, 5.00, 5.00, 5.00, 5.00, 5.00, 5.00, 5.00, 4.00, 3, '2026-01-22 10:45:15', 'asd', 'perfect', NULL, 'Reviewed', 'Reviewed', 'Reviewed', NULL, NULL),
(16, 11, '2025', '', '2026-01-23 23:42:13', 5.00, 4.75, 3.88, NULL, 5.00, 5.00, 5.00, 5.00, 5.00, 5.00, 5.00, 4.00, 4.00, 4.00, 3.00, 4.50, 3, '2026-01-24 12:34:11', NULL, NULL, NULL, 'Reviewed', 'Reviewed', 'Reviewed', NULL, NULL),
(17, 12, '2025', '', '2026-01-24 01:54:59', 4.00, 5.00, 4.25, NULL, 5.00, 4.00, 4.00, 3.00, 5.00, 5.00, 5.00, 5.00, 4.00, 4.00, 5.00, 4.00, 3, '2026-01-24 09:55:36', NULL, NULL, NULL, 'Reviewed', 'Reviewed', 'Reviewed', NULL, NULL),
(18, 13, '2025', '', '2026-01-24 02:06:02', 5.00, 4.00, 5.00, NULL, 5.00, 5.00, 5.00, 5.00, 4.00, 4.00, 4.00, 4.00, 5.00, 5.00, 5.00, 5.00, 3, '2026-01-24 10:07:18', NULL, NULL, NULL, 'Reviewed', 'Reviewed', 'Reviewed', NULL, NULL),
(19, 14, '2025', '', '2026-01-24 02:15:12', 4.00, 4.00, 4.00, NULL, 4.00, 4.00, 4.00, 4.00, 4.00, 4.00, 4.00, 4.00, 4.00, 4.00, 4.00, 4.00, 3, '2026-01-24 10:15:55', NULL, NULL, NULL, 'Reviewed', 'Reviewed', 'Reviewed', NULL, NULL),
(20, 15, '2025', '', '2026-01-24 03:16:04', 5.00, 5.00, 5.00, NULL, 5.00, 5.00, 5.00, 5.00, 5.00, 5.00, 5.00, 5.00, 5.00, 5.00, 5.00, 5.00, 3, '2026-01-24 11:16:33', NULL, NULL, NULL, 'Reviewed', 'Reviewed', 'Reviewed', NULL, NULL),
(21, 16, '2025', '', '2026-01-24 03:25:48', 4.00, 4.25, 4.25, NULL, 4.00, 4.00, 4.00, 4.00, 4.00, 4.00, 4.00, 5.00, 4.00, 4.00, 5.00, 4.00, 3, '2026-01-24 11:26:25', NULL, NULL, NULL, 'Reviewed', 'Reviewed', 'Reviewed', NULL, NULL),
(22, 17, '2025', '', '2026-01-24 03:59:24', 4.50, 5.00, 4.00, NULL, 5.00, 5.00, 5.00, 3.00, 5.00, 5.00, 5.00, 5.00, 5.00, 4.00, 4.00, 3.00, 8, '2026-01-24 12:00:49', NULL, NULL, NULL, 'Reviewed', 'Reviewed', 'Reviewed', NULL, NULL),
(23, 18, '2025', '', '2026-01-25 17:12:52', 4.00, 4.00, 4.00, NULL, 4.00, 4.00, 4.00, 4.00, 4.00, 4.00, 4.00, 4.00, 4.00, 4.00, 4.00, 4.00, 8, '2026-01-26 01:13:26', NULL, NULL, NULL, 'Reviewed', 'Reviewed', 'Reviewed', NULL, NULL),
(24, 19, '2025', '', '2026-01-26 02:49:29', 4.50, 4.13, 4.58, NULL, 5.00, 5.00, 5.00, 3.00, 4.00, 5.00, 3.00, 4.50, 4.50, 4.00, 4.80, 5.00, 3, '2026-01-26 10:50:03', NULL, NULL, NULL, 'Reviewed', 'Reviewed', 'Reviewed', NULL, NULL),
(25, 20, '2025', '', '2026-01-26 03:22:29', 4.25, 4.25, 4.25, NULL, 4.00, 4.00, 4.00, 5.00, 3.00, 4.00, 5.00, 5.00, 4.00, 4.00, 4.00, 5.00, 8, '2026-01-26 11:22:58', NULL, NULL, NULL, 'Reviewed', 'Reviewed', 'Reviewed', NULL, NULL),
(26, 21, '2025', '', '2026-01-26 03:29:09', 4.50, 4.00, 4.25, NULL, 5.00, 5.00, 5.00, 3.00, 4.00, 5.00, 4.00, 3.00, 4.00, 4.00, 4.00, 5.00, 3, '2026-01-26 11:29:38', NULL, NULL, NULL, 'Reviewed', 'Reviewed', 'Reviewed', NULL, NULL),
(27, 22, '2025', '', '2026-01-26 04:14:25', 4.25, 4.16, 4.38, NULL, 4.00, 4.00, 4.00, 5.00, 3.00, 4.00, 5.00, 4.64, 4.00, 4.00, 4.50, 5.00, 3, '2026-01-26 12:14:58', NULL, NULL, NULL, 'Reviewed', 'Reviewed', 'Reviewed', NULL, NULL),
(28, 23, '2025', 'Closed', '2026-01-26 04:40:41', 4.75, 4.25, 4.25, NULL, 5.00, 5.00, 5.00, 4.00, 4.00, 5.00, 5.00, 3.00, 4.00, 4.00, 4.00, 5.00, 8, '2026-01-26 12:41:09', NULL, NULL, NULL, 'Reviewed', 'Reviewed', 'Reviewed', NULL, NULL),
(29, 24, '2025', 'Closed', '2026-01-26 06:09:41', 3.75, 4.00, 4.00, NULL, 4.00, 3.00, 4.00, 4.00, 4.00, 4.00, 4.00, 4.00, 4.00, 4.00, 4.00, 4.00, 3, '2026-01-26 14:10:15', NULL, NULL, NULL, 'Reviewed', 'Reviewed', 'Reviewed', NULL, NULL),
(30, 25, '2025', 'Certified', '2026-01-26 07:44:22', 3.75, 4.00, 4.00, NULL, 4.00, 4.00, 3.00, 4.00, 4.00, 4.00, 4.00, 4.00, 4.00, 4.00, 4.00, 4.00, 3, '2026-01-26 16:07:38', NULL, NULL, NULL, 'Reviewed', 'Reviewed', 'Reviewed', NULL, NULL),
(31, 26, '2025', 'Closed', '2026-01-26 08:18:04', 4.00, 3.75, 4.00, NULL, 4.00, 4.00, 4.00, 4.00, 3.00, 4.00, 5.00, 3.00, 4.00, 4.00, 4.00, 4.00, 3, '2026-01-26 16:18:53', NULL, NULL, NULL, 'Reviewed', 'Reviewed', 'Reviewed', NULL, NULL),
(32, 27, '2025', 'Certified', '2026-01-26 08:29:49', 4.00, 4.25, 3.75, NULL, 4.00, 4.00, 4.00, 4.00, 4.00, 4.00, 5.00, 4.00, 3.00, 3.00, 4.00, 5.00, 3, '2026-01-26 16:30:27', NULL, NULL, NULL, 'Reviewed', 'Reviewed', 'Reviewed', NULL, NULL),
(33, 28, '2025', 'Certified', '2026-01-26 08:37:44', 4.25, 4.00, 4.00, NULL, 4.00, 4.00, 4.00, 5.00, 4.00, 4.00, 4.00, 4.00, 3.00, 4.00, 5.00, 4.00, 3, '2026-01-26 16:38:10', NULL, NULL, NULL, 'Reviewed', 'Reviewed', 'Reviewed', NULL, NULL),
(34, 29, '2025', 'Closed', '2026-01-27 07:46:16', 4.75, 4.00, 4.00, NULL, 5.00, 5.00, 5.00, 4.00, 4.00, 4.00, 4.00, 4.00, 4.00, 4.00, 4.00, 4.00, 3, '2026-01-27 15:47:13', NULL, NULL, NULL, 'Reviewed', 'Reviewed', 'Reviewed', NULL, NULL),
(35, 30, '2025', 'Certified', '2026-01-28 01:38:59', 4.00, 4.25, 4.25, NULL, 4.00, 5.00, 4.00, 3.00, 4.00, 4.00, 4.00, 5.00, 4.00, 5.00, 4.00, 4.00, 3, '2026-01-28 09:39:51', NULL, NULL, NULL, 'Reviewed', 'Reviewed', 'Reviewed', NULL, NULL),
(36, 31, '2025', 'Certified', '2026-01-28 01:41:54', 4.00, 4.00, 4.00, NULL, 4.00, 4.00, 4.00, 4.00, 4.00, 4.00, 4.00, 4.00, 5.00, 4.00, 3.00, 4.00, 3, '2026-01-28 09:42:26', NULL, NULL, NULL, 'Reviewed', 'Reviewed', 'Reviewed', NULL, NULL),
(37, 32, '2025', 'Certified', '2026-01-28 02:16:05', 4.00, 4.00, 4.00, NULL, 4.00, 4.00, 4.00, 4.00, 4.00, 4.00, 4.00, 4.00, 4.00, 4.00, 4.00, 4.00, 3, '2026-01-28 10:17:22', NULL, NULL, NULL, 'Reviewed', 'Reviewed', 'Reviewed', NULL, NULL),
(38, 33, '2025', 'Submitted', '2026-01-29 03:33:31', 4.00, 4.00, NULL, NULL, 4.00, 4.00, 4.00, 4.00, 4.00, 4.00, 4.00, 4.00, NULL, NULL, NULL, NULL, 3, '2026-01-29 16:00:13', NULL, NULL, NULL, 'Reviewed', 'Reviewed', 'Pending', NULL, NULL),
(39, 34, '2025', 'Closed', '2026-01-29 04:59:19', 4.25, 4.00, 4.00, NULL, 4.00, 4.00, 4.00, 5.00, 4.00, 4.00, 4.00, 4.00, 4.00, 4.00, 4.00, 4.00, 3, '2026-01-29 15:56:32', NULL, NULL, NULL, 'Reviewed', 'Reviewed', 'Reviewed', NULL, NULL),
(40, 35, '2025', 'Certified', '2026-01-29 08:26:44', 4.25, 4.00, 4.00, NULL, 4.00, 4.00, 4.00, 5.00, 4.00, 4.00, 4.00, 4.00, 4.00, 4.00, 4.00, 4.00, 3, '2026-01-29 17:36:50', NULL, NULL, NULL, 'Reviewed', 'Reviewed', 'Reviewed', NULL, NULL),
(41, 36, '2025', 'Certified', '2026-01-30 02:27:22', 4.25, 5.00, 5.00, NULL, 4.00, 4.00, 4.00, 5.00, 5.00, 5.00, 5.00, 5.00, 5.00, 5.00, 5.00, 5.00, 3, '2026-01-30 10:29:50', NULL, NULL, NULL, 'Reviewed', 'Reviewed', 'Reviewed', NULL, NULL),
(42, 23, '2025', 'Closed', '2026-01-30 09:10:58', 4.50, 4.00, NULL, NULL, 4.00, 4.00, 5.00, 5.00, 4.00, 4.00, 4.00, 4.00, NULL, NULL, NULL, NULL, 3, '2026-01-31 16:48:49', NULL, NULL, 'revised', 'Reviewed', 'Reviewed', 'Returned', NULL, NULL),
(43, 26, '2026', 'Closed', '2026-01-30 09:10:59', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Pending', 'Pending', 'Pending', NULL, NULL),
(44, 34, '2026', 'Closed', '2026-01-30 09:10:59', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Pending', 'Pending', 'Pending', NULL, NULL),
(45, 24, '2026', 'Closed', '2026-01-30 09:11:00', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Pending', 'Pending', 'Pending', NULL, NULL),
(46, 37, '2026', 'Certified', '2026-01-30 09:11:51', 4.00, 4.00, 4.00, NULL, 4.00, 4.00, 4.00, 4.00, 4.00, 4.00, 4.00, 4.00, 4.00, 4.00, 4.00, 4.00, 3, '2026-01-31 01:41:14', NULL, NULL, NULL, 'Reviewed', 'Reviewed', 'Reviewed', NULL, NULL),
(47, 29, '2026', 'Closed', '2026-01-30 18:26:33', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Pending', 'Pending', 'Pending', NULL, NULL),
(48, 38, '2026', 'Closed', '2026-01-30 18:26:39', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Pending', 'Pending', 'Pending', NULL, NULL),
(49, 38, '2025', 'Open', '2026-01-30 18:36:41', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Returned', 'Pending', 'Pending', NULL, NULL),
(50, 40, '2025', 'Certified', '2026-01-31 08:52:46', 4.13, 4.00, 4.00, NULL, 4.00, 4.00, 4.50, 4.00, 4.00, 4.00, 4.00, 4.00, 4.00, 5.00, 4.00, 3.00, 8, '2026-02-01 03:01:56', NULL, NULL, NULL, 'Reviewed', 'Reviewed', 'Reviewed', NULL, NULL),
(51, 41, '2025', 'For Audit', '2026-02-01 11:48:57', 4.33, 4.50, 4.83, NULL, 5.00, 4.50, 4.00, 3.80, 5.00, 4.00, 4.00, 5.00, 5.00, 5.00, 5.00, 4.30, 3, '2026-02-01 20:40:38', NULL, NULL, NULL, 'Reviewed', 'Reviewed', 'Reviewed', NULL, NULL),
(52, 42, '', 'Open', '2026-03-08 06:23:55', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Pending', 'Pending', 'Pending', NULL, NULL),
(53, 43, '2026', 'Certified', '2026-03-19 03:04:31', 5.00, 5.00, 5.00, NULL, 5.00, 5.00, 5.00, 5.00, 5.00, 5.00, 5.00, 5.00, 5.00, 5.00, 5.00, 5.00, 3, '2026-03-19 11:14:01', NULL, NULL, NULL, 'Reviewed', 'Reviewed', 'Reviewed', NULL, NULL),
(54, 44, '', 'Open', '2026-04-24 20:44:34', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Pending', 'Pending', 'Pending', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `objectives`
--

CREATE TABLE `objectives` (
  `objective_id` int(11) NOT NULL,
  `ipcrf_id` int(11) NOT NULL,
  `objective_description` text NOT NULL,
  `category` enum('Core','Strategic','Support') NOT NULL,
  `proof_attachment` varchar(255) NOT NULL,
  `supervisor_rating` int(11) DEFAULT NULL,
  `supervisor_comments` text DEFAULT NULL,
  `weight` decimal(5,2) DEFAULT NULL,
  `weighted_score` decimal(5,2) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `output` text NOT NULL,
  `success_indicator` text NOT NULL,
  `actual_accomplishment` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `objectives`
--

INSERT INTO `objectives` (`objective_id`, `ipcrf_id`, `objective_description`, `category`, `proof_attachment`, `supervisor_rating`, `supervisor_comments`, `weight`, `weighted_score`, `created_at`, `output`, `success_indicator`, `actual_accomplishment`) VALUES
(23, 4, 'new', 'Core', '1767845936_695f303098157.docx', NULL, NULL, NULL, NULL, '2026-01-08 04:18:56', 'new', 'new', 'new'),
(26, 10, 'try', 'Support', '1767846782_695f337e2d574.docx', NULL, NULL, NULL, NULL, '2026-01-08 04:33:02', 'try', 'try', 'try'),
(27, 10, 'try', 'Strategic', '1767846984_695f344844d63.docx', NULL, NULL, NULL, NULL, '2026-01-08 04:36:24', 'try', 'try', 'try'),
(28, 10, 'try', 'Support', '1767847052_695f348c03795.docx', NULL, NULL, NULL, NULL, '2026-01-08 04:37:32', 'try', 'try', 'try'),
(29, 11, 'new', 'Core', '1767847142_695f34e6b23ac.docx', NULL, NULL, NULL, NULL, '2026-01-08 04:39:02', 'new', 'new', 'new'),
(30, 11, 'new', 'Strategic', '1767847154_695f34f2baceb.docx', NULL, NULL, NULL, NULL, '2026-01-08 04:39:14', 'new', 'new', 'new'),
(31, 11, 'new', 'Support', '1767847163_695f34fb17ec3.docx', NULL, NULL, NULL, NULL, '2026-01-08 04:39:23', 'new', 'new', 'new'),
(33, 2, 'new', 'Strategic', '1767925064_696065487ff43.docx', NULL, NULL, NULL, NULL, '2026-01-09 02:17:44', 'new', 'new', 'new'),
(34, 2, 'new', 'Support', '1767925077_696065554b071.docx', NULL, NULL, NULL, NULL, '2026-01-09 02:17:57', 'new', 'new', 'new'),
(35, 12, 'aldrin', 'Core', '1768457697_696885e149366.docx', NULL, NULL, NULL, NULL, '2026-01-15 06:14:57', 'aldrin', 'aldrin', 'aldrin'),
(37, 12, 'aldrin', 'Support', '1768457711_696885ef349e4.docx', NULL, NULL, NULL, NULL, '2026-01-15 06:15:11', 'aldrin', 'aldrin', 'aldrin'),
(38, 12, 'test', 'Strategic', '1768603244_696abe6c37e1d.docx', NULL, NULL, NULL, NULL, '2026-01-16 22:40:44', 'test', 'test', 'test'),
(40, 13, 'test', 'Core', '1768603619_696abfe337f7a.docx', NULL, NULL, NULL, NULL, '2026-01-16 22:46:59', 'test', 'test', 'test'),
(41, 13, 'test', 'Strategic', '1768614216_696ae948102db.docx', NULL, NULL, NULL, NULL, '2026-01-17 01:43:36', 'test', 'test', 'test'),
(42, 13, 'test', 'Support', '1768615958_696af01680fdf.docx', NULL, NULL, NULL, NULL, '2026-01-17 02:12:38', 'test', 'test', 'test'),
(44, 14, 'test', 'Core', '1768616113_696af0b1bad48.docx', NULL, NULL, NULL, NULL, '2026-01-17 02:15:13', 'test', 'test', 'test'),
(45, 14, 'test', 'Strategic', '1768616118_696af0b6da167.docx', NULL, NULL, NULL, NULL, '2026-01-17 02:15:18', 'test', 'test', 'test'),
(50, 15, 'asd', 'Core', '1768875734_696ee6d6f0c37.docx', NULL, NULL, NULL, NULL, '2026-01-20 02:22:14', 'asd', 'asd', 'asd'),
(56, 15, 'asd', 'Strategic', '1768875936_696ee7a076ada.docx', NULL, NULL, NULL, NULL, '2026-01-20 02:25:36', 'asd', 'asd', 'asd'),
(59, 15, 'asd', 'Support', '1769048083_69718813cc775.docx', NULL, NULL, NULL, NULL, '2026-01-22 02:14:43', 'asd', 'asd', 'asd'),
(60, 16, 'asd', 'Core', '1769211733_697407558d3c7.docx', NULL, NULL, NULL, NULL, '2026-01-23 23:42:13', 'asd', 'asd', 'asd'),
(61, 14, 'asd', 'Support', '1769218669_6974226da655c.docx', NULL, NULL, NULL, NULL, '2026-01-24 01:37:49', 'asd', 'asd', 'asd'),
(62, 17, 'asd', 'Core', '1769219699_697426731b8f9.docx', NULL, NULL, NULL, NULL, '2026-01-24 01:54:59', 'asd', 'asd', 'asd'),
(63, 17, 'asd', 'Strategic', '1769219703_697426770775a.docx', NULL, NULL, NULL, NULL, '2026-01-24 01:55:03', 'asd', 'asd', 'asd'),
(64, 17, 'asd', 'Support', '1769219706_6974267a0b2d5.docx', NULL, NULL, NULL, NULL, '2026-01-24 01:55:06', 'asd', 'asd', 'asd'),
(65, 18, 'sad', 'Core', '1769220362_6974290a5d630.docx', NULL, NULL, NULL, NULL, '2026-01-24 02:06:02', 'asd', 'asd', 'asd'),
(66, 18, 'sad', 'Strategic', '1769220365_6974290de9a74.docx', NULL, NULL, NULL, NULL, '2026-01-24 02:06:05', 'asd', 'asd', 'asd'),
(67, 18, 'sad', 'Support', '1769220369_69742911a24ea.docx', NULL, NULL, NULL, NULL, '2026-01-24 02:06:09', 'asd', 'asd', 'asd'),
(70, 19, 'asd', 'Core', '1769220919_69742b37e19a8.docx', NULL, NULL, NULL, NULL, '2026-01-24 02:15:19', 'asd', 'asd', 'asd'),
(71, 19, 'asd', 'Strategic', '1769220924_69742b3ca47a5.docx', NULL, NULL, NULL, NULL, '2026-01-24 02:15:24', 'asd', 'asd', 'asd'),
(72, 19, 'asd', 'Support', '1769220928_69742b40d80b6.docx', NULL, NULL, NULL, NULL, '2026-01-24 02:15:28', 'asd', 'asd', 'asd'),
(73, 20, 'asd', 'Core', '1769224564_6974397428aa9.docx', NULL, NULL, NULL, NULL, '2026-01-24 03:16:04', 'asd', 'asd', 'asd'),
(74, 20, 'asd', 'Strategic', '1769224566_69743976e86bc.docx', NULL, NULL, NULL, NULL, '2026-01-24 03:16:06', 'asd', 'asd', 'asd'),
(75, 20, 'asd', 'Support', '1769224569_6974397972937.docx', NULL, NULL, NULL, NULL, '2026-01-24 03:16:09', 'asd', 'asd', 'asd'),
(76, 21, 'asd', 'Core', '1769225148_69743bbcdda91.docx', NULL, NULL, NULL, NULL, '2026-01-24 03:25:48', 'asd', 'asd', 'asd'),
(77, 21, 'asd', 'Strategic', '1769225152_69743bc0574b6.docx', NULL, NULL, NULL, NULL, '2026-01-24 03:25:52', 'asd', 'asd', 'asd'),
(78, 21, 'asd', 'Support', '1769225155_69743bc34aacf.docx', NULL, NULL, NULL, NULL, '2026-01-24 03:25:55', 'asd', 'asd', 'asd'),
(80, 22, 'asd', 'Core', '1769227166_6974439ea4e18.docx', NULL, NULL, NULL, NULL, '2026-01-24 03:59:26', 'asd', 'asd', 'asd'),
(81, 22, 'asd', 'Strategic', '1769227215_697443cf4f865.docx', NULL, NULL, NULL, NULL, '2026-01-24 04:00:15', 'asd', 'asd', 'asd'),
(82, 22, 'asd', 'Support', '1769227217_697443d1e1f04.docx', NULL, NULL, NULL, NULL, '2026-01-24 04:00:17', 'asd', 'asd', 'asd'),
(83, 16, 'asd', 'Strategic', '1769229222_69744ba63c117.docx', NULL, NULL, NULL, NULL, '2026-01-24 04:33:42', 'asd', 'asd', 'asd'),
(84, 16, 'asd', 'Support', '1769229225_69744ba92c02d.docx', NULL, NULL, NULL, NULL, '2026-01-24 04:33:45', 'asd', 'asd', 'asd'),
(85, 23, 'asd', 'Core', '1769361172_69764f148b197.docx', NULL, NULL, NULL, NULL, '2026-01-25 17:12:52', 'asd', 'asd', 'asd'),
(86, 23, 'asd', 'Support', '1769361176_69764f181daf8.docx', NULL, NULL, NULL, NULL, '2026-01-25 17:12:56', 'asd', 'asd', 'asd'),
(87, 23, 'asd', 'Strategic', '1769361178_69764f1a6cbe8.docx', NULL, NULL, NULL, NULL, '2026-01-25 17:12:58', 'asd', 'asd', 'asd'),
(88, 24, 'asd', 'Core', '1769395769_6976d63990e42.docx', NULL, NULL, NULL, NULL, '2026-01-26 02:49:29', 'asd', 'asd', 'asd'),
(89, 24, 'asd', 'Strategic', '1769395772_6976d63ca8fbd.docx', NULL, NULL, NULL, NULL, '2026-01-26 02:49:32', 'asd', 'asd', 'asd'),
(90, 24, 'asd', 'Support', '1769395775_6976d63f94859.docx', NULL, NULL, NULL, NULL, '2026-01-26 02:49:35', 'asd', 'asd', 'asd'),
(91, 25, 'asd', 'Core', '1769397749_6976ddf586819.docx', NULL, NULL, NULL, NULL, '2026-01-26 03:22:29', 'asd', 'asdasd', 'asd'),
(92, 25, 'asd', 'Strategic', '1769397752_6976ddf880b6b.docx', NULL, NULL, NULL, NULL, '2026-01-26 03:22:32', 'asd', 'asdasd', 'asd'),
(93, 25, 'asd', 'Support', '1769397754_6976ddfae207a.docx', NULL, NULL, NULL, NULL, '2026-01-26 03:22:34', 'asd', 'asdasd', 'asd'),
(94, 26, 'asd', 'Core', '1769398149_6976df8539b30.docx', NULL, NULL, NULL, NULL, '2026-01-26 03:29:09', 'asd', 'asd', 'asd'),
(95, 26, 'asd', 'Strategic', '1769398151_6976df87de585.docx', NULL, NULL, NULL, NULL, '2026-01-26 03:29:11', 'asd', 'asd', 'asd'),
(96, 26, 'asd', 'Support', '1769398154_6976df8a7fd70.docx', NULL, NULL, NULL, NULL, '2026-01-26 03:29:14', 'asd', 'asd', 'asd'),
(97, 27, 'asd', 'Core', '1769400865_6976ea210caa8.docx', NULL, NULL, NULL, NULL, '2026-01-26 04:14:25', 'asd', 'asd', 'asd'),
(98, 27, 'asd', 'Strategic', '1769400867_6976ea23d3fe8.docx', NULL, NULL, NULL, NULL, '2026-01-26 04:14:27', 'asd', 'asd', 'asd'),
(99, 27, 'asd', 'Support', '1769400871_6976ea276136e.docx', NULL, NULL, NULL, NULL, '2026-01-26 04:14:31', 'asd', 'asd', 'asd'),
(100, 28, 'asd', 'Core', '1769402441_6976f049db4c6.docx', NULL, NULL, NULL, NULL, '2026-01-26 04:40:41', 'asd', 'asd', 'asd'),
(101, 28, 'asd', 'Strategic', '1769402444_6976f04c81a2c.docx', NULL, NULL, NULL, NULL, '2026-01-26 04:40:44', 'asd', 'asd', 'asd'),
(102, 28, 'asd', 'Support', '1769402446_6976f04ee1e6d.docx', NULL, NULL, NULL, NULL, '2026-01-26 04:40:46', 'asd', 'asd', 'asd'),
(103, 29, 'asd', 'Core', '1769407781_697705250f3df.docx', NULL, NULL, NULL, NULL, '2026-01-26 06:09:41', 'asd', 'asd', 'asd'),
(104, 29, 'asd', 'Strategic', '1769407785_6977052943990.docx', NULL, NULL, NULL, NULL, '2026-01-26 06:09:45', 'asd', 'asd', 'asd'),
(105, 29, 'asd', 'Support', '1769407788_6977052c1570b.docx', NULL, NULL, NULL, NULL, '2026-01-26 06:09:48', 'asd', 'asd', 'asd'),
(106, 30, 'asd', 'Core', '1769413462_69771b566464e.docx', NULL, NULL, NULL, NULL, '2026-01-26 07:44:22', 'asd', 'asd', 'asd'),
(107, 30, 'asd', 'Strategic', '1769414807_69772097aa2c6.docx', NULL, NULL, NULL, NULL, '2026-01-26 08:06:47', 'asd', 'asdasdasd', 'asd'),
(108, 30, 'asd', 'Support', '1769414810_6977209ac071d.docx', NULL, NULL, NULL, NULL, '2026-01-26 08:06:50', 'asd', 'asdasdasd', 'asd'),
(109, 31, 'asd', 'Core', '1769415484_6977233c6dfc3.docx', NULL, NULL, NULL, NULL, '2026-01-26 08:18:04', 'asd', 'asd', 'asd'),
(110, 31, 'asd', 'Strategic', '1769415500_6977234c96a52.docx', NULL, NULL, NULL, NULL, '2026-01-26 08:18:20', 'asd', 'asd', 'asd'),
(111, 31, 'asd', 'Support', '1769415504_69772350293b2.docx', NULL, NULL, NULL, NULL, '2026-01-26 08:18:24', 'asd', 'asd', 'asd'),
(112, 32, 'asd', 'Core', '1769416189_697725fdad423.docx', NULL, NULL, NULL, NULL, '2026-01-26 08:29:49', 'asd', 'asd', 'asd'),
(113, 32, 'asd', 'Strategic', '1769416192_697726004617e.docx', NULL, NULL, NULL, NULL, '2026-01-26 08:29:52', 'asd', 'asd', 'asd'),
(114, 32, 'asd', 'Support', '1769416195_6977260365f69.docx', NULL, NULL, NULL, NULL, '2026-01-26 08:29:55', 'asd', 'asd', 'asd'),
(115, 33, 'asd', 'Core', '1769416664_697727d82f485.docx', NULL, NULL, NULL, NULL, '2026-01-26 08:37:44', 'asd', 'asd', 'asd'),
(116, 33, 'asd', 'Strategic', '1769416667_697727dbd238d.docx', NULL, NULL, NULL, NULL, '2026-01-26 08:37:47', 'asd', 'asd', 'asd'),
(117, 33, 'asd', 'Support', '1769416670_697727de3ef62.docx', NULL, NULL, NULL, NULL, '2026-01-26 08:37:50', 'asd', 'asd', 'asd'),
(118, 34, 'asd', 'Core', '1769499976_69786d48ea324.docx', NULL, NULL, NULL, NULL, '2026-01-27 07:46:16', 'asd', 'asd', 'asd'),
(119, 34, 'asd', 'Strategic', '1769499980_69786d4c155a2.docx', NULL, NULL, NULL, NULL, '2026-01-27 07:46:20', 'asd', 'asd', 'asd'),
(120, 34, 'asd', 'Support', '1769499983_69786d4f0a257.docx', NULL, NULL, NULL, NULL, '2026-01-27 07:46:23', 'asd', 'asd', 'asd'),
(121, 35, 'asd', 'Core', '1769564339_697968b3901dc.docx', NULL, NULL, NULL, NULL, '2026-01-28 01:38:59', 'asd', 'asd', 'asd'),
(122, 35, 'asd', 'Strategic', '1769564345_697968b97cf54.docx', NULL, NULL, NULL, NULL, '2026-01-28 01:39:05', 'asd', 'asd', 'asd'),
(123, 35, 'asd', 'Support', '1769564348_697968bc67152.docx', NULL, NULL, NULL, NULL, '2026-01-28 01:39:08', 'asd', 'asd', 'asd'),
(124, 36, 'asd', 'Core', '1769564514_697969626fb29.docx', NULL, NULL, NULL, NULL, '2026-01-28 01:41:54', 'asd', 'asd', 'asd'),
(125, 36, 'asd', 'Strategic', '1769564518_69796966cd56a.docx', NULL, NULL, NULL, NULL, '2026-01-28 01:41:58', 'asd', 'asd', 'asd'),
(126, 36, 'asd', 'Support', '1769564521_697969699f228.docx', NULL, NULL, NULL, NULL, '2026-01-28 01:42:01', 'asd', 'asd', 'asd'),
(127, 37, 'sad', 'Core', '1769566565_6979716506d2f.docx', NULL, NULL, NULL, NULL, '2026-01-28 02:16:05', 'asd', 'asd', 'asd'),
(128, 37, 'sad', 'Strategic', '1769566568_6979716869a23.docx', NULL, NULL, NULL, NULL, '2026-01-28 02:16:08', 'asd', 'asd', 'asd'),
(129, 37, 'sad', 'Support', '1769566571_6979716b6b3c7.docx', NULL, NULL, NULL, NULL, '2026-01-28 02:16:11', 'asd', 'asd', 'asd'),
(134, 38, 'asd', 'Core', '1769658659_697ad923d607a.docx', NULL, NULL, NULL, NULL, '2026-01-29 03:50:59', 'asd', 'asd', 'asd'),
(135, 38, 'asd', 'Strategic', '1769659973_697ade45a6b62.docx', NULL, NULL, NULL, NULL, '2026-01-29 04:12:53', 'asd', 'asd', 'asd'),
(137, 39, 'asd', 'Core', '1769662830_697ae96e46f12.docx', NULL, NULL, NULL, NULL, '2026-01-29 05:00:30', 'asd', 'asd', 'asd'),
(142, 39, 'asd', 'Support', '1769666707_697af893115c4.docx', NULL, NULL, NULL, NULL, '2026-01-29 06:05:07', 'asd', 'asd', 'asd'),
(146, 39, 'asd', 'Strategic', '1769673359_697b128fdd175.docx', NULL, NULL, NULL, NULL, '2026-01-29 07:55:59', 'asd', 'asd', 'asd'),
(147, 40, 'asd', 'Core', '1769675204_697b19c4ae719.docx', NULL, NULL, NULL, NULL, '2026-01-29 08:26:44', 'asd', 'asd', 'asd'),
(151, 40, 'asd', 'Strategic', '1769677342_697b221e421aa.docx', NULL, NULL, NULL, NULL, '2026-01-29 09:02:22', 'asd', 'asd', 'asd'),
(153, 40, 'asd', 'Support', '1769679393_697b2a217b1f5.docx', NULL, NULL, NULL, NULL, '2026-01-29 09:36:33', 'asd', 'asd', 'asd'),
(154, 41, 'asd', 'Core', '1769740042_697c170ab478a.docx', NULL, NULL, NULL, NULL, '2026-01-30 02:27:22', 'asd', 'asd', 'asd'),
(155, 41, 'asd', 'Strategic', '1769740057_697c1719e34e4.docx', NULL, NULL, NULL, NULL, '2026-01-30 02:27:37', 'asd', 'asd', 'asd'),
(156, 41, 'asd', 'Support', '1769740079_697c172f675d8.docx', NULL, NULL, NULL, NULL, '2026-01-30 02:27:59', 'asd', 'asd', 'asd'),
(165, 46, 'asd', 'Core', '1769794500_697cebc42b541.docx', NULL, NULL, NULL, NULL, '2026-01-30 17:35:00', 'asd', 'asd', 'asd'),
(166, 46, 'asd', 'Strategic', '1769794517_697cebd561c1b.docx', NULL, NULL, NULL, NULL, '2026-01-30 17:35:17', 'asd', 'asd', 'asd'),
(167, 46, 'asd', 'Support', '1769794852_697ced24094dc.docx', NULL, NULL, NULL, NULL, '2026-01-30 17:40:52', 'asd', 'asdasd', 'asd'),
(168, 48, 'asd', 'Core', '1769797624_697cf7f8627c9.docx', NULL, NULL, NULL, NULL, '2026-01-30 18:27:04', 'asd', 'asd', 'asd'),
(170, 49, 'asd', 'Strategic', '1769826332_697d681c64dc9.docx', NULL, NULL, NULL, NULL, '2026-01-31 02:25:32', 'asd', 'asd', 'asd'),
(171, 49, 'asd', 'Support', '1769826445_697d688d1e27f.docx', NULL, NULL, NULL, NULL, '2026-01-31 02:27:25', 'asd', 'asd', 'asd'),
(172, 49, 'asd', 'Core', '1769846373_697db66501764.docx', NULL, NULL, NULL, NULL, '2026-01-31 07:59:33', 'asd', 'asd', 'asd'),
(173, 42, 'asd', 'Core', '1769848049_697dbcf1eee2d.docx', NULL, NULL, NULL, NULL, '2026-01-31 08:27:29', 'asd', 'asd', 'asd'),
(174, 42, 'asd', 'Strategic', '1769848062_697dbcfe607a1.docx', NULL, NULL, NULL, NULL, '2026-01-31 08:27:42', 'asd', 'asd', 'asd'),
(175, 42, 'asd', 'Support', '1769848070_697dbd064af6d.docx', NULL, NULL, NULL, NULL, '2026-01-31 08:27:50', 'asd', 'asd', 'asd'),
(177, 50, 'asd', 'Core', '1769883888_697e48f094bd6.docx', NULL, NULL, NULL, NULL, '2026-01-31 18:24:48', 'asd', 'asd', 'asd'),
(180, 50, 'asd', 'Strategic', '1769884779_697e4c6b75353.docx', NULL, NULL, NULL, NULL, '2026-01-31 18:39:39', 'asd', 'asd', 'new'),
(181, 50, 'asd', 'Support', '1769886076_697e517c58744.docx', NULL, NULL, NULL, NULL, '2026-01-31 19:01:16', 'asd', 'asd', 'asd'),
(182, 51, 'Test Objective', 'Core', '1769947231_697f405f4b290.docx', NULL, NULL, NULL, NULL, '2026-02-01 12:00:31', 'Test Output', 'Test Success', 'Test Accomplishment'),
(183, 51, 'asd', 'Strategic', '1769949590_697f499665543.docx', NULL, NULL, NULL, NULL, '2026-02-01 12:39:50', 'asd', 'asd', 'asd'),
(184, 51, 'test', 'Support', '1769949602_697f49a2a18b1.docx', NULL, NULL, NULL, NULL, '2026-02-01 12:40:02', 'test', 'test', 'test'),
(185, 53, 'Test', 'Core', '1773889650_69bb687255d92.docx', NULL, NULL, NULL, NULL, '2026-03-19 03:07:30', 'test', 'test', 'test'),
(186, 53, 'test', 'Strategic', '1773889659_69bb687b3418e.docx', NULL, NULL, NULL, NULL, '2026-03-19 03:07:39', 'test', 'test', 'test'),
(187, 53, 'test', 'Support', '1773889667_69bb6883ba5b9.docx', NULL, NULL, NULL, NULL, '2026-03-19 03:07:47', 'test', 'test', 'test');

-- --------------------------------------------------------

--
-- Table structure for table `objective_weights`
--
-- Error reading structure for table aepes-db.objective_weights: #1932 - Table &#039;aepes-db.objective_weights&#039; doesn&#039;t exist in engine
-- Error reading data for table aepes-db.objective_weights: #1064 - You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near &#039;FROM `aepes-db`.`objective_weights`&#039; at line 1

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `employee_no` varchar(50) NOT NULL,
  `full_name` varchar(150) NOT NULL,
  `role` enum('Employee','Supervisor','HR','Auditor') NOT NULL,
  `department` varchar(100) DEFAULT NULL,
  `username` varchar(50) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `status` enum('Active','Inactive') DEFAULT 'Active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `employee_no`, `full_name`, `role`, `department`, `username`, `password_hash`, `status`, `created_at`) VALUES
(1, 'EMP-001', 'Juan Dela Cruz', 'Employee', 'Training', 'employee1', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Active', '2025-12-18 15:53:45'),
(2, 'HR-001', 'Maria Santos', 'HR', 'Human Resources', 'hr1', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Active', '2025-12-18 15:57:22'),
(3, 'SUP-001', 'Ana Cruz', 'Supervisor', 'Operations', 'head1', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Active', '2025-12-18 15:57:22'),
(4, 'AUD-001', 'Pedro Reyes', 'Auditor', 'Audit', 'auditor1', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Active', '2025-12-18 15:57:22'),
(8, 'head002', 'Johnathan Lopez', 'Supervisor', 'Supervisor', 'head2', '$2y$10$zmP8ZGUc9Oz7xcfj4/4cMeU4HJACFJ74Tm/I9krGFD38wqeHfwb8u', 'Active', '2026-01-20 01:42:08'),
(9, 'auditor002', 'Dave Strong', 'Auditor', 'Auditor', 'auditor2', '$2y$10$GjjSHEJ1kmYHHU.6U/A2feIzma.ADt1krgwF7cqr1w5yvS0SDQsMG', 'Active', '2026-01-20 01:43:49'),
(21, 'emp016', 'Levi Ray', 'Employee', 'IT', 'employee16', '$2y$10$XyuhwbgMPW.gbWUNlL3BVO2ccRpzHgAJ3DFBR8BwDP1xggHB5xCDK', 'Active', '2026-01-26 03:28:49'),
(22, 'emp017', 'Jane Garcia', 'Employee', 'IT', 'employee17', '$2y$10$liVXu5sy3CNgbmuLoFoKPuey7ImgVMAEUGMJ6A5eayvNHIJQyqTem', 'Active', '2026-01-26 04:14:03'),
(23, 'emp018', 'Aldrin Anasis', 'Employee', 'IT', 'employee18', '$2y$10$n79iioIfhy3WxDHi0eDnFurIxkBlDCfgwpRNkF.vltESPxXK4aRgi', 'Active', '2026-01-26 04:40:27'),
(24, 'emp019', 'Evans Ben', 'Employee', 'IT', 'employee19', '$2y$10$mA6c2hgtgpEkWoJZuIJcrusHyPiq58jaa/.FpXrOBrRlZLAwVygP.', 'Active', '2026-01-26 06:09:23'),
(25, 'emp020', 'Hans Sel', 'Employee', 'IT', 'employee20', '$2y$10$MNZ44w6SGbDK9q.NN8hGGOtGzIs/2GuKG798Z7cIGtTpR67dUM0rq', 'Active', '2026-01-26 07:34:57'),
(26, 'emp021', 'Alexander Reyes', 'Employee', 'IT', 'employee21', '$2y$10$J1uNJhqsyj1BsfFtmHQYHuNhNgVUYSz.yr7nr/T806YcyDUj3YtBa', 'Active', '2026-01-26 08:17:46'),
(27, 'emp22', 'Maricel Ainz', 'Employee', 'IT', 'employee22', '$2y$10$mglOHH4xohNUxunI1cp7NOxIdJ6xinZV6t6S3MjxLnEsE8P4fUk7y', 'Active', '2026-01-26 08:29:33'),
(28, 'emp23', 'Head Shaq', 'Employee', 'Teacher', 'employee23', '$2y$10$JkgQANorn/ESvZlvDfQenu/9X0vv95dNCEiq/2MzPADAdPbuLPMOu', 'Active', '2026-01-26 08:37:27'),
(29, 'emp024', 'Gret Tel', 'Employee', 'IT', 'employee24', '$2y$10$8sp6S/XbQK8OYoyj0PVVBu.n1TGS0.LgNIfG7mDNSKYL49VZ1pnui', 'Active', '2026-01-27 07:45:55'),
(30, 'emp025', 'Karen Spears', 'Employee', 'Teacher', 'employee25', '$2y$10$CGPbKIih.fbqvyb9hpScbuCOPtV8.VQbwrTbAaTCjXtk6ABdsm8ua', 'Active', '2026-01-28 01:38:37'),
(31, 'emp026', 'Kevin Ten', 'Employee', 'Maintenance', 'employee26', '$2y$10$O9GlTHAJhgVhUs14KX2v8ebWtqM6EnmstkpxKbwXqhd.FKmFFYPae', 'Active', '2026-01-28 01:41:38'),
(32, 'emp027', 'Lea Mouw', 'Employee', 'IT', 'employee27', '$2y$10$/Yyhl8NOmZUDBBZkwxAK2eN3.18zKkVnDRmUea4fHHXtXU9yTIYN2', 'Active', '2026-01-28 02:15:40'),
(33, 'emp028', 'Helena Rain', 'Employee', 'IT', 'employee28', '$2y$10$1ggI9nOGFpFBK4kje9ROQ.jfcVX5dkkhq/NWUyt6bhmltQSldeWQG', 'Active', '2026-01-29 03:33:12'),
(34, 'emp029', 'Elizabeth Near', 'Employee', 'IT', 'employee29', '$2y$10$yw7kEUwUaAotnWXgcHTzZO8nUKmkfAkTvMrZN0OIIS0SWj/i1jeQy', 'Active', '2026-01-29 04:44:18'),
(35, 'emp030', 'Marky Allen', 'Employee', 'IT', 'employee30', '$2y$10$AylvKqy7554lvkWO5FCbReaJvc7bdYZTIEv9pc3UAWNWvGgSf72EW', 'Active', '2026-01-29 08:26:25'),
(36, 'emp031', 'Jana Pauline', 'Employee', 'Chef', 'employee31', '$2y$10$HKAm2FI8hMNgEK8SO9w4XuF8RgPbqiH759NO72QfpOq4HlvfqTGz2', 'Active', '2026-01-30 02:26:47'),
(37, 'emp032', 'Azrael Anasis', 'Employee', 'IT', 'employee32', '$2y$10$D1zQcE8lyMO6DVdddU3bfONbZtyTZT3VeQVNxlKkW6XCu85VCKhZi', 'Active', '2026-01-30 09:11:23'),
(38, 'emp033', 'Yuno Aldrich', 'Employee', 'IT', 'employee33', '$2y$10$d3JC5zhSH7r1UAeefKYTuukbzFQxWx5HMf2MEmrHuwJZV9LbY8bIy', 'Active', '2026-01-30 18:25:47'),
(39, 'hr002', 'Ellie Maine De Jesus', 'HR', 'Human Resources', 'hr2', '$2y$10$DJBz7UqynZy03Ip.rs/V4.5/8v5a9GfyM3ruxribTsALhmZ8ikJeq', 'Active', '2026-01-30 19:42:10'),
(40, 'emp034', 'Ben Jack', 'Employee', 'IT', 'employee34', '$2y$10$ldMM0d.RZoaFAAiaaFLqQO0hqdMAf8ag./SMtuA0tLXIcTlaQaAD2', 'Active', '2026-01-31 08:52:22'),
(41, 'emp035', 'John Aldrin M. Anasis', 'Employee', 'IT', 'employee35', '$2y$10$wfthznrUWhApjK0HFFYKDuqDbEYoQg1TNZI9kt5JwNnv670QBTPSO', 'Active', '2026-02-01 11:35:15'),
(42, 'emp040', 'Levi Ackerman', 'Employee', 'IT', 'employee40', '$2y$10$bFkYE6WQSsvSMqMo5/Cps.Dkl7tBjRPbAgnbK1KH2y6ReXvIeFfZe', 'Active', '2026-03-08 06:23:48'),
(43, 'emp99', 'John Aldrin Anasis', 'Employee', 'IT', 'employee99', '$2y$10$j/QJ9pPTkSiX8pkYmZHoKupdsx72aC2ClwPIp9PbgeX/h70Zi.fR.', 'Active', '2026-03-19 03:04:22'),
(44, 'emp055', 'Ja Anasis', 'Employee', 'IT', 'employee55', '$2y$10$vdnesdndvv6mUDH7tnldo.SjeUIECG1/C1TLwTcxffZd9hKQ4feTq', 'Active', '2026-04-24 20:44:26');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD PRIMARY KEY (`audit_id`);

--
-- Indexes for table `audit_trail`
--
ALTER TABLE `audit_trail`
  ADD PRIMARY KEY (`audit_id`);

--
-- Indexes for table `ipcrf`
--
ALTER TABLE `ipcrf`
  ADD PRIMARY KEY (`ipcrf_id`);

--
-- Indexes for table `objectives`
--
ALTER TABLE `objectives`
  ADD PRIMARY KEY (`objective_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `employee_no` (`employee_no`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `audit_logs`
--
ALTER TABLE `audit_logs`
  MODIFY `audit_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `audit_trail`
--
ALTER TABLE `audit_trail`
  MODIFY `audit_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=133;

--
-- AUTO_INCREMENT for table `ipcrf`
--
ALTER TABLE `ipcrf`
  MODIFY `ipcrf_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=55;

--
-- AUTO_INCREMENT for table `objectives`
--
ALTER TABLE `objectives`
  MODIFY `objective_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=188;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=45;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
