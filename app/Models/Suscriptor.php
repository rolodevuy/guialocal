<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Suscriptor extends Model
{
    protected $table = 'suscriptores';

    protected $fillable = ['email', 'zona_id', 'token_baja', 'activo'];

    protected $casts = [
        'activo' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::creating(function (Suscriptor $suscriptor) {
            $suscriptor->token_baja ??= Str::uuid()->toString();
        });
    }

    public function zona(): BelongsTo
    {
        return $this->belongsTo(Zona::class);
    }

    public function scopeActivo($query)
    {
        return $query->where('activo', true);
    }
}
