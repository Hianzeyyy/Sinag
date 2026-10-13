-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3307
-- Generation Time: Oct 09, 2026 at 02:56 AM
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
-- Database: `sinag_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `announcements`
--

CREATE TABLE `announcements` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `body` text NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `appointment_requests`
--

CREATE TABLE `appointment_requests` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `student_id` varchar(255) DEFAULT NULL,
  `purpose_of_visit` varchar(255) NOT NULL,
  `urgency_level` enum('Low','Medium','High') NOT NULL,
  `scheduled_date` date NOT NULL,
  `scheduled_time` time NOT NULL,
  `description` longtext NOT NULL,
  `format` enum('Face-to-Face','Online') NOT NULL,
  `status` enum('pending','completed','approved','rejected') NOT NULL DEFAULT 'pending',
  `admin_notes` longtext DEFAULT NULL,
  `cancellation_reason` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `appointment_requests`
--

INSERT INTO `appointment_requests` (`id`, `user_id`, `full_name`, `student_id`, `purpose_of_visit`, `urgency_level`, `scheduled_date`, `scheduled_time`, `description`, `format`, `status`, `admin_notes`, `cancellation_reason`, `created_at`, `updated_at`) VALUES
(2, 4, 'Angeline Dela Cruz Ambrosio', '23as2050', 'Not specified', 'Medium', '2026-07-01', '05:57:19', 'xcvbnerbfubwhbfcjhbfhbbefhjebjf', 'Face-to-Face', 'pending', NULL, NULL, '2026-06-30 21:57:19', '2026-06-30 21:57:19'),
(3, 4, 'Angeline Dela Cruz Ambrosio', '23as2050', 'Not specified', 'High', '2026-07-01', '05:57:59', 'qweww4wrwrrww', 'Face-to-Face', 'pending', NULL, NULL, '2026-06-30 21:57:59', '2026-06-30 21:57:59'),
(4, 11, 'Test User', '23as2050', 'Not specified', 'Medium', '2026-09-16', '12:04:00', 'concern', 'Face-to-Face', 'pending', NULL, NULL, '2026-09-09 00:47:37', '2026-09-14 19:24:25');

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `cache`
--

INSERT INTO `cache` (`key`, `value`, `expiration`) VALUES
('sinag-cache-23as2050@psu.edu.ph|127.0.0.1', 'i:2;', 1791385072),
('sinag-cache-23as2050@psu.edu.ph|127.0.0.1:timer', 'i:1791385072;', 1791385072),
('sinag-cache-e81bbb5ccfa07ef8a15108e624d2b92a', 'i:1;', 1790142588),
('sinag-cache-e81bbb5ccfa07ef8a15108e624d2b92a:timer', 'i:1790142588;', 1790142588);

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `gad_schedules`
--

CREATE TABLE `gad_schedules` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `location` varchar(255) NOT NULL,
  `available_from` datetime NOT NULL,
  `available_until` datetime DEFAULT NULL,
  `details` text DEFAULT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'active',
  `created_by` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `gad_schedules`
--

INSERT INTO `gad_schedules` (`id`, `title`, `location`, `available_from`, `available_until`, `details`, `status`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 'Office Available', 'GAD Office', '2026-05-14 00:00:00', '2026-05-14 23:59:00', NULL, 'active', 2, '2026-05-12 16:02:50', '2026-05-12 16:02:50'),
(2, 'Office Available', 'GAD Office', '2026-05-05 00:00:00', '2026-05-05 23:59:00', NULL, 'active', 2, '2026-05-12 16:02:50', '2026-05-12 16:02:50'),
(3, 'Office Available', 'GAD Office', '2026-05-06 00:00:00', '2026-05-06 23:59:00', NULL, 'active', 2, '2026-05-12 16:02:50', '2026-05-12 16:02:50'),
(4, 'Office Available', 'GAD Office', '2026-05-18 00:00:00', '2026-05-18 23:59:00', NULL, 'active', 2, '2026-05-12 16:02:50', '2026-05-12 16:02:50'),
(5, 'Office Available', 'GAD Office', '2026-05-19 00:00:00', '2026-05-19 23:59:00', NULL, 'active', 2, '2026-05-12 16:02:50', '2026-05-12 16:02:50'),
(6, 'Office Available', 'GAD Office', '2026-05-28 00:00:00', '2026-05-28 23:59:00', NULL, 'active', 2, '2026-05-12 16:02:50', '2026-05-12 16:02:50'),
(7, 'Office Available', 'GAD Office', '2026-05-29 00:00:00', '2026-05-29 23:59:00', NULL, 'active', 2, '2026-05-12 16:02:50', '2026-05-12 16:02:50'),
(8, 'Office Hours', 'GAD Office', '2026-06-01 09:00:00', '2026-06-01 17:00:00', NULL, 'active', 2, '2026-06-02 18:19:48', '2026-06-02 18:19:48'),
(9, 'Office Hours', 'GAD Office', '2026-07-01 09:00:00', '2026-07-01 17:00:00', NULL, 'active', 2, '2026-07-06 23:18:42', '2026-07-06 23:18:42'),
(10, 'Unavailable / Closed', 'GAD Office', '2026-10-13 00:00:00', '2026-10-13 23:59:59', 'unavailable', 'unavailable', 2, '2026-10-08 06:16:27', '2026-10-08 06:16:27'),
(11, 'Office Hours', 'GAD Office', '2026-10-14 09:00:00', '2026-10-14 12:00:00', 'specific_hours', 'active', 2, '2026-10-08 06:16:27', '2026-10-08 06:16:27'),
(12, 'Available (All Day)', 'GAD Office', '2026-10-15 08:00:00', '2026-10-15 17:00:00', 'all_day', 'active', 2, '2026-10-08 06:16:27', '2026-10-08 06:16:27');

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `sender_id` bigint(20) UNSIGNED NOT NULL,
  `receiver_id` bigint(20) UNSIGNED NOT NULL,
  `message` text NOT NULL,
  `sender_type` enum('student','admin') NOT NULL DEFAULT 'student',
  `read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `messages`
