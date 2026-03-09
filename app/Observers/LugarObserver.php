<?php

namespace App\Observers;

use App\Models\Lugar;
use App\Models\SlugRedirect;

class LugarObserver
{
    /**
     * Cuando un Lugar cambia de slug, guarda el slug viejo como redirect 301
     * para que las URLs existentes no queden rotas.
     */
    public function updating(Lugar $lugar): void
    {
        if ($lugar->isDirty('slug') && $lugar->getOriginal('slug')) {
            $oldSlug = $lugar->getOriginal('slug');

            // Sólo insertar si no existe ya
            SlugRedirect::firstOrCreate(
                ['old_slug' => $oldSlug],
                ['lugar_id' => $lugar->id]
            );
        }
    }
}
