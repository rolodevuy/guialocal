<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use App\Models\Negocio;
use App\Models\Zona;
use Illuminate\Http\Request;

class NegocioController extends Controller
{
    public function index(Request $request)
    {
        $categorias = Categoria::activo()->orderBy('nombre')->get();
        $zonas      = Zona::orderBy('nombre')->get();

        if ($request->filled('q')) {
            $negocios = Negocio::search($request->q)
                ->query(fn ($q) => $q
                    ->with(['categoria', 'zona'])
                    ->where('activo', true)
                    ->when(
                        $request->filled('categoria'),
                        fn ($q) => $q->whereHas('categoria', fn ($c) => $c->where('slug', $request->categoria))
                    )
                    ->when(
                        $request->filled('zona'),
                        fn ($q) => $q->whereHas('zona', fn ($z) => $z->where('slug', $request->zona))
                    )
                    ->orderByDesc('featured')
                    ->orderBy('nombre')
                )
                ->paginate(12)
                ->withQueryString();
        } else {
            $query = Negocio::activo()->with(['categoria', 'zona']);

            if ($request->filled('categoria')) {
                $query->whereHas('categoria', fn ($c) => $c->where('slug', $request->categoria));
            }

            if ($request->filled('zona')) {
                $query->whereHas('zona', fn ($z) => $z->where('slug', $request->zona));
            }

            $negocios = $query->orderByDesc('featured')->orderBy('nombre')->paginate(12)->withQueryString();
        }

        return view('negocios.index', compact('negocios', 'categorias', 'zonas'));
    }

    public function show(Negocio $negocio)
    {
        abort_unless($negocio->activo, 404);
        return view('negocios.show', compact('negocio'));
    }
}