--

INSERT INTO `messages` (`id`, `sender_id`, `receiver_id`, `message`, `sender_type`, `read_at`, `created_at`, `updated_at`) VALUES
(2, 4, 2, 'hello admin', 'student', '2026-09-07 20:50:40', '2026-08-23 20:18:42', '2026-09-07 20:50:40'),
(3, 11, 2, 'Hello GAD admin', 'student', '2026-09-09 00:55:58', '2026-09-08 01:27:35', '2026-09-09 00:55:58'),
(4, 20, 2, 'hello admin', 'student', NULL, '2026-09-22 21:24:03', '2026-09-22 21:24:03'),
(5, 4, 2, 'hello po', 'student', '2026-10-05 23:58:42', '2026-10-05 23:45:34', '2026-10-05 23:58:42'),
(6, 4, 2, 'i have concern today', 'student', '2026-10-05 23:58:42', '2026-10-05 23:45:43', '2026-10-05 23:58:42');

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2026_03_13_060405_create_reports_table', 1),
(5, '2026_03_13_061927_create_suggestions_table', 1),
(6, '2026_03_26_000001_create_suggestion_user_upvotes_table', 1),
(7, '2026_03_26_100000_create_suggestion_comments_table', 1),
(8, '2026_03_28_000001_create_announcements_table', 1),
(9, '2026_04_06_090000_add_seen_at_to_reports_table', 1),
(10, '2026_04_06_100000_add_verification_fields_to_users_table', 1),
(11, '2026_04_21_000002_add_course_and_gender_to_reports_table', 1),
(12, '2026_04_21_000003_add_incident_time_to_reports_table', 1),
(13, '2026_04_23_000004_add_profile_photo_path_to_users_table', 1),
(14, '2026_04_28_090000_add_age_and_gender_to_users_table', 1),
(15, '2026_04_29_000001_add_department_to_users_table', 1),
(16, '2026_04_29_000002_create_messages_table', 1),
(17, '2026_04_30_000001_create_gad_schedules_table', 1),
(18, '2026_04_30_000002_add_account_type_to_users_table', 1),
(19, '2026_05_02_040516_create_appointment_requests_table', 1),
(20, '2026_05_03_040726_add_employee_role_to_users_table', 1),
(21, '2026_05_12_000001_add_missing_registration_fields_to_users_table', 1),
(22, '2026_05_12_083759_add_split_name_fields_to_users_table', 1),
(23, '2026_05_12_090000_add_personal_data_sections_to_users_table', 1),
(24, '2026_07_01_000001_add_completed_status_to_appointment_requests_table', 2),
(25, '2026_09_14_000001_add_employee_category_to_users_table', 2),
(26, '2026_09_14_000002_add_availability_status_to_users_table', 2),
(27, '2026_09_14_000003_add_cancellation_reason_to_appointment_requests_table', 3),
(28, '2026_09_14_120000_add_last_seen_at_to_users_table', 4),
(29, '2026_09_14_130000_create_password_reset_requests_table', 5),
(30, '2026_09_14_140000_create_suggestion_reports_table', 6);

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_requests`
--

CREATE TABLE `password_reset_requests` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `reference_number` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `student_employee_id` varchar(255) NOT NULL,
  `department` varchar(255) DEFAULT NULL,
  `id_picture_path` varchar(255) NOT NULL,
  `reason` text DEFAULT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'pending',
  `admin_id` bigint(20) UNSIGNED DEFAULT NULL,
  `admin_remarks` text DEFAULT NULL,
  `reset_token_hash` varchar(255) DEFAULT NULL,
  `reset_token_expires_at` timestamp NULL DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `completed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `password_reset_requests`
--

INSERT INTO `password_reset_requests` (`id`, `user_id`, `reference_number`, `email`, `name`, `student_employee_id`, `department`, `id_picture_path`, `reason`, `status`, `admin_id`, `admin_remarks`, `reset_token_hash`, `reset_token_expires_at`, `approved_at`, `completed_at`, `created_at`, `updated_at`) VALUES
(1, 11, 'PRR-SVMT1GD5QP', '29as2090_ms@psu.edu.ph', 'Test User', '29-AS-2090', 'BS Information Technology (BSIT)', 'password-reset-requests/KxMrH1bmPhyibFTXpvbE0FrcamdvbkIetrIlyU1r.jpg', 'nakalimutan', 'pending', NULL, NULL, NULL, NULL, NULL, NULL, '2026-09-14 06:33:09', '2026-09-14 06:33:09'),
(2, 11, 'PRR-OMC3CT8JUK', '29as2090_ms@psu.edu.ph', 'Test User', '29-AS-2090', 'BS Information Technology (BSIT)', 'password-reset-requests/cKjRi1pC8ASZPDJs4YOlFfqiRpNQuqBe7Aa56r1k.jpg', 'nakalimutan', 'completed', 2, NULL, NULL, NULL, '2026-09-14 06:34:49', '2026-09-14 06:38:12', '2026-09-14 06:33:58', '2026-09-14 06:38:12');

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `password_reset_tokens`
--

INSERT INTO `password_reset_tokens` (`email`, `token`, `created_at`) VALUES
('23as2050_ms@psu.edu.ph', '$2y$12$g0hQah30IkpAwEu9101Oue3XQw9jLHjoU8yJdNKtjdeysjU4zCPDG', '2026-09-08 23:45:00'),
('admin@psu.edu.ph', '$2y$12$HL07lsZhgl4jkEnCZMmg9OdfY27lb/p0.nqDlcDQKzfKPq4PaskJ.', '2026-09-13 23:31:31');

-- --------------------------------------------------------

--
-- Table structure for table `reports`
--

CREATE TABLE `reports` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `incident_id` varchar(255) NOT NULL,
  `cloak_alias` varchar(255) NOT NULL,
  `nature` varchar(255) NOT NULL,
  `course` varchar(120) DEFAULT NULL,
  `gender` varchar(30) DEFAULT NULL,
  `incident_date` date DEFAULT NULL,
  `incident_time` time DEFAULT NULL,
  `location` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `priority` varchar(255) NOT NULL DEFAULT 'Medium',
  `status` varchar(255) NOT NULL DEFAULT 'Pending',
  `seen_at` timestamp NULL DEFAULT NULL,
  `evidence` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`evidence`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `reports`
