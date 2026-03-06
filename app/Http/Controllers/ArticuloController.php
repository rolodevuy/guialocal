<?php

namespace App\Http\Controllers;

use App\Models\Articulo;

class ArticuloController extends Controller
{
    public function index()
    {
        $articulos = Articulo::publicado()
            ->with(['categoria'])
            ->orderByDesc('publicado_en')
            ->orderByDesc('created_at')
            ->paginate(12);

        return view('articulos.index', compact('articulos'));
    }

    public function show(Articulo $articulo)
    {
        abort_unless($articulo->publicado, 404);
        return view('articulos.show', compact('articulo'));
    }
}
