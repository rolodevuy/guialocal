<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
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
        'lugar_id',
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

    public function registerMediaConversions(Media $media = null): void
    {
        $this->addMediaConversion('optimized')
            ->format('webp')
            ->quality(80)
            ->width(1200)
            ->height(630)
            ->sharpen(10)
            ->nonQueued()
            ->performOnCollections('portada');
    }

    public function scopePublicado(Builder $query): Builder
    {
        return $query->where('publicado', true);
    }

    public function categoria()
    {
        return $this->belongsTo(Categoria::class);
    }

    public function lugar()
    {
        return $this->belongsTo(Lugar::class);
    }
}
