-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 18, 2025 at 09:36 PM
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
-- Database: `laravel`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin_settings`
--

CREATE TABLE `admin_settings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `key` varchar(255) NOT NULL,
  `value` text NOT NULL,
  `type` enum('string','integer','boolean','json') NOT NULL DEFAULT 'string',
  `description` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `admin_settings`
--

INSERT INTO `admin_settings` (`id`, `key`, `value`, `type`, `description`, `created_at`, `updated_at`) VALUES
(1, 'auto_archive_days', '30', 'integer', 'Number of days after which resolved reports are automatically archived', '2025-06-06 11:19:15', '2025-06-06 11:19:15');

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
-- Table structure for table `comment_attachments`
--

CREATE TABLE `comment_attachments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `remark_id` bigint(20) UNSIGNED NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `original_name` varchar(255) NOT NULL,
  `file_type` varchar(255) NOT NULL,
  `file_size` bigint(20) NOT NULL,
  `mime_type` varchar(255) NOT NULL,
  `disk` varchar(255) NOT NULL DEFAULT 'public',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `departments`
--

CREATE TABLE `departments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `worker_id_identifier` varchar(255) DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) DEFAULT NULL,
  `head_name` varchar(255) DEFAULT NULL,
  `head_email` varchar(255) DEFAULT NULL,
  `head_phone` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `otp` varchar(255) DEFAULT NULL,
  `otp_expires_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `departments`
--

INSERT INTO `departments` (`id`, `name`, `worker_id_identifier`, `email`, `password`, `head_name`, `head_email`, `head_phone`, `is_active`, `created_at`, `updated_at`, `otp`, `otp_expires_at`) VALUES
(1, 'UCUA Department', 'UCUA', 'ucuaport@gmail.com', '$2y$12$xni10300FFXQY.qKsbNLy.a20ECZRXgzV5dmXhwPabqC/TphZ6cCu', 'UCUA Head', 'ucua.head@port.com', '+60123456789', 1, '2025-06-06 12:33:23', '2025-06-06 12:33:23', NULL, NULL),
(2, 'Port Safety & Security Department (PSD)', 'SEC', 'securityjohorport@gmail.com', '$2y$12$rrTlT0Zf8XIDdjvZNL9CAu4FHRZOSLPmJLXelIXcpItIoeZplCQvi', 'Asri Bin Tajol', 'syahminasukamcflurry@port.com', '+60123456790', 1, '2025-06-06 12:33:23', '2025-06-16 17:40:37', NULL, NULL),
(3, 'Maintenance & Repair Department (M&R)', 'MNT', 'maintenanceport@gmail.com', '$2y$12$Ay.8cM2008YlxYe/F7VSfeIW3JjxPxW421zhT1VqmwlOs/hCBUC9.', 'Kumar A/L Siramugam', 'minashimosdy@gmail.com', '+60123456791', 1, '2025-06-06 12:33:23', '2025-06-07 03:12:35', NULL, NULL),
(4, 'Electrical and Services Department (E&S)', 'ES', 'electricport@gmail.com', '$2y$12$.hseMXf0fMPz0AIvo4BrAeTRaoZZTxwccFtibnRH9dqY7D4ORnhw.', 'Zhao Li Ying', 'kamisahlatuwo@gmail.com', '+60123456792', 1, '2025-06-06 12:33:23', '2025-06-15 20:26:09', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `escalation_rules`
--

CREATE TABLE `escalation_rules` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `violation_type` enum('unsafe_act','unsafe_condition','general') NOT NULL DEFAULT 'unsafe_act',
  `warning_threshold` int(11) NOT NULL DEFAULT 3,
  `time_period_months` int(11) NOT NULL DEFAULT 3,
  `escalation_action` enum('disciplinary_action','supervisor_notification','mandatory_training','suspension') NOT NULL DEFAULT 'disciplinary_action',
  `notify_hod` tinyint(1) NOT NULL DEFAULT 1,
  `notify_employee` tinyint(1) NOT NULL DEFAULT 1,
  `notify_department_email` tinyint(1) NOT NULL DEFAULT 1,
  `reset_period_months` int(11) NOT NULL DEFAULT 6,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_by` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `escalation_rules`
--

INSERT INTO `escalation_rules` (`id`, `name`, `violation_type`, `warning_threshold`, `time_period_months`, `escalation_action`, `notify_hod`, `notify_employee`, `notify_department_email`, `reset_period_months`, `is_active`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 'Default Escalation Rule', 'unsafe_act', 3, 3, 'disciplinary_action', 1, 1, 1, 6, 1, 1, '2025-06-08 04:28:55', '2025-06-08 04:28:55');

-- --------------------------------------------------------

--
-- Table structure for table `escalation_warnings`
--

