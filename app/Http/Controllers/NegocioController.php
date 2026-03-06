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
        return view('negocios.show', compact('negocio'));
    }
}
