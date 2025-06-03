<?php

namespace App\Services;

use App\Models\Report;
use App\Models\Remark;
use App\Models\User;
use App\Models\Department;
use App\Models\CommentAttachment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\UploadedFile;

class EnhancedRemarkService
{
    /**
     * Add a remark from a regular user with threading and attachment support
     */
    public function addUserRemark(Report $report, string $content, User $user = null, ?UploadedFile $attachment = null, ?int $parentId = null): Remark
    {
        $user = $user ?? Auth::user();
        
        return $this->createRemark($report, $content, [
            'user_id' => $user->id,
            'user_type' => 'user',
            'department_id' => null,
            'parent_id' => $parentId,
            'thread_level' => $parentId ? $this->calculateThreadLevel($parentId) : 0
        ], $attachment);
    }

    /**
     * Add a remark from a UCUA officer with threading and attachment support
     */
    public function addUCUARemark(Report $report, string $content, User $user = null, ?UploadedFile $attachment = null, ?int $parentId = null): Remark
    {
        // UCUA officers use the web guard, not a separate ucua guard
        $user = $user ?? Auth::user();

        return $this->createRemark($report, $content, [
            'user_id' => $user->id,
            'user_type' => 'ucua_officer',
            'department_id' => null,
            'parent_id' => $parentId,
            'thread_level' => $parentId ? $this->calculateThreadLevel($parentId) : 0
        ], $attachment);
    }

    /**
     * Add a remark from an admin with threading and attachment support
     */
    public function addAdminRemark(Report $report, string $content, User $user = null, ?UploadedFile $attachment = null, ?int $parentId = null): Remark
    {
        $user = $user ?? Auth::user();
        
        return $this->createRemark($report, $content, [
            'user_id' => $user->id,
            'user_type' => 'admin',
            'department_id' => null,
            'parent_id' => $parentId,
            'thread_level' => $parentId ? $this->calculateThreadLevel($parentId) : 0
        ], $attachment);
    }

    /**
     * Add a confidential remark from a department with threading and attachment support
     */
    public function addDepartmentRemark(Report $report, string $content, Department $department = null, ?UploadedFile $attachment = null, ?int $parentId = null): Remark
    {
        $department = $department ?? Auth::guard('department')->user();

        // Verify department has access to this report
        if ($report->handling_department_id !== $department->id) {
            throw new \Exception('Department does not have access to this report.');
        }

        return $this->createRemark($report, $content, [
            'user_id' => null,
            'user_type' => 'department',
            'department_id' => $department->id,
            'parent_id' => $parentId,
            'thread_level' => $parentId ? $this->calculateThreadLevel($parentId) : 0
        ], $attachment);
    }

    /**
     * Add a department remark with violator identification update
     */
    public function addDepartmentRemarkWithViolator(
        Report $report,
        string $content,
        string $violatorEmployeeId,
        string $violatorName,
        string $violatorDepartment = null,
        Department $department = null,
        ?UploadedFile $attachment = null,
        ?int $parentId = null
    ): Remark {
        $department = $department ?? Auth::guard('department')->user();

        // Verify department has access to this report
        if ($report->handling_department_id !== $department->id) {
            throw new \Exception('Department does not have access to this report.');
        }

        // Update the report with violator information
        $report->update([
            'violator_employee_id' => $violatorEmployeeId,
            'violator_name' => $violatorName,
            'violator_department' => $violatorDepartment ?? $department->name
        ]);

        // Create the remark with special metadata
        $enhancedContent = $content . "\n\n[INVESTIGATION UPDATE] Violator identified: {$violatorName} (ID: {$violatorEmployeeId})";

        return $this->createRemark($report, $enhancedContent, [
            'user_id' => null,
            'user_type' => 'department',
            'department_id' => $department->id,
            'parent_id' => $parentId,
            'thread_level' => $parentId ? $this->calculateThreadLevel($parentId) : 0
        ], $attachment);
    }

    /**
     * Get threaded remarks for display based on user permissions
     */
    public function getThreadedRemarksForUser(Report $report, $userType = null, $userId = null): \Illuminate\Database\Eloquent\Collection
    {
        // Get user context if not provided
        if (!$userType) {
            $userContext = $this->getUserContext();
            $userType = $userContext['type'];
            $userId = $userContext['id'];
        }

        // Build base query with relationships
        $query = $report->remarks()
            ->with(['user', 'department', 'attachments', 'replies.user', 'replies.department', 'replies.attachments'])
            ->topLevel() // Only get top-level comments first
            ->orderBy('created_at', 'desc');

        // Apply enhanced visibility rules
        $this->applyEnhancedVisibilityRules($query, $userType, $userId, $report);

        $topLevelRemarks = $query->get();

        // Load nested replies with proper visibility filtering
        foreach ($topLevelRemarks as $remark) {
            $remark->setRelation('replies', $this->getFilteredReplies($remark, $userType, $userId, $report));
        }

        return $topLevelRemarks;
    }

