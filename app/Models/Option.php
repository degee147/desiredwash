<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Option extends Model
{
    protected $fillable = ['key', 'value'];

    // ── Helpers ───────────────────────────────────────────────────────────────

    /**
     * Get a single option value by key.
     * Returns $default if the key does not exist.
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        $option = static::where('key', $key)->first();

        return $option?->value ?? $default;
    }

    /**
     * Set (upsert) a single option value by key.
     */
    public static function set(string $key, mixed $value): void
    {
        static::updateOrCreate(
            ['key' => $key],
            ['value' => $value],
        );
    }
}
