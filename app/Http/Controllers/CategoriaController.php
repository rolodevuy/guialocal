<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use App\Models\Negocio;
use App\Models\Zona;

class CategoriaController extends Controller
{
    public function index()
    {
        $categorias = Categoria::activo()
            ->withCount(['negocios' => fn ($q) => $q->where('activo', true)])
            ->orderBy('nombre')
            ->get();

        return view('categorias.index', compact('categorias'));
    }

    public function show(Categoria $categoria)
    {
        abort_unless($categoria->activo, 404);

        $zonaId = request()->integer('zona') ?: null;

        $negocios = Negocio::activo()
            ->where('categoria_id', $categoria->id)
            ->when($zonaId, fn ($q) => $q->where('zona_id', $zonaId))
            ->with(['categoria', 'zona'])
            ->orderByDesc('featured_score')
            ->orderBy('nombre')
            ->paginate(12)
            ->withQueryString();

        $zonas = Zona::orderBy('nombre')
            ->whereHas('negocios', fn ($q) => $q->activo()->where('categoria_id', $categoria->id))
            ->get();

        return view('categorias.show', compact('categoria', 'negocios', 'zonas', 'zonaId'));
    }
}
