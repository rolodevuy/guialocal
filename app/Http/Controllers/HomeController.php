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
        // Negocios destacados: primero intenta con slots curados,
        // si no hay slots activos cae al fallback con el boolean featured
        $slotsNegocios = FeaturedSlot::activo('home_negocios')
            ->with('slotable.categoria', 'slotable.zona')
            ->get()
            ->pluck('slotable')
            ->filter()          // descarta nulos (slotable eliminado)
            ->take(3);

        $destacados = $slotsNegocios->isNotEmpty()
            ? $slotsNegocios
            : Negocio::activo()->featured()->with(['categoria', 'zona'])->limit(3)->get();

        // Slots editoriales (artículos o guías) para la home
        $slotsEditoriales = FeaturedSlot::activo('home_editorial')
            ->with('slotable.categoria')
            ->get()
            ->pluck('slotable')
            ->filter()
            ->take(3);

        $categorias = Categoria::activo()
            ->withCount(['negocios' => fn ($q) => $q->where('activo', true)])
            ->orderBy('nombre')
            ->get();

        $zonas = Zona::orderBy('nombre')->get();

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
