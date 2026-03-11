<?php

namespace App\Http\Controllers;

use App\Mail\ConsultaRecibida;
use App\Mail\NuevaConsulta;
use App\Models\Consulta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ContactoController extends Controller
{
    public function show()
    {
        return view('contacto');
    }

    public function store(Request $request)
    {
        $asuntoLabels = [
            'upgrade-premium' => 'Interés en plan Premium',
            'upgrade-basico'  => 'Interés en plan Básico',
            'consulta-planes' => 'Consulta sobre planes',
            'alta-negocio'    => 'Alta de negocio',
        ];

        $validated = $request->validate([
            'nombre'  => ['required', 'string', 'max:100'],
            'email'   => ['required', 'email', 'max:150'],
            'mensaje' => ['required', 'string', 'min:10', 'max:2000'],
        ], [
            'nombre.required'  => 'El nombre es obligatorio.',
            'email.required'   => 'El email es obligatorio.',
            'email.email'      => 'Ingresá un email válido.',
            'mensaje.required' => 'El mensaje es obligatorio.',
            'mensaje.min'      => 'El mensaje debe tener al menos 10 caracteres.',
        ]);

        $validated['asunto'] = $asuntoLabels[$request->query('asunto')] ?? null;

        $consulta = Consulta::create($validated);

        Mail::to(config('app.admin_email'))->send(new NuevaConsulta($consulta));
        Mail::to($consulta->email)->send(new ConsultaRecibida($consulta));

        return redirect()
            ->route('contacto.show')
            ->with('success', '¡Mensaje enviado! Te responderemos a la brevedad.');
    }
}
