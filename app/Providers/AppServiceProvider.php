<?php

namespace App\Providers;

use App\Models\Articulo;
use App\Models\Guia;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        // Adaptar URLs cuando se accede vía proxy (ngrok, etc.)
        $forwardedHost = request()->header('X-Forwarded-Host');
        if ($forwardedHost) {
            $scheme = request()->header('X-Forwarded-Proto', 'https');
            $rootUrl = $scheme . '://' . $forwardedHost;
            URL::forceRootUrl($rootUrl);
            URL::forceScheme($scheme);
        }

        // Comparte si hay guías publicadas con todas las vistas del layout
        View::composer('layouts.app', function ($view) {
            $view->with('hayArticulos', Articulo::publicado()->exists());
            $view->with('hayGuias', Guia::publicado()->exists());
        });
    }
}
