<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Ficha extends Model implements HasMedia
{
    use InteractsWithMedia, Searchable;

    const ESTADOS = [
        'pendiente'  => 'Pendiente de aprobación',
        'activa'     => 'Activa',
        'rechazada'  => 'Rechazada',
        'suspendida' => 'Suspendida',
    ];

    /**
     * Qué incluye cada plan (soft gating).
     * false        = no incluido
     * true         = incluido sin límite
     * int > 0      = límite numérico (máx fotos, máx promociones)
     */
    const PLAN_LIMITS = [
        'gratuito' => [
            'visitas'     => false,
            'whatsapp'    => false,
            'promociones' => 0,
            'fotos'       => 0,
            'logo'        => false,
            'destacado'   => false,
        ],
        'basico' => [
            'visitas'     => true,
            'whatsapp'    => true,
            'promociones' => 1,
            'fotos'       => 3,
            'logo'        => true,
            'destacado'   => false,
        ],
        'premium' => [
            'visitas'     => true,
            'whatsapp'    => true,
            'promociones' => PHP_INT_MAX,
            'fotos'       => 10,
            'logo'        => true,
            'destacado'   => true,
        ],
    ];

    /** Devuelve el límite de una feature para el plan actual (false = no incluido) */
    public function planIncluye(string $feature): bool|int
    {
        return self::PLAN_LIMITS[$this->plan ?? 'gratuito'][$feature] ?? false;
    }

    protected $fillable = [
        'lugar_id',
        'user_id',
        'estado',
        'descripcion',
        'telefono',
        'email',
        'sitio_web',
        'redes_sociales',
        'horarios',
        'horarios_especiales',
        'plan',
        'featured',
        'featured_score',
        'visitas',
        'activo',
        'verified_at',
    ];

    protected $casts = [
        'horarios'            => 'array',
        'horarios_especiales' => 'array',
        'redes_sociales'      => 'array',
        'featured'            => 'boolean',
        'activo'              => 'boolean',
        'featured_score'      => 'integer',
        'visitas'             => 'integer',
        'verified_at'         => 'datetime',
    ];

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('logo')->singleFile();
        $this->addMediaCollection('portada')->singleFile();
        $this->addMediaCollection('galeria');
    }

    public function registerMediaConversions(Media $media = null): void
    {
        $this->addMediaConversion('optimized')
            ->format('webp')
            ->quality(80)
            ->width(1200)
            ->height(400)
            ->sharpen(10)
            ->nonQueued()
            ->performOnCollections('portada');

        $this->addMediaConversion('optimized')
            ->format('webp')
            ->quality(80)
            ->width(300)
            ->height(300)
            ->sharpen(10)
            ->nonQueued()
            ->performOnCollections('logo');

        $this->addMediaConversion('optimized')
            ->format('webp')
            ->quality(80)
            ->width(1200)
            ->height(800)
            ->sharpen(10)
            ->nonQueued()
            ->performOnCollections('galeria');
    }

    // ── Imagen con fallback ───────────────────────────────────────────────────

    /**
     * Devuelve la URL de la portada con fallback:
     * 1. Portada propia de la ficha
     * 2. Imagen genérica de la categoría
     * 3. Cadena vacía (el template decide qué mostrar)
     */
    public function getPortadaUrl(): string
    {
        // Propia: optimized > webp legacy > original
        $url = $this->getFirstMediaUrl('portada', 'optimized')
            ?: $this->getFirstMediaUrl('portada', 'webp')
            ?: $this->getFirstMediaUrl('portada');

        if ($url) {
            return $url;
        }

        // Fallback categoría: optimized > webp legacy > original
        return $this->lugar?->categoria?->getFirstMediaUrl('imagen_generica', 'optimized')
            ?: $this->lugar?->categoria?->getFirstMediaUrl('imagen_generica', 'webp')
            ?: ($this->lugar?->categoria?->getFirstMediaUrl('imagen_generica') ?? '');
    }

    public function getIsVerifiedAttribute(): bool
    {
        return $this->user_id !== null && $this->verified_at !== null;
    }

    // ── Scopes ────────────────────────────────────────────────────────────────

    /** Visible públicamente: activa y aprobada por admin */
    public function scopeActivo(Builder $query): Builder
    {
        return $query->where('activo', true)->where('estado', 'activa');
    }

    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('featured', true);
    }

    // ── Relaciones ────────────────────────────────────────────────────────────

    public function lugar()
    {
        return $this->belongsTo(Lugar::class);
    }

    public function propietario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function promociones()
    {
        return $this->hasMany(Promocion::class);
    }

    public function resenas()
    {
        return $this->hasMany(Resena::class);
    }

    // ── ¿Abierto ahora? ───────────────────────────────────────────────────────

    public function isAbiertoAhora(): bool
    {
        if (empty($this->horarios)) {
            return false;
        }

        $diasMap = [
            'Lunes'     => 1,
            'Martes'    => 2,
            'Miércoles' => 3,
            'Jueves'    => 4,
            'Viernes'   => 5,
            'Sábado'    => 6,
            'Domingo'   => 7,
        ];

        $ahora   = now();
        $diaHoy  = (int) $ahora->isoFormat('E'); // 1=Lunes … 7=Domingo
        $horaHoy = $ahora->format('H:i');

        // Primero: ¿hay una fecha especial activa hoy que sobreescriba el horario normal?
        foreach ($this->horarios_especiales ?? [] as $he) {
            if (!($he['activo'] ?? false) || empty($he['fecha'])) {
                continue;
            }

            $fecha = Carbon::parse($he['fecha']);
            if ($he['se_repite'] ?? false) {
                $fecha = $fecha->setYear($ahora->year);
                if ($fecha->lt($ahora->startOfDay())) {
                    $fecha = $fecha->addYear();
                }
            }

            if (!$fecha->isSameDay($ahora)) {
                continue;
            }

            // Hoy aplica esta fecha especial
            if ($he['cerrado'] ?? false) {
                return false;
            }
            if (empty($he['apertura']) || empty($he['cierre'])) {
                return false;
            }
            return $this->horaEnRango($horaHoy, $he['apertura'], $he['cierre']);
        }

        // Horarios regulares
        foreach ($this->horarios as $franja) {
            if ($franja['cerrado'] ?? false) {
                continue;
            }
            if (empty($franja['apertura']) || empty($franja['cierre'])) {
                continue;
            }

            $diaInicio = $diasMap[$franja['dia_inicio']] ?? null;
            if ($diaInicio === null) {
                continue;
            }
            $diaFin = !empty($franja['dia_fin'])
                ? ($diasMap[$franja['dia_fin']] ?? $diaInicio)
                : $diaInicio;

            if ($diaHoy < $diaInicio || $diaHoy > $diaFin) {
                continue;
            }

            if ($this->horaEnRango($horaHoy, $franja['apertura'], $franja['cierre'])) {
                return true;
            }
        }

        return false;
    }

    /** Compara hora actual contra apertura/cierre, maneja cruce de medianoche */
    private function horaEnRango(string $hora, string $apertura, string $cierre): bool
    {
        if ($cierre <= $apertura) {
            // Cruza medianoche (ej: 20:00 → 01:00)
            return $hora >= $apertura || $hora < $cierre;
        }

        return $hora >= $apertura && $hora < $cierre;
    }

    // ── Scout ─────────────────────────────────────────────────────────────────

    public function toSearchableArray(): array
    {
        return [
            'descripcion' => $this->descripcion,
            'telefono'    => $this->telefono,
            'email'       => $this->email,
            'nombre'      => $this->lugar?->nombre,
            'direccion'   => $this->lugar?->direccion,
        ];
    }

    public function shouldBeSearchable(): bool
    {
        return (bool) $this->activo && $this->estado === 'activa';
    }

    // ── Eventos ───────────────────────────────────────────────────────────────

    protected static function booted(): void
    {
        static::saving(function (Ficha $ficha) {
            $score = match ($ficha->plan ?? 'gratuito') {
                'premium' => 50,
                'basico'  => 20,
                default   => 0,
            };
            if ($ficha->featured) {
                $score += 30;
            }
            $ficha->featured_score = $score;
        });

        static::saved(function (Ficha $ficha) {
            $categoriaId = $ficha->lugar?->categoria_id
                ?? optional(Lugar::find($ficha->lugar_id))->categoria_id;

            if ($categoriaId) {
                $cat = Categoria::find($categoriaId);
                if ($cat) {
                    $activos = self::whereHas('lugar', fn ($q) => $q
                        ->where('categoria_id', $categoriaId)
                        ->where('activo', true)
                    )->where('activo', true)->where('estado', 'activa')->count();

                    $premium = self::whereHas('lugar', fn ($q) => $q
                        ->where('categoria_id', $categoriaId)
                        ->where('activo', true)
                    )->where('activo', true)->where('estado', 'activa')->where('plan', 'premium')->count();

                    Categoria::withoutEvents(
                        fn () => $cat->update([
                            'popularidad_score' => $activos * 5 + $premium * 10,
                        ])
                    );
                }
            }
        });
    }
}
