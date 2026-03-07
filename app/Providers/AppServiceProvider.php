<?php

namespace App\Providers;

use App\Models\Articulo;
use App\Models\Guia;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        // Comparte si hay guías publicadas con todas las vistas del layout
        View::composer('layouts.app', function ($view) {
            $view->with('hayArticulos', Articulo::publicado()->exists());
            $view->with('hayGuias', Guia::publicado()->exists());
        });
    }
}
