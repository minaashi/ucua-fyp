<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Department;
use Illuminate\Support\Facades\Hash;

class UCUAOfficerSeeder extends Seeder
{
    public function run(): void
    {
        // Find the SS Department
        $department = Department::where('name', 'SS Department')->first();

        $officer = User::create([
            'name' => 'UCUA Officer',
            'email' => 'ucua.officer@port.com',
            'password' => Hash::make('password123'),
            'department_id' => $department ? $department->id : null,
            'email_verified_at' => now(), // Mark as verified since manually created
        ]);

        // Assign role with the correct guard
        $officer->assignRole('ucua_officer');
    }
}