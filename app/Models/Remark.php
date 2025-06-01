<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Remark extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'content',
        'user_id',
        'report_id',
        'user_type',
        'department_id',
        'parent_id',
        'thread_level',
        'reply_count',
        'attachment_path',
        'attachment_name',
        'attachment_type',
        'attachment_size',
        'is_edited',
        'edited_at',
        'edited_by'
    ];

    protected $casts = [
        'user_type' => 'string',
        'thread_level' => 'integer',
        'reply_count' => 'integer',
        'attachment_size' => 'integer',
        'is_edited' => 'boolean',
        'edited_at' => 'datetime'
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
        'edited_at'
    ];

    // Relationships
    public function report()
    {
        return $this->belongsTo(Report::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function editedBy()
    {
        return $this->belongsTo(User::class, 'edited_by');
    }

    // Threading relationships
    public function parent()
    {
        return $this->belongsTo(Remark::class, 'parent_id');
    }

    public function replies()
    {
        return $this->hasMany(Remark::class, 'parent_id')->orderBy('created_at', 'asc');
    }

    public function allReplies()
    {
        return $this->replies()->with('allReplies');
    }

    // Attachment relationships
    public function attachments()
    {
        return $this->hasMany(CommentAttachment::class);
    }

    // Scopes
    public function scopeTopLevel($query)
    {
        return $query->whereNull('parent_id');
    }

    public function scopeReplies($query)
    {
        return $query->whereNotNull('parent_id');
    }

    public function scopeDepartmentRemarks($query)
    {
        return $query->where('user_type', 'department');
    }

    public function scopePublicRemarks($query)
    {
        return $query->where('user_type', '!=', 'department');
    }

    /**
     * Get the author of the remark (either user or department)
     */
    public function getAuthorAttribute()
    {
        switch ($this->user_type) {
            case 'department':
                return $this->department;
            case 'user':
            case 'ucua_officer':
            case 'admin':
            default:
                return $this->user;
        }
    }

    /**
     * Get the author name for display
     */
    public function getAuthorNameAttribute()
    {
        $author = $this->author;
        if (!$author) {
            return 'Unknown User';
        }

        return $this->user_type === 'department' ? $author->name : $author->name;
    }

    /**
     * Check if this is a department remark
     */
    public function isDepartmentRemark()
    {
        return $this->user_type === 'department';
    }

    /**
     * Check if this remark is confidential
     */
    public function isConfidential()
    {
        return $this->isDepartmentRemark();
    }

    /**
     * Check if this is a top-level comment
     */
    public function isTopLevel()
    {
        return is_null($this->parent_id);
    }

    /**
     * Check if this comment has replies
     */
    public function hasReplies()
    {
        return $this->reply_count > 0;
    }

    /**
     * Check if this comment has attachments
     */
    public function hasAttachments()
    {
        return $this->attachments()->count() > 0;
    }

    /**
     * Get the thread depth for display indentation
     */
    public function getIndentationClass()
    {
        $maxIndent = 5; // Limit indentation to prevent excessive nesting
        $level = min($this->thread_level, $maxIndent);
        return 'ml-' . ($level * 4); // Tailwind CSS margin-left classes
    }

    /**
     * Update reply count for parent comment
     */
    public function updateParentReplyCount()
    {
        if ($this->parent_id) {
            $parent = $this->parent;
            if ($parent) {
                $parent->increment('reply_count');
            }
        }
    }

    /**
     * Boot method to handle model events
     */
    protected static function boot()
    {
        parent::boot();

        // Update parent reply count when a reply is created
        static::created(function ($remark) {
            if ($remark->parent_id) {
                $remark->updateParentReplyCount();
            }
        });

        // Update parent reply count when a reply is deleted
        static::deleted(function ($remark) {
            if ($remark->parent_id) {
                $parent = $remark->parent;
                if ($parent && $parent->reply_count > 0) {
                    $parent->decrement('reply_count');
                }
            }
        });
    }
}