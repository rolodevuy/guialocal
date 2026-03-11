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

        $claims = ClaimRequest::where('estado', 'rechazado')
            ->where('reviewed_at', '<', now()->subDays($days))
            ->get();

        $count = 0;
        foreach ($claims as $claim) {
            $claim->clearMediaCollection('constancia_rut');
            $count++;
        }

        $this->info("Constancias eliminadas: {$count}");

        return self::SUCCESS;
    }
}
