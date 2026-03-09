<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Categoria extends Model implements HasMedia
{
    use HasSlug, InteractsWithMedia;

    protected $fillable = [
        'nombre',
        'slug',
        'descripcion',
        'icono',
        'activo',
        'popularidad_score',
        'parent_id',
        'nivel',
        'sector_id',
    ];

    protected $casts = [
        'activo'            => 'boolean',
        'popularidad_score' => 'integer',
        'nivel'             => 'integer',
    ];

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('imagen_generica')->singleFile();
    }

    public function registerMediaConversions(Media $media = null): void
    {
        $this->addMediaConversion('webp')
            ->format('webp')
            ->quality(82)
            ->performOnCollections('imagen_generica');
    }

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

    public function sector()
    {
        return $this->belongsTo(Sector::class);
    }

    /**
     * Sector efectivo: propio o heredado del padre nivel 1.
     */
    public function getSectorEfectivoAttribute(): ?Sector
    {
        if ($this->sector_id) {
            return $this->sector;
        }

        return $this->parent?->sector;
    }

    public function parent()
    {
        return $this->belongsTo(Categoria::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Categoria::class, 'parent_id');
    }

    /**
     * Devuelve la categoría raíz (nivel 1) de esta categoría.
     */
    public function getRaizAttribute(): Categoria
    {
        return $this->nivel === 1 ? $this : $this->parent->raiz;
    }

    /**
     * Nombre completo con jerarquía: "Restaurantes > Parrilla > Parrilla uruguaya"
     */
    public function getNombreCompletoAttribute(): string
    {
        if ($this->nivel === 1) {
            return $this->nombre;
        }

        return $this->parent->nombre_completo . ' > ' . $this->nombre;
    }

    public function lugares()
    {
        return $this->hasMany(Lugar::class);
    }

    public function fichas()
    {
        return $this->hasManyThrough(Ficha::class, Lugar::class);
    }

    /**
     * Lugares propios + de subcategorías (para mostrar en página de categoría nivel 1).
     */
    public function scopeConDescendientes(Builder $query, int $categoriaId): Builder
    {
        return $query->where('id', $categoriaId)
            ->orWhere('parent_id', $categoriaId)
            ->orWhereIn('parent_id', function ($q) use ($categoriaId) {
                $q->select('id')
                    ->from('categorias')
                    ->where('parent_id', $categoriaId);
            });
    }
}
