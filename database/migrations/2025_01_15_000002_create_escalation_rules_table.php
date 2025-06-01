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
        Schema::create('escalation_rules', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('violation_type', ['unsafe_act', 'unsafe_condition', 'general'])->default('unsafe_act');
            $table->integer('warning_threshold')->default(3);
            $table->integer('time_period_months')->default(3);
            $table->enum('escalation_action', ['disciplinary_action', 'supervisor_notification', 'mandatory_training', 'suspension'])->default('disciplinary_action');
            $table->boolean('notify_hod')->default(true);
            $table->boolean('notify_employee')->default(true);
            $table->boolean('notify_department_email')->default(true);
            $table->integer('reset_period_months')->default(6);
            $table->boolean('is_active')->default(true);
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();

            $table->index(['violation_type', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('escalation_rules');
    }
};
