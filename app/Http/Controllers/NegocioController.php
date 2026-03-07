<?php

namespace App\Http\Controllers;

use App\Models\Ficha;
use App\Models\Lugar;
use App\Models\SlugRedirect;

class NegocioController extends Controller
{
    public function index()
    {
        return view('negocios.index');
    }

    public function show(string $slug)
    {
        $lugar = Lugar::where('slug', $slug)->first();

        if (! $lugar) {
            $redirect = SlugRedirect::where('old_slug', $slug)->with('lugar')->first();

            if ($redirect?->lugar) {
                return redirect()->route('negocios.show', $redirect->lugar->slug, 301);
            }

            abort(404);
        }

        abort_unless($lugar->activo, 404);

        $ficha = $lugar->fichas()->activo()->with('media')->first();

        $promociones = $ficha
            ? $ficha->promociones()->vigente()->orderBy('fecha_fin')->get()
            : collect();

        return view('negocios.show', compact('lugar', 'ficha', 'promociones'));
    }
}
