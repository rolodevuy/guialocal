<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Lugar extends Model
{
    use HasSlug;

    protected $table = 'lugares';

    protected $fillable = [
        'nombre',
        'slug',
        'rut',
        'direccion',
        'lat',
        'lng',
        'categoria_id',
        'zona_id',
        'activo',
    ];

    protected $casts = [
        'lat'    => 'float',
        'lng'    => 'float',
        'activo' => 'boolean',
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

    public function scopeActivo(Builder $query): Builder
    {
        return $query->where('activo', true);
    }

    public function categoria()
    {
        return $this->belongsTo(Categoria::class);
    }

    public function zona()
    {
        return $this->belongsTo(Zona::class);
    }

    public function fichas()
    {
        return $this->hasMany(Ficha::class);
    }

    public function slugRedirects()
    {
        return $this->hasMany(SlugRedirect::class);
    }

    // ── Slug redirect al cambiar slug ─────────────────────────────────────────

    protected static function booted(): void
    {
        static::updating(function (Lugar $lugar) {
            if ($lugar->isDirty('slug')) {
                SlugRedirect::updateOrCreate(
                    ['old_slug' => $lugar->getOriginal('slug')],
                    ['lugar_id' => $lugar->id],
                );
            }
        });
    }
}
