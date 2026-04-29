<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SessionDevice extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'session_id',
        'device_id',
        'joined_at',
        'left_at',
        'is_locked',
        'violation_count',
    ];

    protected function casts(): array
    {
        return [
            'joined_at'       => 'datetime',
            'left_at'         => 'datetime',
            'is_locked'       => 'boolean',
            'violation_count' => 'integer',
        ];
    }

    // ── Relationships ────────────────────────────────────────────

    public function session(): BelongsTo
    {
        return $this->belongsTo(ClassSession::class, 'session_id');
    }

    public function device(): BelongsTo
    {
        return $this->belongsTo(Device::class);
    }

    // ── Helpers ──────────────────────────────────────────────────

    public function lock(): void
    {
        $this->update(['is_locked' => true]);
    }

    public function unlock(): void
    {
        $this->update(['is_locked' => false]);
    }

    public function incrementViolation(): void
    {
        $this->increment('violation_count', 1);
    }

    public function warningLevel(): int
    {
        return match(true) {
            $this->violation_count >= 3 => 3,
            $this->violation_count === 2 => 2,
            $this->violation_count === 1 => 1,
            default                      => 0,
        };
    }
}