CREATE TABLE `escalation_warnings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `violation_escalation_id` bigint(20) UNSIGNED NOT NULL,
  `warning_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
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
(4, '2025_01_09_154400_create_departments_table', 1),
(5, '2025_01_09_154506_create_reports_table', 1),
(6, '2025_01_09_154507_create_remarks_table', 1),
(7, '2025_01_09_154508_create_reminders_table', 1),
(8, '2025_01_09_154509_create_warnings_table', 1),
(9, '2025_01_09_154608_create_notifications_table', 1),
(10, '2025_01_09_155012_create_permission_tables', 1),
(11, '2025_01_11_172142_create_tasks_table', 1),
(12, '2025_01_15_000001_create_warning_templates_table', 1),
(13, '2025_01_15_000002_create_escalation_rules_table', 1),
(14, '2025_01_15_000003_create_violation_escalations_table', 1),
(15, '2025_01_15_000004_create_escalation_warnings_table', 1),
(16, '2025_01_15_000005_add_template_and_email_fields_to_warnings_table', 1),
(17, '2025_01_15_052025_remove_title_column_from_reports_table', 1),
(18, '2025_01_15_120000_add_assignment_remark_to_reports_table', 1),
(19, '2025_01_16_000001_fix_remarks_system_for_departments', 1),
(20, '2025_01_16_000002_create_report_status_history_table', 1),
(21, '2025_01_20_000000_add_otp_fields_to_departments_table', 1),
(22, '2025_01_20_000001_create_admin_settings_table', 1),
(23, '2025_01_20_000001_enhance_remarks_system_threading_attachments', 1),
(24, '2025_01_21_000000_add_violator_fields_to_reports_table', 1),
(25, '2025_03_21_033912_create_personal_access_tokens_table', 1),
(26, '2025_03_26_042248_add_attachment_to_reports_table', 1),
(27, '2025_05_08_145200_add_department_to_users_table', 1),
(28, '2025_05_08_151300_add_deadline_to_reports_table', 1),
(29, '2025_05_29_092302_add_new_fields_to_reports_table', 1),
(30, '2025_05_29_093756_add_profile_picture_to_users_table', 1),
(31, '2025_05_29_101516_remove_profile_picture_from_users_table', 1),
(32, '2025_05_30_004947_add_department_head_role', 1),
(33, '2025_05_30_061630_add_password_to_departments_table', 1),
(34, '2025_05_30_183843_add_remarks_to_reports_table', 1),
(35, '2025_05_30_234854_create_unsafe_act_details_table', 1),
(36, '2025_05_30_234903_create_unsafe_condition_details_table', 1),
(37, '2025_05_30_234940_remove_redundant_report_columns', 1),
(38, '2025_05_31_073132_add_otp_fields_to_users_table', 1),
(39, '2025_05_31_074817_add_worker_id_and_department_id_to_users_table', 1),
(40, '2025_05_31_081531_add_worker_id_identifier_to_departments_table', 1),
(41, '2025_05_31_212156_add_worker_id_to_users_table', 1),
(42, '2025_05_31_212951_remove_old_department_field_from_users_table', 1),
(43, '2025_05_31_213647_fix_reports_table_structure', 1),
(44, '2025_06_01_002433_add_status_and_workflow_columns_to_warnings_table', 1),
(45, '2025_06_01_070617_add_resolution_fields_to_reports_table', 1),
(46, '2025_06_02_131338_add_is_admin_to_users_table_proper', 1),
(47, '2025_06_02_131542_make_user_id_nullable_in_reports_table', 1),
(48, '2025_06_03_232858_fix_notifications_table_structure', 1),
(49, '2025_06_06_160852_add_violator_email_to_reports_table', 1),
(50, '[timestamp]_add_is_admin_to_users_table', 1),
(51, '2025_06_06_195228_add_unique_constraints_and_reset_auto_increment', 2),
(52, '2025_06_07_130929_add_formatted_id_to_reports_table', 3),
(53, '2025_06_15_000000_add_phone_to_users_table', 4),
(54, '2025_06_16_042155_populate_worker_id_identifiers_for_departments', 5),
(55, '2025_06_15_000000_add_rejection_reason_to_reports_table', 6),
(56, '2025_06_16_052458_add_rejection_reason_to_reports_table', 6);

-- --------------------------------------------------------

--
-- Table structure for table `model_has_permissions`
--

CREATE TABLE `model_has_permissions` (
  `permission_id` bigint(20) UNSIGNED NOT NULL,
  `model_type` varchar(255) NOT NULL,
  `model_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `model_has_roles`
--

CREATE TABLE `model_has_roles` (
  `role_id` bigint(20) UNSIGNED NOT NULL,
  `model_type` varchar(255) NOT NULL,
  `model_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `model_has_roles`
--

INSERT INTO `model_has_roles` (`role_id`, `model_type`, `model_id`) VALUES
(2, 'App\\Models\\User', 1),
(3, 'App\\Models\\User', 2),
(4, 'App\\Models\\User', 3),
(4, 'App\\Models\\User', 4),
(4, 'App\\Models\\User', 5),
(4, 'App\\Models\\User', 6),
(4, 'App\\Models\\User', 7),
(4, 'App\\Models\\User', 9);

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` char(36) NOT NULL,
  `type` varchar(255) NOT NULL,
  `notifiable_type` varchar(255) NOT NULL,
  `notifiable_id` bigint(20) UNSIGNED NOT NULL,
  `data` text NOT NULL,
  `read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `type`, `notifiable_type`, `notifiable_id`, `data`, `read_at`, `created_at`, `updated_at`) VALUES
