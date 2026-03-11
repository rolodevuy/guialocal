<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    public $timestamps = false;
    public $incrementing = false;

    protected $primaryKey = 'key';
    protected $keyType = 'string';
    protected $fillable = ['key', 'value'];

    protected static array $cache = [];

    public static function get(string $key, mixed $default = null): mixed
    {
        if (isset(static::$cache[$key])) {
            return static::$cache[$key];
        }

        $setting = static::find($key);
        $value = $setting?->value ?? $default;
        static::$cache[$key] = $value;

        return $value;
    }

    public static function set(string $key, mixed $value): void
    {
        static::updateOrCreate(['key' => $key], ['value' => $value]);
        static::$cache[$key] = $value;
    }
}
