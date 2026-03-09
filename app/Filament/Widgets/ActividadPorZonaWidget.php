<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use Illuminate\Support\Facades\DB;

class ActividadPorZonaWidget extends Widget
{
    protected static ?int $sort = 3;
    protected static string $view = 'filament.widgets.actividad-por-zona';
    protected int | string | array $columnSpan = 'full';

    protected function getViewData(): array
    {
        $zonas = DB::table('zonas')
            ->leftJoin('lugares', function ($join) {
                $join->on('zonas.id', '=', 'lugares.zona_id')
                     ->where('lugares.activo', true);
            })
            ->leftJoin('fichas', function ($join) {
                $join->on('lugares.id', '=', 'fichas.lugar_id')
                     ->where('fichas.activo', true)
                     ->where('fichas.estado', 'activa');
            })
            ->select(
                'zonas.nombre',
                DB::raw('COUNT(DISTINCT fichas.id) as total_fichas'),
                DB::raw('SUM(fichas.visitas) as total_visitas'),
                DB::raw('SUM(CASE WHEN fichas.plan = "premium" THEN 1 ELSE 0 END) as fichas_premium'),
                DB::raw('SUM(CASE WHEN fichas.plan = "basico"  THEN 1 ELSE 0 END) as fichas_basico')
            )
            ->groupBy('zonas.id', 'zonas.nombre')
            ->orderByDesc('total_visitas')
            ->get();

        $totalVisitas = $zonas->sum('total_visitas') ?: 1; // evitar div/0

        return compact('zonas', 'totalVisitas');
    }
}
