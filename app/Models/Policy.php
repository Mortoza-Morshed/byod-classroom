<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Policy extends Model
{
    use HasFactory;

    protected $fillable = [
        'classroom_id',
        'name',
        'allowed_urls',
        'blocked_keywords',
        'internet_access',
        'is_default',
    ];

    protected function casts(): array
    {
        return [
            'allowed_urls'      => 'array',
            'blocked_keywords'  => 'array',
            'internet_access'   => 'boolean',
            'is_default'        => 'boolean',
        ];
    }

    // ── Relationships ────────────────────────────────────────────

    public function classroom(): BelongsTo
    {
        return $this->belongsTo(Classroom::class);
    }

    // ── Helpers ──────────────────────────────────────────────────

    public function isUrlAllowed(string $url): bool
    {
        // If no whitelist set, everything is allowed
        if (empty($this->allowed_urls)) return true;

        $host = parse_url($url, PHP_URL_HOST);

        foreach ($this->allowed_urls as $allowedUrl) {
            if (str_contains($host, $allowedUrl)) return true;
        }

        return false;
    }

    public function containsBlockedKeyword(string $text): bool
    {
        if (empty($this->blocked_keywords)) return false;

        $lower = strtolower($text);

        foreach ($this->blocked_keywords as $keyword) {
            if (str_contains($lower, strtolower($keyword))) return true;
        }

        return false;
    }
}