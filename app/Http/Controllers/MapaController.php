<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use App\Models\Lugar;
use App\Models\Zona;

class MapaController extends Controller
{
    public function index()
    {
        $zonas = Zona::orderBy('nombre')->get();

        $categorias = Categoria::activo()->orderBy('nombre')->get();

        $lugares = Lugar::activo()
            ->whereNotNull('lat')
            ->whereNotNull('lng')
            ->with(['categoria', 'zona'])
            ->select(['id', 'nombre', 'slug', 'lat', 'lng', 'categoria_id', 'zona_id'])
            ->get();

        $zonaInicial = request()->integer('zona') ?: null;

        return view('mapa', compact('zonas', 'categorias', 'lugares', 'zonaInicial'));
    }
}
