<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use App\Models\Negocio;
use App\Models\Zona;

class ZonaController extends Controller
{
    public function show(Zona $zona)
    {
        $categoriaId = request()->integer('categoria') ?: null;

        $negocios = Negocio::activo()
            ->where('zona_id', $zona->id)
            ->when($categoriaId, fn ($q) => $q->where('categoria_id', $categoriaId))
            ->with(['categoria', 'zona'])
            ->orderByDesc('featured')
            ->orderBy('nombre')
            ->paginate(12)
            ->withQueryString();

        $categorias = Categoria::activo()
            ->orderBy('nombre')
            ->whereHas('negocios', fn ($q) => $q->activo()->where('zona_id', $zona->id))
            ->get();

        return view('zonas.show', compact('zona', 'negocios', 'categorias', 'categoriaId'));
    }
}