('5a1f31c2-8ddc-4753-846a-a7591169caf3', 'App\\Notifications\\ReminderNotification', 'App\\Models\\Department', 1, '{\"type\":\"reminder\",\"reminder_id\":4,\"reminder_formatted_id\":\"RL-004\",\"reminder_type\":\"gentle\",\"report_id\":7,\"report_formatted_id\":\"RPT-007\",\"report_description\":\"Test safety report created for email system testing\",\"report_location\":\"Test Location - Email System\",\"report_deadline\":\"2025-06-15\",\"sent_by\":\"UCUA Officer\",\"message\":\"Test reminder for email system verification\",\"urgency_level\":\"low\",\"action_required\":\"Please provide a status update on this safety report at your earliest convenience.\",\"link\":\"http:\\/\\/127.0.0.1:8000\\/department\\/dashboard\",\"created_at\":\"2025-06-07T16:05:24.000000Z\"}', NULL, '2025-06-07 16:05:28', '2025-06-07 16:05:28'),
('9db1e5ca-01e5-4de6-9b1f-aeca0b4fdbab', 'App\\Notifications\\ReminderNotification', 'App\\Models\\Department', 1, '{\"type\":\"reminder\",\"reminder_id\":1,\"reminder_formatted_id\":\"RL-001\",\"reminder_type\":\"gentle\",\"report_id\":4,\"report_formatted_id\":\"RPT-004\",\"report_description\":\"Test safety report for email testing - worker not wearing required PPE\",\"report_location\":\"Test Dock Area\",\"report_deadline\":\"2025-06-14\",\"sent_by\":\"UCUA Officer\",\"message\":\"This is a test reminder to verify email functionality. Please provide status update.\",\"urgency_level\":\"low\",\"action_required\":\"Please provide a status update on this safety report at your earliest convenience.\",\"link\":\"http:\\/\\/127.0.0.1:8000\\/department\\/dashboard\",\"created_at\":\"2025-06-07T15:51:06.000000Z\"}', NULL, '2025-06-07 15:52:06', '2025-06-07 15:52:06'),
('c29607cb-3f25-43dc-91de-4b1402168d11', 'App\\Notifications\\ReminderNotification', 'App\\Models\\Department', 2, '{\"type\":\"reminder\",\"reminder_id\":6,\"reminder_formatted_id\":\"RL-006\",\"reminder_type\":\"urgent\",\"report_id\":1,\"report_formatted_id\":\"RPT-001\",\"report_description\":\"MEMBAWA DENGAN MERBAHAYA\",\"report_location\":\"Building C\",\"report_deadline\":\"2025-06-09\",\"sent_by\":\"UCUA Officer\",\"message\":\"test test test test test test test test test test test test\",\"urgency_level\":\"medium\",\"action_required\":\"Immediate attention required. Please provide an update and take necessary action promptly.\",\"link\":\"http:\\/\\/127.0.0.1:8000\\/department\\/reports\\/1\",\"created_at\":\"2025-06-07T17:28:02.000000Z\"}', '2025-06-07 17:54:27', '2025-06-07 17:28:07', '2025-06-07 17:54:27'),
('d92bff5d-28a9-450e-8624-c14c3c13e918', 'App\\Notifications\\ReminderNotification', 'App\\Models\\Department', 1, '{\"type\":\"reminder\",\"reminder_id\":5,\"reminder_formatted_id\":\"RL-005\",\"reminder_type\":\"gentle\",\"report_id\":8,\"report_formatted_id\":\"RPT-008\",\"report_description\":\"Test safety report created for email system testing\",\"report_location\":\"Test Location - Email System\",\"report_deadline\":\"2025-06-15\",\"sent_by\":\"UCUA Officer\",\"message\":\"Test reminder for email system verification\",\"urgency_level\":\"low\",\"action_required\":\"Please provide a status update on this safety report at your earliest convenience.\",\"link\":\"http:\\/\\/127.0.0.1:8000\\/department\\/dashboard\",\"created_at\":\"2025-06-07T16:07:30.000000Z\"}', NULL, '2025-06-07 16:07:34', '2025-06-07 16:07:34'),
('ec079026-8a9f-4265-bb44-577de92c8479', 'App\\Notifications\\ReminderNotification', 'App\\Models\\Department', 1, '{\"type\":\"reminder\",\"reminder_id\":3,\"reminder_formatted_id\":\"RL-003\",\"reminder_type\":\"gentle\",\"report_id\":6,\"report_formatted_id\":\"RPT-006\",\"report_description\":\"Test safety report created for email system testing\",\"report_location\":\"Test Location - Email System\",\"report_deadline\":\"2025-06-15\",\"sent_by\":\"UCUA Officer\",\"message\":\"Test reminder for email system verification\",\"urgency_level\":\"low\",\"action_required\":\"Please provide a status update on this safety report at your earliest convenience.\",\"link\":\"http:\\/\\/127.0.0.1:8000\\/department\\/dashboard\",\"created_at\":\"2025-06-07T16:01:23.000000Z\"}', NULL, '2025-06-07 16:01:48', '2025-06-07 16:01:48');

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `permissions`
--

CREATE TABLE `permissions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `guard_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `remarks`
--

CREATE TABLE `remarks` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `report_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `user_type` enum('user','department','ucua_officer','admin') NOT NULL DEFAULT 'user',
  `content` text NOT NULL,
  `attachment_path` varchar(255) DEFAULT NULL,
  `attachment_name` varchar(255) DEFAULT NULL,
  `attachment_type` varchar(255) DEFAULT NULL,
  `attachment_size` bigint(20) DEFAULT NULL,
  `is_edited` tinyint(1) NOT NULL DEFAULT 0,
  `edited_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `department_id` bigint(20) UNSIGNED DEFAULT NULL,
  `parent_id` bigint(20) UNSIGNED DEFAULT NULL,
  `thread_level` int(11) NOT NULL DEFAULT 0,
  `reply_count` int(11) NOT NULL DEFAULT 0,
  `edited_by` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `remarks`
--

