<?php

namespace App\Console\Commands;

use App\Models\Categoria;
use App\Models\Negocio;
use Illuminate\Console\Command;

class RecalcularScores extends Command
{
    protected $signature   = 'app:recalcular-scores';
    protected $description = 'Recalcula featured_score de negocios y popularidad_score de categorías';

    public function handle(): int
    {
        // ── 1. featured_score por negocio ──────────────────────────────────────
        $this->info('Calculando featured_score de negocios...');

        $negocios = Negocio::all();
        $bar      = $this->output->createProgressBar($negocios->count());
        $bar->start();

        foreach ($negocios as $negocio) {
            $score = match ($negocio->plan ?? 'gratuito') {
                'premium' => 50,
                'basico'  => 20,
                default   => 0,
            };
            if ($negocio->featured) {
                $score += 30;
            }

            // Actualizar directo en BD sin disparar eventos
            Negocio::withoutEvents(
                fn () => $negocio->update(['featured_score' => $score])
            );

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();

        // ── 2. popularidad_score por categoría ────────────────────────────────
        $this->info('Calculando popularidad_score de categorías...');

        $categorias = Categoria::withCount([
            'negocios as activos_count'  => fn ($q) => $q->where('activo', true),
            'negocios as premium_count'  => fn ($q) => $q->where('activo', true)->where('plan', 'premium'),
        ])->get();

        foreach ($categorias as $cat) {
            $score = ($cat->activos_count * 5) + ($cat->premium_count * 10);
            Categoria::withoutEvents(
                fn () => $cat->update(['popularidad_score' => $score])
            );
        }

        $this->info("✓ {$categorias->count()} categorías actualizadas.");
        $this->info('Listo.');

        return Command::SUCCESS;
    }
}
