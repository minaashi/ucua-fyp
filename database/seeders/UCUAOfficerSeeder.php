<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UCUAOfficerSeeder extends Seeder
{
    public function run(): void
    {
        $officer = User::create([
            'name' => 'UCUA Officer',
            'email' => 'ucua.officer@port.com',
            'password' => Hash::make('password123'),
            'department' => 'SS Department'
        ]);

        $officer->assignRole('ucua_officer');
    }
} 