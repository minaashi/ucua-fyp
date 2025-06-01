<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('remarks', function (Blueprint $table) {
            // Add user_type field to distinguish between different types of users
            $table->enum('user_type', ['user', 'department', 'ucua_officer', 'admin'])
                  ->default('user')
                  ->after('user_id');
            
            // Add nullable department_id for department remarks
            $table->foreignId('department_id')->nullable()->constrained()->onDelete('cascade')->after('user_type');
            
            // Make user_id nullable since department remarks won't have a user_id
            $table->foreignId('user_id')->nullable()->change();
        });

        // Add index for better performance
        Schema::table('remarks', function (Blueprint $table) {
            $table->index(['user_type', 'department_id']);
            $table->index(['report_id', 'user_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('remarks', function (Blueprint $table) {
            $table->dropIndex(['user_type', 'department_id']);
            $table->dropIndex(['report_id', 'user_type']);
            $table->dropForeign(['department_id']);
            $table->dropColumn(['user_type', 'department_id']);
            
            // Restore user_id as not nullable
            $table->foreignId('user_id')->nullable(false)->change();
        });
    }
};
