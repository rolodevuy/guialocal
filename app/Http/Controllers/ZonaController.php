<?php

namespace App\Http\Controllers;

use App\Models\Negocio;
use App\Models\Zona;

class ZonaController extends Controller
{
    public function show(Zona $zona)
    {
        $negocios = Negocio::activo()
            ->where('zona_id', $zona->id)
            ->with(['categoria', 'zona'])
            ->orderByDesc('featured')
            ->orderBy('nombre')
            ->paginate(12);

        return view('zonas.show', compact('zona', 'negocios'));
    }
}
