<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'head_name',
        'head_email',
        'head_phone',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    public function reports()
    {
        return $this->hasMany(Report::class, 'handling_department_id');
    }

    public function staff()
    {
        return $this->hasMany(User::class, 'department_id');
    }
} 