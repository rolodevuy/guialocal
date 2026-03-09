<?php

namespace App\Http\Controllers;

use App\Models\Ficha;
use App\Models\Lugar;
use App\Models\SlugRedirect;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class NegocioController extends Controller
{
    /**
     * Devuelve el ID de la categoría raíz (nivel 1) de la categoría del lugar.
     */
    private function getCategoriaRaizId(Lugar $lugar): int
    {
        $cat = $lugar->categoria;

        if ($cat->nivel === 1 || !$cat->parent_id) {
            return $cat->id;
        }

        $cat->loadMissing('parent.parent');

        if ($cat->parent->nivel === 1 || !$cat->parent->parent_id) {
            return $cat->parent->id;
        }

        return $cat->parent->parent->id;
    }

    /**
     * Devuelve hasta 4 fichas de la misma categoría raíz, ordenadas por distancia
     * Haversine desde el lugar actual. Solo funciona si el lugar tiene lat/lng.
     */
    private function getCerca(Lugar $lugar): Collection
    {
        if (!$lugar->lat || !$lugar->lng) {
            return collect();
        }

        $raizId = $this->getCategoriaRaizId($lugar);

        // Todos los IDs de categorías en la misma familia (raíz + hijos + nietos)
        $catIds = DB::table('categorias')
            ->where('id', $raizId)
            ->orWhere('parent_id', $raizId)
            ->orWhereIn('parent_id', function ($sub) use ($raizId) {
                $sub->select('id')->from('categorias')->where('parent_id', $raizId);
            })
            ->pluck('id');

        // Haversine en SQL — LEAST(1, …) evita NaN si el float supera 1 por redondeo
        $distancias = DB::table('fichas')
            ->join('lugares', 'fichas.lugar_id', '=', 'lugares.id')
            ->where('fichas.activo', true)
            ->where('fichas.estado', 'activa')
            ->where('lugares.activo', true)
            ->where('lugares.id', '!=', $lugar->id)
            ->whereNotNull('lugares.lat')
            ->whereNotNull('lugares.lng')
            ->whereIn('lugares.categoria_id', $catIds)
            ->selectRaw('fichas.id, (6371 * acos(LEAST(1,
                cos(radians(?)) * cos(radians(lugares.lat)) * cos(radians(lugares.lng) - radians(?)) +
                sin(radians(?)) * sin(radians(lugares.lat))
            ))) AS distancia_km', [$lugar->lat, $lugar->lng, $lugar->lat])
            ->orderBy('distancia_km')
            ->limit(4)
            ->pluck('distancia_km', 'id');

        if ($distancias->isEmpty()) {
            return collect();
        }

        return Ficha::whereIn('id', $distancias->keys())
            ->with(['lugar.categoria', 'lugar.zona'])
            ->get()
            ->sortBy(fn ($f) => $distancias[$f->id])
            ->values()
            ->map(function ($f) use ($distancias) {
                $f->distancia_km = round($distancias[$f->id], 1);
                return $f;
            });
    }

    private function getSimilares(Lugar $lugar): Collection
    {
        $base = Ficha::activo()
            ->whereHas('lugar', fn ($q) => $q
                ->where('activo', true)
                ->where('id', '!=', $lugar->id)
                ->whereHas('categoria', fn ($c) => $c
                    ->where('id', $lugar->categoria_id)
                    ->orWhere('parent_id', $lugar->categoria_id)
                    ->orWhereHas('parent', fn ($p) => $p->where('id', $lugar->categoria_id))
                )
            )
            ->with(['lugar.categoria', 'lugar.zona'])
            ->orderByDesc('featured_score');

        // Primero: misma zona
        if ($lugar->zona_id) {
            $enZona = (clone $base)
                ->whereHas('lugar', fn ($q) => $q->where('zona_id', $lugar->zona_id))
                ->limit(4)
                ->get();

            if ($enZona->count() >= 4) {
                return $enZona;
            }

            // Completar con cualquier zona excluyendo los ya encontrados
            $resto = (clone $base)
                ->whereNotIn('id', $enZona->pluck('id'))
                ->limit(4 - $enZona->count())
                ->get();

            return $enZona->concat($resto);
        }

        return $base->limit(4)->get();
    }

    public function index()
    {
        return view('negocios.index');
    }

    public function show(string $slug)
    {
        $lugar = Lugar::where('slug', $slug)->first();

        if (! $lugar) {
            $redirect = SlugRedirect::where('old_slug', $slug)->with('lugar')->first();

            if ($redirect?->lugar) {
                return redirect()->route('negocios.show', $redirect->lugar->slug, 301);
            }

            abort(404);
        }

        abort_unless($lugar->activo, 404);

        $ficha = $lugar->fichas()->activo()->with('media')->first();

        // Contador de visitas — sin deduplicación
        if ($ficha) {
            $ficha->increment('visitas');

            // Log diario para métricas Premium
            DB::table('ficha_visitas')->upsert(
                [['ficha_id' => $ficha->id, 'fecha' => now()->toDateString(), 'cantidad' => 1, 'created_at' => now(), 'updated_at' => now()]],
                ['ficha_id', 'fecha'],
                ['cantidad' => DB::raw('cantidad + 1'), 'updated_at' => now()],
            );
        }

        $promociones = $ficha
            ? $ficha->promociones()->vigente()->orderBy('fecha_fin')->get()
            : collect();

        // ── Cerca: misma categoría raíz, ordenados por distancia Haversine ──
        // Solo si el lugar tiene lat/lng. Si no hay resultados, caemos a similares.
        $cerca = $this->getCerca($lugar);

        // ── Negocios similares (fallback cuando no hay resultados "cerca") ────
        $similares = $cerca->isEmpty() ? $this->getSimilares($lugar) : collect();

        // ── Nombre de la categoría raíz para el heading de "cerca" ───────────
        $categoriaRaizId   = ($cerca->isNotEmpty() && $lugar->lat && $lugar->lng)
            ? $this->getCategoriaRaizId($lugar)
            : null;
        $categoriaRaizNombre = $categoriaRaizId
            ? \App\Models\Categoria::find($categoriaRaizId)?->nombre
            : null;

        // ── Reseñas (solo carga si la feature está activa) ───────────────────
        $resenas = (config('features.resenas') && $ficha)
            ? $ficha->resenas()->aprobada()->latest()->get()
            : collect();

        return view('negocios.show', compact(
            'lugar', 'ficha', 'promociones',
            'cerca', 'categoriaRaizNombre',
            'similares', 'resenas'
        ));
    }
}
