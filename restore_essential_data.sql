-- Restore Essential Data for UCUA System
-- This will restore your admin and UCUA officer accounts

-- 1. Insert Department (PSD)
INSERT INTO `departments` (`id`, `name`, `email`, `password`, `head_name`, `head_email`, `head_phone`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Port Security Department (PSD)', 'psd@port.com', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Security Head', 'security@port.com', '+1234567890', 1, NOW(), NOW())
ON DUPLICATE KEY UPDATE name = VALUES(name);

-- 2. Insert Admin User
INSERT INTO `users` (`id`, `name`, `email`, `password`, `department_id`, `is_admin`, `is_ucua_officer`, `worker_id`, `email_verified_at`, `created_at`, `updated_at`) VALUES
(1, 'Admin User', 'nursyahminabintimosdy@gmail.com', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1, 1, 0, 'ADM001', NOW(), NOW(), NOW())
ON DUPLICATE KEY UPDATE name = VALUES(name);

-- 3. Insert UCUA Officer
INSERT INTO `users` (`id`, `name`, `email`, `password`, `department_id`, `is_admin`, `is_ucua_officer`, `worker_id`, `email_verified_at`, `created_at`, `updated_at`) VALUES
(2, 'UCUA Officer', 'nazzreezahar@gmail.com', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1, 0, 1, 'UCUA001', NOW(), NOW(), NOW())
ON DUPLICATE KEY UPDATE name = VALUES(name);

-- 4. Insert Port Worker
INSERT INTO `users` (`id`, `name`, `email`, `password`, `department_id`, `is_admin`, `is_ucua_officer`, `worker_id`, `email_verified_at`, `created_at`, `updated_at`) VALUES
(3, 'Port Worker', 'worker@gmail.com', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1, 0, 0, 'PW001', NOW(), NOW(), NOW())
ON DUPLICATE KEY UPDATE name = VALUES(name);

-- 5. Insert Roles (using web guard)
INSERT INTO `roles` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
(1, 'admin', 'web', NOW(), NOW()),
(2, 'port_worker', 'web', NOW(), NOW()),
(3, 'ucua_officer', 'web', NOW(), NOW()),
(4, 'department_head', 'web', NOW(), NOW())
ON DUPLICATE KEY UPDATE name = VALUES(name);

-- 6. Assign Roles to Users (web guard)
INSERT INTO `model_has_roles` (`role_id`, `model_type`, `model_id`) VALUES
(1, 'App\\Models\\User', 1),  -- Admin role to admin user
(3, 'App\\Models\\User', 2),  -- UCUA officer role to UCUA user  
(2, 'App\\Models\\User', 3)   -- Port worker role to worker
ON DUPLICATE KEY UPDATE role_id = VALUES(role_id);
