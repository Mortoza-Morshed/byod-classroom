<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class Resource extends Model
{
    use HasFactory;

    protected $fillable = [
        'session_id',
        'teacher_id',
        'title',
        'type',
        'url',
        'file_path',
        'rendering_mode',
    ];

    protected $appends = ['access_url'];

    // ── Relationships ────────────────────────────────────────────

    public function session(): BelongsTo
    {
        return $this->belongsTo(ClassSession::class, 'session_id');
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    // ── Helpers ──────────────────────────────────────────────────

    public function getAccessUrlAttribute(): string
    {
        return $this->accessUrl();
    }

    public function isFile(): bool
    {
        return $this->type === 'file';
    }

    public function isLink(): bool
    {
        return $this->type === 'link';
    }

    public function isPdf(): bool
    {
        return $this->isFile() &&
               str_ends_with(strtolower($this->file_path ?? ''), '.pdf');
    }

    // Returns the accessible URL for this resource
    public function accessUrl(): string
    {
        return $this->isFile()
            ? Storage::url($this->file_path)
            : $this->url;
    }
}