# Database Constraints and Auto-Increment Management

## Overview
This guide explains the unique constraints and auto-increment management implemented in the UCUA system to prevent duplicate data and maintain data integrity. The system now supports starting auto-increment from ID 1 and maintains proper department-user relationships.

## Unique Constraints Implemented

### Users Table
- **name**: Unique constraint to prevent duplicate user names
- **email**: Unique constraint (already existed)
- **worker_id**: Unique constraint (already existed)

### Departments Table
- **name**: Unique constraint to prevent duplicate department names
- **email**: Unique constraint to prevent duplicate department emails

### Reports Table
- **id**: Primary key with auto-increment (no additional unique constraints needed)

## Auto-Increment Management

### Current Status
The auto-increment values now start from ID 1 after data clearing:
- **users**: Starting from ID 1 (current: 7 users created)
- **departments**: Starting from ID 1 (current: 4 departments created)
- **reports**: Starting from ID 1 (ready for new reports)

### Department-User Relationships (One-to-Many)
- **Department ID 1 (UCUA Department)**: 2 users
  - Admin User (ADM001)
  - UCUA Officer (UCUA001)
- **Department ID 2 (PSD Department)**: 2 users
  - Port Worker (PSD001)
  - Security Officer 1 (SEC001)
- **Department ID 3 (M&R Department)**: 2 users
  - Maintenance Worker 1 (MNT001)
  - Maintenance Worker 2 (MNT002)
- **Department ID 4 (E&S Department)**: 1 user
  - Electrical Worker 1 (ELC001)

### Important Notes
- Each department can have MANY users
- Each user belongs to ONE department (via department_id foreign key)
- Auto-increment starts from 1 for new data
- Unique constraints prevent duplicate names and worker IDs

## Files Created/Modified

### Migration File
- `database/migrations/2025_06_06_195228_add_unique_constraints_and_reset_auto_increment.php`
  - Handles duplicate data before applying constraints
  - Adds unique constraints to prevent future duplicates
  - Resets auto-increment values appropriately

### Artisan Commands
- `app/Console/Commands/ResetAutoIncrement.php`
  - Custom command: `php artisan db:reset-auto-increment`
  - Can reset specific tables or all main tables
  - Displays current auto-increment values for verification

- `app/Console/Commands/ClearDataAndResetToOne.php`
  - Custom command: `php artisan db:clear-and-reset-to-one`
  - Clears all data and resets auto-increment to 1
  - Includes safety confirmations

### Seeders
- `database/seeders/BasicDataWithDepartmentUsersSeeder.php`
  - Creates sample data with proper department-user relationships
  - Demonstrates one-to-many relationship structure
  - Creates users starting from ID 1

### SQL Scripts
- `database/reset_auto_increment.sql` - Direct SQL commands for resetting auto-increment
- `clear_and_reset_to_one.sql` - Script to clear data and reset to ID 1

## Usage Examples

### Clear Data and Reset to ID 1
```bash
# Clear all data and reset auto-increment to 1 (with confirmation)
php artisan db:clear-and-reset-to-one

# Skip confirmation prompt
php artisan db:clear-and-reset-to-one --confirm
```

### Create Sample Data
```bash
# Create sample data with department-user relationships
php artisan db:seed --class=BasicDataWithDepartmentUsersSeeder
```

### Reset Auto-Increment Values (without clearing data)
```bash
# Reset all main tables
php artisan db:reset-auto-increment

# Reset specific tables
php artisan db:reset-auto-increment --tables=users --tables=departments
```

### Manual SQL Operations
```bash
# Clear data and reset to 1
mysql -u username -p database_name < clear_and_reset_to_one.sql

# Reset auto-increment only
mysql -u username -p database_name < database/reset_auto_increment.sql
```

## Constraint Behavior

### Duplicate Prevention
When attempting to insert duplicate data:
- **User names**: Will throw a database error and prevent insertion
- **Department names**: Will throw a database error and prevent insertion
- **Department emails**: Will throw a database error and prevent insertion
- **Worker IDs**: Will throw a database error and prevent insertion (existing constraint)

### Error Handling
Your application should handle these constraint violations gracefully:
```php
try {
    User::create([
        'name' => 'Existing Name',
        'email' => 'new@email.com',
        // ... other fields
    ]);
} catch (\Illuminate\Database\QueryException $e) {
    if ($e->getCode() === '23000') {
        // Handle duplicate entry error
        return back()->withErrors(['name' => 'This name is already taken.']);
    }
    throw $e;
}
```

## Migration Details

### Duplicate Data Handling
The migration automatically handles existing duplicates by:
1. Finding duplicate names/emails
2. Appending numeric suffixes to duplicates (e.g., "Name_2", "Name_3")
3. Preserving the original record unchanged

### Rollback Support
The migration can be rolled back to remove the unique constraints:
```bash
php artisan migrate:rollback --step=1
```

## Best Practices

1. **Always validate uniqueness** in your forms before database insertion
2. **Handle constraint violations** gracefully in your application
3. **Use the provided commands** for auto-increment management
4. **Test constraint behavior** in development before deploying
5. **Monitor for constraint violations** in production logs

## Troubleshooting

### Common Issues
1. **Migration fails**: Check for existing duplicates using `check_duplicates.php`
2. **Auto-increment not reset**: This is normal behavior when records exist
3. **Constraint violations**: Update your application to handle unique validation

### Support Commands
```bash
# Check migration status
php artisan migrate:status

# View current constraints
php verify_constraints.php

# Reset auto-increment if needed
php artisan db:reset-auto-increment
```
