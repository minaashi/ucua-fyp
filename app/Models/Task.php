<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    // Define the table if different
    protected $table = 'tasks';

    // Define the fillable fields (adjust as necessary)
    protected $fillable = [
        'user_id',
        'title',
        'description',
        'status',
        'due_date',
    ];
}
