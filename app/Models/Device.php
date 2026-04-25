<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Device extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'mac_address',
        'device_type',
        'status',
        'registered_at',
    ];

    protected function casts(): array
    {
        return [
            'registered_at' => 'datetime',
        ];
    }

    // ── Relationships ────────────────────────────────────────────

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function sessions(): BelongsToMany
    {
        return $this->belongsToMany(
            ClassSession::class,
            'session_devices',
            'device_id',
            'session_id'
        )->withPivot('joined_at', 'left_at', 'is_locked', 'violation_count');
    }

    public function activityLogs(): HasMany
    {
        return $this->hasMany(ActivityLog::class);
    }

    // ── Helpers ──────────────────────────────────────────────────

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function isBlocked(): bool
    {
        return $this->status === 'blocked';
    }

    public function approve(): void
    {
        $this->update(['status' => 'approved']);
    }

    public function block(): void
    {
        $this->update(['status' => 'blocked']);
    }
}