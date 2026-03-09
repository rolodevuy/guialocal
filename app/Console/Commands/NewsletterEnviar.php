<?php

namespace App\Console\Commands;

use App\Mail\NewsletterMail;
use App\Models\Articulo;
use App\Models\Ficha;
use App\Models\Promocion;
use App\Models\Suscriptor;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class NewsletterEnviar extends Command
{
    protected $signature = 'newsletter:enviar
                            {--zona= : ID de zona específica (omitir = todas las zonas)}
                            {--dry-run : Mostrar cuántos mails se enviarían sin enviar}';

    protected $description = 'Envía el newsletter semanal a los suscriptores activos';

    public function handle(): int
    {
        $zonaId  = $this->option('zona');
        $dryRun  = $this->option('dry-run');

        $query = Suscriptor::activo()->with('zona');
        if ($zonaId) {
            $query->where('zona_id', $zonaId);
        }

        $suscriptores = $query->get();

        if ($suscriptores->isEmpty()) {
            $this->info('No hay suscriptores activos para enviar.');
            return self::SUCCESS;
        }

        $this->info("Suscriptores a notificar: {$suscriptores->count()}");

        if ($dryRun) {
            $this->warn('--dry-run activo: no se envían mails.');
            return self::SUCCESS;
        }

        // Último artículo publicado (global)
        $ultimoArticulo = Articulo::where('publicado', true)
            ->whereNotNull('publicado_en')
            ->orderByDesc('publicado_en')
            ->first();

        $enviados = 0;

        $bar = $this->output->createProgressBar($suscriptores->count());
        $bar->start();

        foreach ($suscriptores as $suscriptor) {
            // Nuevos negocios: fichas activas en la zona del suscriptor (últimos 7 días)
            $nuevosNegocios = Ficha::activo()
                ->where('created_at', '>=', now()->subDays(7))
                ->when($suscriptor->zona_id, fn ($q) =>
                    $q->whereHas('lugar', fn ($l) => $l->where('zona_id', $suscriptor->zona_id))
                )
                ->with(['lugar.categoria', 'lugar.zona'])
                ->orderByDesc('featured_score')
                ->limit(5)
                ->get();

            // Promociones vigentes en la zona del suscriptor
            $promociones = Promocion::where('activo', true)
                ->where(fn ($q) => $q
                    ->whereNull('fecha_fin')
                    ->orWhere('fecha_fin', '>=', now()->toDateString())
                )
                ->when($suscriptor->zona_id, fn ($q) =>
                    $q->whereHas('ficha.lugar', fn ($l) => $l->where('zona_id', $suscriptor->zona_id))
                )
                ->with(['ficha.lugar'])
                ->limit(3)
                ->get();

            // Solo enviar si hay al menos algo que mostrar
            if ($nuevosNegocios->isEmpty() && $promociones->isEmpty() && !$ultimoArticulo) {
                $bar->advance();
                continue;
            }

            try {
                Mail::to($suscriptor->email)
                    ->send(new NewsletterMail($suscriptor, $nuevosNegocios, $promociones, $ultimoArticulo));
                $enviados++;
            } catch (\Throwable $e) {
                $this->newLine();
                $this->error("Error enviando a {$suscriptor->email}: {$e->getMessage()}");
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info("✓ Newsletter enviado a {$enviados} suscriptores.");

        return self::SUCCESS;
    }
}
