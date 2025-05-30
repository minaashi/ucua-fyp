<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Department;
use Illuminate\Support\Facades\Hash;

class SetupDepartment extends Command
{
    protected $signature = 'department:setup {departmentEmail} {departmentName} {password}';
    protected $description = 'Set up a department with proper authentication';

    public function handle()
    {
        $departmentEmail = $this->argument('departmentEmail');
        $departmentName = $this->argument('departmentName');
        $password = $this->argument('password');

        $department = Department::updateOrCreate(
            ['email' => $departmentEmail],
            [
                'name' => $departmentName,
                'password' => Hash::make($password),
                // You might want to add default head_name, head_email, head_phone here if they are required
                // For example:
                // 'head_name' => $departmentName . ' Head',
                // 'head_email' => 'head_' . $departmentEmail,
                // 'head_phone' => 'N/A',
                'is_active' => true
            ]
        );

        $this->info("Department {$department->name} has been set up successfully.");
        $this->info("Email: {$department->email}");
        $this->info("Password: {$password}");
        $this->warn("Remember to use this email and password on the dedicated department login page.");
    }
} 