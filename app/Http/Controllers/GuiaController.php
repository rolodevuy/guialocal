<?php

namespace App\Http\Controllers;

use App\Models\Guia;

class GuiaController extends Controller
{
    public function index()
    {
        $guias = Guia::publicado()
            ->with('categoria')
            ->withCount('negocios')
            ->orderByDesc('publicado_en')
            ->orderByDesc('created_at')
            ->paginate(12);

        return view('guias.index', compact('guias'));
    }

    public function show(Guia $guia)
    {
        abort_unless($guia->publicado, 404);

        $guia->load(['categoria', 'negocios.categoria', 'negocios.zona']);

        return view('guias.show', compact('guia'));
    }
}
