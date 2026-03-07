<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use App\Models\Ficha;
use App\Models\Zona;

class ZonaController extends Controller
{
    public function show(Zona $zona)
    {
        $categoriaId = request()->integer('categoria') ?: null;

        $fichas = Ficha::activo()
            ->whereHas('lugar', fn ($q) => $q
                ->where('zona_id', $zona->id)
                ->where('activo', true)
                ->when($categoriaId, fn ($q) => $q->where('categoria_id', $categoriaId))
            )
            ->with(['lugar.categoria', 'lugar.zona'])
            ->orderByDesc('featured_score')
            ->paginate(12)
            ->withQueryString();

        $categorias = Categoria::activo()
            ->orderBy('nombre')
            ->whereHas('lugares', fn ($q) => $q
                ->where('zona_id', $zona->id)
                ->where('activo', true)
            )
            ->get();

        return view('zonas.show', compact('zona', 'fichas', 'categorias', 'categoriaId'));
    }
}
