-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 01, 2025 at 09:05 AM
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
-- Database: `evelio_ams_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `accounts`
--

CREATE TABLE `accounts` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `role` enum('admin','teacher','student') NOT NULL,
  `first_login_required` tinyint(1) NOT NULL DEFAULT 1,
  `person_type` enum('admin','teacher','student') NOT NULL,
  `person_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `accounts`
--

INSERT INTO `accounts` (`id`, `email`, `username`, `password_hash`, `role`, `first_login_required`, `person_type`, `person_id`, `created_at`) VALUES
(1, 'admin@evelio.ams.edu', 'adminjuan', '$2y$10$o2/OA/EJ2RiKakRVI0D9UelCYwJdujlrsuaOMBZSxLt9xJrUjeA9y', 'admin', 0, 'admin', 1, '2025-12-01 05:11:28'),
(2, 'admin2@evelio.ams.edu', 'adminmaria', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEaE/1Q5.6G8LM5FhBoAoYkG8DEa', 'admin', 0, 'admin', 2, '2025-12-01 05:11:28'),
(3, 'jrizal@evelio.ams.edu', 'trizal', '$2y$10$o2/OA/EJ2RiKakRVI0D9UelCYwJdujlrsuaOMBZSxLt9xJrUjeA9y', 'teacher', 1, 'teacher', 1, '2025-12-01 05:11:28'),
(4, 'abonifacio@evelio.ams.edu', 'abonifacio', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEaE/1Q5.6G8LM5FhBoAoYkG8DEa', 'teacher', 1, 'teacher', 2, '2025-12-01 05:11:28'),
(5, 'gsilang@evelio.ams.edu', 'gsilang', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEaE/1Q5.6G8LM5FhBoAoYkG8DEa', 'teacher', 1, 'teacher', 3, '2025-12-01 05:11:28'),
(6, 'ejacinto@evelio.ams.edu', 'ejacinto', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEaE/1Q5.6G8LM5FhBoAoYkG8DEa', 'teacher', 1, 'teacher', 4, '2025-12-01 05:11:28'),
(7, 'maquino@evelio.ams.edu', 'maquino', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEaE/1Q5.6G8LM5FhBoAoYkG8DEa', 'teacher', 1, 'teacher', 5, '2025-12-01 05:11:28'),
(8, 'S-LRN0000001@evelio.ams.edu', 'student1', '$2y$10$o2/OA/EJ2RiKakRVI0D9UelCYwJdujlrsuaOMBZSxLt9xJrUjeA9y', 'student', 1, 'student', 1, '2025-12-01 05:11:28'),
(9, 'S-LRN0000002@evelio.ams.edu', 'student2', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEaE/1Q5.6G8LM5FhBoAoYkG8DEa', 'student', 1, 'student', 2, '2025-12-01 05:11:28'),
(10, 'S-LRN0000003@evelio.ams.edu', 'student3', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEaE/1Q5.6G8LM5FhBoAoYkG8DEa', 'student', 1, 'student', 3, '2025-12-01 05:11:28'),
(11, 'S-LRN0000004@evelio.ams.edu', 'student4', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEaE/1Q5.6G8LM5FhBoAoYkG8DEa', 'student', 1, 'student', 4, '2025-12-01 05:11:28'),
(12, 'S-LRN0000005@evelio.ams.edu', 'student5', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEaE/1Q5.6G8LM5FhBoAoYkG8DEa', 'student', 1, 'student', 5, '2025-12-01 05:11:28'),
(13, 'S-LRN0000006@evelio.ams.edu', 'student6', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEaE/1Q5.6G8LM5FhBoAoYkG8DEa', 'student', 1, 'student', 6, '2025-12-01 05:11:28'),
(14, 'S-LRN0000007@evelio.ams.edu', 'student7', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEaE/1Q5.6G8LM5FhBoAoYkG8DEa', 'student', 1, 'student', 7, '2025-12-01 05:11:28'),
(15, 'S-LRN0000008@evelio.ams.edu', 'student8', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEaE/1Q5.6G8LM5FhBoAoYkG8DEa', 'student', 1, 'student', 8, '2025-12-01 05:11:28'),
(16, 'S-LRN0000009@evelio.ams.edu', 'student9', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEaE/1Q5.6G8LM5FhBoAoYkG8DEa', 'student', 1, 'student', 9, '2025-12-01 05:11:28'),
(17, 'S-LRN0000010@evelio.ams.edu', 'student10', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEaE/1Q5.6G8LM5FhBoAoYkG8DEa', 'student', 1, 'student', 10, '2025-12-01 05:11:28'),
(18, 'S-LRN0000011@evelio.ams.edu', 'student11', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEaE/1Q5.6G8LM5FhBoAoYkG8DEa', 'student', 1, 'student', 11, '2025-12-01 05:11:28'),
(19, 'S-LRN0000012@evelio.ams.edu', 'student12', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEaE/1Q5.6G8LM5FhBoAoYkG8DEa', 'student', 1, 'student', 12, '2025-12-01 05:11:28'),
(20, 'S-LRN0000013@evelio.ams.edu', 'student13', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEaE/1Q5.6G8LM5FhBoAoYkG8DEa', 'student', 1, 'student', 13, '2025-12-01 05:11:28'),
(21, 'S-LRN0000014@evelio.ams.edu', 'student14', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEaE/1Q5.6G8LM5FhBoAoYkG8DEa', 'student', 1, 'student', 14, '2025-12-01 05:11:28'),
(22, 'S-LRN0000015@evelio.ams.edu', 'student15', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEaE/1Q5.6G8LM5FhBoAoYkG8DEa', 'student', 1, 'student', 15, '2025-12-01 05:11:28'),
(23, 'S-LRN0000016@evelio.ams.edu', 'student16', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEaE/1Q5.6G8LM5FhBoAoYkG8DEa', 'student', 1, 'student', 16, '2025-12-01 05:11:28'),
(24, 'S-LRN0000017@evelio.ams.edu', 'student17', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEaE/1Q5.6G8LM5FhBoAoYkG8DEa', 'student', 1, 'student', 17, '2025-12-01 05:11:28'),
(25, 'S-LRN0000018@evelio.ams.edu', 'student18', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEaE/1Q5.6G8LM5FhBoAoYkG8DEa', 'student', 1, 'student', 18, '2025-12-01 05:11:28'),
(26, 'S-LRN0000019@evelio.ams.edu', 'student19', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEaE/1Q5.6G8LM5FhBoAoYkG8DEa', 'student', 1, 'student', 19, '2025-12-01 05:11:28'),
(27, 'S-LRN0000020@evelio.ams.edu', 'student20', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEaE/1Q5.6G8LM5FhBoAoYkG8DEa', 'student', 1, 'student', 20, '2025-12-01 05:11:28'),
(28, 'dimplelayacan59@gmail.com', 'divina', '$2y$10$JGTKFbd.UdTOORGCheOhcexT0WnuPrH8wpVxqfhTx6Bb6ZpfYoh/m', 'teacher', 1, 'teacher', 6, '2025-12-01 05:28:15'),
(29, '123123123123123@gmail.com', '1', '$2y$10$VQNc8wXcjkkMu30gEoOA1O6ughTMSOfJaAYEW.Qd8d8RJfESCPKsy', 'teacher', 1, 'teacher', 7, '2025-12-01 05:51:59'),
(30, 'asdasdasd@gmail.com', 'awd', '$2y$10$9ClynkrVrVwtHdTYAlEDROcY2HrOfs73.9S.Oh7ns91NHpsfw6WWK', 'teacher', 1, 'teacher', 8, '2025-12-01 05:52:16');

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `faculty_id` varchar(50) NOT NULL,
  `full_name` varchar(150) NOT NULL,
  `username` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `sex` enum('Male','Female') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `faculty_id`, `full_name`, `username`, `email`, `sex`, `created_at`) VALUES
(1, 'ADM-001', 'Juan Dela Cruz', 'adminjuan', 'admin@evelio.ams.edu', 'Male', '2025-12-01 05:11:27'),
(2, 'ADM-002', 'Maria Santos', 'adminmaria', 'admin2@evelio.ams.edu', 'Female', '2025-12-01 05:11:27');

-- --------------------------------------------------------

--
-- Table structure for table `applicants`
--

CREATE TABLE `applicants` (
  `id` int(11) NOT NULL,
  `lrn` varchar(50) DEFAULT NULL,
  `department` enum('JHS','SHS') NOT NULL,
  `grade_level` enum('Grade 7','Grade 8','Grade 9','Grade 10','Grade 11','Grade 12') NOT NULL,
  `strand` enum('HUMSS','TVL') DEFAULT NULL,
  `student_type` enum('Old Student','New Student','Transferee') NOT NULL,
  `family_name` varchar(100) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `middle_name` varchar(100) NOT NULL,
  `suffix` varchar(20) DEFAULT NULL,
  `birthdate` date NOT NULL,
  `birthplace` varchar(150) NOT NULL,
  `religion` varchar(100) NOT NULL,
  `civil_status` varchar(50) NOT NULL,
  `sex` enum('Male','Female') NOT NULL,
  `mobile` varchar(30) NOT NULL,
  `email` varchar(255) NOT NULL,
  `curr_house_street` varchar(150) NOT NULL,
  `curr_barangay` varchar(100) NOT NULL,
  `curr_city` varchar(100) NOT NULL,
  `curr_province` varchar(100) NOT NULL,
  `curr_zip` varchar(10) NOT NULL,
  `perm_house_street` varchar(150) NOT NULL,
  `perm_barangay` varchar(100) NOT NULL,
  `perm_city` varchar(100) NOT NULL,
  `perm_province` varchar(100) NOT NULL,
  `perm_zip` varchar(10) NOT NULL,
  `elem_name` varchar(150) DEFAULT NULL,
  `elem_address` varchar(200) DEFAULT NULL,
  `elem_year_graduated` varchar(10) DEFAULT NULL,
  `last_school_name` varchar(150) DEFAULT NULL,
  `last_school_address` varchar(200) DEFAULT NULL,
  `jhs_name` varchar(150) DEFAULT NULL,
  `jhs_address` varchar(200) DEFAULT NULL,
  `jhs_year_graduated` varchar(10) DEFAULT NULL,
  `guardian_last_name` varchar(100) NOT NULL,
  `guardian_first_name` varchar(100) NOT NULL,
  `guardian_middle_name` varchar(100) NOT NULL,
  `guardian_contact` varchar(30) NOT NULL,
  `guardian_occupation` varchar(100) NOT NULL,
  `guardian_address` varchar(200) NOT NULL,
  `guardian_relationship` varchar(100) NOT NULL,
  `mother_last_name` varchar(100) NOT NULL,
  `mother_first_name` varchar(100) NOT NULL,
  `mother_middle_name` varchar(100) NOT NULL,
  `mother_contact` varchar(30) NOT NULL,
  `mother_occupation` varchar(100) NOT NULL,
  `mother_address` varchar(200) NOT NULL,
  `father_last_name` varchar(100) NOT NULL,
  `father_first_name` varchar(100) NOT NULL,
  `father_middle_name` varchar(100) NOT NULL,
  `father_contact` varchar(30) NOT NULL,
  `father_occupation` varchar(100) NOT NULL,
  `father_address` varchar(200) NOT NULL,
  `status` enum('pending','approved','declined') NOT NULL DEFAULT 'pending',
  `status_changed_at` timestamp NULL DEFAULT current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL,
  `soft_delete_expires_at` timestamp NULL DEFAULT NULL,
  `last_action_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `applicants`
--

INSERT INTO `applicants` (`id`, `lrn`, `department`, `grade_level`, `strand`, `student_type`, `family_name`, `first_name`, `middle_name`, `suffix`, `birthdate`, `birthplace`, `religion`, `civil_status`, `sex`, `mobile`, `email`, `curr_house_street`, `curr_barangay`, `curr_city`, `curr_province`, `curr_zip`, `perm_house_street`, `perm_barangay`, `perm_city`, `perm_province`, `perm_zip`, `elem_name`, `elem_address`, `elem_year_graduated`, `last_school_name`, `last_school_address`, `jhs_name`, `jhs_address`, `jhs_year_graduated`, `guardian_last_name`, `guardian_first_name`, `guardian_middle_name`, `guardian_contact`, `guardian_occupation`, `guardian_address`, `guardian_relationship`, `mother_last_name`, `mother_first_name`, `mother_middle_name`, `mother_contact`, `mother_occupation`, `mother_address`, `father_last_name`, `father_first_name`, `father_middle_name`, `father_contact`, `father_occupation`, `father_address`, `status`, `status_changed_at`, `deleted_at`, `soft_delete_expires_at`, `last_action_by`, `created_at`) VALUES
(1, '982374093482', 'SHS', 'Grade 12', 'TVL', 'New Student', 'Madrid', 'Richard', 'Manila', 'Jr', '2004-04-11', 'Baguio City Benguet', 'Roman Catholic', 'Single', 'Male', '+639105048651', 'richardmadrid11042004@gmail.com', '#34 kalapati street', 'Dizon Subdivision', 'baguio city', 'benguet', '2600', '#34 kalapati street', 'Dizon Subdivision', 'baguio city', 'benguet', '2600', 'Lucban Elementary School', 'old lucban camdas', '2016', 'Lucban Elementary School', 'old lucban camdas', '', '', '', 'madrid', 'anna lisa', 'Manila', '09309300473', 'business woman', '#34 kalapati street dizon subdivision baguio city 2600 benguet philippines', 'Parent', 'madrid', 'anna lisa', 'Manila', '09309300473', 'business woman', '#34 kalapati street dizon subdivision baguio city 2600 benguet philippines', '.', '.', '.', '.', '.', '.', 'pending', '2025-12-01 05:13:16', NULL, NULL, NULL, '2025-12-01 05:13:16');

-- --------------------------------------------------------

--
-- Table structure for table `applicant_history`
--

CREATE TABLE `applicant_history` (
  `id` int(11) NOT NULL,
  `applicant_id` int(11) NOT NULL,
  `action` enum('approved','declined') NOT NULL,
  `action_by` int(11) DEFAULT NULL,
  `action_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `purge_after` timestamp NULL DEFAULT NULL,
  `lrn` varchar(50) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `full_name` varchar(200) DEFAULT NULL,
  `payload` longtext NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `attendance`
--

CREATE TABLE `attendance` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `subject_load_id` int(11) NOT NULL,
  `date` date NOT NULL,
  `status` enum('present','absent','tardy') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `attendance`
--

INSERT INTO `attendance` (`id`, `student_id`, `subject_load_id`, `date`, `status`) VALUES
(1, 1, 1, '2024-09-02', 'present'),
(2, 2, 1, '2024-09-02', 'present'),
(3, 3, 1, '2024-09-02', 'absent'),
(4, 4, 1, '2024-09-02', 'present'),
(5, 5, 1, '2024-09-02', 'tardy'),
(6, 6, 1, '2024-09-03', 'present'),
(7, 7, 1, '2024-09-03', 'present'),
(8, 8, 1, '2024-09-03', 'present'),
(9, 9, 1, '2024-09-03', 'absent'),
(10, 10, 1, '2024-09-03', 'present'),
(11, 6, 2, '2024-09-02', 'present'),
(12, 7, 2, '2024-09-02', 'tardy'),
(13, 8, 2, '2024-09-02', 'present'),
(14, 9, 2, '2024-09-02', 'present'),
(15, 10, 2, '2024-09-02', 'absent'),
(16, 1, 2, '2024-09-04', 'present'),
(17, 2, 2, '2024-09-04', 'present'),
(18, 3, 2, '2024-09-04', 'present'),
(19, 4, 2, '2024-09-04', 'tardy'),
(20, 5, 2, '2024-09-04', 'present'),
(21, 11, 3, '2024-09-05', 'present'),
(22, 12, 3, '2024-09-05', 'present'),
(23, 13, 3, '2024-09-05', 'absent'),
(24, 14, 3, '2024-09-05', 'present'),
(25, 15, 3, '2024-09-05', 'present'),
(26, 11, 3, '2024-09-06', 'present'),
(27, 12, 3, '2024-09-06', 'tardy'),
(28, 13, 3, '2024-09-06', 'present'),
(29, 14, 3, '2024-09-06', 'present'),
(30, 15, 3, '2024-09-06', 'absent'),
(31, 16, 4, '2024-09-07', 'present'),
(32, 17, 4, '2024-09-07', 'present'),
(33, 18, 4, '2024-09-07', 'present'),
(34, 19, 4, '2024-09-07', 'tardy'),
(35, 20, 4, '2024-09-07', 'present'),
(36, 16, 4, '2024-09-08', 'present'),
(37, 17, 4, '2024-09-08', 'absent'),
(38, 18, 4, '2024-09-08', 'present'),
(39, 19, 4, '2024-09-08', 'present'),
(40, 20, 4, '2024-09-08', 'present'),
(41, 1, 5, '2024-10-01', 'present'),
(42, 2, 5, '2024-10-01', 'present'),
(43, 3, 5, '2024-10-01', 'present'),
(44, 4, 5, '2024-10-01', 'absent'),
(45, 5, 5, '2024-10-01', 'present'),
(46, 1, 5, '2024-10-02', 'present'),
(47, 2, 5, '2024-10-02', 'tardy'),
(48, 3, 5, '2024-10-02', 'present'),
(49, 4, 5, '2024-10-02', 'present'),
(50, 5, 5, '2024-10-02', 'present'),
(51, 6, 6, '2025-01-06', 'present'),
(52, 7, 6, '2025-01-06', 'present'),
(53, 8, 6, '2025-01-06', 'absent'),
(54, 9, 6, '2025-01-06', 'present'),
(55, 10, 6, '2025-01-06', 'present'),
(56, 6, 6, '2025-01-07', 'present'),
(57, 7, 6, '2025-01-07', 'tardy'),
(58, 8, 6, '2025-01-07', 'present'),
(59, 9, 6, '2025-01-07', 'present'),
(60, 10, 6, '2025-01-07', 'absent'),
(61, 11, 7, '2025-01-08', 'present'),
(62, 12, 7, '2025-01-08', 'present'),
(63, 13, 7, '2025-01-08', 'present'),
(64, 14, 7, '2025-01-08', 'present'),
(65, 15, 7, '2025-01-08', 'tardy'),
(66, 11, 7, '2025-01-09', 'present'),
(67, 12, 7, '2025-01-09', 'absent'),
(68, 13, 7, '2025-01-09', 'present'),
(69, 14, 7, '2025-01-09', 'present'),
(70, 15, 7, '2025-01-09', 'present'),
(71, 16, 8, '2025-01-10', 'present'),
(72, 17, 8, '2025-01-10', 'present'),
(73, 18, 8, '2025-01-10', 'present'),
(74, 19, 8, '2025-01-10', 'present'),
(75, 20, 8, '2025-01-10', 'present'),
(76, 16, 8, '2025-01-11', 'tardy'),
(77, 17, 8, '2025-01-11', 'present'),
(78, 18, 8, '2025-01-11', 'absent'),
(79, 19, 8, '2025-01-11', 'present'),
(80, 20, 8, '2025-01-11', 'present'),
(81, 1, 9, '2024-11-11', 'present'),
(82, 2, 9, '2024-11-11', 'present'),
(83, 3, 9, '2024-11-11', 'present'),
(84, 4, 9, '2024-11-11', 'present'),
(85, 5, 9, '2024-11-11', 'present'),
(86, 1, 9, '2024-11-12', 'present'),
(87, 2, 9, '2024-11-12', 'tardy'),
(88, 3, 9, '2024-11-12', 'absent'),
(89, 4, 9, '2024-11-12', 'present'),
(90, 5, 9, '2024-11-12', 'present'),
(91, 6, 10, '2025-02-14', 'present'),
(92, 7, 10, '2025-02-14', 'present'),
(93, 8, 10, '2025-02-14', 'present'),
(94, 9, 10, '2025-02-14', 'tardy'),
(95, 10, 10, '2025-02-14', 'present'),
(96, 6, 10, '2025-02-15', 'present'),
(97, 7, 10, '2025-02-15', 'absent'),
(98, 8, 10, '2025-02-15', 'present'),
(99, 9, 10, '2025-02-15', 'present'),
(100, 10, 10, '2025-02-15', 'present'),
(101, 11, 1, '2024-12-01', 'present'),
(102, 12, 2, '2024-12-01', 'present'),
(103, 13, 3, '2024-12-02', 'present'),
(104, 14, 4, '2024-12-02', 'present'),
(105, 15, 5, '2024-12-03', 'present'),
(106, 16, 6, '2025-03-03', 'present'),
(107, 17, 7, '2025-03-03', 'present'),
(108, 18, 8, '2025-03-04', 'present'),
(109, 19, 9, '2024-12-04', 'present'),
(110, 20, 10, '2025-03-05', 'present'),
(111, 1, 1, '2024-09-09', 'present'),
(112, 2, 1, '2024-09-09', 'present'),
(113, 3, 2, '2024-09-10', 'present'),
(114, 4, 2, '2024-09-10', 'present'),
(115, 5, 3, '2024-09-11', 'present'),
(116, 6, 4, '2024-09-12', 'present'),
(117, 7, 5, '2024-10-03', 'present'),
(118, 8, 6, '2025-01-12', 'present'),
(119, 9, 7, '2025-01-13', 'present'),
(120, 10, 8, '2025-01-14', 'present'),
(121, 11, 9, '2024-11-13', 'present'),
(122, 12, 10, '2025-02-16', 'present'),
(123, 13, 1, '2024-09-15', 'present'),
(124, 14, 2, '2024-09-16', 'present'),
(125, 15, 3, '2024-09-17', 'present'),
(126, 16, 4, '2024-09-18', 'present'),
(127, 17, 5, '2024-10-04', 'present'),
(128, 18, 6, '2025-01-15', 'present'),
(129, 19, 7, '2025-01-16', 'present'),
(130, 20, 8, '2025-01-17', 'present'),
(131, 1, 9, '2024-11-14', 'present'),
(132, 2, 10, '2025-02-17', 'present'),
(133, 3, 1, '2024-09-20', 'present'),
(134, 4, 2, '2024-09-21', 'present'),
(135, 5, 3, '2024-09-22', 'present'),
(136, 6, 4, '2024-09-23', 'present'),
(137, 7, 5, '2024-10-05', 'present'),
(138, 8, 6, '2025-01-20', 'present'),
(139, 9, 7, '2025-01-21', 'present'),
(140, 10, 8, '2025-01-22', 'present'),
(141, 11, 9, '2024-11-15', 'tardy'),
(142, 12, 10, '2025-02-18', 'absent'),
(143, 13, 1, '2024-09-25', 'present'),
(144, 14, 2, '2024-09-26', 'present'),
(145, 15, 3, '2024-09-27', 'present'),
(146, 16, 4, '2024-09-28', 'present'),
(147, 17, 5, '2024-10-06', 'present'),
(148, 18, 6, '2025-01-23', 'present'),
(149, 19, 7, '2025-01-24', 'present'),
(150, 20, 8, '2025-01-25', 'present'),
(151, 1, 1, '2025-12-01', 'present'),
(152, 2, 1, '2025-12-01', 'present'),
(153, 3, 1, '2025-12-01', 'present'),
(154, 4, 1, '2025-12-01', 'present'),
(155, 5, 1, '2025-12-01', 'present');

-- --------------------------------------------------------

--
-- Table structure for table `enrollments`
--

CREATE TABLE `enrollments` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `subject_load_id` int(11) NOT NULL,
  `school_year` varchar(20) NOT NULL,
  `semester` enum('First','Second') DEFAULT NULL,
  `status` enum('enrolled','dropped','completed') NOT NULL DEFAULT 'enrolled'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `enrollments`
--

INSERT INTO `enrollments` (`id`, `student_id`, `subject_load_id`, `school_year`, `semester`, `status`) VALUES
(1, 1, 1, '2024-2025', 'First', 'enrolled'),
(2, 2, 1, '2024-2025', 'First', 'enrolled'),
(3, 3, 1, '2024-2025', 'First', 'enrolled'),
(4, 4, 1, '2024-2025', 'First', 'enrolled'),
(5, 5, 1, '2024-2025', 'First', 'enrolled'),
(6, 6, 2, '2024-2025', 'First', 'enrolled'),
(7, 7, 2, '2024-2025', 'First', 'enrolled'),
(8, 8, 2, '2024-2025', 'First', 'enrolled'),
(9, 9, 2, '2024-2025', 'First', 'enrolled'),
(10, 10, 2, '2024-2025', 'First', 'enrolled'),
(11, 11, 3, '2024-2025', 'First', 'enrolled'),
(12, 12, 3, '2024-2025', 'First', 'enrolled'),
(13, 13, 3, '2024-2025', 'First', 'enrolled'),
(14, 14, 3, '2024-2025', 'First', 'enrolled'),
(15, 15, 3, '2024-2025', 'First', 'enrolled'),
(16, 16, 4, '2024-2025', 'First', 'enrolled'),
(17, 17, 4, '2024-2025', 'First', 'enrolled'),
(18, 18, 4, '2024-2025', 'First', 'enrolled'),
(19, 19, 4, '2024-2025', 'First', 'enrolled'),
(20, 20, 4, '2024-2025', 'First', 'enrolled'),
(21, 1, 5, '2024-2025', 'First', 'enrolled'),
(22, 2, 5, '2024-2025', 'First', 'enrolled'),
(23, 3, 5, '2024-2025', 'First', 'enrolled'),
(24, 4, 5, '2024-2025', 'First', 'enrolled'),
(25, 5, 5, '2024-2025', 'First', 'enrolled'),
(26, 6, 6, '2024-2025', 'Second', 'enrolled'),
(27, 7, 6, '2024-2025', 'Second', 'enrolled'),
(28, 8, 6, '2024-2025', 'Second', 'enrolled'),
(29, 9, 6, '2024-2025', 'Second', 'enrolled'),
(30, 10, 6, '2024-2025', 'Second', 'enrolled'),
(31, 11, 7, '2024-2025', 'Second', 'enrolled'),
(32, 12, 7, '2024-2025', 'Second', 'enrolled'),
(33, 13, 7, '2024-2025', 'Second', 'enrolled'),
(34, 14, 7, '2024-2025', 'Second', 'enrolled'),
(35, 15, 7, '2024-2025', 'Second', 'enrolled'),
(36, 16, 8, '2024-2025', 'Second', 'enrolled'),
(37, 17, 8, '2024-2025', 'Second', 'enrolled'),
(38, 18, 8, '2024-2025', 'Second', 'enrolled'),
(39, 19, 8, '2024-2025', 'Second', 'enrolled'),
(40, 20, 8, '2024-2025', 'Second', 'enrolled'),
(41, 1, 9, '2024-2025', 'First', 'enrolled'),
(42, 2, 9, '2024-2025', 'First', 'enrolled'),
(43, 3, 9, '2024-2025', 'First', 'enrolled'),
(44, 4, 9, '2024-2025', 'First', 'enrolled'),
(45, 5, 9, '2024-2025', 'First', 'enrolled'),
(46, 6, 10, '2024-2025', 'Second', 'enrolled'),
(47, 7, 10, '2024-2025', 'Second', 'enrolled'),
(48, 8, 10, '2024-2025', 'Second', 'enrolled'),
(49, 9, 10, '2024-2025', 'Second', 'enrolled'),
(50, 10, 10, '2024-2025', 'Second', 'enrolled'),
(51, 15, 23, '2025-2026', 'First', 'enrolled'),
(52, 15, 23, '2025-2026', 'First', 'enrolled');

-- --------------------------------------------------------

--
-- Table structure for table `grades`
--

CREATE TABLE `grades` (
  `id` int(11) NOT NULL,
  `enrollment_id` int(11) NOT NULL,
  `q1` decimal(5,2) DEFAULT NULL,
  `q2` decimal(5,2) DEFAULT NULL,
  `q3` decimal(5,2) DEFAULT NULL,
  `q4` decimal(5,2) DEFAULT NULL,
  `grade` decimal(5,2) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `grades`
--

INSERT INTO `grades` (`id`, `enrollment_id`, `grade`, `created_at`) VALUES
(1, 1, 88.50, '2025-12-01 05:11:28'),
(2, 2, 91.00, '2025-12-01 05:11:28'),
(3, 3, 76.25, '2025-12-01 05:11:28'),
(4, 4, 84.00, '2025-12-01 05:11:28'),
(5, 5, 79.50, '2025-12-01 05:11:28'),
(6, 6, 85.00, '2025-12-01 05:11:28'),
(7, 7, 90.50, '2025-12-01 05:11:28'),
(8, 8, 72.75, '2025-12-01 05:11:28'),
(9, 9, 88.00, '2025-12-01 05:11:28'),
(10, 10, 93.25, '2025-12-01 05:11:28'),
(11, 11, 70.00, '2025-12-01 05:11:28'),
(12, 12, 82.50, '2025-12-01 05:11:28'),
(13, 13, 77.75, '2025-12-01 05:11:28'),
(14, 14, 69.50, '2025-12-01 05:11:28'),
(15, 15, 95.00, '2025-12-01 05:11:28'),
(16, 16, 88.00, '2025-12-01 05:11:28'),
(17, 17, 91.25, '2025-12-01 05:11:28'),
(18, 18, 86.50, '2025-12-01 05:11:28'),
(19, 19, 80.00, '2025-12-01 05:11:28'),
(20, 20, 74.25, '2025-12-01 05:11:28'),
(21, 21, 89.50, '2025-12-01 05:11:28'),
(22, 22, 78.25, '2025-12-01 05:11:28'),
(23, 23, 92.00, '2025-12-01 05:11:28'),
(24, 24, 85.75, '2025-12-01 05:11:28'),
(25, 25, 81.50, '2025-12-01 05:11:28'),
(26, 26, 68.00, '2025-12-01 05:11:28'),
(27, 27, 79.00, '2025-12-01 05:11:28'),
(28, 28, 84.50, '2025-12-01 05:11:28'),
(29, 29, 90.00, '2025-12-01 05:11:28'),
(30, 30, 87.25, '2025-12-01 05:11:28'),
(31, 31, 73.50, '2025-12-01 05:11:28'),
(32, 32, 88.75, '2025-12-01 05:11:28'),
(33, 33, 69.00, '2025-12-01 05:11:28'),
(34, 34, 92.50, '2025-12-01 05:11:28'),
(35, 35, 86.00, '2025-12-01 05:11:28'),
(36, 36, 77.00, '2025-12-01 05:11:28'),
(37, 37, 80.50, '2025-12-01 05:11:28'),
(38, 38, 94.00, '2025-12-01 05:11:28'),
(39, 39, 83.25, '2025-12-01 05:11:28'),
(40, 40, 75.50, '2025-12-01 05:11:28'),
(41, 41, 89.00, '2025-12-01 05:11:28'),
(42, 42, 91.75, '2025-12-01 05:11:28'),
(43, 43, 78.50, '2025-12-01 05:11:28'),
(44, 44, 85.00, '2025-12-01 05:11:28'),
(45, 45, 88.25, '2025-12-01 05:11:28'),
(46, 46, 71.00, '2025-12-01 05:11:28'),
(47, 47, 82.00, '2025-12-01 05:11:28'),
(48, 48, 76.50, '2025-12-01 05:11:28'),
(49, 49, 90.75, '2025-12-01 05:11:28'),
(50, 50, 84.00, '2025-12-01 05:11:28');

-- --------------------------------------------------------

--
-- Table structure for table `sections`
--

CREATE TABLE `sections` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `department` enum('JHS','SHS') NOT NULL,
  `grade_level` enum('Grade 7','Grade 8','Grade 9','Grade 10','Grade 11','Grade 12') NOT NULL,
  `strand` enum('HUMSS','TVL') DEFAULT NULL,
  `capacity` int(11) NOT NULL DEFAULT 40,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sections`
--

INSERT INTO `sections` (`id`, `name`, `department`, `grade_level`, `strand`, `capacity`, `created_at`) VALUES
(1, 'Grade 7 - B', 'SHS', 'Grade 8', 'HUMSS', 51, '2025-12-01 05:11:27'),
(2, 'Grade 8 - A', 'JHS', 'Grade 8', NULL, 40, '2025-12-01 05:11:27'),
(3, 'Grade 9 - A', 'JHS', 'Grade 9', NULL, 40, '2025-12-01 05:11:27'),
(4, 'Grade 11 - HUMSS A', 'SHS', 'Grade 11', 'HUMSS', 40, '2025-12-01 05:11:27'),
(5, 'Grade 11 - TVL A', 'SHS', 'Grade 11', 'TVL', 40, '2025-12-01 05:11:27'),
(6, 'Grade 7 - B', 'JHS', 'Grade 7', NULL, 49, '2025-12-01 06:06:37');

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE `students` (
  `id` int(11) NOT NULL,
  `lrn` varchar(50) NOT NULL,
  `department` enum('JHS','SHS') NOT NULL,
  `grade_level` enum('Grade 7','Grade 8','Grade 9','Grade 10','Grade 11','Grade 12') NOT NULL,
  `strand` enum('HUMSS','TVL') DEFAULT NULL,
  `student_type` enum('Old Student','New Student','Transferee') NOT NULL,
  `advisory_section_id` int(11) DEFAULT NULL,
  `family_name` varchar(100) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `middle_name` varchar(100) NOT NULL,
  `suffix` varchar(20) DEFAULT NULL,
  `birthdate` date NOT NULL,
  `birthplace` varchar(150) NOT NULL,
  `religion` varchar(100) NOT NULL,
  `civil_status` varchar(50) NOT NULL,
  `sex` enum('Male','Female') NOT NULL,
  `mobile` varchar(30) NOT NULL,
  `email` varchar(255) NOT NULL,
  `curr_house_street` varchar(150) NOT NULL,
  `curr_barangay` varchar(100) NOT NULL,
  `curr_city` varchar(100) NOT NULL,
  `curr_province` varchar(100) NOT NULL,
  `curr_zip` varchar(10) NOT NULL,
  `perm_house_street` varchar(150) NOT NULL,
  `perm_barangay` varchar(100) NOT NULL,
  `perm_city` varchar(100) NOT NULL,
  `perm_province` varchar(100) NOT NULL,
  `perm_zip` varchar(10) NOT NULL,
  `elem_name` varchar(150) DEFAULT NULL,
  `elem_address` varchar(200) DEFAULT NULL,
  `elem_year_graduated` varchar(10) DEFAULT NULL,
  `last_school_name` varchar(150) DEFAULT NULL,
  `last_school_address` varchar(200) DEFAULT NULL,
  `jhs_name` varchar(150) DEFAULT NULL,
  `jhs_address` varchar(200) DEFAULT NULL,
  `jhs_year_graduated` varchar(10) DEFAULT NULL,
  `guardian_last_name` varchar(100) NOT NULL,
  `guardian_first_name` varchar(100) NOT NULL,
  `guardian_middle_name` varchar(100) NOT NULL,
  `guardian_contact` varchar(30) NOT NULL,
  `guardian_occupation` varchar(100) NOT NULL,
  `guardian_address` varchar(200) NOT NULL,
  `guardian_relationship` varchar(100) NOT NULL,
  `mother_last_name` varchar(100) NOT NULL,
  `mother_first_name` varchar(100) NOT NULL,
  `mother_middle_name` varchar(100) NOT NULL,
  `mother_contact` varchar(30) NOT NULL,
  `mother_occupation` varchar(100) NOT NULL,
  `mother_address` varchar(200) NOT NULL,
  `father_last_name` varchar(100) NOT NULL,
  `father_first_name` varchar(100) NOT NULL,
  `father_middle_name` varchar(100) NOT NULL,
  `father_contact` varchar(30) NOT NULL,
  `father_occupation` varchar(100) NOT NULL,
  `father_address` varchar(200) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`id`, `lrn`, `department`, `grade_level`, `strand`, `student_type`, `advisory_section_id`, `family_name`, `first_name`, `middle_name`, `suffix`, `birthdate`, `birthplace`, `religion`, `civil_status`, `sex`, `mobile`, `email`, `curr_house_street`, `curr_barangay`, `curr_city`, `curr_province`, `curr_zip`, `perm_house_street`, `perm_barangay`, `perm_city`, `perm_province`, `perm_zip`, `elem_name`, `elem_address`, `elem_year_graduated`, `last_school_name`, `last_school_address`, `jhs_name`, `jhs_address`, `jhs_year_graduated`, `guardian_last_name`, `guardian_first_name`, `guardian_middle_name`, `guardian_contact`, `guardian_occupation`, `guardian_address`, `guardian_relationship`, `mother_last_name`, `mother_first_name`, `mother_middle_name`, `mother_contact`, `mother_occupation`, `mother_address`, `father_last_name`, `father_first_name`, `father_middle_name`, `father_contact`, `father_occupation`, `father_address`, `created_at`) VALUES
(1, 'LRN0000001', 'JHS', 'Grade 7', NULL, 'New Student', 1, 'Garcia', 'Pedro', 'A', '', '2012-05-01', 'Antique', 'Catholic', 'Single', 'Male', '09170000001', 'S-LRN0000001@evelio.ams.edu', '123 St', 'Barangay 1', 'San Jose', 'Antique', '5700', '123 St', 'Barangay 1', 'San Jose', 'Antique', '5700', 'Elem 1', 'Addr E1', '2023', 'LastSchool1', 'AddrLS1', '', '', '', 'Garcia', 'Juan', 'A', '09171234567', 'Farmer', 'San Jose', 'Father', 'Garcia', 'Maria', 'B', '09179876543', 'Vendor', 'San Jose', 'Garcia', 'Pedro Sr.', 'C', '09179999999', 'Laborer', 'San Jose', '2025-12-01 05:11:28'),
(2, 'LRN0000002', 'JHS', 'Grade 7', NULL, 'Old Student', 1, 'Santos', 'Ana', 'B', '', '2012-08-10', 'Antique', 'Catholic', 'Single', 'Female', '09170000002', 'S-LRN0000002@evelio.ams.edu', '456 St', 'Barangay 2', 'San Jose', 'Antique', '5700', '456 St', 'Barangay 2', 'San Jose', 'Antique', '5700', 'Elem 2', 'Addr E2', '2023', 'LastSchool2', 'AddrLS2', '', '', '', 'Santos', 'Jose', 'B', '09171234568', 'Driver', 'San Jose', 'Father', 'Santos', 'Maria', 'C', '09179876544', 'Teacher', 'San Jose', 'Santos', 'Jose Sr.', 'D', '09179999998', 'Farmer', 'San Jose', '2025-12-01 05:11:28'),
(3, 'LRN0000003', 'JHS', 'Grade 8', NULL, 'Transferee', 2, 'Lopez', 'Juan', 'C', '', '2011-03-12', 'Antique', 'Catholic', 'Single', 'Male', '09170000003', 'S-LRN0000003@evelio.ams.edu', '789 St', 'Barangay 3', 'San Jose', 'Antique', '5700', '789 St', 'Barangay 3', 'San Jose', 'Antique', '5700', 'Elem 3', 'Addr E3', '2022', 'LastSchool3', 'AddrLS3', '', '', '', 'Lopez', 'Carlos', 'C', '09171234569', 'Carpenter', 'San Jose', 'Uncle', 'Lopez', 'Maria', 'D', '09179876545', 'Nurse', 'San Jose', 'Lopez', 'Carlos Sr.', 'E', '09179999997', 'Farmer', 'San Jose', '2025-12-01 05:11:28'),
(4, 'LRN0000004', 'SHS', 'Grade 11', 'HUMSS', 'New Student', 4, 'Cruz', 'Lara', 'D', '', '2008-11-23', 'Antique', 'Catholic', 'Single', 'Female', '09170000004', 'S-LRN0000004@evelio.ams.edu', '101 St', 'Barangay 4', 'San Jose', 'Antique', '5700', '101 St', 'Barangay 4', 'San Jose', 'Antique', '5700', 'Elem 4', 'Addr E4', '2020', 'LastSchool4', 'AddrLS4', 'JHS School', 'AddrJHS', '2024', 'Cruz', 'Manuel', 'E', '09171234570', 'Clerk', 'San Jose', 'Father', 'Cruz', 'Elena', 'F', '09179876546', 'Officer', 'San Jose', 'Cruz', 'Manuel Sr.', 'G', '09179999996', 'Farmer', 'San Jose', '2025-12-01 05:11:28'),
(5, 'LRN0000005', 'SHS', 'Grade 11', 'TVL', 'Transferee', 5, 'Reyes', 'Marco', 'E', '', '2008-04-30', 'Antique', 'Catholic', 'Single', 'Male', '09170000005', 'S-LRN0000005@evelio.ams.edu', '202 St', 'Barangay 5', 'San Jose', 'Antique', '5700', '202 St', 'Barangay 5', 'San Jose', 'Antique', '5700', 'Elem 5', 'Addr E5', '2020', 'LastSchool5', 'AddrLS5', 'JHS School', 'AddrJHS', '2024', 'Reyes', 'Mario', 'F', '09171234571', 'Cook', 'San Jose', 'Father', 'Reyes', 'Marta', 'G', '09179876547', 'Vendor', 'San Jose', 'Reyes', 'Mario Sr.', 'H', '09179999995', 'Driver', 'San Jose', '2025-12-01 05:11:28'),
(6, 'LRN0000006', 'JHS', 'Grade 7', NULL, 'Old Student', 1, 'Delos Reyes', 'Mia', 'A', '', '2012-02-14', 'Antique', 'Catholic', 'Single', 'Female', '09170000006', 'S-LRN0000006@evelio.ams.edu', '11 St', 'Barangay 1', 'San Jose', 'Antique', '5700', '11 St', 'Barangay 1', 'San Jose', 'Antique', '5700', 'Elem 6', 'Addr E6', '2023', 'LastSchool6', 'AddrLS6', '', '', '', 'Delos Reyes', 'Ramon', 'A', '09171234572', 'Farmer', 'San Jose', 'Father', 'Delos Reyes', 'Luz', 'B', '09179876548', 'Vendor', 'San Jose', 'Delos Reyes', 'Ramon Sr.', 'C', '09179999994', 'Laborer', 'San Jose', '2025-12-01 05:11:28'),
(7, 'LRN0000007', 'JHS', 'Grade 8', NULL, 'New Student', 2, 'Torres', 'Kevin', 'B', '', '2011-07-07', 'Antique', 'Catholic', 'Single', 'Male', '09170000007', 'S-LRN0000007@evelio.ams.edu', '22 St', 'Barangay 3', 'San Jose', 'Antique', '5700', '22 St', 'Barangay 3', 'San Jose', 'Antique', '5700', 'Elem 7', 'Addr E7', '2022', 'LastSchool7', 'AddrLS7', '', '', '', 'Torres', 'Rico', 'B', '09171234573', 'Driver', 'San Jose', 'Father', 'Torres', 'Nina', 'C', '09179876549', 'Teacher', 'San Jose', 'Torres', 'Rico Sr.', 'D', '09179999993', 'Farmer', 'San Jose', '2025-12-01 05:11:28'),
(8, 'LRN0000008', 'JHS', 'Grade 9', NULL, 'Transferee', 3, 'Velasco', 'Rosa', 'C', '', '2010-09-01', 'Antique', 'Catholic', 'Single', 'Female', '09170000008', 'S-LRN0000008@evelio.ams.edu', '33 St', 'Barangay 2', 'San Jose', 'Antique', '5700', '33 St', 'Barangay 2', 'San Jose', 'Antique', '5700', 'Elem 8', 'Addr E8', '2021', 'LastSchool8', 'AddrLS8', '', '', '', 'Velasco', 'Jose', 'D', '09171234574', 'Clerk', 'San Jose', 'Uncle', 'Velasco', 'Marta', 'E', '09179876550', 'Vendor', 'San Jose', 'Velasco', 'Jose Sr.', 'F', '09179999992', 'Fisher', 'San Jose', '2025-12-01 05:11:28'),
(9, 'LRN0000009', 'JHS', 'Grade 7', NULL, 'New Student', 1, 'Gonzales', 'Ella', 'D', '', '2012-03-03', 'Antique', 'Catholic', 'Single', 'Female', '09170000009', 'S-LRN0000009@evelio.ams.edu', '44 St', 'Barangay 1', 'San Jose', 'Antique', '5700', '44 St', 'Barangay 1', 'San Jose', 'Antique', '5700', 'Elem 9', 'Addr E9', '2023', 'LastSchool9', 'AddrLS9', '', '', '', 'Gonzales', 'Luis', 'A', '09171234575', 'Fisher', 'San Jose', 'Father', 'Gonzales', 'Ana', 'B', '09179876551', 'Vendor', 'San Jose', 'Gonzales', 'Luis Sr.', 'C', '09179999991', 'Driver', 'San Jose', '2025-12-01 05:11:28'),
(10, 'LRN0000010', 'JHS', 'Grade 8', NULL, 'Old Student', 2, 'Hernandez', 'Mark', 'E', '', '2011-11-11', 'Antique', 'Catholic', 'Single', 'Male', '09170000010', 'S-LRN0000010@evelio.ams.edu', '55 St', 'Barangay 3', 'San Jose', 'Antique', '5700', '55 St', 'Barangay 3', 'San Jose', 'Antique', '5700', 'Elem 10', 'Addr E10', '2022', 'LastSchool10', 'AddrLS10', '', '', '', 'Hernandez', 'Renato', 'F', '09171234576', 'Carpenter', 'San Jose', 'Father', 'Hernandez', 'Liza', 'G', '09179876552', 'Teacher', 'San Jose', 'Hernandez', 'Renato Sr.', 'H', '09179999990', 'Farmer', 'San Jose', '2025-12-01 05:11:28'),
(11, 'LRN0000011', 'SHS', 'Grade 11', 'HUMSS', 'New Student', 4, 'Sanchez', 'Ivy', 'F', '', '2008-12-12', 'Antique', 'Catholic', 'Single', 'Female', '09170000011', 'S-LRN0000011@evelio.ams.edu', '66 St', 'Barangay 4', 'San Jose', 'Antique', '5700', '66 St', 'Barangay 4', 'San Jose', 'Antique', '5700', 'Elem 11', 'Addr E11', '2020', 'LastSchool11', 'AddrLS11', 'JHS School', 'AddrJHS', '2024', 'Sanchez', 'Mario', 'A', '09171234577', 'Clerk', 'San Jose', 'Father', 'Sanchez', 'Luz', 'B', '09179876553', 'Vendor', 'San Jose', 'Sanchez', 'Mario Sr.', 'C', '09179999989', 'Driver', 'San Jose', '2025-12-01 05:11:28'),
(12, 'LRN0000012', 'SHS', 'Grade 11', 'HUMSS', 'Old Student', 4, 'Ortega', 'Liza', 'G', '', '2009-06-06', 'Antique', 'Catholic', 'Single', 'Female', '09170000012', 'S-LRN0000012@evelio.ams.edu', '77 St', 'Barangay 4', 'San Jose', 'Antique', '5700', '77 St', 'Barangay 4', 'San Jose', 'Antique', '5700', 'Elem 12', 'Addr E12', '2020', 'LastSchool12', 'AddrLS12', 'JHS School', 'AddrJHS', '2024', 'Ortega', 'Ramon', 'B', '09171234578', 'Farmer', 'San Jose', 'Father', 'Ortega', 'Mina', 'C', '09179876554', 'Vendor', 'San Jose', 'Ortega', 'Ramon Sr.', 'D', '09179999988', 'Fisher', 'San Jose', '2025-12-01 05:11:28'),
(13, 'LRN0000013', 'SHS', 'Grade 11', 'TVL', 'New Student', 5, 'Villar', 'Ben', 'H', '', '2009-02-02', 'Antique', 'Catholic', 'Single', 'Male', '09170000013', 'S-LRN0000013@evelio.ams.edu', '88 St', 'Barangay 5', 'San Jose', 'Antique', '5700', '88 St', 'Barangay 5', 'San Jose', 'Antique', '5700', 'Elem 13', 'Addr E13', '2020', 'LastSchool13', 'AddrLS13', 'JHS School', 'AddrJHS', '2024', 'Villar', 'Ruben', 'C', '09171234579', 'Cook', 'San Jose', 'Father', 'Villar', 'Liza', 'D', '09179876555', 'Vendor', 'San Jose', 'Villar', 'Ruben Sr.', 'E', '09179999987', 'Driver', 'San Jose', '2025-12-01 05:11:28'),
(14, 'LRN0000014', 'JHS', 'Grade 9', NULL, 'Old Student', 3, 'Panganiban', 'Tess', 'I', '', '2010-01-15', 'Antique', 'Catholic', 'Single', 'Female', '09170000014', 'S-LRN0000014@evelio.ams.edu', '99 St', 'Barangay 2', 'San Jose', 'Antique', '5700', '99 St', 'Barangay 2', 'San Jose', 'Antique', '5700', 'Elem 14', 'Addr E14', '2021', 'LastSchool14', 'AddrLS14', '', '', '', 'Panganiban', 'Jose', 'A', '09171234580', 'Farmer', 'San Jose', 'Father', 'Panganiban', 'Mara', 'B', '09179876556', 'Vendor', 'San Jose', 'Panganiban', 'Jose Sr.', 'C', '09179999986', 'Driver', 'San Jose', '2025-12-01 05:11:28'),
(15, 'LRN0000015', 'JHS', 'Grade 7', NULL, 'Transferee', 4, 'Aguilar', 'Nico', 'J', '', '2012-10-10', 'Antique', 'Catholic', 'Single', 'Male', '09170000015', 'S-LRN0000015@evelio.ams.edu', '100 St', 'Barangay 1', 'San Jose', 'Antique', '5700', '100 St', 'Barangay 1', 'San Jose', 'Antique', '5700', 'Elem 15', 'Addr E15', '2023', 'LastSchool15', 'AddrLS15', '', '', '', 'Aguilar', 'Rico', 'A', '09171234581', 'Driver', 'San Jose', 'Father', 'Aguilar', 'Mona', 'B', '09179876557', 'Vendor', 'San Jose', 'Aguilar', 'Rico Sr.', 'C', '09179999985', 'Farmer', 'San Jose', '2025-12-01 05:11:28'),
(16, 'LRN0000016', 'JHS', 'Grade 8', NULL, 'Old Student', 2, 'Castro', 'Bea', 'K', '', '2011-05-05', 'Antique', 'Catholic', 'Single', 'Female', '09170000016', 'S-LRN0000016@evelio.ams.edu', '101A St', 'Barangay 3', 'San Jose', 'Antique', '5700', '101A St', 'Barangay 3', 'San Jose', 'Antique', '5700', 'Elem 16', 'Addr E16', '2022', 'LastSchool16', 'AddrLS16', '', '', '', 'Castro', 'Lito', 'A', '09171234582', 'Carpenter', 'San Jose', 'Father', 'Castro', 'Mila', 'B', '09179876558', 'Vendor', 'San Jose', 'Castro', 'Lito Sr.', 'C', '09179999984', 'Laborer', 'San Jose', '2025-12-01 05:11:28'),
(17, 'LRN0000017', 'JHS', 'Grade 9', NULL, 'New Student', 3, 'Garcia', 'Zara', 'L', '', '2010-04-04', 'Antique', 'Catholic', 'Single', 'Female', '09170000017', 'S-LRN0000017@evelio.ams.edu', '102 St', 'Barangay 2', 'San Jose', 'Antique', '5700', '102 St', 'Barangay 2', 'San Jose', 'Antique', '5700', 'Elem 17', 'Addr E17', '2021', 'LastSchool17', 'AddrLS17', '', '', '', 'Garcia', 'Ramon', 'A', '09171234583', 'Fisher', 'San Jose', 'Father', 'Garcia', 'Lola', 'B', '09179876559', 'Vendor', 'San Jose', 'Garcia', 'Ramon Sr.', 'C', '09179999983', 'Farmer', 'San Jose', '2025-12-01 05:11:28'),
(18, 'LRN0000018', 'SHS', 'Grade 11', 'HUMSS', 'Transferee', 4, 'Lacson', 'Ian', 'M', '', '2008-06-06', 'Antique', 'Catholic', 'Single', 'Male', '09170000018', 'S-LRN0000018@evelio.ams.edu', '103 St', 'Barangay 4', 'San Jose', 'Antique', '5700', '103 St', 'Barangay 4', 'San Jose', 'Antique', '5700', 'Elem 18', 'Addr E18', '2020', 'LastSchool18', 'AddrLS18', 'JHS School', 'AddrJHS', '2024', 'Lacson', 'Tony', 'A', '09171234584', 'Driver', 'San Jose', 'Father', 'Lacson', 'Nina', 'B', '09179876560', 'Vendor', 'San Jose', 'Lacson', 'Tony Sr.', 'C', '09179999982', 'Laborer', 'San Jose', '2025-12-01 05:11:28'),
(19, 'LRN0000019', 'SHS', 'Grade 11', 'TVL', 'Old Student', 5, 'Manalo', 'Kylie', 'N', '', '2009-09-09', 'Antique', 'Catholic', 'Single', 'Female', '09170000019', 'S-LRN0000019@evelio.ams.edu', '104 St', 'Barangay 5', 'San Jose', 'Antique', '5700', '104 St', 'Barangay 5', 'San Jose', 'Antique', '5700', 'Elem 19', 'Addr E19', '2020', 'LastSchool19', 'AddrLS19', 'JHS School', 'AddrJHS', '2024', 'Manalo', 'Dante', 'A', '09171234585', 'Cook', 'San Jose', 'Father', 'Manalo', 'Ria', 'B', '09179876561', 'Vendor', 'San Jose', 'Manalo', 'Dante Sr.', 'C', '09179999981', 'Driver', 'San Jose', '2025-12-01 05:11:28'),
(20, 'LRN0000020', 'JHS', 'Grade 7', NULL, 'New Student', 1, 'Flores', 'Ralph', 'O', '', '2012-12-12', 'Antique', 'Catholic', 'Single', 'Male', '09170000020', 'S-LRN0000020@evelio.ams.edu', '105 St', 'Barangay 1', 'San Jose', 'Antique', '5700', '105 St', 'Barangay 1', 'San Jose', 'Antique', '5700', 'Elem 20', 'Addr E20', '2023', 'LastSchool20', 'AddrLS20', '', '', '', 'Flores', 'Nora', 'A', '09171234586', 'Vendor', 'San Jose', 'Mother', 'Flores', 'Ruben', 'B', '09179876562', 'Vendor', 'San Jose', 'Flores', 'Ruben Sr.', 'C', '09179999980', 'Driver', 'San Jose', '2025-12-01 05:11:28'),
(21, '542985746248', 'JHS', 'Grade 7', NULL, 'Transferee', 6, 'asdasd', 'fdg', '234', NULL, '2025-12-12', 'wqsd', 'sfwe', 'Single', 'Male', '09262559507', 'richardmadrid11042004@gmail.com', '#34 kalapati street', 'Dizon Subdivision', 'baguio city', 'benguet', '2600', '#34 kalapati street', 'Dizon Subdivision', 'baguio city', 'benguet', '2600', 'Lucban Elementary School', 'asdasdasdasdasd', '2016', 'Lucban Elementary School', 'asdasdasdasdasd', '', '', '', 'uiaw', 'iqwuyhiu', 'iusahu', '09321655423', 'business woman', 'asdasdasdasdasd', 'Parent', 'uiaw', 'iqwuyhiu', 'iusahu', '09321655423', 'business woman', 'asdasdasdasdasd', '.', '.', '.', '.', '.', '.', '2025-12-01 05:27:46');

-- --------------------------------------------------------

--
-- Table structure for table `subjects`
--

CREATE TABLE `subjects` (
  `id` int(11) NOT NULL,
  `code` varchar(50) NOT NULL,
  `name` varchar(150) NOT NULL,
  `department` enum('JHS','SHS') NOT NULL,
  `grade_level` enum('Grade 7','Grade 8','Grade 9','Grade 10','Grade 11','Grade 12') NOT NULL,
  `strand` enum('HUMSS','TVL') DEFAULT NULL,
  `semester` enum('First','Second') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `subjects`
--

INSERT INTO `subjects` (`id`, `code`, `name`, `department`, `grade_level`, `strand`, `semester`) VALUES
(1, 'ENG7', 'English 7', 'JHS', 'Grade 7', NULL, 'First'),
(2, 'MATH7', 'Mathematics 7', 'JHS', 'Grade 7', NULL, 'First'),
(3, 'SCI7', 'Science 7', 'JHS', 'Grade 7', NULL, 'Second'),
(4, 'HIST8', 'History 8', 'JHS', 'Grade 8', NULL, 'First'),
(5, 'MATH8', 'Mathematics 8', 'JHS', 'Grade 8', NULL, 'Second'),
(6, 'ENG9', 'English 9', 'JHS', 'Grade 9', NULL, 'First'),
(7, 'HUMSS11-ENG', 'HUMSS English', 'SHS', 'Grade 11', 'HUMSS', 'First'),
(8, 'HUMSS11-SS', 'HUMSS Social Studies', 'SHS', 'Grade 11', 'HUMSS', 'Second'),
(9, 'TVL11-TECH', 'TVL Tech', 'SHS', 'Grade 11', 'TVL', 'First'),
(10, 'COMP11', 'Computer Applications', 'SHS', 'Grade 11', 'TVL', 'Second');

-- --------------------------------------------------------

--
-- Table structure for table `subject_loads`
--

CREATE TABLE `subject_loads` (
  `id` int(11) NOT NULL,
  `teacher_id` int(11) NOT NULL,
  `subject_id` int(11) NOT NULL,
  `section_id` int(11) NOT NULL,
  `school_year` varchar(20) NOT NULL,
  `semester` enum('First','Second') DEFAULT NULL,
  `active` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `subject_loads`
--

INSERT INTO `subject_loads` (`id`, `teacher_id`, `subject_id`, `section_id`, `school_year`, `semester`, `active`) VALUES
(1, 1, 1, 1, '2024-2025', 'First', 1),
(2, 2, 2, 1, '2024-2025', 'First', 1),
(3, 3, 4, 2, '2024-2025', 'First', 1),
(4, 4, 6, 3, '2024-2025', 'First', 1),
(5, 5, 7, 4, '2024-2025', 'First', 1),
(6, 1, 3, 1, '2024-2025', 'Second', 1),
(7, 2, 5, 2, '2024-2025', 'Second', 1),
(8, 3, 8, 4, '2024-2025', 'Second', 1),
(9, 4, 9, 5, '2024-2025', 'First', 1),
(10, 5, 10, 5, '2024-2025', 'Second', 1),
(11, 2, 10, 4, '202502026', 'First', 1),
(12, 2, 10, 4, '202502026', 'First', 1),
(13, 2, 10, 4, '202502026', 'First', 1),
(14, 2, 10, 4, '202502026', 'First', 1),
(15, 2, 10, 4, '202502026', 'First', 1),
(16, 2, 10, 4, '202502026', 'First', 1),
(17, 2, 10, 4, '202502026', 'First', 1),
(18, 2, 10, 4, '202502026', 'First', 1),
(19, 2, 10, 4, '202502026', 'First', 1),
(20, 2, 10, 4, '202502026', 'First', 1),
(21, 2, 10, 4, '202502026', 'First', 1),
(22, 2, 10, 4, '202502026', 'First', 1),
(23, 2, 10, 4, '202502026', 'First', 1),
(24, 2, 10, 4, '202502026', 'First', 1),
(25, 2, 10, 4, '202502026', 'First', 1),
(26, 5, 9, 4, '2025-2026', 'First', 1),
(27, 5, 9, 4, '2025-2026', 'First', 1),
(28, 5, 9, 4, '2025-2026', 'First', 1),
(29, 5, 9, 4, '2025-2026', 'First', 1),
(30, 5, 9, 4, '2025-2026', 'First', 1),
(31, 7, 6, 5, '2025-2026', NULL, 1),
(32, 7, 6, 5, '2025-2026', NULL, 1),
(33, 7, 6, 5, '2025-2026', NULL, 1),
(34, 7, 6, 5, '2025-2026', NULL, 1),
(35, 7, 6, 5, '2025-2026', NULL, 1);

-- --------------------------------------------------------

--
-- Table structure for table `teachers`
--

CREATE TABLE `teachers` (
  `id` int(11) NOT NULL,
  `faculty_id` varchar(50) NOT NULL,
  `full_name` varchar(150) NOT NULL,
  `username` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `sex` enum('Male','Female') NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT 1,
  `advisory_section_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `teachers`
--

INSERT INTO `teachers` (`id`, `faculty_id`, `full_name`, `username`, `email`, `sex`, `active`, `advisory_section_id`, `created_at`) VALUES
(1, 'FAC-101', 'Jose Rizal', 'trizal', 'jrizal@evelio.ams.edu', 'Male', 1, 6, '2025-12-01 05:11:27'),
(2, 'FAC-102', 'Andres Bonifacio', 'abonifacio', 'abonifacio@evelio.ams.edu', 'Male', 1, NULL, '2025-12-01 05:11:27'),
(3, 'FAC-103', 'Gabriela Silang', 'gsilang', 'gsilang@evelio.ams.edu', 'Female', 1, NULL, '2025-12-01 05:11:27'),
(4, 'FAC-104', 'Emilio Jacinto', 'ejacinto', 'ejacinto@evelio.ams.edu', 'Male', 1, NULL, '2025-12-01 05:11:27'),
(5, 'FAC-105', 'Melchora Aquino', 'maquino', 'maquino@evelio.ams.edu', 'Female', 1, NULL, '2025-12-01 05:11:27'),
(6, 'FAC-0056', 'dimple layacan', 'divina', 'dimplelayacan59@gmail.com', 'Female', 1, NULL, '2025-12-01 05:28:15'),
(7, '123', '1', '1', '123123123123123@gmail.com', 'Male', 1, NULL, '2025-12-01 05:51:59'),
(8, '456456', 'asd', 'awd', 'asdasdasd@gmail.com', 'Male', 1, NULL, '2025-12-01 05:52:16');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `accounts`
--
ALTER TABLE `accounts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `username` (`username`),
  ADD KEY `idx_person` (`person_type`,`person_id`);

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `faculty_id` (`faculty_id`);

--
-- Indexes for table `applicants`
--
ALTER TABLE `applicants`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `applicant_history`
--
ALTER TABLE `applicant_history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_applicant_action` (`applicant_id`,`action`);

--
-- Indexes for table `attendance`
--
ALTER TABLE `attendance`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_attendance` (`student_id`,`subject_load_id`,`date`),
  ADD KEY `subject_load_id` (`subject_load_id`);

--
-- Indexes for table `enrollments`
--
ALTER TABLE `enrollments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `student_id` (`student_id`),
  ADD KEY `subject_load_id` (`subject_load_id`);

--
-- Indexes for table `grades`
--
ALTER TABLE `grades`
  ADD PRIMARY KEY (`id`),
  ADD KEY `enrollment_id` (`enrollment_id`);

--
-- Indexes for table `sections`
--
ALTER TABLE `sections`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `lrn` (`lrn`),
  ADD KEY `idx_students_section` (`advisory_section_id`);

--
-- Indexes for table `subjects`
--
ALTER TABLE `subjects`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`);

--
-- Indexes for table `subject_loads`
--
ALTER TABLE `subject_loads`
  ADD PRIMARY KEY (`id`),
  ADD KEY `teacher_id` (`teacher_id`),
  ADD KEY `subject_id` (`subject_id`),
  ADD KEY `section_id` (`section_id`);

--
-- Indexes for table `teachers`
--
ALTER TABLE `teachers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `faculty_id` (`faculty_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `accounts`
--
ALTER TABLE `accounts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `applicants`
--
ALTER TABLE `applicants`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `applicant_history`
--
ALTER TABLE `applicant_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `attendance`
--
ALTER TABLE `attendance`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=156;

--
-- AUTO_INCREMENT for table `enrollments`
--
ALTER TABLE `enrollments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=54;

--
-- AUTO_INCREMENT for table `grades`
--
ALTER TABLE `grades`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

--
-- AUTO_INCREMENT for table `sections`
--
ALTER TABLE `sections`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `subjects`
--
ALTER TABLE `subjects`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `subject_loads`
--
ALTER TABLE `subject_loads`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT for table `teachers`
--
ALTER TABLE `teachers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `attendance`
--
ALTER TABLE `attendance`
  ADD CONSTRAINT `attendance_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `attendance_ibfk_2` FOREIGN KEY (`subject_load_id`) REFERENCES `subject_loads` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `enrollments`
--
ALTER TABLE `enrollments`
  ADD CONSTRAINT `enrollments_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `enrollments_ibfk_2` FOREIGN KEY (`subject_load_id`) REFERENCES `subject_loads` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `grades`
--
ALTER TABLE `grades`
  ADD CONSTRAINT `grades_ibfk_1` FOREIGN KEY (`enrollment_id`) REFERENCES `enrollments` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `subject_loads`
--
ALTER TABLE `subject_loads`
  ADD CONSTRAINT `subject_loads_ibfk_1` FOREIGN KEY (`teacher_id`) REFERENCES `teachers` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `subject_loads_ibfk_2` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `subject_loads_ibfk_3` FOREIGN KEY (`section_id`) REFERENCES `sections` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
