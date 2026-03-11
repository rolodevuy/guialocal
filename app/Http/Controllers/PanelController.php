<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class PanelController extends Controller
{
    // ── Auth ─────────────────────────────────────────────────────────────────

    public function showLogin()
    {
        if (Auth::check()) {
            // Admin logueado → mandarlo a su panel
            if (Auth::user()->is_admin) {
                return redirect('/admin');
            }
            // Propietario logueado → mandarlo al panel
            return redirect()->route('panel.index');
        }
        return view('panel.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            // Solo usuarios con ficha asignada pueden entrar
            if (! Auth::user()->ficha) {
                Auth::logout();
                return back()->withErrors([
                    'email' => 'Tu cuenta no tiene ningún negocio asignado. Contactá al administrador.',
                ]);
            }

            return redirect()->route('panel.index');
        }

        return back()->withErrors([
            'email' => 'Email o contraseña incorrectos.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('panel.login');
    }

    // ── Panel ─────────────────────────────────────────────────────────────────

    public function index()
    {
        $ficha = Auth::user()->ficha()->with([
            'lugar.categoria',
            'lugar.zona',
            'media',
        ])->firstOrFail();

        $promocionesPendientes = $ficha->promociones()->vigente()->count();
        $reseñasPendientes     = config('features.resenas')
            ? $ficha->resenas()->pendiente()->count()
            : 0;

        // ── Métricas Premium: visitas de los últimos 30 días ─────────────────
        $visitasPorDia = collect();
        if ($ficha->plan === 'premium') {
            $desde = Carbon::today()->subDays(29);

            $registros = DB::table('ficha_visitas')
                ->where('ficha_id', $ficha->id)
                ->where('fecha', '>=', $desde->toDateString())
                ->orderBy('fecha')
                ->pluck('cantidad', 'fecha');

            // Rellenar con ceros los días sin registros
            $visitasPorDia = collect();
            for ($i = 0; $i < 30; $i++) {
                $fecha = $desde->copy()->addDays($i)->toDateString();
                $visitasPorDia[$fecha] = $registros[$fecha] ?? 0;
            }
        }

        return view('panel.dashboard', compact(
            'ficha', 'promocionesPendientes', 'reseñasPendientes', 'visitasPorDia'
        ));
    }

    public function edit()
    {
        $ficha = Auth::user()->ficha()->with(['lugar', 'media'])->firstOrFail();
        return view('panel.edit', compact('ficha'));
    }

    public function update(Request $request)
    {
        $ficha = Auth::user()->ficha()->firstOrFail();

        $validated = $request->validate([
            'descripcion' => ['nullable', 'string', 'max:2000'],
            'telefono'    => ['nullable', 'string', 'max:50'],
            'email'       => ['nullable', 'email', 'max:150'],
            'sitio_web'   => ['nullable', 'string', 'max:255'],
            // Redes sociales como campos individuales
            'instagram'   => ['nullable', 'url', 'max:255'],
            'facebook'    => ['nullable', 'url', 'max:255'],
            'whatsapp'    => ['nullable', 'url', 'max:255'],
        ], [
            'sitio_web.max'  => 'El sitio web no puede superar 255 caracteres.',
            'instagram.url'  => 'La URL de Instagram debe ser válida.',
            'facebook.url'   => 'La URL de Facebook debe ser válida.',
            'whatsapp.url'   => 'La URL de WhatsApp debe ser válida.',
        ]);

        // Reconstruir redes_sociales manteniendo las que no editamos
        $redesActuales = collect($ficha->redes_sociales ?? [])
            ->keyBy('red')
            ->toArray();

        foreach (['instagram', 'facebook', 'whatsapp'] as $red) {
            if (!empty($validated[$red])) {
                $redesActuales[$red] = ['red' => $red, 'url' => $validated[$red]];
            } else {
                unset($redesActuales[$red]);
            }
            unset($validated[$red]);
        }

        $validated['redes_sociales'] = array_values($redesActuales);

        // Normalizar sitio_web: quitar protocolo (se muestra con prefix https://)
        if (! empty($validated['sitio_web'])) {
            $validated['sitio_web'] = preg_replace('#^https?://#i', '', $validated['sitio_web']);
        }

        $ficha->update($validated);

        return back()->with('guardado', true);
    }
}
