<?php

namespace App\Providers;

use App\Models\Articulo;
use App\Models\Guia;
use App\Models\Lugar;
use App\Models\Sector;
use App\Observers\LugarObserver;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        // Observer: cuando cambia el slug de un Lugar, guarda redirect 301
        Lugar::observe(LugarObserver::class);

        // Adaptar URLs cuando se accede vía proxy (ngrok, etc.)
        $forwardedHost = request()->header('X-Forwarded-Host');
        if ($forwardedHost) {
            URL::forceRootUrl('https://' . $forwardedHost);
            URL::forceScheme('https');
            request()->server->set('HTTPS', 'on');
        }

        // Comparte datos globales con todas las vistas del layout
        View::composer('layouts.app', function ($view) {
            $view->with('hayArticulos', Articulo::publicado()->exists());
            $view->with('hayGuias', Guia::publicado()->exists());
            $view->with('sectoresNav', Sector::activo()->orderBy('orden')->get());
        });
    }
}
