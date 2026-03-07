<?php

namespace App\Http\Controllers;

use App\Models\Articulo;
use App\Models\Categoria;
use App\Models\Guia;
use App\Models\Lugar;
use App\Models\Zona;

class SitemapController extends Controller
{
    public function index()
    {
        $lugares    = Lugar::activo()->orderBy('updated_at', 'desc')->get(['slug', 'updated_at']);
        $categorias = Categoria::activo()->orderBy('nombre')->get(['slug', 'updated_at']);
        $zonas      = Zona::orderBy('nombre')->get(['slug', 'updated_at']);
        $articulos  = Articulo::publicado()->orderBy('updated_at', 'desc')->get(['slug', 'updated_at']);
        $guias      = Guia::publicado()->orderBy('updated_at', 'desc')->get(['slug', 'updated_at']);

        return response()
            ->view('sitemap', compact('lugares', 'categorias', 'zonas', 'articulos', 'guias'))
            ->header('Content-Type', 'application/xml');
    }
}
