<?php

namespace App\Http\Controllers;

use App\Mail\BienvenidaNewsletterMail;
use App\Models\Suscriptor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class NewsletterController extends Controller
{
    public function subscribe(Request $request)
    {
        $data = $request->validate([
            'email'   => ['required', 'email', 'max:255'],
            'zona_id' => ['nullable', 'exists:zonas,id'],
        ]);

        $suscriptor = Suscriptor::where('email', $data['email'])->first();

        if ($suscriptor) {
            // Ya existe: reactivar si estaba dado de baja, actualizar zona
            $suscriptor->update([
                'activo'  => true,
                'zona_id' => $data['zona_id'] ?? $suscriptor->zona_id,
            ]);

            return redirect(route('home') . '#newsletter')
                ->with('newsletter_ok', '¡Ya estabas suscrito! Actualizamos tus datos.');
        }

        $suscriptor = Suscriptor::create($data);

        Mail::to($suscriptor->email)->send(new BienvenidaNewsletterMail($suscriptor));

        return redirect(route('home') . '#newsletter')
            ->with('newsletter_ok', '¡Te suscribiste! Revisá tu casilla, te mandamos un mail de bienvenida.');
    }

    public function baja(string $token)
    {
        $suscriptor = Suscriptor::where('token_baja', $token)->firstOrFail();
        $suscriptor->update(['activo' => false]);

        return view('newsletter.baja');
    }
}
