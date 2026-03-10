<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use App\Models\Ficha;
use App\Models\Sector;
use App\Models\Zona;

class CategoriaController extends Controller
{
    public function index()
    {
        $sectores = Sector::activo()
            ->orderBy('orden')
            ->with(['categorias' => fn ($q) => $q
                ->activo()
                ->whereNull('parent_id')
                ->withCount(['lugares as negocios_count' => fn ($q) => $q->where('activo', true)])
                ->orderBy('nombre')
            ])
            ->get();

        // Categorías sin sector (fallback)
        $sinSector = Categoria::activo()
            ->whereNull('parent_id')
            ->whereNull('sector_id')
            ->withCount(['lugares as negocios_count' => fn ($q) => $q->where('activo', true)])
            ->orderBy('nombre')
            ->get();

        return view('categorias.index', compact('sectores', 'sinSector'));
    }

    public function show(Categoria $categoria)
    {
        abort_unless($categoria->activo, 404);

        $categoria->load('sector');

        $zonaId = request()->integer('zona') ?: null;

        // Incluir hijos: si la categoría tiene nivel 1, también mostramos fichas de sus subcategorías
        $categoriaIds = Categoria::where('parent_id', $categoria->id)
            ->pluck('id')
            ->prepend($categoria->id);

        $fichas = Ficha::activo()
            ->whereHas('lugar', fn ($q) => $q
                ->whereIn('categoria_id', $categoriaIds)
                ->where('activo', true)
                ->when($zonaId, fn ($q) => $q->where('zona_id', $zonaId))
            )
            ->with(['lugar.zona'])
            ->orderByDesc('featured_score')
            ->paginate(12)
            ->withQueryString();

        $zonas = Zona::orderBy('nombre')
            ->whereHas('lugares', fn ($q) => $q
                ->whereIn('categoria_id', $categoriaIds)
                ->where('activo', true)
            )
            ->get();

        return view('categorias.show', compact('categoria', 'fichas', 'zonas', 'zonaId'));
    }
}
