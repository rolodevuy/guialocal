<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Negocio extends Model implements HasMedia
{
    use HasSlug, InteractsWithMedia, Searchable;

    protected $fillable = [
        'nombre',
        'slug',
        'descripcion',
        'direccion',
        'telefono',
        'email',
        'sitio_web',
        'redes_sociales',
        'lat',
        'lng',
        'horarios',
        'horarios_especiales',
        'featured',
        'activo',
        'plan',
        'categoria_id',
        'zona_id',
    ];

    protected $casts = [
        'horarios'            => 'array',
        'horarios_especiales' => 'array',
        'redes_sociales'      => 'array',
        'featured'            => 'boolean',
        'activo'    => 'boolean',
        'lat'       => 'float',
        'lng'       => 'float',
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

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('logo')->singleFile();
        $this->addMediaCollection('portada')->singleFile();
        $this->addMediaCollection('galeria');
    }

    public function scopeActivo(Builder $query): Builder
    {
        return $query->where('activo', true);
    }

    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('featured', true);
    }

    public function categoria()
    {
        return $this->belongsTo(Categoria::class);
    }

    public function zona()
    {
        return $this->belongsTo(Zona::class);
    }

    public function promociones()
    {
        return $this->hasMany(Promocion::class);
    }

    // ── Scout ─────────────────────────────────────────────────────────────────

    public function toSearchableArray(): array
    {
        return [
            'nombre'      => $this->nombre,
            'descripcion' => $this->descripcion,
            'direccion'   => $this->direccion,
        ];
    }

    public function shouldBeSearchable(): bool
    {
        return (bool) $this->activo;
    }

    // ── Slug redirect ──────────────────────────────────────────────────────────

    protected static function booted(): void
    {
        static::updating(function (Negocio $negocio) {
            if ($negocio->isDirty('slug')) {
                SlugRedirect::updateOrCreate(
                    ['old_slug' => $negocio->getOriginal('slug')],
                    ['negocio_id' => $negocio->id],
                );
            }
        });
    }
}
