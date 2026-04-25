<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ClassSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'classroom_id',
        'title',
        'status',
        'started_at',
        'ended_at',
    ];

    protected function casts(): array
    {
        return [
            'started_at' => 'datetime',
            'ended_at'   => 'datetime',
        ];
    }

    // ── Relationships ────────────────────────────────────────────

    public function classroom(): BelongsTo
    {
        return $this->belongsTo(Classroom::class);
    }

    public function devices(): BelongsToMany
    {
        return $this->belongsToMany(
            Device::class,
            'session_devices',
            'session_id',
            'device_id'
        )->withPivot('joined_at', 'left_at', 'is_locked', 'violation_count');
    }

    public function resources(): HasMany
    {
        return $this->hasMany(Resource::class, 'session_id');
    }

    public function activityLogs(): HasMany
    {
        return $this->hasMany(ActivityLog::class, 'session_id');
    }

    // ── Helpers ──────────────────────────────────────────────────

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function isEnded(): bool
    {
        return $this->status === 'ended';
    }

    public function start(): void
    {
        $this->update([
            'status'     => 'active',
            'started_at' => now(),
        ]);
    }

    public function end(): void
    {
        $this->update([
            'status'   => 'ended',
            'ended_at' => now(),
        ]);

        // Mark all devices as having left
        $this->devices()->newPivotStatement()
             ->where('session_id', $this->id)
             ->whereNull('left_at')
             ->update(['left_at' => now()]);
    }

    public function duration(): ?string
    {
        if (!$this->started_at) return null;

        $end = $this->ended_at ?? now();
        $mins = $this->started_at->diffInMinutes($end);

        return $mins < 60
            ? "{$mins}m"
            : floor($mins / 60) . 'h ' . ($mins % 60) . 'm';
    }
}