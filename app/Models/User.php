<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'worker_id',
        'phone',
        'password',
        'department_id',
        'is_admin',
        'profile_picture',
        'email_verified_at',
        'last_activity_at',
        'last_login_at',
        'last_login_ip',
        'session_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_activity_at' => 'datetime',
            'last_login_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * A user can have many reports.
     */
    public function reports()
    {
        return $this->hasMany(Report::class);
    }

    /**
     * A user belongs to a department.
     */
    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * Check if user is an admin.
     */
    public function isAdmin()
    {
        return $this->is_admin == 1;
    }

    /**
     * Update user's last activity timestamp
     */
    public function updateLastActivity(): void
    {
        $this->update([
            'last_activity_at' => now()
        ]);
    }

    /**
     * Update user's login information
     */
    public function updateLoginInfo(string $ipAddress, string $sessionId): void
    {
        $this->update([
            'last_login_at' => now(),
            'last_login_ip' => $ipAddress,
            'session_id' => $sessionId,
            'last_activity_at' => now()
        ]);
    }

    /**
     * Check if user session is active
     */
    public function isSessionActive(): bool
    {
        if (!$this->session_id) {
            return false;
        }

        // Check if session exists in sessions table
        $sessionExists = \DB::table('sessions')
            ->where('id', $this->session_id)
            ->exists();

        return $sessionExists;
    }

    /**
     * Get time since last activity in minutes
     */
    public function getTimeSinceLastActivity(): int
    {
        if (!$this->last_activity_at) {
            return 0;
        }

        return $this->last_activity_at->diffInMinutes(now());
    }
}