INSERT INTO `remarks` (`id`, `report_id`, `user_id`, `user_type`, `content`, `attachment_path`, `attachment_name`, `attachment_type`, `attachment_size`, `is_edited`, `edited_at`, `created_at`, `updated_at`, `deleted_at`, `department_id`, `parent_id`, `thread_level`, `reply_count`, `edited_by`) VALUES
(1, 1, NULL, 'department', 'through cctv recording she is the violator\n\n[INVESTIGATION UPDATE] Violator identified: NurSyazwina Mahmud (ID: PSD001)', NULL, NULL, NULL, NULL, 0, NULL, '2025-06-07 17:55:19', '2025-06-07 17:55:19', NULL, 2, NULL, 0, 0, NULL),
(2, 9, 1, 'admin', 'please verify and investigate more since the no plat is not complete', NULL, NULL, NULL, NULL, 0, NULL, '2025-06-09 08:58:54', '2025-06-09 09:20:27', NULL, NULL, NULL, 0, 1, NULL),
(3, 9, 2, 'ucua_officer', 'okay admin', NULL, NULL, NULL, NULL, 0, NULL, '2025-06-09 09:09:19', '2025-06-09 09:09:19', NULL, NULL, NULL, 0, 0, NULL),
(4, 9, NULL, 'department', 'okay i will', NULL, NULL, NULL, NULL, 0, NULL, '2025-06-09 09:20:27', '2025-06-09 09:20:27', NULL, 2, 2, 1, 0, NULL),
(5, 9, NULL, 'department', 'based on the cctv recording,this staff was driving a car with no plate \"VLM 2021\" in a dangerous way\n\n[INVESTIGATION UPDATE] Violator identified: Muhammad Iqbal (ID: MNT001)', NULL, NULL, NULL, NULL, 0, NULL, '2025-06-09 09:22:11', '2025-06-09 09:22:11', NULL, 2, NULL, 0, 0, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `reminders`
--

CREATE TABLE `reminders` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `report_id` bigint(20) UNSIGNED NOT NULL,
  `sent_by` bigint(20) UNSIGNED NOT NULL,
  `type` enum('gentle','urgent','final') NOT NULL,
  `message` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `reminders`
--

INSERT INTO `reminders` (`id`, `report_id`, `sent_by`, `type`, `message`, `created_at`, `updated_at`) VALUES
(1, 4, 2, 'gentle', 'This is a test reminder to verify email functionality. Please provide status update.', '2025-06-07 15:51:06', '2025-06-07 15:51:06'),
(2, 5, 2, 'gentle', 'Test reminder for email system verification', '2025-06-07 16:00:38', '2025-06-07 16:00:38'),
(6, 1, 2, 'urgent', 'test test test test test test test test test test test test', '2025-06-07 17:28:02', '2025-06-07 17:28:02');

-- --------------------------------------------------------

--
-- Table structure for table `reports`
--

CREATE TABLE `reports` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `formatted_id` varchar(255) DEFAULT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `employee_id` varchar(255) NOT NULL,
  `violator_employee_id` varchar(255) DEFAULT NULL COMMENT 'Employee ID of the person who committed the violation',
  `violator_name` varchar(255) DEFAULT NULL COMMENT 'Name of violator (for non-system users like contractors, visitors)',
  `violator_department` varchar(255) DEFAULT NULL COMMENT 'Department of the violator (may differ from reporter department)',
  `department` varchar(255) NOT NULL,
  `phone` varchar(255) NOT NULL,
  `location` varchar(255) NOT NULL,
  `other_location` text DEFAULT NULL,
  `incident_date` datetime NOT NULL,
  `description` text NOT NULL,
  `attachment` varchar(255) DEFAULT NULL,
  `category` varchar(255) DEFAULT NULL,
  `unsafe_condition` varchar(255) DEFAULT NULL,
  `other_unsafe_condition` text DEFAULT NULL,
  `unsafe_act` varchar(255) DEFAULT NULL,
  `other_unsafe_act` text DEFAULT NULL,
  `status` enum('pending','review','in_progress','resolved','rejected') NOT NULL DEFAULT 'pending',
  `is_anonymous` tinyint(1) NOT NULL DEFAULT 0,
  `handling_department_id` bigint(20) UNSIGNED DEFAULT NULL,
  `handling_staff_id` bigint(20) UNSIGNED DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `assignment_remark` text DEFAULT NULL,
  `resolution_notes` text DEFAULT NULL,
  `rejection_reason` text DEFAULT NULL COMMENT 'Reason provided when department rejects a report',
  `resolved_at` timestamp NULL DEFAULT NULL,
  `deadline` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `reports`
--

INSERT INTO `reports` (`id`, `formatted_id`, `user_id`, `employee_id`, `violator_employee_id`, `violator_name`, `violator_department`, `department`, `phone`, `location`, `other_location`, `incident_date`, `description`, `attachment`, `category`, `unsafe_condition`, `other_unsafe_condition`, `unsafe_act`, `other_unsafe_act`, `status`, `is_anonymous`, `handling_department_id`, `handling_staff_id`, `remarks`, `assignment_remark`, `resolution_notes`, `rejection_reason`, `resolved_at`, `deadline`, `created_at`, `updated_at`) VALUES
(1, 'RPT-001', 7, 'SEC001', 'PSD001', 'NurSyazwina Mahmud', 'Port Safety & Security Department (PSD)', 'Port Safety & Security Department (PSD)', '0167221991', 'Building C', NULL, '2025-06-07 12:38:00', 'MEMBAWA DENGAN MERBAHAYA', 'reports/TngPJPdFuOsVvpe2pdgo44eMf9LZpeJVVYeF3UXN.png', 'unsafe_act', NULL, NULL, 'Speeding inside premise', NULL, 'resolved', 0, 2, NULL, NULL, 'please check and refer cctv yang ada', 'the violator identified', NULL, '2025-06-07 16:00:00', '2025-06-09', '2025-06-07 04:39:21', '2025-06-07 17:55:53'),
(2, 'RPT-002', 4, 'MNT001', NULL, NULL, NULL, 'Maintenance & Repair Department (M&R)', '0167221995', 'Building B', NULL, '2025-06-04 12:43:00', 'BAU GAS KUAT', NULL, 'unsafe_condition', 'Fire & explosion hazards', NULL, NULL, NULL, 'in_progress', 0, 3, NULL, NULL, 'please check dengan kadar segera', NULL, NULL, NULL, '2025-06-10', '2025-06-07 04:43:54', '2025-06-07 05:25:26'),
(3, 'RPT-003', 4, 'MNT001', NULL, NULL, NULL, 'Maintenance & Repair Department (M&R)', '0167221995', 'Cointaner Yard', NULL, '2025-06-07 12:45:00', 'MEROKOK DI KAWASAN DILARANG, PUNTUNG ROKOK DIBUANG MERATA', 'reports/A4aA6YJR5mbb8qZnkR8Fvft7CXdpKglsnG4L2qdK.png', 'unsafe_act', NULL, NULL, 'Smoking at prohibited area', NULL, 'in_progress', 0, 2, NULL, NULL, 'please check cctv dan refer attachment yang dah disertakan', NULL, NULL, NULL, '2025-06-11', '2025-06-07 04:45:47', '2025-06-07 05:36:19'),
(4, 'RPT-004', 1, 'ADM001', NULL, NULL, NULL, 'UCUA Department', '+60123456789', 'Test Dock Area', NULL, '2025-06-05 23:50:40', 'Test safety report for email testing - worker not wearing required PPE', NULL, 'unsafe_act', NULL, NULL, 'Not wearing safety equipment', NULL, 'review', 0, 1, NULL, NULL, NULL, NULL, NULL, NULL, '2025-06-14', '2025-06-07 15:50:40', '2025-06-07 15:50:40'),
(5, 'RPT-005', 1, 'ADM001', NULL, NULL, NULL, 'UCUA Department', '+60123456789', 'Test Location - Email System', NULL, '2025-06-07 00:00:38', 'Test safety report created for email system testing', NULL, 'unsafe_act', NULL, NULL, 'Test unsafe act for email testing', NULL, 'review', 0, 1, NULL, NULL, NULL, NULL, NULL, NULL, '2025-06-15', '2025-06-07 16:00:38', '2025-06-07 16:00:38'),
(9, 'RPT-006', 9, 'PSD002', 'MNT001', 'Muhammad Iqbal', 'Maintenance & Repair Department (M&R)', 'Port Safety & Security Department (PSD)', '0167317522', 'Building C', NULL, '2025-06-09 16:43:00', 'KERETA VLM MEMBAWA KERETA DENGAN MERBAHAYA,RISIKO', 'reports/IKHli72gRdOuc0DZHrOYgrtMmp4so6xgLzfNSvwx.png', 'unsafe_act', NULL, NULL, 'Speeding inside premise', NULL, 'resolved', 0, 2, NULL, NULL, 'please verify the no plate and update regularly', 'already give the cctv recording and suggest warning letter to the UCUA Officer', NULL, '2025-06-08 16:00:00', '2025-06-13', '2025-06-09 08:44:11', '2025-06-09 09:23:00');

-- --------------------------------------------------------

--
-- Table structure for table `report_status_history`
--

CREATE TABLE `report_status_history` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `report_id` bigint(20) UNSIGNED NOT NULL,
  `previous_status` varchar(255) DEFAULT NULL,
  `new_status` varchar(255) NOT NULL,
  `changed_by` bigint(20) UNSIGNED DEFAULT NULL,
  `department_id` bigint(20) UNSIGNED DEFAULT NULL,
  `changed_by_type` enum('user','department','ucua_officer','admin') NOT NULL DEFAULT 'user',
  `reason` text DEFAULT NULL,
  `metadata` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`metadata`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `guard_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
(1, 'department_head', 'web', '2025-06-06 11:19:16', '2025-06-06 11:19:16'),
(2, 'admin', 'web', '2025-06-06 11:35:10', '2025-06-06 11:35:10'),
(3, 'ucua_officer', 'web', '2025-06-06 11:35:10', '2025-06-06 11:35:10'),
(4, 'port_worker', 'web', '2025-06-06 11:35:10', '2025-06-06 11:35:10');

-- --------------------------------------------------------

--
-- Table structure for table `role_has_permissions`
--

CREATE TABLE `role_has_permissions` (
  `permission_id` bigint(20) UNSIGNED NOT NULL,
  `role_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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

--
-- Dumping data for table `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('Nt8sPcvz8sEROC6Z6GYdctchOFtvkHaGCcfvYFeu', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36 Edg/137.0.0.0', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiUDJHSW9ScEdwUTdHc0g5ZXZONnBRQXBOeEZQa0VlQUhNcTY0d3BuSiI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mzg6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9kZXBhcnRtZW50L2xvZ2luIjt9fQ==', 1750095687),
('VVSpYyTAU5AwCl7iYoqwsKhs0XWI9d4P6Z0QfihQ', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36 Edg/137.0.0.0', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiOGZ0ZlFydTF5UHVSbFdwNTRnUUhxeEVUNWFjWE5BNlBjcEhhenJGUCI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJuZXciO2E6MDp7fXM6Mzoib2xkIjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzM6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9hZG1pbi9sb2dpbiI7fX0=', 1750270829);

-- --------------------------------------------------------

--
-- Table structure for table `tasks`
--

CREATE TABLE `tasks` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'Pending',
  `due_date` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `unsafe_act_details`
--

CREATE TABLE `unsafe_act_details` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `report_id` bigint(20) UNSIGNED NOT NULL,
  `act_type` varchar(255) DEFAULT NULL,
  `other_act_details` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `unsafe_condition_details`
--

CREATE TABLE `unsafe_condition_details` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `report_id` bigint(20) UNSIGNED NOT NULL,
  `condition_type` varchar(255) DEFAULT NULL,
  `other_condition_details` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `worker_id` varchar(255) DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `department_id` bigint(20) UNSIGNED NOT NULL,
  `is_admin` tinyint(1) NOT NULL DEFAULT 0,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `is_anonymous` tinyint(1) NOT NULL DEFAULT 0,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `otp` varchar(255) DEFAULT NULL,
  `otp_expires_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `worker_id`, `phone`, `department_id`, `is_admin`, `email_verified_at`, `password`, `is_anonymous`, `remember_token`, `created_at`, `updated_at`, `otp`, `otp_expires_at`) VALUES
