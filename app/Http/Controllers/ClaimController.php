<?php

namespace App\Http\Controllers;

use App\Mail\ClaimReceived;
use App\Mail\NuevoReclamo;
use App\Models\ClaimRequest;
use App\Models\Lugar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ClaimController extends Controller
{
    public function create(string $slug)
    {
        $lugar = Lugar::where('slug', $slug)->where('activo', true)->firstOrFail();

        // Si ya tiene propietario asignado, no se puede reclamar
        $ficha = $lugar->fichas()->first();
        if ($ficha?->user_id) {
            return redirect()->route('negocios.show', $lugar->slug)
                ->with('error', 'Este negocio ya tiene un propietario asignado.');
        }

        // Si ya hay un claim pendiente para este lugar
        $claimPendiente = ClaimRequest::where('lugar_id', $lugar->id)
            ->where('estado', 'pendiente')
            ->exists();

        return view('negocios.claim', compact('lugar', 'claimPendiente'));
    }

    public function store(Request $request, string $slug)
    {
        $lugar = Lugar::where('slug', $slug)->where('activo', true)->firstOrFail();

        // Validar que no tenga propietario
        $ficha = $lugar->fichas()->first();
        if ($ficha?->user_id) {
            return redirect()->route('negocios.show', $lugar->slug)
                ->with('error', 'Este negocio ya tiene un propietario asignado.');
        }

        // Validar que no haya claim pendiente
        if (ClaimRequest::where('lugar_id', $lugar->id)->where('estado', 'pendiente')->exists()) {
            return redirect()->route('negocios.show', $lugar->slug)
                ->with('error', 'Ya hay una solicitud pendiente para este negocio.');
        }

        $validated = $request->validate([
            'nombre_completo' => ['required', 'string', 'max:100'],
            'email'           => ['required', 'email', 'max:150'],
            'telefono'        => ['required', 'string', 'max:50'],
            'rut_numero'      => ['required', 'string', 'regex:/^\d{12}$/'],
            'constancia'      => ['required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
            'mensaje'         => ['nullable', 'string', 'max:1000'],
        ], [
            'nombre_completo.required' => 'El nombre es obligatorio.',
            'email.required'           => 'El email es obligatorio.',
            'email.email'              => 'Ingresá un email válido.',
            'telefono.required'        => 'El teléfono es obligatorio.',
            'rut_numero.required'      => 'El número de RUT es obligatorio.',
            'rut_numero.regex'         => 'El RUT debe tener exactamente 12 dígitos.',
            'constancia.required'      => 'La constancia de RUT es obligatoria.',
            'constancia.mimes'         => 'La constancia debe ser JPG, PNG o PDF.',
            'constancia.max'           => 'La constancia no puede superar 5 MB.',
        ]);

        $claim = ClaimRequest::create([
            'lugar_id'        => $lugar->id,
            'nombre_completo' => $validated['nombre_completo'],
            'email'           => $validated['email'],
            'telefono'        => $validated['telefono'],
            'rut_numero'      => $validated['rut_numero'],
            'mensaje'         => $validated['mensaje'] ?? null,
        ]);

        $claim->addMediaFromRequest('constancia')
            ->toMediaCollection('constancia_rut');

        // Notificar al admin
        Mail::to(config('app.admin_email'))->send(new NuevoReclamo($claim));

        // Confirmar recepción al solicitante
        Mail::to($claim->email)->send(new ClaimReceived($claim));

        return redirect()->route('negocios.show', $lugar->slug)
            ->with('success', '¡Solicitud enviada! Te responderemos en 24-48 horas.');
    }
}
