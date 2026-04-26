<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, HasRoles, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'registration_id',
        'password',
        'avatar',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    // ── Relationships ────────────────────────────────────────────

    // Devices this user owns
    public function devices(): HasMany
    {
        return $this->hasMany(Device::class);
    }

    // Classrooms this user teaches
    public function classrooms(): HasMany
    {
        return $this->hasMany(Classroom::class, 'teacher_id');
    }

    // Classrooms this user is enrolled in as a student
    public function enrolledClassrooms(): BelongsToMany
    {
        return $this->belongsToMany(
            Classroom::class,
            'classroom_student',
            'student_id',
            'classroom_id'
        )->withPivot('joined_at');
    }

    // Activity logs belonging to this user
    public function activityLogs(): HasMany
    {
        return $this->hasMany(ActivityLog::class);
    }

    // ── Helpers ──────────────────────────────────────────────────

    // Get the user's approved device (most recent)
    public function approvedDevice(): ?Device
    {
        return $this->devices()
            ->where('status', 'approved')
            ->latest()
            ->first();
    }

    public function isAdmin(): bool
    {
        return $this->hasRole('admin');
    }

    public function isTeacher(): bool
    {
        return $this->hasRole('teacher');
    }

    public function isStudent(): bool
    {
        return $this->hasRole('student');
    }
}
