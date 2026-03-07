<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class FeaturedSlot extends Model
{
    protected $fillable = [
        'posicion',
        'slotable_type',
        'slotable_id',
        'orden',
        'activo',
        'valido_hasta',
    ];

    protected $casts = [
        'activo'      => 'boolean',
        'valido_hasta' => 'date',
    ];

    // Posiciones disponibles
    const POSICIONES = [
        'home_negocios'  => 'Home — Negocios destacados',
        'home_editorial' => 'Home — Editorial (artículos/guías)',
    ];

    public function slotable(): MorphTo
    {
        return $this->morphTo();
    }

    // Scope: slots activos y vigentes para una posición
    public function scopeActivo($query, string $posicion)
    {
        return $query
            ->where('posicion', $posicion)
            ->where('activo', true)
            ->where(fn ($q) => $q
                ->whereNull('valido_hasta')
                ->orWhere('valido_hasta', '>=', now()->toDateString())
            )
            ->orderBy('orden');
    }
}
