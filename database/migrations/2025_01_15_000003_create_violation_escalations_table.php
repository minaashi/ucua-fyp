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
        Schema::create('violation_escalations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('escalation_rule_id')->constrained()->onDelete('cascade');
            $table->integer('warning_count')->default(0);
            $table->timestamp('escalation_triggered_at')->nullable();
            $table->string('escalation_action_taken')->nullable();
            $table->json('notified_parties')->nullable();
            $table->timestamp('reset_at')->nullable();
            $table->enum('status', ['active', 'resolved', 'reset'])->default('active');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'status']);
            $table->index('escalation_triggered_at');
            $table->index('reset_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('violation_escalations');
    }
};
