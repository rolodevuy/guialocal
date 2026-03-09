<?php

namespace App\Filament\Widgets;

use App\Models\Consulta;
use App\Models\Ficha;
use App\Models\Resena;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverviewWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $totalActivas  = Ficha::where('activo', true)->where('estado', 'activa')->count();
        $totalPremium  = Ficha::where('plan', 'premium')->where('activo', true)->count();
        $totalBasico   = Ficha::where('plan', 'basico')->where('activo', true)->count();
        $totalGratuito = Ficha::where('plan', 'gratuito')->where('activo', true)->count();
        $visitasTotales = Ficha::sum('visitas');
        $consultasPendientes = Consulta::where('leido', false)->count();

        $resenasPendientes = 0;
        if (config('features.resenas', false)) {
            $resenasPendientes = Resena::where('aprobada', false)->count();
        }

        return [
            Stat::make('Fichas activas', $totalActivas)
                ->description("Premium: {$totalPremium} · Básico: {$totalBasico} · Gratuito: {$totalGratuito}")
                ->icon('heroicon-o-building-storefront')
                ->color('success'),

            Stat::make('Plan Premium', $totalPremium)
                ->description("Básico: {$totalBasico} · Gratuito: {$totalGratuito}")
                ->icon('heroicon-o-star')
                ->color('warning'),

            Stat::make('Visitas totales', number_format($visitasTotales))
                ->description('Acumulado de todas las fichas')
                ->icon('heroicon-o-eye')
                ->color('info'),

            Stat::make('Consultas sin leer', $consultasPendientes)
                ->description($consultasPendientes > 0 ? 'Requieren atención' : 'Todo al día')
                ->icon('heroicon-o-envelope')
                ->color($consultasPendientes > 0 ? 'danger' : 'success'),

            Stat::make('Reseñas pendientes', $resenasPendientes)
                ->description($resenasPendientes > 0 ? 'Esperan moderación' : (config('features.resenas') ? 'Todo al día' : 'Feature desactivada'))
                ->icon('heroicon-o-chat-bubble-left-right')
                ->color($resenasPendientes > 0 ? 'danger' : 'gray'),
        ];
    }
}
