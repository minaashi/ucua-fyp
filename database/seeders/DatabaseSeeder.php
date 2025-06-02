<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
// use Spatie\Permission\Models\Role;
// use Spatie\Permission\Models\Permission;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Use the CleanTestSeeder which matches the current database structure
        $this->call([
            CleanTestSeeder::class,
        ]);
    }
}
