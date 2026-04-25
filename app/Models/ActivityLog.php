<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;

class ActivityLog extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'device_id',
        'session_id',
        'action',
        'description',
        'ip_address',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'metadata' => 'array',
            'created_at' => 'datetime',
        ];
    }

    // ── Relationships ────────────────────────────────────────────

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function device(): BelongsTo
    {
        return $this->belongsTo(Device::class);
    }

    public function session(): BelongsTo
    {
        return $this->belongsTo(ClassSession::class, 'session_id');
    }

    // ── Static helper — call this everywhere in the app ──────────

    public static function record(
        string $action,
        string $description = '',
        ?int $userId = null,
        ?int $deviceId = null,
        ?int $sessionId = null,
        array $metadata = []
    ): void {
        static::create([
            'user_id' => $userId ?? Auth::id(),
            'device_id' => $deviceId,
            'session_id' => $sessionId,
            'action' => $action,
            'description' => $description,
            'ip_address' => request()->ip(),
            'metadata' => $metadata ?: null,
        ]);
    }
}
