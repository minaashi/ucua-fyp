<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create the admin role
        Role::create(['name' => 'admin']);
        
        // Create the port_worker role
        Role::create(['name' => 'port_worker']);
    }
}
