<?php

namespace App\Http\Controllers;

use App\Models\Ficha;
use App\Models\Lugar;
use App\Models\Sector;

class SectorController extends Controller
{
    public function show(Sector $sector)
    {
        abort_unless($sector->activo, 404);

        $categorias = $sector->categorias()
            ->activo()
            ->whereNull('parent_id')
            ->with('children:id,parent_id')
            ->orderBy('nombre')
            ->get();

        // Conteo agregado de negocios (patrón de HomeController)
        $allCatIds = $categorias->flatMap(
            fn ($cat) => collect([$cat->id])->merge($cat->children->pluck('id'))
        );

        $counts = Lugar::where('activo', true)
            ->whereIn('categoria_id', $allCatIds)
            ->selectRaw('categoria_id, COUNT(*) as total')
            ->groupBy('categoria_id')
            ->pluck('total', 'categoria_id');

        $categorias->each(function ($cat) use ($counts) {
            $ids = collect([$cat->id])->merge($cat->children->pluck('id'));
            $cat->negocios_count = $ids->sum(fn ($id) => $counts->get($id, 0));
        });

        // Fichas destacadas del sector (top 6 por featured_score)
        $destacados = Ficha::activo()
            ->whereHas('lugar', fn ($q) => $q
                ->where('activo', true)
                ->whereIn('categoria_id', $allCatIds)
            )
            ->with(['lugar.categoria', 'lugar.zona'])
            ->orderByDesc('featured_score')
            ->limit(6)
            ->get();

        $totalNegocios = $counts->sum();

        return view('sectores.show', compact('sector', 'categorias', 'destacados', 'totalNegocios'));
    }
}
