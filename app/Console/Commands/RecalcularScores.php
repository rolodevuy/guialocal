<?php

namespace App\Console\Commands;

use App\Models\Categoria;
use App\Models\Ficha;
use Illuminate\Console\Command;

class RecalcularScores extends Command
{
    protected $signature   = 'app:recalcular-scores';
    protected $description = 'Recalcula featured_score de fichas y popularidad_score de categorías';

    public function handle(): int
    {
        // ── 1. featured_score por ficha ───────────────────────────────────────
        $this->info('Calculando featured_score de fichas...');

        $fichas = Ficha::all();
        $bar    = $this->output->createProgressBar($fichas->count());
        $bar->start();

        foreach ($fichas as $ficha) {
            $score = match ($ficha->plan ?? 'gratuito') {
                'premium' => 50,
                'basico'  => 20,
                default   => 0,
            };
            if ($ficha->featured) {
                $score += 30;
            }

            Ficha::withoutEvents(
                fn () => $ficha->update(['featured_score' => $score])
            );

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();

        // ── 2. popularidad_score por categoría ────────────────────────────────
        $this->info('Calculando popularidad_score de categorías...');

        $categorias = Categoria::all();

        foreach ($categorias as $cat) {
            $activos = Ficha::whereHas('lugar', fn ($q) => $q
                ->where('categoria_id', $cat->id)
                ->where('activo', true)
            )->where('activo', true)->count();

            $premium = Ficha::whereHas('lugar', fn ($q) => $q
                ->where('categoria_id', $cat->id)
                ->where('activo', true)
            )->where('activo', true)->where('plan', 'premium')->count();

            $score = ($activos * 5) + ($premium * 10);

            Categoria::withoutEvents(
                fn () => $cat->update(['popularidad_score' => $score])
            );
        }

        $this->info("✓ {$categorias->count()} categorías actualizadas.");
        $this->info('Listo.');

        return Command::SUCCESS;
    }
}
