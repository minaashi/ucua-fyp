<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Department;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class CreateDepartmentHead extends Command
{
    protected $signature = 'department:create-head {email} {name} {password}';
    protected $description = 'Create a department head user for a specific department';

    public function handle()
    {
        $email = $this->argument('email');
        $name = $this->argument('name');
        $password = $this->argument('password');

        // Find the department by email
        $department = Department::where('email', 'maintenanceport@gmail.com')->first();

        if (!$department) {
            $this->error('Department not found.');
            return 1;
        }

        // Create the user
        $user = User::create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($password),
            'department_id' => $department->id,
        ]);

        // Assign the department_head role
        $user->assignRole('department_head');

        $this->info("User {$user->email} created and assigned to department {$department->name} with department_head role.");
        return 0;
    }
} 