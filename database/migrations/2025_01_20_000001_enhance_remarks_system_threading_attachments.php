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
        // Add threading and attachment support to remarks table
        Schema::table('remarks', function (Blueprint $table) {
            // Threading support
            $table->foreignId('parent_id')->nullable()->constrained('remarks')->onDelete('cascade')->after('department_id');
            $table->integer('thread_level')->default(0)->after('parent_id');
            $table->integer('reply_count')->default(0)->after('thread_level');
            
            // File attachment support
            $table->string('attachment_path')->nullable()->after('content');
            $table->string('attachment_name')->nullable()->after('attachment_path');
            $table->string('attachment_type')->nullable()->after('attachment_name');
            $table->bigInteger('attachment_size')->nullable()->after('attachment_type');
            
            // Enhanced metadata
            $table->boolean('is_edited')->default(false)->after('attachment_size');
            $table->timestamp('edited_at')->nullable()->after('is_edited');
            $table->foreignId('edited_by')->nullable()->constrained('users')->onDelete('set null')->after('edited_at');
            
            // Soft deletes for better audit trail
            $table->softDeletes()->after('updated_at');
        });

        // Add indexes for better performance
        Schema::table('remarks', function (Blueprint $table) {
            $table->index(['parent_id', 'thread_level']);
            $table->index(['report_id', 'parent_id']);
            $table->index(['user_type', 'department_id', 'deleted_at']);
            $table->index(['created_at', 'deleted_at']);
        });

        // Create comment_attachments table for better file management
        Schema::create('comment_attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('remark_id')->constrained('remarks')->onDelete('cascade');
            $table->string('file_path');
            $table->string('original_name');
            $table->string('file_type');
            $table->bigInteger('file_size');
            $table->string('mime_type');
            $table->string('disk')->default('public');
            $table->timestamps();
            
            $table->index(['remark_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('comment_attachments');
        
        Schema::table('remarks', function (Blueprint $table) {
            // Drop indexes first
            $table->dropIndex(['parent_id', 'thread_level']);
            $table->dropIndex(['report_id', 'parent_id']);
            $table->dropIndex(['user_type', 'department_id', 'deleted_at']);
            $table->dropIndex(['created_at', 'deleted_at']);
            
            // Drop foreign key constraints
            $table->dropForeign(['parent_id']);
            $table->dropForeign(['edited_by']);
            
            // Drop columns
            $table->dropColumn([
                'parent_id',
                'thread_level', 
                'reply_count',
                'attachment_path',
                'attachment_name',
                'attachment_type',
                'attachment_size',
                'is_edited',
                'edited_at',
                'edited_by',
                'deleted_at'
            ]);
        });
    }
};
