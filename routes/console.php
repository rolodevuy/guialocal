<?php

use App\Models\Setting;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Backup diario: hora configurable desde admin
$backupTime = Setting::get('backup_time', '01:30');
Schedule::command('backup:run --only-db')->daily()->at($backupTime);
Schedule::command('backup:clean')->daily()->at(
    \Carbon\Carbon::createFromFormat('H:i', $backupTime)->addMinutes(30)->format('H:i')
);
