<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use App\Models\Negocio;

class HomeController extends Controller
{
    public function index()
    {
        $destacados = Negocio::activo()->featured()
            ->with(['categoria', 'zona'])
            ->limit(6)
            ->get();

        $categorias = Categoria::activo()
            ->withCount(['negocios' => fn ($q) => $q->where('activo', true)])
            ->orderBy('nombre')
            ->get();

        return view('home', compact('destacados', 'categorias'));
    }
}
