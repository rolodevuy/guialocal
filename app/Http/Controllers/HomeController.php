<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use App\Models\Negocio;
use App\Models\Zona;

class HomeController extends Controller
{
    public function index()
    {
        $destacados = Negocio::activo()->featured()
            ->with(['categoria', 'zona'])
            ->limit(3)
            ->get();

        $categorias = Categoria::activo()
            ->withCount(['negocios' => fn ($q) => $q->where('activo', true)])
            ->orderBy('nombre')
            ->get();

        $zonas = Zona::orderBy('nombre')->get();

        return view('home', compact('destacados', 'categorias', 'zonas'));
    }
}