    /**
     * Apply enhanced visibility rules based on new strategy
     */
    private function applyEnhancedVisibilityRules($query, $userType, $userId, $report)
    {
        switch ($userType) {
            case 'admin':
            case 'ucua_officer':
                // Full visibility - no filtering needed
                break;
                
            case 'department':
                // Department can see: UCUA, Admin, their own department, original submitter
                $query->where(function($q) use ($userId, $report) {
                    $q->whereIn('user_type', ['ucua_officer', 'admin'])
                      ->orWhere('department_id', $userId)
                      ->orWhere(function($subQ) use ($report) {
                          $subQ->where('user_type', 'user')
                               ->where('user_id', $report->user_id);
                      });
                });
                break;
                
            case 'user':
                // Users can see: UCUA, Admin, their own comments on their own reports
                $query->where(function($q) use ($userId, $report) {
                    $q->whereIn('user_type', ['ucua_officer', 'admin']);
                    
                    // If this is their report, they can also see their own comments
                    if ($report->user_id == $userId) {
                        $q->orWhere(function($subQ) use ($userId) {
                            $subQ->where('user_type', 'user')
                                 ->where('user_id', $userId);
                        });
                    }
                });
                break;
        }
    }

    /**
     * Get filtered replies for a parent comment
     */
    private function getFilteredReplies($parentRemark, $userType, $userId, $report)
    {
        $query = $parentRemark->replies()
            ->with(['user', 'department', 'attachments'])
            ->orderBy('created_at', 'asc');

        $this->applyEnhancedVisibilityRules($query, $userType, $userId, $report);

        $replies = $query->get();

        // Recursively load nested replies
        foreach ($replies as $reply) {
            if ($reply->hasReplies()) {
                $reply->setRelation('replies', $this->getFilteredReplies($reply, $userType, $userId, $report));
            }
        }

        return $replies;
    }

    /**
     * Calculate thread level for nested replies
     */
    private function calculateThreadLevel($parentId): int
    {
        $parent = Remark::find($parentId);
        return $parent ? $parent->thread_level + 1 : 0;
    }

    /**
     * Get current user context
     */
    private function getUserContext(): array
    {
        if (Auth::guard('department')->check()) {
            return ['type' => 'department', 'id' => Auth::guard('department')->id()];
        } elseif (Auth::check()) {
            $user = Auth::user();

            // Check user roles to determine type
            if ($user->hasRole('admin')) {
                return ['type' => 'admin', 'id' => $user->id];
            } elseif ($user->hasRole('ucua_officer')) {
                return ['type' => 'ucua_officer', 'id' => $user->id];
            } else {
                return ['type' => 'user', 'id' => $user->id];
            }
        } else {
            return ['type' => 'guest', 'id' => null];
        }
    }

    /**
     * Handle file attachment upload
     */
    private function handleAttachment(?UploadedFile $file, Remark $remark): ?CommentAttachment
    {
        if (!$file) {
            return null;
        }

        // Validate file
        $this->validateAttachment($file);

        // Generate unique filename
        $filename = time() . '_' . $file->getClientOriginalName();
        $path = 'comment-attachments/' . date('Y/m');
        
        // Store file
        $filePath = $file->storeAs($path, $filename, 'public');

        // Create attachment record
        return CommentAttachment::create([
            'remark_id' => $remark->id,
            'file_path' => $filePath,
            'original_name' => $file->getClientOriginalName(),
            'file_type' => $file->getClientOriginalExtension(),
            'file_size' => $file->getSize(),
            'mime_type' => $file->getMimeType(),
            'disk' => 'public'
        ]);
    }

    /**
     * Validate uploaded attachment
     */
    private function validateAttachment(UploadedFile $file): void
    {
        $allowedTypes = ['jpg', 'jpeg', 'png', 'gif', 'pdf', 'doc', 'docx', 'xls', 'xlsx', 'txt'];
        $maxSize = 10 * 1024 * 1024; // 10MB

        if (!in_array(strtolower($file->getClientOriginalExtension()), $allowedTypes)) {
            throw new \Exception('File type not allowed. Allowed types: ' . implode(', ', $allowedTypes));
        }

        if ($file->getSize() > $maxSize) {
            throw new \Exception('File size too large. Maximum size: 10MB');
        }
    }

    /**
     * Create remark with threading and attachment support
     */
    private function createRemark(Report $report, string $content, array $attributes, ?UploadedFile $attachment = null): Remark
    {
        try {
            $remarkData = array_merge([
                'report_id' => $report->id,
                'content' => $content
            ], $attributes);
            
            $remark = Remark::create($remarkData);
            
            // Handle attachment if provided
            if ($attachment) {
                $this->handleAttachment($attachment, $remark);
            }
            
            Log::info('Enhanced remark created successfully', [
                'remark_id' => $remark->id,
                'report_id' => $report->id,
                'user_type' => $attributes['user_type'],
                'parent_id' => $attributes['parent_id'] ?? null,
                'thread_level' => $attributes['thread_level'] ?? 0,
                'has_attachment' => !is_null($attachment)
            ]);
            
            return $remark;
            
        } catch (\Exception $e) {
            Log::error('Failed to create enhanced remark', [
                'report_id' => $report->id,
                'user_type' => $attributes['user_type'],
                'error' => $e->getMessage()
            ]);
            
            throw new \Exception('Failed to create remark: ' . $e->getMessage());
        }
    }
}
