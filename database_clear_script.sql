-- Script to manually clear all database tables
-- Run this in your database management tool (phpMyAdmin, MySQL Workbench, etc.)

-- Disable foreign key checks to avoid constraint errors
SET FOREIGN_KEY_CHECKS = 0;

-- Clear all tables (in order to avoid foreign key constraints)
TRUNCATE TABLE `escalation_warnings`;
TRUNCATE TABLE `violation_escalations`;
TRUNCATE TABLE `escalation_rules`;
TRUNCATE TABLE `warning_templates`;
TRUNCATE TABLE `warnings`;
TRUNCATE TABLE `reminders`;
TRUNCATE TABLE `remarks`;
TRUNCATE TABLE `unsafe_act_details`;
TRUNCATE TABLE `unsafe_condition_details`;
TRUNCATE TABLE `report_status_history`;
TRUNCATE TABLE `reports`;
TRUNCATE TABLE `tasks`;
TRUNCATE TABLE `notifications`;
TRUNCATE TABLE `admin_settings`;
TRUNCATE TABLE `model_has_permissions`;
TRUNCATE TABLE `model_has_roles`;
TRUNCATE TABLE `role_has_permissions`;
TRUNCATE TABLE `permissions`;
TRUNCATE TABLE `roles`;
TRUNCATE TABLE `personal_access_tokens`;
TRUNCATE TABLE `users`;
TRUNCATE TABLE `departments`;
TRUNCATE TABLE `password_reset_tokens`;
TRUNCATE TABLE `sessions`;
TRUNCATE TABLE `cache`;
TRUNCATE TABLE `cache_locks`;
TRUNCATE TABLE `jobs`;
TRUNCATE TABLE `job_batches`;
TRUNCATE TABLE `failed_jobs`;

-- Re-enable foreign key checks
SET FOREIGN_KEY_CHECKS = 1;

-- Reset auto-increment counters
ALTER TABLE `users` AUTO_INCREMENT = 1;
ALTER TABLE `departments` AUTO_INCREMENT = 1;
ALTER TABLE `reports` AUTO_INCREMENT = 1;
ALTER TABLE `warnings` AUTO_INCREMENT = 1;
ALTER TABLE `reminders` AUTO_INCREMENT = 1;
ALTER TABLE `remarks` AUTO_INCREMENT = 1;
ALTER TABLE `roles` AUTO_INCREMENT = 1;
ALTER TABLE `permissions` AUTO_INCREMENT = 1;
ALTER TABLE `notifications` AUTO_INCREMENT = 1;
ALTER TABLE `tasks` AUTO_INCREMENT = 1;
ALTER TABLE `warning_templates` AUTO_INCREMENT = 1;
ALTER TABLE `escalation_rules` AUTO_INCREMENT = 1;
ALTER TABLE `violation_escalations` AUTO_INCREMENT = 1;
ALTER TABLE `escalation_warnings` AUTO_INCREMENT = 1;
ALTER TABLE `unsafe_act_details` AUTO_INCREMENT = 1;
ALTER TABLE `unsafe_condition_details` AUTO_INCREMENT = 1;
ALTER TABLE `report_status_history` AUTO_INCREMENT = 1;
ALTER TABLE `admin_settings` AUTO_INCREMENT = 1;
