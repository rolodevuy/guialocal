<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Promocion extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected $table = 'promociones';

    protected $fillable = [
        'ficha_id',
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

    public function ficha(): BelongsTo
    {
        return $this->belongsTo(Ficha::class);
    }

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
