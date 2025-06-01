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
        Schema::create('escalation_warnings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('violation_escalation_id')->constrained()->onDelete('cascade');
            $table->foreignId('warning_id')->constrained()->onDelete('cascade');
            $table->timestamps();

            $table->unique(['violation_escalation_id', 'warning_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('escalation_warnings');
    }
};
