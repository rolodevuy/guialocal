<?php

namespace App\Http\Controllers;

use App\Models\Evento;

class EventoController extends Controller
{
    public function index()
    {
        $eventos = Evento::publicado()
            ->proximo()
            ->with('lugar.zona')
            ->orderBy('fecha_inicio')
            ->paginate(12);

        return view('eventos.index', compact('eventos'));
    }

    public function show(Evento $evento)
    {
        abort_if(! $evento->publicado, 404);

        $evento->load('lugar.zona');

        return view('eventos.show', compact('evento'));
    }
}
