<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

class Resena extends Model
{
    protected $table = 'resenas';

    protected $fillable = [
        'ficha_id',
        'nombre',
        'email',
        'rating',
        'cuerpo',
        'aprobada',
    ];

    protected $casts = [
        'rating'   => 'integer',
        'aprobada' => 'boolean',
    ];

    // ── Relaciones ────────────────────────────────────────────────────────

    public function ficha(): BelongsTo
    {
        return $this->belongsTo(Ficha::class);
    }

    // ── Scopes ────────────────────────────────────────────────────────────

    public function scopeAprobada(Builder $query): Builder
    {
        return $query->where('aprobada', true);
    }

    public function scopePendiente(Builder $query): Builder
    {
        return $query->where('aprobada', false);
    }

    // ── Helpers ───────────────────────────────────────────────────────────

    /** Estrellas como string "★★★☆☆" */
    public function getStarsAttribute(): string
    {
        return str_repeat('★', $this->rating) . str_repeat('☆', 5 - $this->rating);
    }
}