--

INSERT INTO `reports` (`id`, `user_id`, `incident_id`, `cloak_alias`, `nature`, `course`, `gender`, `incident_date`, `incident_time`, `location`, `description`, `priority`, `status`, `seen_at`, `evidence`, `created_at`, `updated_at`) VALUES
(3, 4, 'SN-SKCLSA', 'StudentQ2KW', 'Cyber-harassment', NULL, NULL, '2026-09-03', '13:08:00', 'psu asingan campus', 'Tell us', 'Medium', 'Pending', '2026-09-14 09:11:52', '[]', '2026-09-03 21:06:05', '2026-09-14 09:11:52'),
(4, 4, 'SN-KDUWA0', 'StudentQ2KW', 'Catcalling', NULL, NULL, '2026-09-04', '15:36:00', 'psu', 'Catcalling', 'Medium', 'Pending', '2026-09-03 23:33:54', '[]', '2026-09-03 23:31:07', '2026-09-03 23:33:54'),
(5, 4, 'SN-XK6YCA', 'StudentQ2KW', 'Kinagat ng pusang ligaw', NULL, NULL, '2026-09-06', '22:44:00', 'psu', 'qwertyopsdfghjklzxcvbnm', 'Medium', 'Pending', '2026-09-14 07:00:03', '[]', '2026-09-06 06:44:32', '2026-09-14 07:00:03'),
(6, 11, 'SN-EB2M5F', 'StudentJQ2Z', 'Cyber-harassment', NULL, NULL, '2026-09-15', '11:27:00', 'psu', 'This is my report', 'Medium', 'Pending', '2026-09-15 07:34:57', '[]', '2026-09-15 07:28:07', '2026-09-15 07:34:57'),
(7, 11, 'SN-YKDSOM', 'StudentJQ2Z', 'Stalking', NULL, NULL, '2026-03-21', '11:25:00', 'psu asingan campus', 'someone is stalking to me', 'Medium', 'Pending', '2026-09-22 21:52:20', '[]', '2026-09-22 21:10:57', '2026-09-22 21:52:20'),
(8, 4, 'SN-VSCWPI', 'StudentQ2KW', 'Stalking', NULL, NULL, '2026-02-09', '22:03:00', 'psu', 'I am reporting an incident where I received intimidating messages after expressing a concern about an inappropriate situation. I was warned not to report the matter further, which made me feel afraid and uncomfortable. I would like to request assistance and  uncomfortable while going to school.', 'Medium', 'Resolved', '2026-10-05 23:55:52', '[\"evidence\\/onTmpXRzAVOHNAYPeqRMpGZ4i8UyqvtnUS1lSicC.jpg\"]', '2026-09-29 06:06:33', '2026-10-06 22:29:32');

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `suggestions`
--

CREATE TABLE `suggestions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `category` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `is_anonymous` tinyint(1) NOT NULL DEFAULT 1,
  `status` varchar(255) NOT NULL DEFAULT 'New',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `suggestions`
--

INSERT INTO `suggestions` (`id`, `user_id`, `category`, `message`, `is_anonymous`, `status`, `created_at`, `updated_at`) VALUES
(5, 4, 'Facilities Improvement', 'Fix lights', 0, 'Approved', '2026-09-03 23:31:46', '2026-09-09 00:43:55'),
(6, 4, 'Facilities Improvement', 'sana maayos na ang public cr ng mga student', 0, 'Rejected', '2026-09-06 06:45:37', '2026-09-09 00:20:00'),
(7, 13, 'Facilities Improvement', 'One important improvement for PWD accessibility is the installation of a safe and accessible ramp beside the stairs to help wheelchair users, senior citizens, and people with mobility difficulties enter and move around the building independently. The ramp should have a gentle slope, sturdy handrails, proper lighting, and a non-slip surface to prevent accidents, especially during rainy weather. It must also be wide enough for wheelchair users to pass through comfortably without difficulty. Resting platforms should be provided if the ramp is long, allowing users to stop and regain their strength when needed. Clear signs should also be placed to guide PWDs toward the accessible entrance. If there is not enough space for a ramp, a stairlift or platform lift can be installed as an alternative. These improvements will make the building safer, more inclusive, and easier to use for everyone. Providing accessible pathways shows respect for the rights and needs of persons with disabilities and promotes equal opportunities in public spaces', 0, 'Pending', '2026-09-14 10:36:42', '2026-09-14 10:36:42');

-- --------------------------------------------------------

--
-- Table structure for table `suggestion_comments`
--

CREATE TABLE `suggestion_comments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `suggestion_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `comment` text NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `suggestion_comments`
--

INSERT INTO `suggestion_comments` (`id`, `suggestion_id`, `user_id`, `comment`, `created_at`, `updated_at`) VALUES
(1, 5, 11, 'tamaa agree ako dyan', '2026-09-08 02:36:23', '2026-09-08 02:36:23'),
(2, 6, 11, 'okay', '2026-09-09 00:38:32', '2026-09-09 00:38:32');

-- --------------------------------------------------------

--
-- Table structure for table `suggestion_reports`
--

CREATE TABLE `suggestion_reports` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `reporter_id` bigint(20) UNSIGNED NOT NULL,
  `suggestion_id` bigint(20) UNSIGNED DEFAULT NULL,
  `comment_id` bigint(20) UNSIGNED DEFAULT NULL,
  `reason` varchar(500) NOT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'pending',
  `reviewed_by` bigint(20) UNSIGNED DEFAULT NULL,
  `reviewed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `suggestion_reports`
--

INSERT INTO `suggestion_reports` (`id`, `reporter_id`, `suggestion_id`, `comment_id`, `reason`, `status`, `reviewed_by`, `reviewed_at`, `created_at`, `updated_at`) VALUES
(1, 11, 6, NULL, 'bad words', 'pending', NULL, NULL, '2026-09-14 09:21:17', '2026-09-14 09:21:17'),
(2, 13, 6, 2, 'bad comments he said the word okay', 'pending', NULL, NULL, '2026-09-14 10:33:24', '2026-09-14 10:33:24');

