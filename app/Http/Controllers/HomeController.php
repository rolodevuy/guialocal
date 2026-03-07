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
            ->take(3);

        if ($slotsNegocios->isNotEmpty()) {
            $destacados = $slotsNegocios;
        } else {
            // Top 3 categorías con más actividad
            $topCategorias = Categoria::activo()
                ->where('popularidad_score', '>', 0)
                ->orderByDesc('popularidad_score')
                ->limit(3)
                ->get();

            $destacados = $topCategorias->map(
                fn (Categoria $cat) => Negocio::activo()
                    ->where('categoria_id', $cat->id)
                    ->orderByDesc('featured_score')
                    ->with(['categoria', 'zona'])
                    ->first()
            )->filter()->values();

            // Fallback si todavía no hay scores calculados
            if ($destacados->isEmpty()) {
                $destacados = Negocio::activo()
                    ->featured()
                    ->with(['categoria', 'zona'])
                    ->limit(3)
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
            'negocios_mapa',
        ));
    }
}
