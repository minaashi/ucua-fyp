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
        Schema::create('report_status_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('report_id')->constrained()->onDelete('cascade');
            $table->string('previous_status')->nullable();
            $table->string('new_status');
            $table->foreignId('changed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('department_id')->nullable()->constrained()->onDelete('set null');
            $table->enum('changed_by_type', ['user', 'department', 'ucua_officer', 'admin'])->default('user');
            $table->text('reason')->nullable();
            $table->json('metadata')->nullable(); // Store additional context like deadline changes
            $table->timestamps();

            // Indexes for better performance
            $table->index(['report_id', 'created_at']);
            $table->index(['new_status', 'created_at']);
            $table->index(['changed_by_type', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('report_status_history');
    }
};
