<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use App\Models\Ficha;
use App\Models\Zona;

class CategoriaController extends Controller
{
    public function index()
    {
        $categorias = Categoria::activo()
            ->withCount(['lugares as negocios_count' => fn ($q) => $q->where('activo', true)])
            ->orderBy('nombre')
            ->get();

        return view('categorias.index', compact('categorias'));
    }

    public function show(Categoria $categoria)
    {
        abort_unless($categoria->activo, 404);

        $zonaId = request()->integer('zona') ?: null;

        $fichas = Ficha::activo()
            ->whereHas('lugar', fn ($q) => $q
                ->where('categoria_id', $categoria->id)
                ->where('activo', true)
                ->when($zonaId, fn ($q) => $q->where('zona_id', $zonaId))
            )
            ->with(['lugar.zona'])
            ->orderByDesc('featured_score')
            ->paginate(12)
            ->withQueryString();

        $zonas = Zona::orderBy('nombre')
            ->whereHas('lugares', fn ($q) => $q
                ->where('categoria_id', $categoria->id)
                ->where('activo', true)
            )
            ->get();

        return view('categorias.show', compact('categoria', 'fichas', 'zonas', 'zonaId'));
    }
}
