<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use App\Models\Department;
use Spatie\Permission\Models\Role;

try {
    echo "Starting data restoration...\n";

    // Create Department
    $department = Department::firstOrCreate([
        'name' => 'Port Security Department (PSD)'
    ], [
        'email' => 'psd@port.com',
        'password' => bcrypt('Security@Port25'),
        'head_name' => 'Security Head',
        'head_email' => 'security@port.com',
        'head_phone' => '+1234567890',
        'is_active' => 1
    ]);
    echo "Department created: {$department->name}\n";

    // Create Roles with web guard
    $adminRole = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
    $ucuaRole = Role::firstOrCreate(['name' => 'ucua_officer', 'guard_name' => 'web']);
    $workerRole = Role::firstOrCreate(['name' => 'port_worker', 'guard_name' => 'web']);
    $deptRole = Role::firstOrCreate(['name' => 'department_head', 'guard_name' => 'web']);
    echo "Roles created with web guard\n";

    // Create Admin User
    $admin = User::firstOrCreate([
        'email' => 'nursyahminabintimosdy@gmail.com'
    ], [
        'name' => 'Admin User',
        'password' => bcrypt('Admin@123'),
        'department_id' => $department->id,
        'is_admin' => true,
        'is_ucua_officer' => false,
        'worker_id' => 'ADM001',
        'email_verified_at' => now()
    ]);
    $admin->assignRole($adminRole);
    echo "Admin user created: {$admin->email}\n";

    // Create UCUA Officer
    $ucua = User::firstOrCreate([
        'email' => 'nazzreezahar@gmail.com'
    ], [
        'name' => 'UCUA Officer',
        'password' => bcrypt('TestPassword123!'),
        'department_id' => $department->id,
        'is_admin' => false,
        'is_ucua_officer' => true,
        'worker_id' => 'UCUA001',
        'email_verified_at' => now()
    ]);
    $ucua->assignRole($ucuaRole);
    echo "UCUA Officer created: {$ucua->email}\n";

    // Create Port Worker
    $worker = User::firstOrCreate([
        'email' => 'worker@gmail.com'
    ], [
        'name' => 'Port Worker',
        'password' => bcrypt('Worker123!'),
        'department_id' => $department->id,
        'is_admin' => false,
        'is_ucua_officer' => false,
        'worker_id' => 'PW001',
        'email_verified_at' => now()
    ]);
    $worker->assignRole($workerRole);
    echo "Port Worker created: {$worker->email}\n";

    echo "\n✅ Essential data restored successfully!\n";
    echo "Admin: nursyahminabintimosdy@gmail.com / Admin@123\n";
    echo "UCUA Officer: nazzreezahar@gmail.com / TestPassword123!\n";
    echo "Port Worker: worker@gmail.com / Worker123!\n";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