-- --------------------------------------------------------

--
-- Table structure for table `suggestion_user_upvotes`
--

CREATE TABLE `suggestion_user_upvotes` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `suggestion_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `suggestion_user_upvotes`
--

INSERT INTO `suggestion_user_upvotes` (`id`, `suggestion_id`, `user_id`, `created_at`, `updated_at`) VALUES
(3, 5, 11, NULL, NULL),
(4, 7, 11, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `last_name` varchar(255) DEFAULT NULL,
  `first_name` varchar(255) DEFAULT NULL,
  `middle_name` varchar(255) DEFAULT NULL,
  `age` tinyint(3) UNSIGNED DEFAULT NULL,
  `gender` varchar(255) DEFAULT NULL,
  `department` varchar(255) DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `phone_number` varchar(255) DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('student','admin','security','employee') NOT NULL,
  `account_type` varchar(255) NOT NULL DEFAULT 'student',
  `employee_category` varchar(255) DEFAULT NULL,
  `cloak_alias` varchar(255) DEFAULT NULL,
  `id_image_path` varchar(255) DEFAULT NULL,
  `selfie_image_path` varchar(255) DEFAULT NULL,
  `profile_photo_path` varchar(255) DEFAULT NULL,
  `account_status` varchar(255) NOT NULL DEFAULT 'pending',
  `availability_status` varchar(255) NOT NULL DEFAULT 'active',
  `last_seen_at` timestamp NULL DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `approved_by` bigint(20) UNSIGNED DEFAULT NULL,
  `rejection_reason` text DEFAULT NULL,
  `education` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`education`)),
  `personal_information` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`personal_information`)),
  `family_background` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`family_background`)),
  `student_information` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`student_information`)),
  `employee_education` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`employee_education`)),
  `civil_service_eligibility` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`civil_service_eligibility`)),
  `work_experience` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`work_experience`)),
  `voluntary_work` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`voluntary_work`)),
  `gad_training` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`gad_training`)),
  `other_information` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`other_information`)),
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `last_name`, `first_name`, `middle_name`, `age`, `gender`, `department`, `email`, `phone_number`, `email_verified_at`, `password`, `role`, `account_type`, `employee_category`, `cloak_alias`, `id_image_path`, `selfie_image_path`, `profile_photo_path`, `account_status`, `availability_status`, `last_seen_at`, `approved_at`, `approved_by`, `rejection_reason`, `education`, `personal_information`, `family_background`, `student_information`, `employee_education`, `civil_service_eligibility`, `work_experience`, `voluntary_work`, `gad_training`, `other_information`, `remember_token`, `created_at`, `updated_at`) VALUES
(2, 'GAD Admin', 'Admin', 'GAD', NULL, NULL, NULL, 'GAD Office', 'admin@psu.edu.ph', '09000000000', NULL, '$2y$12$XVSS4U2Hc5eGQbjJGFrQhuywllgcRXtOUJzprHqSS5xGCy2DLL5ze', 'admin', 'admin', NULL, NULL, NULL, NULL, NULL, 'active', 'active', '2026-10-08 06:16:28', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'yxhU7HGes4wvCtPHDIWeIhMzIGbpsidfO6bdRyNztuAI1PM0STST1UkHIxl5', '2026-05-12 15:30:43', '2026-10-08 06:16:28'),
(4, 'Angeline Dela Cruz Ambrosio', 'Ambrosio', 'Angeline', 'Dela Cruz', 21, 'Female', 'BS Information Technology (BSIT)', '23as2050_ms@psu.edu.ph', '09305346878', NULL, '$2y$12$zTYLcAbFYqNtI3oi6Q8v1eJsUsFglq64sRlrMo5H9.J/B163PJ0by', 'student', 'student', NULL, 'StudentQ2KW', NULL, NULL, 'profile-photos/QuYfYafGIJyfHmcHatF0g6XbgDlJJT1wWfQhv4N3.jpg', 'active', 'active', '2026-10-08 06:24:56', '2026-05-12 19:30:45', 2, NULL, NULL, '{\"date_of_birth\":\"1994-01-15\",\"place_of_birth\":\"Tiposu, Urdaneta City, Pangasinan\",\"sex\":\"Female\",\"gender_identity\":\"Female\",\"citizenship\":\"Filipino\",\"blood_type\":\"A+\",\"height\":\"5\'6\",\"weight\":\"50\",\"landline_number\":\"12345678\",\"civil_status\":\"Single\",\"current_address\":\"Tiposu Urdaneta City\",\"home_address\":\"Tiposu Urdaneta City\",\"religion\":\"Baptist\",\"religion_other\":\"none\",\"disabilities\":[\"None\"],\"disability_other\":\"none\"}', '{\"spouse_name\":\"None\",\"spouse_occupation\":\"none\",\"father_name\":\"Rolly Ambrosio\",\"father_occupation\":\"Farmer\",\"father_age\":\"52\",\"mother_name\":\"Marissa Pangan\",\"mother_occupation\":\"House Wife\",\"mother_age\":\"48\",\"children_total\":\"2\",\"children_boys\":\"1\",\"children_girls\":\"1\",\"brothers_count\":\"...\",\"sisters_count\":\"....\",\"monthly_income\":\"0\",\"family_type\":null,\"family_type_other\":null,\"home_ownership_type\":null,\"home_ownership_other\":\"qqqq\",\"residential_home_type\":null,\"residential_home_other\":\"qqqqqqq\"}', '{\"year_level\":\"4th year\",\"school_type\":\"Public\",\"last_school_attended\":\"macalong\",\"achievement_other_rank\":\"wow\",\"achievements\":[\"fghjk\"],\"financing_sources\":[\"456i\"],\"financing_other_work\":\"ertyui\",\"gad_training_attended\":false,\"gad_training_details\":\"ertyuio\"}', '[]', '[]', '[]', '[]', '[]', '{\"emp_skills_hobbies\":\"Dancing\",\"emp_distinctions\":\"ertyio\",\"emp_membership\":\"dfghjk\"}', '1BNyDQ1oK2GgzSVaGsbwy6bcJjyYeF0Y0zLYqDOYy3sXx6CI4PjIfShLthHn', '2026-05-12 15:43:11', '2026-10-08 06:24:56'),
(8, 'Jennifer Lopez Reyes', 'Reyes', 'Jennifer', 'Lopez', 23, 'Female', 'Bachelor of Science in Accountancy', 'jlopez_ms@psu.edu.ph', '09324364765', NULL, '$2y$12$bExdpBmvTcwmYzAgPG1RAOTnMnW0HwKO.gok.6N5c23XYfBw220te', 'employee', 'employee', NULL, 'StudentU9IH', 'id-verifications/4YRAwt7nMeYfTm9GbRiBlNCtujzi5LIzri9Fs5Ua.png', NULL, 'profile-photos/RkO1J34DyNbLfmL7LVk0IYCZJhCqsfe8WCVym7T4.jpg', 'active', 'active', NULL, '2026-07-07 00:28:27', 2, NULL, NULL, '{\"date_of_birth\":\"1990-02-09\",\"place_of_birth\":\"Quezon City\",\"sex\":\"Female\",\"gender_identity\":\"None\",\"citizenship\":\"Filipino\",\"blood_type\":\"A+\",\"height\":\"190\",\"weight\":\"35\",\"current_address\":\"Macalong Urdaneta City\",\"home_address\":\"Macalong Urdaneta City\",\"civil_status\":\"Annulled\",\"civil_status_other\":null,\"religion\":\"Others\",\"religion_other\":\"baptist\",\"disabilities\":[\"Partial Deafness\"],\"disability_other\":\"none\",\"landline_number\":\"2439\"}', '{\"spouse_name\":\"Alonto Dela cruz\",\"spouse_occupation\":\"astronaut\",\"father_name\":\"Boyet\",\"father_occupation\":\"Farmer\",\"father_age\":\"60\",\"mother_name\":\"Susan\",\"mother_occupation\":\"housekeeper\",\"mother_age\":\"48\",\"children_total\":\"1\",\"children_boys\":\"1\",\"children_girls\":\"0\",\"brothers_count\":\"0\",\"sisters_count\":\"0\",\"monthly_income\":\"30000.03\",\"family_type\":\"Nuclear\\/normal family\",\"family_type_other\":null,\"home_ownership_type\":\"Owned\",\"home_ownership_other\":null,\"residential_home_type\":\"Bahay Kubo\",\"residential_home_other\":null}', '{\"year_level\":\"3rd Year\",\"year_level_other\":null,\"school_type\":\"Private School\",\"last_school_attended\":\"Pedro T Orata\",\"achievements\":[\"Valedictorian\"],\"achievement_other_rank\":null,\"financing_sources\":[\"Parents\"],\"financing_other_work\":null,\"gad_training_attended\":null,\"gad_training_details\":null}', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-07-07 00:27:23', '2026-08-23 20:08:03'),
(9, 'Alyssa Mae Y. Santos', 'Santos', 'Alyssa Mae', 'Y.', 23, 'Female', 'Bsit', 'Asantos_ms@psu.edu.ph', '09324364765', NULL, '$2y$12$Rkt9J4DNpeZASIYBTmH0CuxGDHX24.MpBDkHxFogwUb/zf1YHGLAK', 'student', 'student', NULL, 'StudentBUNN', 'id-verifications/2ImQ63i4GoeTnOxs2bUbK6yFKfrZQI2dLPB95x5n.jpg', NULL, 'profile-photos/EzbiSuF1t4xKvLNK6hasyd7UQZ3qq9O9mPqkPx6M.jpg', 'rejected', 'active', NULL, NULL, 2, 'Please provide clearer verification images.', NULL, '{\"date_of_birth\":\"1990-02-09\",\"place_of_birth\":\"Quezon City\",\"sex\":\"Female\",\"gender_identity\":\"None\",\"citizenship\":\"Filipino\",\"blood_type\":\"A+\",\"height\":\"190\",\"weight\":\"35\",\"current_address\":\"macalong\",\"home_address\":\"macalong\",\"civil_status\":\"Married\",\"civil_status_other\":null,\"religion\":\"Roman Catholic\",\"religion_other\":null,\"disabilities\":[\"Blindness or low vision\"],\"disability_other\":null,\"landline_number\":\"2439\"}', '{\"spouse_name\":\"Alonto Dela cruz\",\"spouse_occupation\":\"astronaut\",\"father_name\":\"Boyet\",\"father_occupation\":\"Farmer\",\"father_age\":\"60\",\"mother_name\":\"Susan\",\"mother_occupation\":\"housekeeper\",\"mother_age\":\"48\",\"children_total\":\"1\",\"children_boys\":\"1\",\"children_girls\":\"0\",\"brothers_count\":\"0\",\"sisters_count\":\"0\",\"monthly_income\":\"30000.03\",\"family_type\":null,\"family_type_other\":null,\"home_ownership_type\":null,\"home_ownership_other\":null,\"residential_home_type\":null,\"residential_home_other\":null}', '{\"year_level\":\"4th Year\",\"year_level_other\":null,\"school_type\":\"Private School\",\"last_school_attended\":\"ADMU\",\"achievements\":[\"Valedictorian\"],\"achievement_other_rank\":\"n\\/a\",\"financing_sources\":[\"Parents\"],\"financing_other_work\":\"n\\/a\",\"gad_training_attended\":\"0\",\"gad_training_details\":\"n\\/a\"}', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-09-06 06:56:31', '2026-09-06 06:57:48'),
(10, 'Mark Anthony M. Garcia', 'Garcia', 'Mark Anthony', 'M.', 23, 'Male', 'Bsa', 'mgarcia_ms@psu.edu.ph', '09324364765', NULL, '$2y$12$BMR2tsq9EEXmDszGrWSwwew9bnCwJLeNXosqgDHz.eCLgI55.6Uw2', 'employee', 'employee', NULL, 'StudentBMVZ', 'id-verifications/U323ssokrwvcV20K37fOZ6swDVV2fmqUygoA00gx.jpg', NULL, 'profile-photos/IIxKWy8dAs1utqntu2AonKeycBwRrgAIUqkYZlVY.jpg', 'rejected', 'active', NULL, NULL, 2, 'Please provide clearer verification images.', NULL, '{\"date_of_birth\":\"2026-09-06\",\"place_of_birth\":\"Quezon City\",\"sex\":\"Male\",\"gender_identity\":\"None\",\"citizenship\":\"Filipino\",\"blood_type\":\"A+\",\"height\":\"190\",\"weight\":\"35\",\"current_address\":\"wertyuio\",\"home_address\":\"wertyuio\",\"civil_status\":\"Single\",\"civil_status_other\":null,\"religion\":\"Iglesia ni Cristo\",\"religion_other\":null,\"disabilities\":[\"None\"],\"disability_other\":\"n\\/a\",\"landline_number\":\"2439\"}', '{\"spouse_name\":\"Alonto Dela cruz\",\"spouse_occupation\":\"astronaut\",\"father_name\":\"Boyet\",\"father_occupation\":\"Farmer\",\"father_age\":\"60\",\"mother_name\":\"Susan\",\"mother_occupation\":\"housekeeper\",\"mother_age\":\"48\",\"children_total\":\"1\",\"children_boys\":\"1\",\"children_girls\":\"0\",\"brothers_count\":\"0\",\"sisters_count\":\"0\",\"monthly_income\":\"30000.03\",\"family_type\":null,\"family_type_other\":null,\"home_ownership_type\":null,\"home_ownership_other\":null,\"residential_home_type\":null,\"residential_home_other\":null}', '{\"year_level\":null,\"year_level_other\":null,\"school_type\":null,\"last_school_attended\":null,\"achievements\":[],\"achievement_other_rank\":null,\"financing_sources\":[],\"financing_other_work\":null,\"gad_training_attended\":null,\"gad_training_details\":null}', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-09-06 07:02:06', '2026-09-08 03:54:18'),
(11, 'Test User', 'User', 'Tes', 'T', 20, 'Male', 'BS Information Technology (BSIT)', '29as2090_ms@psu.edu.ph', '0998765432', NULL, '$2y$12$C58NETxTshBXS7MhJE5g4ut4IyGlndXiaA0I32ufOpYbX9jolitNS', 'student', 'student', NULL, 'StudentJQ2Z', NULL, 'verification/selfie/m3SUb7bG1VDUb8J9BCd5U14UAOR8pDoTPuzyI2ba.png', 'profile-photos/ZAUR2PMiUcGMvmjg4mYTU2HOJRFX0hYbs3KhBE19.jpg', 'active', 'busy', '2026-09-22 21:18:33', '2026-09-07 20:03:41', 2, NULL, NULL, '{\"date_of_birth\":\"2026-09-10\",\"place_of_birth\":\"Domanpot, Aisngan, Pangasinan\",\"sex\":\"Male\",\"gender_identity\":\"Male\",\"citizenship\":\"Filipino\",\"blood_type\":\"O-\",\"height\":\"5\'6\",\"weight\":\"50\",\"landline_number\":\"12345678\",\"civil_status\":\"Single\",\"current_address\":\"Domanpot, Asingan, PAngasinan\",\"home_address\":\"Domanpot\",\"religion\":\"Baptist\",\"religion_other\":null,\"disabilities\":[\"None\"],\"disability_other\":null}', '{\"spouse_name\":\"None\",\"spouse_occupation\":\"none\",\"father_name\":\"Est\",\"father_occupation\":\"Est\",\"father_age\":\"50\",\"mother_name\":\"Esu\",\"mother_occupation\":\"Esur\",\"mother_age\":\"48\",\"children_total\":\"2\",\"children_boys\":\"2\",\"children_girls\":\"0\",\"brothers_count\":\"0\",\"sisters_count\":\"0\",\"monthly_income\":\"0\",\"family_type\":\"Blended\",\"family_type_other\":null,\"home_ownership_type\":\"Government housing\",\"home_ownership_other\":null,\"residential_home_type\":\"Concrete\",\"residential_home_other\":null}', '{\"year_level\":\"4\",\"school_type\":\"Public\",\"last_school_attended\":\"Lmmsat\",\"achievement_other_rank\":null,\"achievements\":[],\"financing_sources\":[\"Parents\"],\"financing_other_work\":\"None\",\"gad_training_attended\":false,\"gad_training_details\":null}', '[]', '[]', '[]', '[]', '[]', '{\"emp_skills_hobbies\":null,\"emp_distinctions\":null,\"emp_membership\":null}', NULL, '2026-09-07 20:03:14', '2026-09-22 21:18:33'),
(12, 'Test Employer', NULL, NULL, NULL, NULL, NULL, NULL, 'temployee23_ms@psu.edu.ph', NULL, NULL, '$2y$12$8f6L3cdmLrrS2yNBMTH9PeBGRF.bh/1QZv/3t7y/WDeQwxd/KLbam', 'employee', 'employee', NULL, 'EmployeeWXD4', NULL, 'verification/selfie/hyDns9l1qMX9QkVXnFpCigHYEJvAn6fBz3251Q3d.png', 'profile-photos/xqiJqMONTY0h4uroUAO7NQuZEjfa39wBTdCJwLEg.jpg', 'rejected', 'active', NULL, NULL, 2, 'Please provide clearer verification images.', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-09-08 04:20:22', '2026-09-08 04:21:41'),
(13, 'Ivan Employee', 'Ariap', 'Ivan', 'Lavarias', 30, 'Male', 'BS Information Technology (BSIT)', 'iemployee23_ms@psu.edu.ph', '09305346834', NULL, '$2y$12$KD11BTiggpg.cMWS8LmQz.Erl/8HxSP492gylW1s/7oxTEHInCCKC', 'employee', 'employee', NULL, 'EmployeeGU9A', NULL, 'verification/selfie/V3kFMmckHuTuHmOdAGYjXwgMoqasWOVZwfNAzuXV.png', 'profile-photos/219lOWU9pQxxFNNdoKmR26KVhNjHYQPkSEqhwoEw.png', 'active', 'active', '2026-09-14 10:36:44', '2026-09-08 04:57:00', 2, NULL, NULL, '{\"date_of_birth\":\"1994-02-28\",\"place_of_birth\":\"Caloocan\",\"sex\":\"Male\",\"gender_identity\":\"Male\",\"citizenship\":\"Filipino\",\"blood_type\":\"O+\",\"height\":\"5\'6\",\"weight\":\"50\",\"landline_number\":\"12345678\",\"civil_status\":\"Single\",\"current_address\":\"Baro\",\"home_address\":\"Baro\",\"religion\":\"Born Again\",\"religion_other\":\"none\",\"disabilities\":[\"None\"],\"disability_other\":\"none\"}', '{\"spouse_name\":\"None\",\"spouse_occupation\":\"none\",\"father_name\":\"Est\",\"father_occupation\":\"Driver\",\"father_age\":\"50\",\"mother_name\":\"Esu\",\"mother_occupation\":\"House Wife\",\"mother_age\":\"48\",\"children_total\":\"4\",\"children_boys\":\"3\",\"children_girls\":\"1\",\"brothers_count\":\"0\",\"sisters_count\":\"0\",\"monthly_income\":\"0\",\"family_type\":\"Blended\",\"family_type_other\":\"none\",\"home_ownership_type\":\"Owned\",\"home_ownership_other\":\"eee\",\"residential_home_type\":\"Concrete\",\"residential_home_other\":\"qqqqqqq\"}', '{\"year_level\":\"4\",\"school_type\":\"State University\",\"last_school_attended\":\"Lmmsat\",\"achievement_other_rank\":\"qqqqqq\",\"achievements\":[\"wwwwww\",\"wwwwwwwww\"],\"financing_sources\":[\"parents\"],\"financing_other_work\":\"no\",\"gad_training_attended\":true,\"gad_training_details\":\"gad attend\"}', '{\"text\":\"[]employ\"}', '{\"text\":\"[]civil\"}', '{\"text\":\"[]work exp\"}', '{\"text\":\"[]volu\"}', '{\"text\":\"[]train\"}', '{\"emp_skills_hobbies\":\"skills\",\"emp_distinctions\":\"acad\",\"emp_membership\":\"org\"}', 'DtnGqILVFjaawzspf0z9ZTLj1Bl1D3nl3hkv6SOI9nV7l9v9179I9o9PYdOM', '2026-09-08 04:55:51', '2026-09-14 10:36:44'),
(14, 'Charlie Angco', NULL, NULL, NULL, 22, 'Male', 'BS Information Technology (BSIT)', '24as2050_ms@psu.edu.ph', NULL, NULL, '$2y$12$jXrfcgYxvEa66knHfGRlzu90rX6QmZwm3UlnQDCvDG/USZoDVjcoC', 'student', 'student', NULL, 'StudentVCPQ', NULL, 'verification/selfie/cZzCpH34XAKmKmyUU6QmjKiUaamboPMuuGGQU8zx.jpg', 'profile-photos/UmQBdR1PtJ0LYTRnWlBWc7sVSHvl7Z854FYZoXC0.jpg', 'pending', 'active', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-09-08 23:52:32', '2026-09-08 23:52:32'),
(15, 'Rey Lavarias', NULL, NULL, NULL, 23, 'Male', 'BS Information Technology (BSIT)', '21as0215_ms@psu.edu.ph', NULL, NULL, '$2y$12$QwFvBZzxru1WvH4TMOcCrOAzdfZxLC2VNrHxYq1OBjBAk0ulYkam2', 'student', 'student', NULL, 'Student5AFO', NULL, 'verification/selfie/zpV4arjmOnizRsI8P212Z0XG3FGlLyrbyYXkT9Cv.jpg', 'profile-photos/sMngp9rIGv3hppeDqyrJwo87bPdTAiHV0c2OHquQ.jpg', 'active', 'active', NULL, '2026-09-13 22:35:10', 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-09-13 22:34:45', '2026-09-13 22:35:10'),
(18, 'Non Teaching', NULL, NULL, NULL, 43, 'Female', 'College of Technology and Business', 'nteaching12_ms@psu.edu.ph', NULL, NULL, '$2y$12$EdAyQ.WoKN4BwFmegobuM.Xf7MLAqgpPdvxxewCejVrCC/nfaDhf.', 'employee', 'employee', 'non_teaching', 'EmployeeDWTX', NULL, 'verification/selfie/owJdNYnv4OM8SWKxQJw2UoIn0eNfTSzVAf1fdyDF.jpg', 'profile-photos/uiJbjImQ2gwjnjMKUPdMsisRGUsERh3VviQZ11W2.jpg', 'pending', 'active', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-09-14 19:11:14', '2026-09-14 19:11:14'),
(19, 'Mary Grace Piattos', NULL, NULL, NULL, 32, 'Female', 'College of Education', 'mpiattos12_ms@psu.edu.ph', NULL, NULL, '$2y$12$QyQVF6mO9TH6cKNQsfUio.Or4JY.tQGo9pM86TUg9XGpgUk4100S2', 'employee', 'employee', 'teaching', 'EmployeeOCSD', NULL, 'verification/selfie/nwhvMVp65HQ7EBSXXg9kWgF0hnhFrgQILSBk39Pv.jpg', 'profile-photos/IDSPnnDdg5d4LULFwNm9umsWR6JyukgoBnxIoiuu.jpg', 'pending', 'active', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-09-14 19:28:35', '2026-09-14 19:28:35'),
(20, 'Charlie Angco', NULL, NULL, NULL, 21, 'Male', 'BS Information Technology (BSIT)', '23as1881_ms@psu.edu.ph', NULL, NULL, '$2y$12$EduFcFKAp0BVFlYdfzRsKeJfiwV/qhwih0pB2xGb9taZW4pFYrGj.', 'student', 'student', NULL, 'Student12OH', NULL, 'verification/selfie/2elpHeYZVfoJlfRQ2L8oTLXQ29RViDApaz4GvDjg.jpg', 'profile-photos/G2PI4jtYzX3yLLnJkla4mNBQ5CveZoC9KULbjqS9.jpg', 'active', 'active', '2026-09-22 21:47:35', '2026-09-22 21:21:34', 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-09-22 21:20:16', '2026-09-22 21:47:35');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `announcements`
--
ALTER TABLE `announcements`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `appointment_requests`
--
ALTER TABLE `appointment_requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `appointment_requests_user_id_foreign` (`user_id`);

