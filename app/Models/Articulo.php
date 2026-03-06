<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Articulo extends Model implements HasMedia
{
    use HasSlug, InteractsWithMedia;

    protected $fillable = [
        'titulo',
        'slug',
        'extracto',
        'cuerpo',
        'publicado',
        'publicado_en',
        'categoria_id',
        'negocio_id',
    ];

    protected $casts = [
        'publicado'    => 'boolean',
        'publicado_en' => 'datetime',
    ];

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('titulo')
            ->saveSlugsTo('slug');
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('portada')->singleFile();
    }

    public function scopePublicado(Builder $query): Builder
    {
        return $query->where('publicado', true);
    }

    public function categoria()
    {
        return $this->belongsTo(Categoria::class);
    }

    public function negocio()
    {
        return $this->belongsTo(Negocio::class);
    }
}
