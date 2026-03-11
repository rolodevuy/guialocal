<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use App\Models\Evento;
use App\Models\Ficha;
use App\Models\FeaturedSlot;
use App\Models\Lugar;
use App\Models\Sector;
use App\Models\Zona;

class HomeController extends Controller
{
    public function index()
    {
        // ── Fichas destacadas ─────────────────────────────────────────────────
        //
        // Prioridad:
        //   1. Slots curados manualmente (home_negocios) → control editorial total
        //   2. Algoritmo automático: top 6 categorías por popularidad_score ×
        //      ficha con mayor featured_score de cada una
        //   3. Fallback: fichas con flag "featured = true"

        $slotsNegocios = FeaturedSlot::activo('home_negocios')
            ->with('slotable')
            ->get()
            ->pluck('slotable')
            ->filter()
            ->map(function ($item) {
                // Normalizar: si el slot apunta a un Lugar, devolver su Ficha
                if ($item instanceof \App\Models\Lugar) {
                    return $item->fichas()->with(['lugar.categoria', 'lugar.zona'])->first();
                }
                return $item->load(['lugar.categoria', 'lugar.zona']);
            })
            ->filter()
            ->take(6);

        if ($slotsNegocios->isNotEmpty()) {
            $destacados = $slotsNegocios;
        } else {
            $topCategorias = Categoria::activo()
                ->where('popularidad_score', '>', 0)
                ->orderByDesc('popularidad_score')
                ->limit(6)
                ->get();

            $destacados = $topCategorias->map(function (Categoria $cat) {
                $candidatos = Ficha::activo()
                    ->whereHas('lugar', fn ($q) => $q
                        ->where('categoria_id', $cat->id)
                        ->where('activo', true)
                    )
                    ->orderByDesc('featured_score')
                    ->with(['lugar.categoria', 'lugar.zona'])
                    ->limit(5)
                    ->get();

                if ($candidatos->isEmpty()) {
                    return null;
                }

                $topScore = $candidatos->first()->featured_score;
                $empates  = $candidatos->where('featured_score', $topScore)->values();
                $offset   = $empates->count() > 1 ? (now()->hour % $empates->count()) : 0;

                return $empates->get($offset);
            })->filter()->values();

            if ($destacados->isEmpty()) {
                $destacados = Ficha::activo()
                    ->featured()
                    ->whereHas('lugar', fn ($q) => $q->where('activo', true))
                    ->with(['lugar.categoria', 'lugar.zona'])
                    ->limit(6)
                    ->get();
            }
        }

        // ── Slots editoriales (artículos o guías) ─────────────────────────────
        $slotsEditoriales = FeaturedSlot::activo('home_editorial')
            ->with('slotable.categoria')
            ->get()
            ->pluck('slotable')
            ->filter()
            ->take(3);

        // ── Sectores con categorías para la grilla "Explorar" ──────────────
        $sectores = Sector::activo()
            ->orderBy('orden')
            ->with(['categorias' => fn ($q) => $q
                ->activo()
                ->whereNull('parent_id')
                ->orderBy('nombre')
                ->with('children:id,parent_id')
            ])
            ->get();

        $allCatIds = $sectores->flatMap(fn ($s) =>
            $s->categorias->flatMap(fn ($cat) =>
                collect([$cat->id])->merge($cat->children->pluck('id'))
            )
        );

        $counts = Lugar::where('activo', true)
            ->whereIn('categoria_id', $allCatIds)
            ->selectRaw('categoria_id, COUNT(*) as total')
            ->groupBy('categoria_id')
            ->pluck('total', 'categoria_id');

        $sectores->each(fn ($sector) =>
            $sector->categorias->each(function ($cat) use ($counts) {
                $ids = collect([$cat->id])->merge($cat->children->pluck('id'));
                $cat->negocios_count = $ids->sum(fn ($id) => $counts->get($id, 0));
            })
        );

        $zonas = Zona::withCount(['lugares as negocios_count' => fn ($q) => $q->where('activo', true)])
            ->orderBy('nombre')
            ->get();

        // ── Zona preferida del usuario (cookie) ───────────────────────────────
        $zonaPreferida = null;
        if ($cookieSlug = request()->cookie('zona_preferida')) {
            $zonaPreferida = $zonas->firstWhere('slug', $cookieSlug);
        }

        // ── Destacados por sector (para tabs) ───────────────────────────────
        // Una sola query con todas las categorías, luego agrupar en memoria
        $allSectorCatMap = [];
        $allSectorCatIds = collect();
        foreach ($sectores as $sector) {
            $ids = $sector->categorias->flatMap(
                fn ($cat) => collect([$cat->id])->merge($cat->children->pluck('id'))
            );
            $allSectorCatMap[$sector->id] = $ids;
            $allSectorCatIds = $allSectorCatIds->merge($ids);
        }

        $allSectorFichas = $allSectorCatIds->isNotEmpty()
            ? Ficha::activo()
                ->whereHas('lugar', fn ($q) => $q
                    ->where('activo', true)
                    ->whereIn('categoria_id', $allSectorCatIds->unique())
                )
                ->with(['lugar.categoria', 'lugar.zona'])
                ->orderByDesc('featured_score')
                ->get()
            : collect();

        $destacadosPorSector = [];
        foreach ($sectores as $sector) {
            $ids = $allSectorCatMap[$sector->id] ?? collect();
            if ($ids->isEmpty()) continue;

            $destacadosPorSector[$sector->id] = $allSectorFichas
                ->filter(fn ($f) => $ids->contains($f->lugar->categoria_id))
                ->take(6)
                ->values();
        }

        // ── Eventos próximos para el home (máx. 3) ────────────────────────────
        $eventosDestacados = Evento::publicado()
            ->proximo()
            ->with('lugar')
            ->orderBy('fecha_inicio')
            ->limit(3)
            ->get();

        return view('home', compact(
            'destacados',
            'destacadosPorSector',
            'slotsEditoriales',
            'sectores',
            'zonas',
            'zonaPreferida',
            'eventosDestacados',
        ));
    }
}
