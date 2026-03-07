<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use App\Models\FeaturedSlot;
use App\Models\Negocio;
use App\Models\Zona;

class HomeController extends Controller
{
    public function index()
    {
        // ── Negocios destacados ───────────────────────────────────────────────
        //
        // Prioridad:
        //   1. Slots curados manualmente (home_negocios) → control editorial total
        //   2. Algoritmo automático: top 3 categorías por popularidad_score ×
        //      negocio con mayor featured_score de cada una
        //   3. Fallback: negocios con flag "featured = true"

        $slotsNegocios = FeaturedSlot::activo('home_negocios')
            ->with('slotable.categoria', 'slotable.zona')
            ->get()
            ->pluck('slotable')
            ->filter()
            ->take(6);

        if ($slotsNegocios->isNotEmpty()) {
            $destacados = $slotsNegocios;
        } else {
            // Top 3 categorías con más actividad
            $topCategorias = Categoria::activo()
                ->where('popularidad_score', '>', 0)
                ->orderByDesc('popularidad_score')
                ->limit(6)
                ->get();

            $destacados = $topCategorias->map(function (Categoria $cat) {
                // Candidatos con el score más alto de la categoría
                $candidatos = Negocio::activo()
                    ->where('categoria_id', $cat->id)
                    ->orderByDesc('featured_score')
                    ->with(['categoria', 'zona'])
                    ->limit(5)
                    ->get();

                if ($candidatos->isEmpty()) {
                    return null;
                }

                $topScore = $candidatos->first()->featured_score;
                $empates  = $candidatos->where('featured_score', $topScore)->values();

                // Si hay empate, rotar por hora del día para dar visibilidad a todos
                $offset = $empates->count() > 1 ? (now()->hour % $empates->count()) : 0;

                return $empates->get($offset);
            })->filter()->values();

            // Fallback si todavía no hay scores calculados
            if ($destacados->isEmpty()) {
                $destacados = Negocio::activo()
                    ->featured()
                    ->with(['categoria', 'zona'])
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
            ->withCount(['negocios' => fn ($q) => $q->where('activo', true)])
            ->orderBy('nombre')
            ->get();

        $zonas = Zona::orderBy('nombre')->get();

        // ── Zona preferida del usuario (cookie) ───────────────────────────────
        $zonaPreferida = null;
        if ($cookieSlug = request()->cookie('zona_preferida')) {
            $zonaPreferida = Zona::where('slug', $cookieSlug)->first();
        }

        // ── Negocios con coordenadas para el mapa ─────────────────────────────
        $negocios_mapa = Negocio::activo()
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
