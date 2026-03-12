<?php

namespace App\Console\Commands;

use App\Models\Categoria;
use App\Models\Ficha;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class RecalcularScores extends Command
{
    protected $signature   = 'app:recalcular-scores';
    protected $description = 'Recalcula featured_score de fichas y popularidad_score de categorías';

    public function handle(): int
    {
        // ── 1. featured_score por ficha (bulk update, sin cargar modelos) ────
        $this->info('Calculando featured_score de fichas...');

        // Un UPDATE por cada combinación plan × featured en vez de N updates
        $cases = [
            ['plan' => 'premium', 'featured' => true,  'score' => 80],
            ['plan' => 'premium', 'featured' => false, 'score' => 50],
            ['plan' => 'basico',  'featured' => true,  'score' => 50],
            ['plan' => 'basico',  'featured' => false, 'score' => 20],
            ['plan' => 'gratuito','featured' => true,  'score' => 30],
            ['plan' => 'gratuito','featured' => false, 'score' => 0],
        ];

        foreach ($cases as $case) {
            Ficha::where('plan', $case['plan'])
                ->where('featured', $case['featured'])
                ->update(['featured_score' => $case['score']]);
        }

        $this->info('✓ featured_score actualizado.');

        // ── 2. popularidad_score por categoría (2 queries en vez de 2N) ──────
        $this->info('Calculando popularidad_score de categorías...');

        // Contar fichas activas por categoría en una sola query
        $stats = DB::table('fichas')
            ->join('lugares', 'fichas.lugar_id', '=', 'lugares.id')
            ->where('fichas.activo', true)
            ->where('fichas.estado', 'activa')
            ->where('lugares.activo', true)
            ->selectRaw('lugares.categoria_id,
                COUNT(*) as activos,
                SUM(CASE WHEN fichas.plan = ? THEN 1 ELSE 0 END) as premium', ['premium'])
            ->groupBy('lugares.categoria_id')
            ->get()
            ->keyBy('categoria_id');

        // Resetear todas a 0 y luego actualizar las que tienen datos
        Categoria::query()->update(['popularidad_score' => 0]);

        foreach ($stats as $catId => $row) {
            Categoria::withoutEvents(fn () =>
                Categoria::where('id', $catId)->update([
                    'popularidad_score' => $row->activos * 5 + $row->premium * 10,
                ])
            );
        }

        $this->info("✓ {$stats->count()} categorías con fichas actualizadas.");
        $this->info('Listo.');

        return Command::SUCCESS;
    }
}
