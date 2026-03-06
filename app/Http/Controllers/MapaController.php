<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use App\Models\Negocio;
use App\Models\Zona;

class MapaController extends Controller
{
    public function index()
    {
        $zonas = Zona::orderBy('nombre')->get();

        $categorias = Categoria::activo()->orderBy('nombre')->get();

        $negocios = Negocio::activo()
            ->whereNotNull('lat')
            ->whereNotNull('lng')
            ->with(['categoria', 'zona'])
            ->select(['id', 'nombre', 'slug', 'lat', 'lng', 'descripcion', 'categoria_id', 'zona_id'])
            ->get();

        return view('mapa', compact('zonas', 'categorias', 'negocios'));
    }
}
