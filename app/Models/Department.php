<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class Department extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'name',
        'email',
        'password',
        'head_name',
        'head_email',
        'head_phone',
        'is_active',
        'otp',
        'otp_expires_at',
        'worker_id_identifier',
        'last_activity_at',
        'last_login_at',
        'last_login_ip',
        'session_id',
    ];

    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'last_activity_at' => 'datetime',
        'last_login_at' => 'datetime',
        'otp_expires_at' => 'datetime'
    ];

    public function reports()
    {
        return $this->hasMany(Report::class, 'handling_department_id');
    }

    public function staff()
    {
        return $this->hasMany(User::class, 'department_id');
    }

    /**
     * Generate the next worker ID for this department
     */
    public function generateNextWorkerId(): string
    {
        if (empty($this->worker_id_identifier)) {
            // Fallback to generic PW prefix if no identifier is set
            $prefix = 'PW';
        } else {
            $prefix = $this->worker_id_identifier;
        }

        // Find the highest existing worker ID for this department
        $lastUser = User::where('department_id', $this->id)
            ->where('worker_id', 'LIKE', $prefix . '%')
            ->orderByRaw('CAST(SUBSTRING(worker_id, ' . (strlen($prefix) + 1) . ') AS UNSIGNED) DESC')
            ->first();

        if ($lastUser && preg_match('/(\d+)$/', $lastUser->worker_id, $matches)) {
            $nextNumber = intval($matches[1]) + 1;
        } else {
            $nextNumber = 1;
        }

        return $prefix . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
    }

    /**
     * Get the worker ID prefix for this department
     */
    public function getWorkerIdPrefix(): string
    {
        return $this->worker_id_identifier ?? 'PW';
    }

    /**
     * Check if a worker ID belongs to this department
     */
    public function ownsWorkerId(string $workerId): bool
    {
        $prefix = $this->getWorkerIdPrefix();
        return str_starts_with($workerId, $prefix);
    }

    /**
     * Update department's last activity timestamp
     */
    public function updateLastActivity(): void
    {
        $this->update([
            'last_activity_at' => now()
        ]);
    }

    /**
     * Update department's login information
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
     * Check if department session is active
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