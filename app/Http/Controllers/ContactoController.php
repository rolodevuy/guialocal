<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ContactoController extends Controller
{
    public function show()
    {
        return view('contacto');
    }

    public function store(Request $request)
    {
        // implementado en Paso 20
        return redirect()->route('contacto.show');
    }
}
