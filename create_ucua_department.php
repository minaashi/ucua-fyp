<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Department;
use Illuminate\Support\Facades\Hash;

try {
    $department = Department::create([
        'name' => 'UCUA Department',
        'email' => 'ucuaDepartment@gmail.com',
        'password' => Hash::make('TestPassword123!'),
        'head_name' => 'UCUA Officer',
        'head_email' => 'ucua.head@gmail.com',
        'head_phone' => '+1234567890',
        'is_active' => true
    ]);

    echo "✅ UCUA Department created successfully!\n";
    echo "ID: " . $department->id . "\n";
    echo "Name: " . $department->name . "\n";
    echo "Email: " . $department->email . "\n";
    echo "Head Name: " . $department->head_name . "\n";
    echo "Head Email: " . $department->head_email . "\n";
    echo "Head Phone: " . $department->head_phone . "\n";
    echo "Status: " . ($department->is_active ? 'Active' : 'Inactive') . "\n";

} catch (Exception $e) {
    echo "❌ Error creating department: " . $e->getMessage() . "\n";
}
