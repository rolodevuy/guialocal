<?php

namespace App\Console\Commands;

use App\Models\ClaimRequest;
use Illuminate\Console\Command;

class PurgeRejectedClaims extends Command
{
    protected $signature = 'claims:purge-rejected {--days=90 : Días después del rechazo}';
    protected $description = 'Elimina constancias de reclamos rechazados con más de N días';

    public function handle(): int
    {
        $days = (int) $this->option('days');

        $count = 0;

        ClaimRequest::where('estado', 'rechazado')
            ->where('reviewed_at', '<', now()->subDays($days))
            ->chunkById(100, function ($claims) use (&$count) {
                foreach ($claims as $claim) {
                    $claim->clearMediaCollection('constancia_rut');
                    $count++;
                }
            });

        $this->info("Constancias eliminadas: {$count}");

        return self::SUCCESS;
    }
}
