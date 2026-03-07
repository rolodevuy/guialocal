<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FeaturedSlotResource\Pages;
use App\Models\Articulo;
use App\Models\FeaturedSlot;
use App\Models\Guia;
use App\Models\Lugar;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class FeaturedSlotResource extends Resource
{
    protected static ?string $model = FeaturedSlot::class;

    protected static ?string $navigationIcon   = 'heroicon-o-star';
    protected static ?string $navigationLabel  = 'Destacados';
    protected static ?string $modelLabel       = 'Slot destacado';
    protected static ?string $pluralModelLabel = 'Slots destacados';
    protected static ?int    $navigationSort   = 7;

    // Mapeo tipo → clase PHP
    const TIPOS = [
        'App\\Models\\Lugar'    => 'Negocio',
        'App\\Models\\Articulo' => 'Artículo',
        'App\\Models\\Guia'     => 'Guía',
    ];

    public static function form(Form $form): Form
    {
        return $form->schema([

            Forms\Components\Select::make('posicion')
                ->label('Sección')
                ->options(FeaturedSlot::POSICIONES)
                ->required()
                ->native(false),

            Forms\Components\Select::make('slotable_type')
                ->label('Tipo de contenido')
                ->options(self::TIPOS)
                ->required()
                ->native(false)
                ->live()
                ->afterStateUpdated(fn ($set) => $set('slotable_id', null)),

            Forms\Components\Select::make('slotable_id')
                ->label('Elemento')
                ->options(function (Get $get) {
                    return match ($get('slotable_type')) {
                        'App\\Models\\Lugar'    => Lugar::activo()->orderBy('nombre')->pluck('nombre', 'id'),
                        'App\\Models\\Articulo' => Articulo::publicado()->orderBy('titulo')->pluck('titulo', 'id'),
                        'App\\Models\\Guia'     => Guia::publicado()->orderBy('titulo')->pluck('titulo', 'id'),
                        default                 => [],
                    };
                })
                ->searchable()
                ->required()
                ->disabled(fn (Get $get) => ! $get('slotable_type')),

            Forms\Components\TextInput::make('orden')
                ->label('Orden')
                ->numeric()
                ->default(0)
                ->required()
                ->helperText('Menor número = aparece primero.'),

            Forms\Components\Toggle::make('activo')
                ->label('Activo')
                ->default(true),

            Forms\Components\DatePicker::make('valido_hasta')
                ->label('Válido hasta')
                ->native(false)
                ->nullable()
                ->helperText('Dejar vacío para que no expire.'),

        ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('posicion')
                    ->label('Sección')
                    ->formatStateUsing(fn ($state) => FeaturedSlot::POSICIONES[$state] ?? $state)
                    ->badge()
                    ->color('warning'),

                Tables\Columns\TextColumn::make('slotable_type')
                    ->label('Tipo')
                    ->formatStateUsing(fn ($state) => self::TIPOS[$state] ?? $state)
                    ->badge()
                    ->color('gray'),

                Tables\Columns\TextColumn::make('elemento')
                    ->label('Elemento')
                    ->state(fn (FeaturedSlot $r) => $r->slotable?->nombre ?? $r->slotable?->titulo ?? '—')
                    ->limit(45),

                Tables\Columns\TextColumn::make('orden')
                    ->label('Orden')
                    ->sortable(),

                Tables\Columns\IconColumn::make('activo')
                    ->label('Activo')
                    ->boolean()
                    ->sortable(),

                Tables\Columns\TextColumn::make('valido_hasta')
                    ->label('Vence')
                    ->date('d/m/Y')
                    ->sortable()
                    ->placeholder('Sin vencimiento')
                    ->color(fn ($state) => $state && $state->isPast() ? 'danger' : null),
            ])
            ->defaultSort('posicion')
            ->reorderable('orden')
            ->filters([
                Tables\Filters\SelectFilter::make('posicion')
                    ->label('Sección')
                    ->options(FeaturedSlot::POSICIONES),
                Tables\Filters\TernaryFilter::make('activo')->label('Activo'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListFeaturedSlots::route('/'),
            'create' => Pages\CreateFeaturedSlot::route('/create'),
            'edit'   => Pages\EditFeaturedSlot::route('/{record}/edit'),
        ];
    }
}
