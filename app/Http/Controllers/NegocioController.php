<?php

namespace App\Http\Controllers;

use App\Models\Negocio;

class NegocioController extends Controller
{
    public function index()
    {
        return view('negocios.index');
    }

    public function show(Negocio $negocio)
    {
        abort_unless($negocio->activo, 404);
        return view('negocios.show', compact('negocio'));
    }
}
