<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::table('reports', function (Blueprint $table) {
        // Add status column with a default value of 'pending'
        $table->string('status')->default('pending');
    });
}

public function down()
{
    Schema::table('reports', function (Blueprint $table) {
        // Rollback the change by dropping the 'status' column
        $table->dropColumn('status');
    });
}

};
