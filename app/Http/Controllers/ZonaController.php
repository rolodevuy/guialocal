<?php

namespace App\Http\Controllers;

use App\Models\Zona;

class ZonaController extends Controller
{
    public function show(Zona $zona)
    {
        return view('zonas.show', compact('zona'));
    }
}
