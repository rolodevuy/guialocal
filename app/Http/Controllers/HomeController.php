<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use App\Models\Ficha;
use App\Models\FeaturedSlot;
use App\Models\Lugar;
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
            ->with('slotable.lugar.categoria', 'slotable.lugar.zona')
            ->get()
            ->pluck('slotable')
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

        // ── Categorías para la grilla "Explorar" ──────────────────────────────
        $categorias = Categoria::activo()
            ->withCount(['lugares as negocios_count' => fn ($q) => $q->where('activo', true)])
            ->orderBy('nombre')
            ->get();

        $zonas = Zona::orderBy('nombre')->get();

        // ── Zona preferida del usuario (cookie) ───────────────────────────────
        $zonaPreferida = null;
        if ($cookieSlug = request()->cookie('zona_preferida')) {
            $zonaPreferida = Zona::where('slug', $cookieSlug)->first();
        }

        // ── Lugares con coordenadas para el mapa ──────────────────────────────
        $negocios_mapa = Lugar::activo()
            ->whereNotNull('lat')
            ->whereNotNull('lng')
            ->with('categoria')
            ->select(['id', 'nombre', 'slug', 'lat', 'lng', 'categoria_id', 'zona_id'])
            ->get();

        return view('home', compact(
            'destacados',
            'slotsEditoriales',
            'categorias',
            'zonas',
            'zonaPreferida',
            'negocios_mapa',
        ));
    }
}
