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
        'locked_until',
        'violation_count',
    ];

    protected function casts(): array
    {
        return [
            'joined_at' => 'datetime',
            'left_at' => 'datetime',
            'locked_until' => 'datetime',
            'is_locked' => 'boolean',
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
        $this->update(['is_locked' => true, 'locked_until' => null]);
    }

    /** Lock for a fixed number of seconds then auto-unlock on next poll. */
    public function lockFor(int $seconds): void
    {
        $this->update(['is_locked' => true, 'locked_until' => now()->addSeconds($seconds)]);
    }

    public function unlock(): void
    {
        $this->update(['is_locked' => false, 'locked_until' => null]);
    }

    public function incrementViolation(): void
    {
        $this->increment('violation_count', 1);
    }

    /**
     * Returns the warning level within the current 3-violation cycle.
     * Total violations are never cleared; every 3 violations triggers a lock.
     *  0 = Good (no violations in cycle yet)
     *  1 = First warning in cycle
     *  2 = Second warning in cycle
     *  3 = Lock threshold hit (violation_count is a multiple of 3)
     */
    public function warningLevel(): int
    {
        if ($this->violation_count === 0) {
            return 0;
        }

        return match ($this->violation_count % 3) {
            0 => 3, // multiple of 3 — lock threshold
            1 => 1, // first in new cycle
            2 => 2, // second in cycle
            default => 0,
        };
    }
}
