<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('reports', function (Blueprint $table) {
            $table->timestamp('deadline')->nullable()->after('status');
            $table->string('handling_department')->nullable()->after('deadline');
            $table->text('remarks')->nullable()->after('handling_department');
        });
    }

    public function down()
    {
        Schema::table('reports', function (Blueprint $table) {
            $table->dropColumn(['deadline', 'handling_department', 'remarks']);
        });
    }
}; 