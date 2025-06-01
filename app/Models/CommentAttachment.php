<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Storage;

class CommentAttachment extends Model
{
    use HasFactory;

    protected $fillable = [
        'remark_id',
        'file_path',
        'original_name',
        'file_type',
        'file_size',
        'mime_type',
        'disk'
    ];

    protected $casts = [
        'file_size' => 'integer'
    ];

    /**
     * Get the remark that owns this attachment
     */
    public function remark()
    {
        return $this->belongsTo(Remark::class);
    }

    /**
     * Get the full URL for the attachment
     */
    public function getUrlAttribute()
    {
        return Storage::disk($this->disk)->url($this->file_path);
    }

    /**
     * Get human readable file size
     */
    public function getFormattedSizeAttribute()
    {
        $bytes = $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }

    /**
     * Check if file is an image
     */
    public function getIsImageAttribute()
    {
        return in_array($this->file_type, ['jpg', 'jpeg', 'png', 'gif', 'webp']);
    }

    /**
     * Check if file is a document
     */
    public function getIsDocumentAttribute()
    {
        return in_array($this->file_type, ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx']);
    }

    /**
     * Get icon class for file type
     */
    public function getIconClassAttribute()
    {
        $iconMap = [
            'pdf' => 'fas fa-file-pdf text-red-500',
            'doc' => 'fas fa-file-word text-blue-500',
            'docx' => 'fas fa-file-word text-blue-500',
            'xls' => 'fas fa-file-excel text-green-500',
            'xlsx' => 'fas fa-file-excel text-green-500',
            'ppt' => 'fas fa-file-powerpoint text-orange-500',
            'pptx' => 'fas fa-file-powerpoint text-orange-500',
            'jpg' => 'fas fa-file-image text-purple-500',
            'jpeg' => 'fas fa-file-image text-purple-500',
            'png' => 'fas fa-file-image text-purple-500',
            'gif' => 'fas fa-file-image text-purple-500',
            'webp' => 'fas fa-file-image text-purple-500',
            'txt' => 'fas fa-file-alt text-gray-500',
            'zip' => 'fas fa-file-archive text-yellow-500',
            'rar' => 'fas fa-file-archive text-yellow-500',
        ];

        return $iconMap[$this->file_type] ?? 'fas fa-file text-gray-500';
    }

    /**
     * Delete the physical file when model is deleted
     */
    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($attachment) {
            if (Storage::disk($attachment->disk)->exists($attachment->file_path)) {
                Storage::disk($attachment->disk)->delete($attachment->file_path);
            }
        });
    }
}
