<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class ClaimRequest extends Model implements HasMedia
{
    use InteractsWithMedia;

    const ESTADOS = [
        'pendiente' => 'Pendiente',
        'aprobado'  => 'Aprobado',
        'rechazado' => 'Rechazado',
    ];

    protected $fillable = [
        'lugar_id',
        'nombre_completo',
        'email',
        'telefono',
        'rut_numero',
        'mensaje',
        'estado',
        'motivo_rechazo',
        'admin_id',
        'reviewed_at',
    ];

    protected $casts = [
        'reviewed_at' => 'datetime',
    ];

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('constancia_rut')->singleFile();
    }

    // ── Relaciones ──────────────────────────────────────────────────────────

    public function lugar()
    {
        return $this->belongsTo(Lugar::class);
    }

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    // ── Scopes ──────────────────────────────────────────────────────────────

    public function scopePendientes(Builder $query): Builder
    {
        return $query->where('estado', 'pendiente');
    }
}
