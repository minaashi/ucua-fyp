# UCUA Database Management Guide

## Table of Contents
1. [Clearing the Database](#clearing-the-database)
2. [Manual Data Insertion](#manual-data-insertion)
3. [Using Laravel Seeders](#using-laravel-seeders)
4. [Database Structure Overview](#database-structure-overview)
5. [Common Commands](#common-commands)
6. [Troubleshooting](#troubleshooting)

## Clearing the Database

### Method 1: Laravel Artisan Commands (Recommended)

#### Fresh Migration (Drops and Recreates All Tables)
```bash
php artisan migrate:fresh
```

#### Fresh Migration with Seeding
```bash
php artisan migrate:fresh --seed
```

#### Reset and Re-run Migrations
```bash
php artisan migrate:reset
php artisan migrate
```

#### Rollback Specific Steps
```bash
# Rollback last migration batch
php artisan migrate:rollback

# Rollback specific number of batches
php artisan migrate:rollback --step=5

# Rollback all migrations
php artisan migrate:rollback --step=1000
```

### Method 2: Manual SQL Script
Use the `database_clear_script.sql` file provided to manually clear all tables.

## Manual Data Insertion

### Method 1: Using SQL Script
1. Run `database_clear_script.sql` to clear the database
2. Run `manual_data_insertion.sql` to insert basic data

### Method 2: Using Laravel Tinker
```bash
php artisan tinker
```

Then run PHP commands:
```php
// Create a department
$dept = App\Models\Department::create([
    'name' => 'Security Department',
    'email' => 'security@port.com',
    'password' => Hash::make('Security@123'),
    'head_name' => 'John Security',
    'head_email' => 'john@security.com',
    'head_phone' => '+1234567890',
    'is_active' => true
]);

// Create a user
$user = App\Models\User::create([
    'name' => 'Test User',
    'email' => 'test@example.com',
    'password' => Hash::make('password123'),
    'department_id' => 1,
    'worker_id' => 'PW001',
    'email_verified_at' => now()
]);

// Assign role to user
$user->assignRole('port_worker');

// Create a report
$report = App\Models\Report::create([
    'user_id' => $user->id,
    'employee_id' => 'PW001',
    'phone' => '+1234567890',
    'unsafe_condition' => 'Slippery floor',
    'location' => 'Dock Area A',
    'incident_date' => now(),
    'description' => 'Water spillage creating slip hazard',
    'status' => 'pending',
    'category' => 'unsafe_condition',
    'is_anonymous' => false,
    'handling_department_id' => 1,
    'deadline' => now()->addDays(5)
]);
```

## Using Laravel Seeders

### Run All Seeders
```bash
php artisan db:seed
```

### Run Specific Seeders
```bash
php artisan db:seed --class=DepartmentSeeder
php artisan db:seed --class=RoleSeeder
php artisan db:seed --class=UCUAOfficerSeeder
php artisan db:seed --class=TestDataSeeder
```

### Create New Seeder
```bash
php artisan make:seeder CustomDataSeeder
```

## Database Structure Overview

### Main Tables:
- **users**: System users (admin, UCUA officers, port workers)
- **departments**: Port departments with login credentials
- **reports**: Safety incident reports
- **remarks**: Comments on reports
- **warnings**: Warning letters issued
- **reminders**: Deadline reminders
- **roles**: User permission roles
- **notifications**: System notifications

### Key Relationships:
- Users belong to departments
- Reports are handled by departments
- Reports can have multiple remarks
- Users can have multiple roles
- Warnings are linked to reports

## Common Commands

### Check Migration Status
```bash
php artisan migrate:status
```

### Create New Migration
```bash
php artisan make:migration create_new_table
```

### Create Model with Migration
```bash
php artisan make:model ModelName -m
```

### Database Information
```bash
# Show database configuration
php artisan config:show database

# Test database connection
php artisan db:show
```

## Troubleshooting

### Foreign Key Constraint Errors
If you get foreign key errors when clearing data:
```sql
SET FOREIGN_KEY_CHECKS = 0;
-- Your DELETE/TRUNCATE statements here
SET FOREIGN_KEY_CHECKS = 1;
```

### Permission Errors
If you get permission-related errors:
```bash
# Clear permission cache
php artisan permission:cache-reset

# Or in tinker:
app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
```

### Migration Errors
```bash
# If migrations are stuck, try:
php artisan migrate:reset
php artisan migrate

# Or force fresh migration:
php artisan migrate:fresh --force
```

## Default Credentials After Fresh Setup

### Admin Login
- **Email**: admin@gmail.com
- **Password**: Admin@123
- **URL**: /admin/login

### UCUA Officer Login
- **Email**: ucua@gmail.com
- **Password**: TestPassword123!
- **URL**: /ucua/login

### Port Worker Login
- **Email**: worker@gmail.com
- **Password**: Worker123!
- **URL**: /login

### Department Login (PSD)
- **Email**: psd@port.com
- **Password**: Security@Port25
- **URL**: /department/login

## Important Notes

1. **Always backup your database** before clearing it
2. **Use Laravel commands** when possible instead of direct SQL
3. **Test in development** environment first
4. **Check foreign key relationships** when manually inserting data
5. **Run seeders after fresh migration** to get consistent test data
6. **Clear caches** after major database changes:
   ```bash
   php artisan cache:clear
   php artisan config:clear
   php artisan route:clear
   php artisan view:clear
   ```
