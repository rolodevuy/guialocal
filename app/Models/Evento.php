<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Evento extends Model implements HasMedia
{
    use HasSlug, InteractsWithMedia;

    protected $fillable = [
        'titulo',
        'slug',
        'descripcion',
        'fecha_inicio',
        'fecha_fin',
        'hora_inicio',
        'hora_fin',
        'lugar_id',
        'publicado',
    ];

    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_fin'    => 'date',
        'publicado'    => 'boolean',
    ];

    // ── Slug ──────────────────────────────────────────────────────────────────

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

    // ── Media ─────────────────────────────────────────────────────────────────

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

    // ── Scopes ────────────────────────────────────────────────────────────────

    /** Solo eventos publicados */
    public function scopePublicado(Builder $query): Builder
    {
        return $query->where('publicado', true);
    }

    /**
     * Eventos que aún no terminaron.
     * - Si tiene fecha_fin → fecha_fin >= hoy
     * - Si no tiene fecha_fin → fecha_inicio >= hoy
     */
    public function scopeProximo(Builder $query): Builder
    {
        $today = now()->toDateString();

        return $query->where(function ($q) use ($today) {
            $q->whereNotNull('fecha_fin')
              ->where('fecha_fin', '>=', $today);
        })->orWhere(function ($q) use ($today) {
            $q->whereNull('fecha_fin')
              ->where('fecha_inicio', '>=', $today);
        });
    }

    // ── Relaciones ────────────────────────────────────────────────────────────

    public function lugar()
    {
        return $this->belongsTo(Lugar::class);
    }
}
