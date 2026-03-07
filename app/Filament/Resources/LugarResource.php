<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LugarResource\Pages;
use App\Models\Categoria;
use App\Models\Lugar;
use App\Models\Zona;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class LugarResource extends Resource
{
    protected static ?string $model = Lugar::class;

    protected static ?string $navigationIcon = 'heroicon-o-map-pin';

    protected static ?string $navigationLabel = 'Lugares';

    protected static ?string $modelLabel = 'Lugar';

    protected static ?string $pluralModelLabel = 'Lugares';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Tabs::make('Lugar')
                    ->tabs([
                        Forms\Components\Tabs\Tab::make('Info básica')
                            ->icon('heroicon-o-information-circle')
                            ->schema([
                                Forms\Components\TextInput::make('nombre')
                                    ->label('Nombre')
                                    ->required()
                                    ->maxLength(255)
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(fn (Set $set, ?string $state) =>
                                        $set('slug', Str::slug($state ?? ''))
                                    ),
                                Forms\Components\TextInput::make('slug')
                                    ->label('Slug')
                                    ->required()
                                    ->maxLength(255)
                                    ->unique(ignoreRecord: true)
                                    ->helperText('Se genera automáticamente desde el nombre.'),
                                Forms\Components\Select::make('categoria_id')
                                    ->label('Categoría')
                                    ->options(Categoria::orderBy('nombre')->pluck('nombre', 'id'))
                                    ->required()
                                    ->searchable(),
                                Forms\Components\TextInput::make('rut')
                                    ->label('RUT (Uruguay)')
                                    ->maxLength(20)
                                    ->unique(ignoreRecord: true)
                                    ->placeholder('210034890014')
                                    ->helperText('12 dígitos sin separadores. Requerido para que el propietario reclame el lugar.')
                                    ->regex('/^\d{12}$/')
                                    ->validationMessages([
                                        'regex' => 'El RUT debe tener exactamente 12 dígitos numéricos.',
                                    ])
                                    ->nullable()
                                    ->dehydrateStateUsing(fn (?string $state): ?string =>
                                        $state ? preg_replace('/\D/', '', $state) : null
                                    ),
                                Forms\Components\Toggle::make('activo')
                                    ->label('Activo')
                                    ->default(true)
                                    ->helperText('Solo los lugares activos son visibles en el sitio.'),
                            ])
                            ->columns(2),

                        Forms\Components\Tabs\Tab::make('Ubicación')
                            ->icon('heroicon-o-map-pin')
                            ->schema([
                                Forms\Components\View::make('filament.forms.components.map-picker')
                                    ->columnSpanFull()
                                    ->dehydrated(false),
                                Forms\Components\TextInput::make('lat')
                                    ->label('Latitud')
                                    ->numeric()
                                    ->step(0.0000001)
                                    ->readOnly()
                                    ->live()
                                    ->afterStateUpdated(fn (Set $set, Get $get) => static::actualizarZona($set, $get))
                                    ->helperText('Se actualiza al hacer click en el mapa.'),
                                Forms\Components\TextInput::make('lng')
                                    ->label('Longitud')
                                    ->numeric()
                                    ->step(0.0000001)
                                    ->readOnly()
                                    ->live()
                                    ->afterStateUpdated(fn (Set $set, Get $get) => static::actualizarZona($set, $get))
                                    ->helperText('Se actualiza al hacer click en el mapa.'),
                                Forms\Components\TextInput::make('direccion')
                                    ->label('Dirección')
                                    ->maxLength(255)
                                    ->columnSpanFull(),
                                Forms\Components\Select::make('zona_id')
                                    ->label('Zona')
                                    ->options(Zona::orderBy('nombre')->pluck('nombre', 'id'))
                                    ->searchable()
                                    ->helperText('Se asigna automáticamente al colocar el pin. Podés ajustarla manualmente.')
                                    ->columnSpanFull(),
                            ])
                            ->columns(2),
                    ])
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nombre')
                    ->label('Lugar')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('categoria.nombre')
                    ->label('Categoría')
                    ->badge()
                    ->sortable(),
                Tables\Columns\TextColumn::make('zona.nombre')
                    ->label('Zona')
                    ->badge()
                    ->color('info')
                    ->sortable(),
                Tables\Columns\TextColumn::make('rut')
                    ->label('RUT')
                    ->searchable()
                    ->copyable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('fichas_count')
                    ->label('Fichas')
                    ->counts('fichas')
                    ->alignCenter()
                    ->badge()
                    ->color('gray'),
                Tables\Columns\IconColumn::make('activo')
                    ->label('Activo')
                    ->boolean()
                    ->alignCenter()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Actualizado')
                    ->dateTime('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('categoria_id')
                    ->label('Categoría')
                    ->options(Categoria::orderBy('nombre')->pluck('nombre', 'id')),
                Tables\Filters\SelectFilter::make('zona_id')
                    ->label('Zona')
                    ->options(Zona::orderBy('nombre')->pluck('nombre', 'id')),
                Tables\Filters\TernaryFilter::make('activo')
                    ->label('Activo'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('nombre');
    }

    private static function actualizarZona(Set $set, Get $get): void
    {
        $lat = (float) $get('lat');
        $lng = (float) $get('lng');

        if (!$lat || !$lng) {
            return;
        }

        $zonas = Zona::whereNotNull('lat_centro')
            ->whereNotNull('lng_centro')
            ->get();

        if ($zonas->isEmpty()) {
            return;
        }

        $nearest = $zonas->sortBy(function (Zona $zona) use ($lat, $lng): float {
            return ($lat - $zona->lat_centro) ** 2 + ($lng - $zona->lng_centro) ** 2;
        })->first();

        if ($nearest) {
            $set('zona_id', $nearest->id);
        }
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListLugares::route('/'),
            'create' => Pages\CreateLugar::route('/create'),
            'edit'   => Pages\EditLugar::route('/{record}/edit'),
        ];
    }
}
