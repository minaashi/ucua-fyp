-- Reset Auto-Increment Values for UCUA System
-- This script resets the auto-increment values for main tables to start from 1

-- Reset auto-increment for users table
ALTER TABLE `users` AUTO_INCREMENT = 1;

-- Reset auto-increment for departments table  
ALTER TABLE `departments` AUTO_INCREMENT = 1;

-- Reset auto-increment for reports table
ALTER TABLE `reports` AUTO_INCREMENT = 1;

-- Reset auto-increment for other important tables
ALTER TABLE `warnings` AUTO_INCREMENT = 1;
ALTER TABLE `reminders` AUTO_INCREMENT = 1;
ALTER TABLE `remarks` AUTO_INCREMENT = 1;
ALTER TABLE `notifications` AUTO_INCREMENT = 1;
ALTER TABLE `report_status_history` AUTO_INCREMENT = 1;

-- Display current auto-increment values to verify
SELECT 
    TABLE_NAME,
    AUTO_INCREMENT
FROM 
    information_schema.TABLES 
WHERE 
    TABLE_SCHEMA = DATABASE()
    AND AUTO_INCREMENT IS NOT NULL
ORDER BY 
    TABLE_NAME;
