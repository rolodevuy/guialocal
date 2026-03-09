<?php

namespace App\Filament\Widgets;

use App\Models\Ficha;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class TopFichasWidget extends BaseWidget
{
    protected static ?int $sort = 2;
    protected static ?string $heading = 'Top 10 fichas más visitadas';
    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Ficha::query()
                    ->with(['lugar.categoria', 'lugar.zona'])
                    ->where('activo', true)
                    ->orderByDesc('visitas')
                    ->limit(10)
            )
            ->columns([
                Tables\Columns\TextColumn::make('lugar.nombre')
                    ->label('Negocio')
                    ->searchable()
                    ->url(fn (Ficha $record) => route('negocios.show', $record->lugar->slug ?? ''))
                    ->openUrlInNewTab()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('lugar.categoria.nombre')
                    ->label('Categoría')
                    ->badge()
                    ->color('gray'),

                Tables\Columns\TextColumn::make('lugar.zona.nombre')
                    ->label('Zona')
                    ->placeholder('Sin zona'),

                Tables\Columns\BadgeColumn::make('plan')
                    ->label('Plan')
                    ->colors([
                        'gray'    => 'gratuito',
                        'info'    => 'basico',
                        'warning' => 'premium',
                    ]),

                Tables\Columns\TextColumn::make('visitas')
                    ->label('Visitas')
                    ->numeric()
                    ->sortable()
                    ->alignEnd()
                    ->weight('bold'),
            ])
            ->paginated(false)
            ->striped();
    }
}
