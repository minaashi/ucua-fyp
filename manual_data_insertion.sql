-- Manual Data Insertion Script for UCUA System
-- Run this after clearing the database

-- 1. Insert Roles (Required for user permissions)
INSERT INTO `roles` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
(1, 'admin', 'web', NOW(), NOW()),
(2, 'port_worker', 'web', NOW(), NOW()),
(3, 'ucua_officer', 'web', NOW(), NOW()),
(4, 'department_head', 'web', NOW(), NOW());

-- 2. Insert Departments
INSERT INTO `departments` (`id`, `name`, `email`, `password`, `head_name`, `head_email`, `head_phone`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Port Security Department (PSD)', 'psd@port.com', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'John Security', 'john.security@port.com', '+1234567890', 1, NOW(), NOW()),
(2, 'Operations Department', 'operations@port.com', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Jane Operations', 'jane.ops@port.com', '+1234567891', 1, NOW(), NOW()),
(3, 'Maintenance Department', 'maintenance@port.com', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Mike Maintenance', 'mike.maint@port.com', '+1234567892', 1, NOW(), NOW()),
(4, 'Safety Department', 'safety@port.com', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Sarah Safety', 'sarah.safety@port.com', '+1234567893', 1, NOW(), NOW());

-- 3. Insert Admin User
INSERT INTO `users` (`id`, `name`, `email`, `password`, `department_id`, `is_admin`, `worker_id`, `email_verified_at`, `created_at`, `updated_at`) VALUES
(1, 'UCUA Admin', 'admin@gmail.com', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1, 1, 'ADM001', NOW(), NOW(), NOW());

-- 4. Insert UCUA Officer
INSERT INTO `users` (`id`, `name`, `email`, `password`, `department_id`, `is_admin`, `worker_id`, `email_verified_at`, `created_at`, `updated_at`) VALUES
(2, 'UCUA Officer', 'ucua@gmail.com', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1, 0, 'UCUA001', NOW(), NOW(), NOW());

-- 5. Insert Test Port Workers
INSERT INTO `users` (`id`, `name`, `email`, `password`, `department_id`, `is_admin`, `worker_id`, `email_verified_at`, `created_at`, `updated_at`) VALUES
(3, 'Port Worker', 'worker@gmail.com', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 2, 0, 'PW001', NOW(), NOW(), NOW()),
(4, 'John Doe', 'john.doe@port.com', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 2, 0, 'PW002', NOW(), NOW(), NOW()),
(5, 'Jane Smith', 'jane.smith@port.com', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 3, 0, 'PW003', NOW(), NOW(), NOW());

-- 6. Assign Roles to Users
INSERT INTO `model_has_roles` (`role_id`, `model_type`, `model_id`) VALUES
(1, 'App\\Models\\User', 1),  -- Admin role to admin user
(3, 'App\\Models\\User', 2),  -- UCUA officer role to UCUA user
(2, 'App\\Models\\User', 3),  -- Port worker role to worker
(2, 'App\\Models\\User', 4),  -- Port worker role to John
(2, 'App\\Models\\User', 5);  -- Port worker role to Jane

-- 7. Insert Sample Reports
INSERT INTO `reports` (`id`, `user_id`, `employee_id`, `phone`, `unsafe_condition`, `unsafe_act`, `location`, `incident_date`, `description`, `status`, `category`, `is_anonymous`, `handling_department_id`, `deadline`, `created_at`, `updated_at`) VALUES
(1, 3, 'PW001', '+1234567890', 'Slippery surfaces', NULL, 'Dock Area A', '2025-01-20 10:00:00', 'Water spillage on dock creating slip hazard', 'pending', 'unsafe_condition', 0, 1, '2025-01-25', NOW(), NOW()),
(2, 4, 'PW002', '+1234567891', NULL, 'Not wearing PPE', 'Warehouse B', '2025-01-19 14:30:00', 'Worker observed without safety helmet in active zone', 'in_progress', 'unsafe_act', 0, 4, '2025-01-24', NOW(), NOW()),
(3, NULL, 'ANON001', NULL, 'Damaged equipment', NULL, 'Crane Station 3', '2025-01-18 09:15:00', 'Crane showing signs of wear and potential failure', 'pending', 'unsafe_condition', 1, 3, '2025-01-23', NOW(), NOW());

-- 8. Insert Admin Settings (Optional)
INSERT INTO `admin_settings` (`id`, `setting_key`, `setting_value`, `created_at`, `updated_at`) VALUES
(1, 'auto_archive_days', '90', NOW(), NOW()),
(2, 'email_notifications', '1', NOW(), NOW()),
(3, 'system_maintenance_mode', '0', NOW(), NOW());
