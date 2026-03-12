<?php

namespace App\Providers;

use App\Models\Articulo;
use App\Models\Guia;
use App\Models\Lugar;
use App\Models\Sector;
use App\Models\Setting;
use App\Observers\LugarObserver;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
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

        // Backup: aplicar password desde settings si está habilitada (cacheado)
        try {
            $backupConfig = Cache::remember('backup_password_config', 3600, function () {
                if (!Schema::hasTable('settings')) return null;
                $enabled = Setting::get('backup_password_enabled', '0');
                if ($enabled !== '1') return null;
                return Setting::get('backup_password', '');
            });
            if ($backupConfig) {
                config(['backup.backup.destination.password' => $backupConfig]);
            }
        } catch (\Throwable) {}

        // Comparte datos globales con todas las vistas del layout (cacheado 1h)
        View::composer('layouts.app', function ($view) {
            $view->with('hayArticulos', Cache::remember('nav_hay_articulos', 3600, fn () => Articulo::publicado()->exists()));
            $view->with('hayGuias', Cache::remember('nav_hay_guias', 3600, fn () => Guia::publicado()->exists()));
            $view->with('sectoresNav', Cache::remember('nav_sectores', 3600, fn () => Sector::activo()->orderBy('orden')->get()));
        });
    }
}
