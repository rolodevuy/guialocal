<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PromocionResource\Pages;
use App\Models\Promocion;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;

class PromocionResource extends Resource
{
    protected static ?string $model = Promocion::class;

    protected static ?string $navigationIcon   = 'heroicon-o-tag';
    protected static ?string $navigationLabel  = 'Promociones';
    protected static ?string $modelLabel       = 'Promoción';
    protected static ?string $pluralModelLabel = 'Promociones';
    protected static ?string $slug             = 'promociones';
    protected static ?int    $navigationSort   = 4;

    public static function form(Form $form): Form
    {
        return $form->schema([

            Forms\Components\Select::make('negocio_id')
                ->label('Negocio')
                ->relationship('negocio', 'nombre')
                ->searchable()
                ->preload()
                ->required()
                ->columnSpanFull(),

            Forms\Components\TextInput::make('titulo')
                ->label('Título')
                ->required()
                ->columnSpanFull(),

            Forms\Components\Textarea::make('descripcion')
                ->label('Descripción')
                ->rows(3)
                ->columnSpanFull(),

            Forms\Components\DatePicker::make('fecha_inicio')
                ->label('Válida desde')
                ->required()
                ->native(false)
                ->default(now()),

            Forms\Components\DatePicker::make('fecha_fin')
                ->label('Válida hasta (inclusive)')
                ->native(false)
                ->nullable()
                ->helperText('La promo estará activa durante todo ese día. Dejar vacío si no tiene vencimiento.'),

            Forms\Components\Toggle::make('activo')
                ->label('Activa')
                ->default(true)
                ->columnSpanFull(),

            SpatieMediaLibraryFileUpload::make('imagen')
                ->collection('imagen')
                ->label('Imagen de la promo')
                ->image()
                ->imageEditor()
                ->maxSize(2048)
                ->columnSpanFull(),

        ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                SpatieMediaLibraryImageColumn::make('imagen')
                    ->collection('imagen')
                    ->circular()
                    ->label(''),

                Tables\Columns\TextColumn::make('titulo')
                    ->label('Título')
                    ->searchable()
                    ->limit(40),

                Tables\Columns\TextColumn::make('negocio.nombre')
                    ->label('Negocio')
                    ->searchable()
                    ->badge()
                    ->color('warning'),

                Tables\Columns\TextColumn::make('fecha_inicio')
                    ->label('Desde')
                    ->date('d/m/Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('fecha_fin')
                    ->label('Hasta')
                    ->date('d/m/Y')
                    ->sortable()
                    ->placeholder('Sin vencimiento')
                    ->color(fn ($state) => $state && $state->isPast() ? 'danger' : null),

                Tables\Columns\IconColumn::make('activo')
                    ->label('Activa')
                    ->boolean()
                    ->sortable(),

                // Columna calculada: ¿está vigente ahora?
                Tables\Columns\IconColumn::make('vigente')
                    ->label('Vigente')
                    ->state(fn (Promocion $r) => $r->activo
                        && $r->fecha_inicio->lte(now())
                        && ($r->fecha_fin === null || $r->fecha_fin->gte(now()))
                    )
                    ->boolean(),
            ])
            ->defaultSort('fecha_inicio', 'desc')
            ->filters([
                Tables\Filters\TernaryFilter::make('activo')->label('Activa'),
                Tables\Filters\SelectFilter::make('negocio')
                    ->relationship('negocio', 'nombre')
                    ->label('Negocio'),
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
            'index'  => Pages\ListPromocions::route('/'),
            'create' => Pages\CreatePromocion::route('/create'),
            'edit'   => Pages\EditPromocion::route('/{record}/edit'),
        ];
    }
}
