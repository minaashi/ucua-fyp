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
        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('employee_id');
            $table->string('department');
            $table->string('phone');
            $table->string('non_compliance_type');
            $table->string('location');
            $table->datetime('incident_date');
            $table->text('description');
            $table->string('category')->nullable(); // Will be set by admin (Unsafe Act/Condition)
            $table->enum('status', ['pending', 'review', 'resolved'])->default('pending');
            $table->boolean('is_anonymous')->default(false);
            $table->foreignId('handling_department_id')->nullable()->constrained('departments');
            $table->foreignId('handling_staff_id')->nullable()->constrained('users');
            $table->text('remarks')->nullable();
            $table->date('deadline')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};
