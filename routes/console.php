<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Backup diario: solo base de datos (las imágenes se respaldan manualmente)
Schedule::command('backup:run --only-db')->daily()->at('01:30');
Schedule::command('backup:clean')->daily()->at('02:00');
