<?php

namespace App\Http\Controllers;

use App\Models\Negocio;
use App\Models\SlugRedirect;

class NegocioController extends Controller
{
    public function index()
    {
        return view('negocios.index');
    }

    public function show(string $slug)
    {
        $negocio = Negocio::where('slug', $slug)->first();

        if (! $negocio) {
            $redirect = SlugRedirect::where('old_slug', $slug)->with('negocio')->first();

            if ($redirect?->negocio) {
                return redirect()->route('negocios.show', $redirect->negocio->slug, 301);
            }

            abort(404);
        }

        abort_unless($negocio->activo, 404);

        $promociones = $negocio->promociones()->vigente()->orderBy('fecha_fin')->get();

        return view('negocios.show', compact('negocio', 'promociones'));
    }
}
