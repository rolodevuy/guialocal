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
        $query = Negocio::activo()->with(['categoria', 'zona']);

        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(fn ($s) =>
                $s->where('nombre', 'like', "%{$q}%")
                  ->orWhere('descripcion', 'like', "%{$q}%")
                  ->orWhereHas('categoria', fn ($c) => $c->where('nombre', 'like', "%{$q}%"))
            );
        }

        if ($request->filled('categoria')) {
            $query->whereHas('categoria', fn ($c) => $c->where('slug', $request->categoria));
        }

        if ($request->filled('zona')) {
            $query->whereHas('zona', fn ($z) => $z->where('slug', $request->zona));
        }

        $negocios   = $query->orderByDesc('featured')->orderBy('nombre')->paginate(12)->withQueryString();
        $categorias = Categoria::activo()->orderBy('nombre')->get();
        $zonas      = Zona::orderBy('nombre')->get();

        return view('negocios.index', compact('negocios', 'categorias', 'zonas'));
    }

    public function show(Negocio $negocio)
    {
        abort_unless($negocio->activo, 404);
        return view('negocios.show', compact('negocio'));
    }
}
