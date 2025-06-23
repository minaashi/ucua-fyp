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
        // Add session tracking fields to users table
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'last_activity_at')) {
                $table->timestamp('last_activity_at')->nullable()->after('updated_at');
            }
            if (!Schema::hasColumn('users', 'last_login_at')) {
                $table->timestamp('last_login_at')->nullable()->after('last_activity_at');
            }
            if (!Schema::hasColumn('users', 'last_login_ip')) {
                $table->string('last_login_ip', 45)->nullable()->after('last_login_at');
            }
            if (!Schema::hasColumn('users', 'session_id')) {
                $table->string('session_id')->nullable()->after('last_login_ip');
            }
        });

        // Add session tracking fields to departments table
        Schema::table('departments', function (Blueprint $table) {
            if (!Schema::hasColumn('departments', 'last_activity_at')) {
                $table->timestamp('last_activity_at')->nullable()->after('updated_at');
            }
            if (!Schema::hasColumn('departments', 'last_login_at')) {
                $table->timestamp('last_login_at')->nullable()->after('last_activity_at');
            }
            if (!Schema::hasColumn('departments', 'last_login_ip')) {
                $table->string('last_login_ip', 45)->nullable()->after('last_login_at');
            }
            if (!Schema::hasColumn('departments', 'session_id')) {
                $table->string('session_id')->nullable()->after('last_login_ip');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'last_activity_at',
                'last_login_at', 
                'last_login_ip',
                'session_id'
            ]);
        });

        Schema::table('departments', function (Blueprint $table) {
            $table->dropColumn([
                'last_activity_at',
                'last_login_at',
                'last_login_ip', 
                'session_id'
            ]);
        });
    }
};
