<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('warnings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('report_id')->constrained()->onDelete('cascade');
            $table->foreignId('suggested_by')->constrained('users')->onDelete('cascade');
            $table->enum('type', ['minor', 'moderate', 'severe']);
            $table->text('reason');
            $table->text('suggested_action');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('warnings');
    }
}; 