-- Clear Data and Reset Auto-Increment to 1
-- This script clears all data from main tables and resets auto-increment to 1

-- Disable foreign key checks temporarily
SET FOREIGN_KEY_CHECKS = 0;

-- Clear dependent tables first
TRUNCATE TABLE `report_status_history`;
TRUNCATE TABLE `remarks`;
TRUNCATE TABLE `warnings`;
TRUNCATE TABLE `reminders`;
TRUNCATE TABLE `notifications`;
TRUNCATE TABLE `model_has_roles`;
TRUNCATE TABLE `personal_access_tokens`;

-- Clear main tables
TRUNCATE TABLE `reports`;
TRUNCATE TABLE `users`;
TRUNCATE TABLE `departments`;

-- Re-enable foreign key checks
SET FOREIGN_KEY_CHECKS = 1;

-- Reset auto-increment values to 1
ALTER TABLE `users` AUTO_INCREMENT = 1;
ALTER TABLE `departments` AUTO_INCREMENT = 1;
ALTER TABLE `reports` AUTO_INCREMENT = 1;

-- Display confirmation
SELECT 'Data cleared and auto-increment reset to 1' as Status;
