<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SuscriptorResource\Pages;
use App\Models\Suscriptor;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class SuscriptorResource extends Resource
{
    protected static ?string $model = Suscriptor::class;
    protected static ?string $navigationIcon  = 'heroicon-o-envelope';
    protected static ?string $navigationGroup = 'Comunicación';
    protected static ?string $navigationLabel = 'Newsletter';
    protected static ?string $modelLabel      = 'suscriptor';
    protected static ?string $pluralModelLabel = 'suscriptores';
    protected static ?int $navigationSort     = 1;

    public static function getNavigationBadge(): ?string
    {
        $count = Suscriptor::activo()->count();
        return $count > 0 ? (string) $count : null;
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('email')
                ->label('Email')
                ->email()
                ->required()
                ->unique(ignoreRecord: true)
                ->maxLength(255),

            Forms\Components\Select::make('zona_id')
                ->label('Zona')
                ->relationship('zona', 'nombre')
                ->nullable()
                ->placeholder('Toda la ciudad'),

            Forms\Components\Toggle::make('activo')
                ->label('Suscripción activa')
                ->default(true),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('zona.nombre')
                    ->label('Zona')
                    ->placeholder('Toda la ciudad')
                    ->badge()
                    ->color('gray'),

                Tables\Columns\IconColumn::make('activo')
                    ->label('Activo')
                    ->boolean()
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Se suscribió')
                    ->dateTime('d/m/Y')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\TernaryFilter::make('activo')
                    ->label('Estado')
                    ->trueLabel('Activos')
                    ->falseLabel('Dados de baja')
                    ->placeholder('Todos'),

                Tables\Filters\SelectFilter::make('zona_id')
                    ->label('Zona')
                    ->relationship('zona', 'nombre')
                    ->placeholder('Todas las zonas'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('baja')
                    ->label('Dar de baja')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->visible(fn (Suscriptor $record) => $record->activo)
                    ->action(fn (Suscriptor $record) => $record->update(['activo' => false])),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\BulkAction::make('dar_baja_bulk')
                        ->label('Dar de baja seleccionados')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->action(fn ($records) => $records->each->update(['activo' => false])),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListSuscriptores::route('/'),
            'create' => Pages\CreateSuscriptor::route('/create'),
            'edit'   => Pages\EditSuscriptor::route('/{record}/edit'),
        ];
    }
}
