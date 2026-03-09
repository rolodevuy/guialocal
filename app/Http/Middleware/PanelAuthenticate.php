<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PanelAuthenticate
{
    public function handle(Request $request, Closure $next)
    {
        if (! Auth::check()) {
            return redirect()->route('panel.login')
                ->with('error', 'Necesitás iniciar sesión para acceder al panel.');
        }

        return $next($request);
    }
}
