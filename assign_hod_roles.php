<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use App\Models\Department;
use Spatie\Permission\Models\Role;

echo "Assigning department_head role to users...\n";

// Get the department_head role
$hodRole = Role::where('name', 'department_head')->first();
if (!$hodRole) {
    echo "Error: department_head role not found!\n";
    exit(1);
}

// Get all departments
$departments = Department::all();
echo "Found " . $departments->count() . " departments\n";

foreach ($departments as $department) {
    echo "\nDepartment: " . $department->name . " (ID: " . $department->id . ")\n";
    
    // Get users in this department
    $users = User::where('department_id', $department->id)->get();
    echo "  Users in department: " . $users->count() . "\n";
    
    if ($users->count() > 0) {
        // Assign the first user as HOD
        $hodUser = $users->first();
        
        // Check if user already has department_head role
        if (!$hodUser->hasRole('department_head')) {
            $hodUser->assignRole('department_head');
            echo "  ✓ Assigned " . $hodUser->name . " as HOD\n";
        } else {
            echo "  - " . $hodUser->name . " is already HOD\n";
        }
    } else {
        echo "  ! No users found in this department\n";
    }
}

echo "\nVerifying HOD assignments...\n";
$hodUsers = User::whereHas('roles', function($query) { 
    $query->where('name', 'department_head'); 
})->with('department')->get();

echo "HOD Users found: " . $hodUsers->count() . "\n";
foreach($hodUsers as $hod) {
    echo "HOD: " . $hod->name . " - Department: " . ($hod->department ? $hod->department->name : 'None') . "\n";
}
