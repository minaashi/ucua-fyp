<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

public function up()
{
    Schema::table('users', function (Blueprint $table) {
        // Add column if it doesn't exist
        if (!Schema::hasColumn('users', 'department_id')) {
            $table->unsignedBigInteger('department_id')->nullable()->after('email');
        }
    });

    // For existing users, assign a default department
    DB::table('users')->whereNull('department_id')->update([
        'department_id' => 1 // Assign to default department
    ]);

    Schema::table('users', function (Blueprint $table) {
        // Now make it required
        $table->unsignedBigInteger('department_id')->nullable(false)->change();
        
        // Add foreign key constraint
        $table->foreign('department_id')->references('id')->on('departments');
    });
}

public function down()
{
    Schema::table('users', function (Blueprint $table) {
        $table->dropForeign(['department_id']);
        $table->dropColumn('department_id');
    });
}
};  