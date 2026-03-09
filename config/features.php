<?php

/**
 * Feature flags — activar/desactivar funcionalidades sin tocar código.
 * Para activar una feature, setear la variable en el .env o cambiar el default.
 */
return [

    /*
    |--------------------------------------------------------------------------
    | Reseñas públicas
    |--------------------------------------------------------------------------
    | Cuando está en false: la sección de reseñas NO aparece en la ficha pública
    | ni en el formulario de carga. Los datos siguen siendo accesibles desde admin.
    |
    | Activar: FEATURE_RESENAS=true en .env
    */
    'resenas' => env('FEATURE_RESENAS', false),

];
