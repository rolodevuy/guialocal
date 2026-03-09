<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ResenaResource\Pages;
use App\Models\Resena;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Notifications\Notification;

class ResenaResource extends Resource
{
    protected static ?string $model = Resena::class;

    protected static ?string $navigationIcon  = 'heroicon-o-star';
    protected static ?string $navigationLabel = 'Reseñas';
    protected static ?string $modelLabel      = 'Reseña';
    protected static ?string $pluralModelLabel = 'Reseñas';
    protected static ?string $navigationGroup = 'Comunidad';
    protected static ?int    $navigationSort  = 1;

    /** Muestra badge con cantidad de reseñas pendientes */
    public static function getNavigationBadge(): ?string
    {
        $pendientes = Resena::pendiente()->count();
        return $pendientes > 0 ? (string) $pendientes : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Reseña')
                    ->columns(2)
                    ->schema([
                        Forms\Components\Select::make('ficha_id')
                            ->label('Negocio')
                            ->relationship('ficha.lugar', 'nombre')
                            ->searchable()
                            ->required()
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('nombre')
                            ->label('Autor')
                            ->required()
                            ->maxLength(100),
                        Forms\Components\TextInput::make('email')
                            ->label('Email (privado)')
                            ->email()
                            ->nullable(),
                        Forms\Components\Select::make('rating')
                            ->label('Puntuación')
                            ->options([
                                1 => '★ — Muy malo',
                                2 => '★★ — Malo',
                                3 => '★★★ — Regular',
                                4 => '★★★★ — Bueno',
                                5 => '★★★★★ — Excelente',
                            ])
                            ->required(),
                        Forms\Components\Toggle::make('aprobada')
                            ->label('Aprobada')
                            ->helperText('Solo las reseñas aprobadas son visibles en el sitio.'),
                        Forms\Components\Textarea::make('cuerpo')
                            ->label('Reseña')
                            ->required()
                            ->rows(4)
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('ficha.lugar.nombre')
                    ->label('Negocio')
                    ->searchable()
                    ->sortable()
                    ->limit(30),
                Tables\Columns\TextColumn::make('nombre')
                    ->label('Autor')
                    ->searchable(),
                Tables\Columns\TextColumn::make('rating')
                    ->label('Puntuación')
                    ->formatStateUsing(fn (int $state): string =>
                        str_repeat('★', $state) . str_repeat('☆', 5 - $state)
                    )
                    ->color(fn (int $state): string => match (true) {
                        $state >= 4 => 'success',
                        $state === 3 => 'warning',
                        default      => 'danger',
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('cuerpo')
                    ->label('Reseña')
                    ->limit(60)
                    ->tooltip(fn (Resena $record): string => $record->cuerpo),
                Tables\Columns\IconColumn::make('aprobada')
                    ->label('Aprobada')
                    ->boolean()
                    ->alignCenter()
                    ->trueColor('success')
                    ->falseColor('warning'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Enviada')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->filters([
                TernaryFilter::make('aprobada')
                    ->label('Estado')
                    ->trueLabel('Aprobadas')
                    ->falseLabel('Pendientes')
                    ->placeholder('Todas'),
            ])
            ->actions([
                // Aprobar con un click
                Tables\Actions\Action::make('aprobar')
                    ->label('Aprobar')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (Resena $record): bool => ! $record->aprobada)
                    ->requiresConfirmation()
                    ->modalHeading('Aprobar reseña')
                    ->modalDescription('Esta reseña quedará visible en el sitio.')
                    ->action(function (Resena $record): void {
                        $record->update(['aprobada' => true]);
                        Notification::make()
                            ->title('Reseña aprobada')
                            ->success()
                            ->send();
                    }),
                Tables\Actions\Action::make('rechazar')
                    ->label('Rechazar')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn (Resena $record): bool => $record->aprobada)
                    ->action(function (Resena $record): void {
                        $record->update(['aprobada' => false]);
                        Notification::make()
                            ->title('Reseña desaprobada')
                            ->warning()
                            ->send();
                    }),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('aprobar_seleccionadas')
                        ->label('Aprobar seleccionadas')
                        ->icon('heroicon-o-check')
                        ->color('success')
                        ->action(fn ($records) => $records->each->update(['aprobada' => true]))
                        ->deselectRecordsAfterCompletion(),
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListResenas::route('/'),
            'edit'  => Pages\EditResena::route('/{record}/edit'),
        ];
    }
}
