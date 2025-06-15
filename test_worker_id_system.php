<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== Department-Specific Worker ID System Test ===\n\n";

echo "1. Current Departments with Prefixes:\n";
echo "=====================================\n";
$departments = App\Models\Department::all(['id', 'name', 'worker_id_identifier']);
foreach ($departments as $dept) {
    echo sprintf("ID: %d | %-40s | Prefix: %s\n", 
        $dept->id, 
        $dept->name, 
        $dept->worker_id_identifier ?? 'NULL'
    );
}

echo "\n2. Testing Worker ID Generation:\n";
echo "=================================\n";
foreach ($departments as $dept) {
    $nextId = $dept->generateNextWorkerId();
    echo sprintf("%-40s -> Next ID: %s\n", $dept->name, $nextId);
}

echo "\n3. Current Users and their Worker IDs:\n";
echo "======================================\n";
$users = App\Models\User::with('department')->get(['id', 'name', 'worker_id', 'department_id']);
foreach ($users as $user) {
    echo sprintf("%-20s | Worker ID: %-8s | Department: %s\n", 
        $user->name, 
        $user->worker_id ?? 'NULL',
        $user->department->name ?? 'NULL'
    );
}

echo "\n4. Testing Department Worker ID Validation:\n";
echo "===========================================\n";
foreach ($departments as $dept) {
    $prefix = $dept->getWorkerIdPrefix();
    $testId = $prefix . '001';
    $isValid = $dept->ownsWorkerId($testId);
    echo sprintf("Department: %-40s | Test ID: %-8s | Valid: %s\n", 
        $dept->name, 
        $testId, 
        $isValid ? 'YES' : 'NO'
    );
}

echo "\n=== Test Complete ===\n";
