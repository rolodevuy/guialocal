<?php

namespace App\Http\Controllers;

use App\Models\Articulo;

class FeedController extends Controller
{
    public function index()
    {
        $articulos = Articulo::publicado()
            ->with('categoria')
            ->orderByDesc('publicado_en')
            ->orderByDesc('created_at')
            ->limit(20)
            ->get();

        return response()
            ->view('feed', compact('articulos'))
            ->header('Content-Type', 'application/rss+xml; charset=utf-8');
    }
}
