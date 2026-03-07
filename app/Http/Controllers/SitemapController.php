<?php

namespace App\Http\Controllers;

use App\Models\Articulo;
use App\Models\Categoria;
use App\Models\Guia;
use App\Models\Negocio;
use App\Models\Zona;

class SitemapController extends Controller
{
    public function index()
    {
        $negocios   = Negocio::activo()->orderBy('updated_at', 'desc')->get(['slug', 'updated_at']);
        $categorias = Categoria::activo()->orderBy('nombre')->get(['slug', 'updated_at']);
        $zonas      = Zona::orderBy('nombre')->get(['slug', 'updated_at']);
        $articulos  = Articulo::publicado()->orderBy('updated_at', 'desc')->get(['slug', 'updated_at']);
        $guias      = Guia::publicado()->orderBy('updated_at', 'desc')->get(['slug', 'updated_at']);

        return response()
            ->view('sitemap', compact('negocios', 'categorias', 'zonas', 'articulos', 'guias'))
            ->header('Content-Type', 'application/xml');
    }
}