(1, 'Admin User', 'nursyahminabintimosdy@gmail.com', 'ADM001', '+60123456789', 1, 0, '2025-06-06 12:33:23', '$2y$12$t6XS0JCQwq53sZFrrCU.mOgzSvObfO4yPwqppddYc.O3OquqAtDRe', 0, NULL, '2025-06-06 12:33:23', '2025-06-16 05:52:25', NULL, NULL),
(2, 'UCUA Officer', 'nazzreezahar@gmail.com', 'UCUA001', '+60198765432', 1, 0, '2025-06-06 12:33:24', '$2y$12$64yh5RKxHT7ds1H4qvaP8.oyjnN2WJ1ns.HCi4WTi3Gdp/uJrFvCm', 0, NULL, '2025-06-06 12:33:24', '2025-06-16 17:37:51', NULL, NULL),
(3, 'NurSyazwina Mahmud', 'sharkizz30@gmail.com', 'PSD001', '+60187654321', 2, 0, '2025-06-06 12:33:24', '$2y$12$ChoRG.UZhUNjD3GeYd28VeddO4KbffppjJ1AzjTY4qnNj/5tZY96e', 0, NULL, '2025-06-06 12:33:24', '2025-06-15 19:16:45', NULL, NULL),
(4, 'Muhammad Iqbal', 'muhammadiqbalmosdy@gmail.com', 'MNT001', '+60176543210', 3, 0, '2025-06-06 12:33:25', '$2y$12$y1A5ZYDmhYT2GKqoOZ791ezITft9aFG6GPNzaL/YaPJqdMRqevsmW', 0, NULL, '2025-06-06 12:33:25', '2025-06-15 19:16:45', NULL, NULL),
(5, 'Muhammad Irfan', 'irf109530@gmail.com', 'MNT002', '+60165432109', 3, 0, '2025-06-06 12:33:25', '$2y$12$V8e7GxWTP8U9q0Tad/vgb.QTYP.aivIuElW/N7E9O51pcTu5cOFKC', 0, NULL, '2025-06-06 12:33:25', '2025-06-15 19:16:45', NULL, NULL),
(6, 'Nur Adriana Qaisara', 'syahminamosdy03@gmail.com', 'ELC001', '+60154321098', 4, 0, '2025-06-06 12:33:25', '$2y$12$.vVP2ejc5n0q1g24zgYWqeYHbGx5QpNounNDahd8g9NJ085te5URa', 0, NULL, '2025-06-06 12:33:25', '2025-06-18 18:19:15', NULL, NULL),
(7, 'Radin Arjuna', 'nsijaja30@gmail.com', 'SEC001', '+60143210987', 2, 0, '2025-06-06 12:33:25', '$2y$12$20YXM1bzA9UgdVSe3C64HOqwJSTPwRWTF4/ncblEZlhzMDREwDjPS', 0, NULL, '2025-06-06 12:33:25', '2025-06-15 19:16:45', NULL, NULL),
(9, 'Nursyamimi Ahmad', 'nursyahminamosdy03@gmail.com', 'PSD002', '+60132109876', 2, 0, '2025-06-09 08:42:58', '$2y$12$cPbcDnf0sNdbjMyYga/C0.9ca9QfQ4SghHVOcAq3PWpIy41WHCs9G', 0, NULL, '2025-06-09 08:41:59', '2025-06-15 19:16:45', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `violation_escalations`
--

CREATE TABLE `violation_escalations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `escalation_rule_id` bigint(20) UNSIGNED NOT NULL,
  `warning_count` int(11) NOT NULL DEFAULT 0,
  `escalation_triggered_at` timestamp NULL DEFAULT NULL,
  `escalation_action_taken` varchar(255) DEFAULT NULL,
  `notified_parties` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`notified_parties`)),
  `reset_at` timestamp NULL DEFAULT NULL,
  `status` enum('active','resolved','reset') NOT NULL DEFAULT 'active',
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `warnings`
--

CREATE TABLE `warnings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `report_id` bigint(20) UNSIGNED NOT NULL,
  `suggested_by` bigint(20) UNSIGNED NOT NULL,
  `type` enum('minor','moderate','severe') NOT NULL,
  `reason` text NOT NULL,
  `suggested_action` text NOT NULL,
  `status` enum('pending','approved','rejected','sent') NOT NULL DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `template_id` bigint(20) UNSIGNED DEFAULT NULL,
  `email_sent_at` timestamp NULL DEFAULT NULL,
  `email_delivery_status` enum('pending','sent','failed','bounced') NOT NULL DEFAULT 'pending',
  `approved_by` bigint(20) UNSIGNED DEFAULT NULL,
  `admin_notes` text DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `sent_at` timestamp NULL DEFAULT NULL,
  `recipient_id` bigint(20) UNSIGNED DEFAULT NULL,
  `warning_message` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `warnings`
--

INSERT INTO `warnings` (`id`, `report_id`, `suggested_by`, `type`, `reason`, `suggested_action`, `status`, `created_at`, `updated_at`, `template_id`, `email_sent_at`, `email_delivery_status`, `approved_by`, `admin_notes`, `approved_at`, `sent_at`, `recipient_id`, `warning_message`) VALUES
(1, 4, 2, 'minor', 'Failure to wear required personal protective equipment', 'Attend safety training and ensure proper PPE usage', 'sent', '2025-06-07 15:52:19', '2025-06-07 15:53:11', NULL, '2025-06-07 15:53:11', 'sent', 1, NULL, '2025-06-07 15:52:19', '2025-06-07 15:53:11', 1, NULL),
(2, 5, 2, 'minor', 'Test violation for email system testing', 'Complete safety training', 'approved', '2025-06-07 16:00:38', '2025-06-07 16:00:38', NULL, NULL, 'pending', 1, NULL, '2025-06-07 16:00:38', NULL, 1, NULL),
(6, 1, 2, 'minor', 'gentle reminder', 'test test', 'sent', '2025-06-07 17:58:40', '2025-06-08 04:46:38', NULL, '2025-06-08 04:46:38', 'sent', 1, NULL, '2025-06-07 18:00:07', '2025-06-08 04:46:38', 3, 'Warning Letter for: NurSyazwina Mahmud (PSD001)\r\nDepartment: Port Safety & Security Department (PSD)\r\n\r\ngentle reminder\r\n\r\nSuggested Action: test test'),
(7, 9, 2, 'minor', 'gentle reminder', 'increase supervision for 2 weeks', 'sent', '2025-06-09 10:25:03', '2025-06-09 10:36:01', NULL, '2025-06-09 10:36:01', 'sent', 1, NULL, '2025-06-09 10:35:45', '2025-06-09 10:36:01', 4, 'Warning Letter for: Muhammad Iqbal (MNT001)\r\nDepartment: Maintenance & Repair Department (M&R)\r\n\r\ngentle reminder\r\n\r\nSuggested Action: increase supervision for 2 weeks'),
(8, 9, 2, 'moderate', 'GENTLE REMINDER', '\"Complete safety certification course\"', 'pending', '2025-06-15 16:09:47', '2025-06-15 16:09:47', NULL, NULL, 'pending', NULL, NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `warning_templates`
--

CREATE TABLE `warning_templates` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `violation_type` enum('unsafe_act','unsafe_condition','general') NOT NULL,
  `warning_level` enum('minor','moderate','severe') NOT NULL,
  `subject_template` varchar(255) NOT NULL,
  `body_template` text NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_by` bigint(20) UNSIGNED NOT NULL,
  `version` int(11) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `warning_templates`
--

INSERT INTO `warning_templates` (`id`, `name`, `violation_type`, `warning_level`, `subject_template`, `body_template`, `is_active`, `created_by`, `version`, `created_at`, `updated_at`) VALUES
(1, 'Minor Safety Violation - First Warning', 'unsafe_act', 'minor', 'Safety Warning: Minor Violation - {{employee_name}}', 'Dear {{employee_name}},\n\nThis letter serves as a formal warning regarding a minor safety violation that occurred on {{violation_date}}.\n\nViolation Details:\n- Employee ID: {{employee_id}}\n- Department: {{department}}\n- Violation Type: {{violation_type}}\n- Description: {{violation_description}}\n\nThis is considered a minor violation and serves as a reminder to follow all safety protocols. Please take the following corrective action:\n\n{{corrective_action}}\n\nWe trust that this reminder will help prevent future incidents. Please ensure strict adherence to all safety procedures.\n\nIf you have any questions about this warning or need clarification on safety procedures, please contact your supervisor immediately.\n\nBest regards,\n{{supervisor_name}}\n{{company_name}} Safety Department', 1, 1, 1, '2025-06-08 04:21:10', '2025-06-08 04:21:10'),
(2, 'Moderate Safety Violation - Formal Warning', 'unsafe_act', 'moderate', 'FORMAL WARNING: Moderate Safety Violation - {{employee_name}}', 'Dear {{employee_name}},\n\nThis letter serves as a formal MODERATE warning regarding a safety violation that occurred on {{violation_date}}.\n\nViolation Details:\n- Employee ID: {{employee_id}}\n- Department: {{department}}\n- Violation Type: {{violation_type}}\n- Description: {{violation_description}}\n\nThis is a moderate violation that requires immediate attention. This warning indicates a pattern of safety non-compliance that must be addressed.\n\nRequired Corrective Actions:\n{{corrective_action}}\n\nIMPORTANT NOTICE:\n- This warning will remain on your employment record\n- Further violations may result in severe disciplinary action\n- You are required to attend additional safety training within 7 days\n- Your supervisor will monitor your compliance closely\n\nPlease acknowledge receipt of this warning and confirm your understanding of the corrective actions required.\n\nRegards,\n{{supervisor_name}}\n{{company_name}} Safety Department', 1, 1, 1, '2025-06-08 04:21:10', '2025-06-08 04:21:10');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin_settings`
--
ALTER TABLE `admin_settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `admin_settings_key_unique` (`key`);

--
-- Indexes for table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `comment_attachments`
--
ALTER TABLE `comment_attachments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `comment_attachments_remark_id_created_at_index` (`remark_id`,`created_at`);

--
-- Indexes for table `departments`
--
ALTER TABLE `departments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `departments_name_unique` (`name`),
  ADD UNIQUE KEY `departments_email_unique` (`email`);

--
-- Indexes for table `escalation_rules`
--
ALTER TABLE `escalation_rules`
  ADD PRIMARY KEY (`id`),
  ADD KEY `escalation_rules_created_by_foreign` (`created_by`),
  ADD KEY `escalation_rules_violation_type_is_active_index` (`violation_type`,`is_active`);

--
-- Indexes for table `escalation_warnings`
--
ALTER TABLE `escalation_warnings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `escalation_warnings_violation_escalation_id_warning_id_unique` (`violation_escalation_id`,`warning_id`),
  ADD KEY `escalation_warnings_warning_id_foreign` (`warning_id`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indexes for table `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `model_has_permissions`
--
ALTER TABLE `model_has_permissions`
  ADD PRIMARY KEY (`permission_id`,`model_id`,`model_type`),
  ADD KEY `model_has_permissions_model_id_model_type_index` (`model_id`,`model_type`);

--
-- Indexes for table `model_has_roles`
--
ALTER TABLE `model_has_roles`
  ADD PRIMARY KEY (`role_id`,`model_id`,`model_type`),
  ADD KEY `model_has_roles_model_id_model_type_index` (`model_id`,`model_type`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `notifications_notifiable_type_notifiable_id_index` (`notifiable_type`,`notifiable_id`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `permissions_name_guard_name_unique` (`name`,`guard_name`);

--
-- Indexes for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`);

--
-- Indexes for table `remarks`
--
ALTER TABLE `remarks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `remarks_user_id_foreign` (`user_id`),
  ADD KEY `remarks_department_id_foreign` (`department_id`),
  ADD KEY `remarks_user_type_department_id_index` (`user_type`,`department_id`),
  ADD KEY `remarks_report_id_user_type_index` (`report_id`,`user_type`),
  ADD KEY `remarks_edited_by_foreign` (`edited_by`),
  ADD KEY `remarks_parent_id_thread_level_index` (`parent_id`,`thread_level`),
  ADD KEY `remarks_report_id_parent_id_index` (`report_id`,`parent_id`),
  ADD KEY `remarks_user_type_department_id_deleted_at_index` (`user_type`,`department_id`,`deleted_at`),
  ADD KEY `remarks_created_at_deleted_at_index` (`created_at`,`deleted_at`);

--
-- Indexes for table `reminders`
--
ALTER TABLE `reminders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `reminders_report_id_foreign` (`report_id`),
  ADD KEY `reminders_sent_by_foreign` (`sent_by`);

--
-- Indexes for table `reports`
--
ALTER TABLE `reports`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `reports_formatted_id_unique` (`formatted_id`),
  ADD KEY `reports_handling_department_id_foreign` (`handling_department_id`),
  ADD KEY `reports_handling_staff_id_foreign` (`handling_staff_id`),
  ADD KEY `reports_user_id_foreign` (`user_id`);

--
-- Indexes for table `report_status_history`
--
ALTER TABLE `report_status_history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `report_status_history_changed_by_foreign` (`changed_by`),
  ADD KEY `report_status_history_department_id_foreign` (`department_id`),
  ADD KEY `report_status_history_report_id_created_at_index` (`report_id`,`created_at`),
  ADD KEY `report_status_history_new_status_created_at_index` (`new_status`,`created_at`),
  ADD KEY `report_status_history_changed_by_type_created_at_index` (`changed_by_type`,`created_at`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `roles_name_guard_name_unique` (`name`,`guard_name`);

--
-- Indexes for table `role_has_permissions`
--
ALTER TABLE `role_has_permissions`
  ADD PRIMARY KEY (`permission_id`,`role_id`),
  ADD KEY `role_has_permissions_role_id_foreign` (`role_id`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `tasks`
--
ALTER TABLE `tasks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tasks_user_id_foreign` (`user_id`);

--
-- Indexes for table `unsafe_act_details`
--
ALTER TABLE `unsafe_act_details`
  ADD PRIMARY KEY (`id`),
  ADD KEY `unsafe_act_details_report_id_foreign` (`report_id`);

--
-- Indexes for table `unsafe_condition_details`
--
ALTER TABLE `unsafe_condition_details`
  ADD PRIMARY KEY (`id`),
  ADD KEY `unsafe_condition_details_report_id_foreign` (`report_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`),
  ADD UNIQUE KEY `users_name_unique` (`name`),
  ADD UNIQUE KEY `users_worker_id_unique` (`worker_id`),
  ADD KEY `users_department_id_foreign` (`department_id`);

--
-- Indexes for table `violation_escalations`
--
ALTER TABLE `violation_escalations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `violation_escalations_escalation_rule_id_foreign` (`escalation_rule_id`),
  ADD KEY `violation_escalations_user_id_status_index` (`user_id`,`status`),
  ADD KEY `violation_escalations_escalation_triggered_at_index` (`escalation_triggered_at`),
  ADD KEY `violation_escalations_reset_at_index` (`reset_at`);

--
-- Indexes for table `warnings`
--
ALTER TABLE `warnings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `warnings_report_id_foreign` (`report_id`),
  ADD KEY `warnings_suggested_by_foreign` (`suggested_by`),
  ADD KEY `warnings_template_id_foreign` (`template_id`),
  ADD KEY `warnings_approved_by_foreign` (`approved_by`),
  ADD KEY `warnings_recipient_id_foreign` (`recipient_id`);

--
-- Indexes for table `warning_templates`
--
ALTER TABLE `warning_templates`
  ADD PRIMARY KEY (`id`),
  ADD KEY `warning_templates_created_by_foreign` (`created_by`),
  ADD KEY `warning_templates_violation_type_warning_level_index` (`violation_type`,`warning_level`),
  ADD KEY `warning_templates_is_active_index` (`is_active`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin_settings`
--
ALTER TABLE `admin_settings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `comment_attachments`
--
ALTER TABLE `comment_attachments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `departments`
--
ALTER TABLE `departments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `escalation_rules`
--
ALTER TABLE `escalation_rules`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `escalation_warnings`
--
ALTER TABLE `escalation_warnings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=57;

--
-- AUTO_INCREMENT for table `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `remarks`
--
ALTER TABLE `remarks`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `reminders`
--
ALTER TABLE `reminders`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `reports`
--
ALTER TABLE `reports`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `report_status_history`
--
ALTER TABLE `report_status_history`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `tasks`
--
ALTER TABLE `tasks`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `unsafe_act_details`
--
ALTER TABLE `unsafe_act_details`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `unsafe_condition_details`
--
ALTER TABLE `unsafe_condition_details`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `violation_escalations`
--
ALTER TABLE `violation_escalations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `warnings`
--
ALTER TABLE `warnings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `warning_templates`
--
ALTER TABLE `warning_templates`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `comment_attachments`
--
ALTER TABLE `comment_attachments`
  ADD CONSTRAINT `comment_attachments_remark_id_foreign` FOREIGN KEY (`remark_id`) REFERENCES `remarks` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `escalation_rules`
--
ALTER TABLE `escalation_rules`
  ADD CONSTRAINT `escalation_rules_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `escalation_warnings`
--
ALTER TABLE `escalation_warnings`
  ADD CONSTRAINT `escalation_warnings_violation_escalation_id_foreign` FOREIGN KEY (`violation_escalation_id`) REFERENCES `violation_escalations` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `escalation_warnings_warning_id_foreign` FOREIGN KEY (`warning_id`) REFERENCES `warnings` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `model_has_permissions`
--
ALTER TABLE `model_has_permissions`
  ADD CONSTRAINT `model_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `model_has_roles`
--
ALTER TABLE `model_has_roles`
  ADD CONSTRAINT `model_has_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `remarks`
--
ALTER TABLE `remarks`
  ADD CONSTRAINT `remarks_department_id_foreign` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `remarks_edited_by_foreign` FOREIGN KEY (`edited_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `remarks_parent_id_foreign` FOREIGN KEY (`parent_id`) REFERENCES `remarks` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `remarks_report_id_foreign` FOREIGN KEY (`report_id`) REFERENCES `reports` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `remarks_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `reminders`
--
ALTER TABLE `reminders`
  ADD CONSTRAINT `reminders_report_id_foreign` FOREIGN KEY (`report_id`) REFERENCES `reports` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `reminders_sent_by_foreign` FOREIGN KEY (`sent_by`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `reports`
--
ALTER TABLE `reports`
  ADD CONSTRAINT `reports_handling_department_id_foreign` FOREIGN KEY (`handling_department_id`) REFERENCES `departments` (`id`),
  ADD CONSTRAINT `reports_handling_staff_id_foreign` FOREIGN KEY (`handling_staff_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `reports_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `report_status_history`
--
ALTER TABLE `report_status_history`
  ADD CONSTRAINT `report_status_history_changed_by_foreign` FOREIGN KEY (`changed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `report_status_history_department_id_foreign` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `report_status_history_report_id_foreign` FOREIGN KEY (`report_id`) REFERENCES `reports` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `role_has_permissions`
--
ALTER TABLE `role_has_permissions`
  ADD CONSTRAINT `role_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `role_has_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `tasks`
--
ALTER TABLE `tasks`
  ADD CONSTRAINT `tasks_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `unsafe_act_details`
--
ALTER TABLE `unsafe_act_details`
  ADD CONSTRAINT `unsafe_act_details_report_id_foreign` FOREIGN KEY (`report_id`) REFERENCES `reports` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `unsafe_condition_details`
--
ALTER TABLE `unsafe_condition_details`
  ADD CONSTRAINT `unsafe_condition_details_report_id_foreign` FOREIGN KEY (`report_id`) REFERENCES `reports` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_department_id_foreign` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`);

--
-- Constraints for table `violation_escalations`
--
ALTER TABLE `violation_escalations`
  ADD CONSTRAINT `violation_escalations_escalation_rule_id_foreign` FOREIGN KEY (`escalation_rule_id`) REFERENCES `escalation_rules` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `violation_escalations_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `warnings`
--
ALTER TABLE `warnings`
  ADD CONSTRAINT `warnings_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `warnings_recipient_id_foreign` FOREIGN KEY (`recipient_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `warnings_report_id_foreign` FOREIGN KEY (`report_id`) REFERENCES `reports` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `warnings_suggested_by_foreign` FOREIGN KEY (`suggested_by`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `warnings_template_id_foreign` FOREIGN KEY (`template_id`) REFERENCES `warning_templates` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `warning_templates`
--
ALTER TABLE `warning_templates`
  ADD CONSTRAINT `warning_templates_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
