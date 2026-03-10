<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Sector extends Model
{
    use HasSlug;

    protected $table = 'sectores';

    protected $fillable = [
        'nombre',
        'nombre_corto',
        'slug',
        'descripcion',
        'icono',
        'color_classes',
        'orden',
        'activo',
    ];

    protected $casts = [
        'color_classes' => 'array',
        'activo'        => 'boolean',
        'orden'         => 'integer',
    ];

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('nombre')
            ->saveSlugsTo('slug');
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /* ── Scopes ───────────────────────────────────── */

    public function scopeActivo(Builder $query): Builder
    {
        return $query->where('activo', true);
    }

    /* ── Relaciones ───────────────────────────────── */

    public function categorias(): HasMany
    {
        return $this->hasMany(Categoria::class);
    }

    /* ── Helpers ──────────────────────────────────── */

    /**
     * Retorna una clase Tailwind del set de color del sector.
     * Keys esperadas: bg, bg_light, text, text_hover, border, icon
     */
    public function color(string $key, string $default = ''): string
    {
        return $this->color_classes[$key] ?? $default;
    }
}