--
-- Indexes for table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_expiration_index` (`expiration`);

--
-- Indexes for table `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_locks_expiration_index` (`expiration`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `gad_schedules`
--
ALTER TABLE `gad_schedules`
  ADD PRIMARY KEY (`id`),
  ADD KEY `gad_schedules_created_by_foreign` (`created_by`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_reserved_at_available_at_index` (`queue`,`reserved_at`,`available_at`);

--
-- Indexes for table `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `messages_receiver_id_foreign` (`receiver_id`),
  ADD KEY `messages_sender_id_receiver_id_index` (`sender_id`,`receiver_id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `password_reset_requests`
--
ALTER TABLE `password_reset_requests`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `password_reset_requests_reference_number_unique` (`reference_number`),
  ADD KEY `password_reset_requests_user_id_foreign` (`user_id`),
  ADD KEY `password_reset_requests_admin_id_foreign` (`admin_id`),
  ADD KEY `password_reset_requests_email_index` (`email`),
  ADD KEY `password_reset_requests_student_employee_id_index` (`student_employee_id`),
  ADD KEY `password_reset_requests_status_index` (`status`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `reports`
--
ALTER TABLE `reports`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `reports_incident_id_unique` (`incident_id`),
  ADD KEY `reports_user_id_foreign` (`user_id`),
  ADD KEY `reports_seen_at_index` (`seen_at`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `suggestions`
--
ALTER TABLE `suggestions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `suggestions_user_id_foreign` (`user_id`);

--
-- Indexes for table `suggestion_comments`
--
ALTER TABLE `suggestion_comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `suggestion_comments_suggestion_id_foreign` (`suggestion_id`),
  ADD KEY `suggestion_comments_user_id_foreign` (`user_id`);

--
-- Indexes for table `suggestion_reports`
--
ALTER TABLE `suggestion_reports`
  ADD PRIMARY KEY (`id`),
  ADD KEY `suggestion_reports_reporter_id_foreign` (`reporter_id`),
  ADD KEY `suggestion_reports_suggestion_id_foreign` (`suggestion_id`),
  ADD KEY `suggestion_reports_comment_id_foreign` (`comment_id`),
  ADD KEY `suggestion_reports_reviewed_by_foreign` (`reviewed_by`),
  ADD KEY `suggestion_reports_status_index` (`status`);

--
-- Indexes for table `suggestion_user_upvotes`
--
ALTER TABLE `suggestion_user_upvotes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `suggestion_user_upvotes_suggestion_id_foreign` (`suggestion_id`),
  ADD KEY `suggestion_user_upvotes_user_id_foreign` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`),
  ADD UNIQUE KEY `users_cloak_alias_unique` (`cloak_alias`),
  ADD KEY `users_last_seen_at_index` (`last_seen_at`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `announcements`
--
ALTER TABLE `announcements`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `appointment_requests`
--
ALTER TABLE `appointment_requests`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `gad_schedules`
--
ALTER TABLE `gad_schedules`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `password_reset_requests`
--
ALTER TABLE `password_reset_requests`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `reports`
--
ALTER TABLE `reports`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `suggestions`
--
ALTER TABLE `suggestions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `suggestion_comments`
--
ALTER TABLE `suggestion_comments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `suggestion_reports`
--
ALTER TABLE `suggestion_reports`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `suggestion_user_upvotes`
--
ALTER TABLE `suggestion_user_upvotes`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `appointment_requests`
--
ALTER TABLE `appointment_requests`
  ADD CONSTRAINT `appointment_requests_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `gad_schedules`
--
ALTER TABLE `gad_schedules`
  ADD CONSTRAINT `gad_schedules_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `messages`
--
ALTER TABLE `messages`
  ADD CONSTRAINT `messages_receiver_id_foreign` FOREIGN KEY (`receiver_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `messages_sender_id_foreign` FOREIGN KEY (`sender_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `password_reset_requests`
--
ALTER TABLE `password_reset_requests`
  ADD CONSTRAINT `password_reset_requests_admin_id_foreign` FOREIGN KEY (`admin_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `password_reset_requests_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `reports`
--
ALTER TABLE `reports`
  ADD CONSTRAINT `reports_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `suggestions`
--
ALTER TABLE `suggestions`
  ADD CONSTRAINT `suggestions_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `suggestion_comments`
--
ALTER TABLE `suggestion_comments`
  ADD CONSTRAINT `suggestion_comments_suggestion_id_foreign` FOREIGN KEY (`suggestion_id`) REFERENCES `suggestions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `suggestion_comments_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `suggestion_reports`
--
ALTER TABLE `suggestion_reports`
  ADD CONSTRAINT `suggestion_reports_comment_id_foreign` FOREIGN KEY (`comment_id`) REFERENCES `suggestion_comments` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `suggestion_reports_reporter_id_foreign` FOREIGN KEY (`reporter_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `suggestion_reports_reviewed_by_foreign` FOREIGN KEY (`reviewed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `suggestion_reports_suggestion_id_foreign` FOREIGN KEY (`suggestion_id`) REFERENCES `suggestions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `suggestion_user_upvotes`
--
ALTER TABLE `suggestion_user_upvotes`
  ADD CONSTRAINT `suggestion_user_upvotes_suggestion_id_foreign` FOREIGN KEY (`suggestion_id`) REFERENCES `suggestions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `suggestion_user_upvotes_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
