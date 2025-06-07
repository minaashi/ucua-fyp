<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // First, handle any duplicate data before adding unique constraints
        $this->handleDuplicateData();

        // Add unique constraints
        $this->addUniqueConstraints();

        // Reset auto-increment values
        $this->resetAutoIncrement();
    }

    /**
     * Handle duplicate data before adding unique constraints
     */
    private function handleDuplicateData(): void
    {
        // Handle duplicate user names
        $duplicateUsers = DB::select("
            SELECT name, COUNT(*) as count
            FROM users
            GROUP BY name
            HAVING COUNT(*) > 1
        ");

        foreach ($duplicateUsers as $duplicate) {
            $users = DB::table('users')->where('name', $duplicate->name)->get();
            $counter = 1;

            foreach ($users as $user) {
                if ($counter > 1) {
                    DB::table('users')
                        ->where('id', $user->id)
                        ->update(['name' => $duplicate->name . '_' . $counter]);
                }
                $counter++;
            }
        }

        // Handle duplicate department names
        $duplicateDepartments = DB::select("
            SELECT name, COUNT(*) as count
            FROM departments
            GROUP BY name
            HAVING COUNT(*) > 1
        ");

        foreach ($duplicateDepartments as $duplicate) {
            $departments = DB::table('departments')->where('name', $duplicate->name)->get();
            $counter = 1;

            foreach ($departments as $department) {
                if ($counter > 1) {
                    DB::table('departments')
                        ->where('id', $department->id)
                        ->update(['name' => $duplicate->name . '_' . $counter]);
                }
                $counter++;
            }
        }

        // Handle duplicate department emails
        $duplicateEmails = DB::select("
            SELECT email, COUNT(*) as count
            FROM departments
            GROUP BY email
            HAVING COUNT(*) > 1
        ");

        foreach ($duplicateEmails as $duplicate) {
            $departments = DB::table('departments')->where('email', $duplicate->email)->get();
            $counter = 1;

            foreach ($departments as $department) {
                if ($counter > 1) {
                    $emailParts = explode('@', $duplicate->email);
                    $newEmail = $emailParts[0] . '_' . $counter . '@' . $emailParts[1];
                    DB::table('departments')
                        ->where('id', $department->id)
                        ->update(['email' => $newEmail]);
                }
                $counter++;
            }
        }
    }

    /**
     * Add unique constraints to tables
     */
    private function addUniqueConstraints(): void
    {
        // Add unique constraint to users.name
        Schema::table('users', function (Blueprint $table) {
            $table->unique('name');
        });

        // Add unique constraints to departments
        Schema::table('departments', function (Blueprint $table) {
            $table->unique('name');

            // Check if email unique constraint already exists
            $indexes = DB::select("SHOW INDEX FROM departments WHERE Column_name = 'email'");
            $hasUniqueEmail = false;

            foreach ($indexes as $index) {
                if ($index->Non_unique == 0) { // 0 means unique
                    $hasUniqueEmail = true;
                    break;
                }
            }

            if (!$hasUniqueEmail) {
                $table->unique('email');
            }
        });
    }

    /**
     * Reset auto-increment values to 1
     */
    private function resetAutoIncrement(): void
    {
        // Reset auto-increment for users table
        DB::statement('ALTER TABLE users AUTO_INCREMENT = 1');

        // Reset auto-increment for departments table
        DB::statement('ALTER TABLE departments AUTO_INCREMENT = 1');

        // Reset auto-increment for reports table
        DB::statement('ALTER TABLE reports AUTO_INCREMENT = 1');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove unique constraints
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique(['name']);
        });

        Schema::table('departments', function (Blueprint $table) {
            $table->dropUnique(['name']);
            // Note: We don't drop email unique constraint as it might have existed before
        });

        // Note: Auto-increment values cannot be "reversed" to their previous state
        // as this would require knowing what the previous values were
    }
};
