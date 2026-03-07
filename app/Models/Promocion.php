<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Promocion extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected $fillable = [
        'negocio_id',
        'titulo',
        'descripcion',
        'fecha_inicio',
        'fecha_fin',
        'activo',
    ];

    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_fin'    => 'date',
        'activo'       => 'boolean',
    ];

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('imagen')->singleFile();
    }

    public function negocio(): BelongsTo
    {
        return $this->belongsTo(Negocio::class);
    }

    // Scope: activa y dentro del período de vigencia
    public function scopeVigente($query)
    {
        $hoy = now()->toDateString();

        return $query
            ->where('activo', true)
            ->where('fecha_inicio', '<=', $hoy)
            ->where(fn ($q) => $q
                ->whereNull('fecha_fin')
                ->orWhere('fecha_fin', '>=', $hoy)
            );
    }
}
