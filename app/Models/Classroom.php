<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Classroom extends Model
{
    use HasFactory;

    protected $fillable = [
        'teacher_id',
        'name',
        'subject',
        'join_code',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    // ── Relationships ────────────────────────────────────────────

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function students(): BelongsToMany
    {
        return $this->belongsToMany(
            User::class,
            'classroom_student',
            'classroom_id',
            'student_id'
        )->withPivot('joined_at');
    }

    public function sessions(): HasMany
    {
        return $this->hasMany(ClassSession::class);
    }

    public function policies(): HasMany
    {
        return $this->hasMany(Policy::class);
    }

    // ── Helpers ──────────────────────────────────────────────────

    // Auto-generate a unique join code when creating a classroom
    protected static function booted(): void
    {
        static::creating(function (Classroom $classroom) {
            if (empty($classroom->join_code)) {
                $classroom->join_code = static::generateUniqueCode();
            }
        });
    }

    private static function generateUniqueCode(): string
    {
        do {
            $code = strtoupper(Str::random(6));
        } while (static::where('join_code', $code)->exists());

        return $code;
    }

    public function activeSession(): ?ClassSession
    {
        return $this->sessions()
                    ->where('status', 'active')
                    ->latest()
                    ->first();
    }

    public function hasActiveSession(): bool
    {
        return $this->sessions()
                    ->where('status', 'active')
                    ->exists();
    }

    public function defaultPolicy(): ?Policy
    {
        return $this->policies()
                    ->where('is_default', true)
                    ->first();
    }
}