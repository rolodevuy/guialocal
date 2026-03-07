<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Guia extends Model implements HasMedia
{
    use HasSlug, InteractsWithMedia;

    protected $fillable = [
        'titulo',
        'slug',
        'intro',
        'cuerpo',
        'categoria_id',
        'publicado',
        'publicado_en',
    ];

    protected $casts = [
        'publicado'    => 'boolean',
        'publicado_en' => 'datetime',
    ];

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('titulo')
            ->saveSlugsTo('slug')
            ->doNotGenerateSlugsOnUpdate();
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('portada')->singleFile();
    }

    // Scopes
    public function scopePublicado($query)
    {
        return $query->where('publicado', true);
    }

    // Relaciones
    public function categoria(): BelongsTo
    {
        return $this->belongsTo(Categoria::class);
    }

    public function negocios(): BelongsToMany
    {
        return $this->belongsToMany(Negocio::class, 'guia_negocio')
            ->withPivot('orden')
            ->orderByPivot('orden');
    }
}
