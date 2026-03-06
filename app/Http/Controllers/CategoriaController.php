<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use App\Models\Negocio;

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

        $negocios = Negocio::activo()
            ->where('categoria_id', $categoria->id)
            ->with(['categoria', 'zona'])
            ->orderByDesc('featured')
            ->orderBy('nombre')
            ->paginate(12);

        return view('categorias.show', compact('categoria', 'negocios'));
    }
}
