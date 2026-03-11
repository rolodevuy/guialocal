<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CategoriaResource\Pages;
use App\Models\Categoria;
use Filament\Forms;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class CategoriaResource extends Resource
{
    protected static ?string $model = Categoria::class;

    protected static ?string $navigationIcon = 'heroicon-o-tag';

    protected static ?string $navigationLabel = 'Categorías';

    protected static ?string $modelLabel = 'Categoría';

    protected static ?string $pluralModelLabel = 'Categorías';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('sector_id')
                    ->label('Sector')
                    ->relationship('sector', 'nombre')
                    ->searchable()
                    ->preload()
                    ->placeholder('Sin sector')
                    ->helperText('Agrupar esta categoría en un sector (solo para nivel 1).')
                    ->visible(fn (Get $get): bool => ! $get('parent_id')),

                Forms\Components\Select::make('parent_id')
                    ->label('Categoría padre')
                    ->relationship('parent', 'nombre')
                    ->options(function (Get $get, ?Categoria $record) {
                        $query = Categoria::query()
                            ->where('nivel', '<', 3)
                            ->orderBy('nivel')
                            ->orderBy('nombre');

                        // No permitir seleccionarse a sí misma ni a sus hijos
                        if ($record) {
                            $query->where('id', '!=', $record->id)
                                ->where('parent_id', '!=', $record->id);
                        }

                        return $query->get()
                            ->mapWithKeys(fn (Categoria $cat) => [
                                $cat->id => $cat->nombre_completo,
                            ]);
                    })
                    ->searchable()
                    ->preload()
                    ->placeholder('Ninguna (categoría raíz)')
                    ->helperText('Dejar vacío para categoría de nivel 1.')
                    ->reactive()
                    ->afterStateUpdated(function (Set $set, ?string $state) {
                        if (! $state) {
                            $set('nivel', 1);
                            return;
                        }
                        $parent = Categoria::find($state);
                        $set('nivel', $parent ? $parent->nivel + 1 : 1);
                    }),

                Forms\Components\TextInput::make('nivel')
                    ->label('Nivel')
                    ->disabled()
                    ->dehydrated()
                    ->default(1)
                    ->helperText('Se calcula automáticamente según la categoría padre.'),

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
                Forms\Components\Textarea::make('descripcion')
                    ->label('Descripción')
                    ->rows(3)
                    ->columnSpanFull(),
                Forms\Components\Select::make('icono')
                    ->label('Ícono')
                    ->searchable()
                    ->placeholder('Buscá un ícono...')
                    ->options([
                        'Gastronomía' => [
                            'utensils'      => 'utensils — Restaurantes',
                            'coffee'        => 'coffee — Cafés y Bares',
                            'cake'          => 'cake — Panaderías / Confiterías',
                            'wine'          => 'wine — Vinotecas / Bares',
                        ],
                        'Salud' => [
                            'pill'          => 'pill — Farmacias',
                            'heart-pulse'   => 'heart-pulse — Salud y Bienestar',
                            'eye'           => 'eye — Óptica',
                            'face-smile'    => 'face-smile — Odontología / Dental',
                        ],
                        'Comercio' => [
                            'shopping-cart' => 'shopping-cart — Supermercados',
                            'shirt'         => 'shirt — Indumentaria / Ropa',
                            'sparkles'      => 'sparkles — Estética / Belleza',
                            'scissors'      => 'scissors — Peluquería / Barbería',
                            'paint-brush'   => 'paint-brush — Arte / Decoración',
                        ],
                        'Servicios' => [
                            'briefcase'           => 'briefcase — Servicios Profesionales',
                            'home'                => 'home — Inmobiliaria / Hogar',
                            'key'                 => 'key — Cerrajería',
                            'wrench'              => 'wrench — Reparaciones / Talleres',
                            'banknotes'           => 'banknotes — Bancos / Financiero',
                            'device-phone-mobile' => 'device-phone-mobile — Telefonía',
                        ],
                        'Turismo / Transporte' => [
                            'building-office' => 'building-office — Hotel / Alojamiento',
                            'globe-alt'       => 'globe-alt — Turismo / Agencias de viaje',
                            'map-pin'         => 'map-pin — Puntos turísticos',
                            'sun'             => 'sun — Playa',
                            'truck'           => 'truck — Transporte / Logística',
                        ],
                        'Educación / Cultura' => [
                            'academic-cap'    => 'academic-cap — Educación / Institutos',
                            'building-library'=> 'building-library — Cultura / Museos',
                        ],
                        'Ocio / Entretenimiento' => [
                            'bolt'         => 'bolt — Deporte / Gimnasio',
                            'trophy'       => 'trophy — Deportes / Clubes',
                            'musical-note' => 'musical-note — Música / Espectáculos',
                            'camera'       => 'camera — Fotografía',
                        ],
                    ]),
                Forms\Components\Toggle::make('activo')
                    ->label('Activa')
                    ->default(true),
                SpatieMediaLibraryFileUpload::make('imagen_generica')
                    ->label('Imagen genérica')
                    ->collection('imagen_generica')
                    ->image()
                    ->imageResizeMode('cover')
                    ->imageCropAspectRatio('16:9')
                    ->imageResizeTargetWidth('800')
                    ->imageResizeTargetHeight('450')
                    ->helperText('Recomendado: 800x450px. Se usa como portada de negocios sin imagen propia. Se optimiza automáticamente.')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                SpatieMediaLibraryImageColumn::make('imagen_generica')
                    ->label('Imagen')
                    ->collection('imagen_generica')
                    ->conversion('thumb')
                    ->width(60)
                    ->height(40)
                    ->extraImgAttributes(['class' => 'rounded object-cover']),
                Tables\Columns\TextColumn::make('nombre')
                    ->label('Nombre')
                    ->searchable()
                    ->sortable()
                    ->formatStateUsing(function (Categoria $record): string {
                        $indent = str_repeat('— ', $record->nivel - 1);
                        return $indent . $record->nombre;
                    }),
                Tables\Columns\TextColumn::make('sector.nombre')
                    ->label('Sector')
                    ->placeholder('—')
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('parent.nombre')
                    ->label('Padre')
                    ->placeholder('—')
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('nivel')
                    ->label('Nivel')
                    ->badge()
                    ->color(fn (int $state): string => match ($state) {
                        1 => 'primary',
                        2 => 'success',
                        3 => 'warning',
                        default => 'gray',
                    })
                    ->alignCenter()
                    ->sortable(),
                Tables\Columns\TextColumn::make('slug')
                    ->label('Slug')
                    ->searchable()
                    ->copyable()
                    ->color('gray')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('icono')
                    ->label('Ícono')
                    ->badge()
                    ->color('info'),
                Tables\Columns\TextColumn::make('lugares_count')
                    ->label('Negocios')
                    ->counts('lugares')
                    ->sortable()
                    ->alignCenter(),
                Tables\Columns\TextColumn::make('popularidad_score')
                    ->label('Popularidad')
                    ->alignCenter()
                    ->sortable()
                    ->badge()
                    ->color(fn (int $state): string => match (true) {
                        $state >= 30 => 'warning',
                        $state >= 10 => 'success',
                        default      => 'gray',
                    }),
                Tables\Columns\IconColumn::make('activo')
                    ->label('Activa')
                    ->boolean()
                    ->alignCenter(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Actualizada')
                    ->dateTime('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('activo')
                    ->label('Estado')
                    ->trueLabel('Solo activas')
                    ->falseLabel('Solo inactivas'),
                Tables\Filters\SelectFilter::make('nivel')
                    ->label('Nivel')
                    ->options([
                        1 => 'Nivel 1 — Familia',
                        2 => 'Nivel 2 — Tipo',
                        3 => 'Nivel 3 — Especialización',
                    ]),
                Tables\Filters\SelectFilter::make('sector_id')
                    ->label('Sector')
                    ->relationship('sector', 'nombre'),
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
            ->defaultSort('nivel')
            ->defaultSort('nombre');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCategorias::route('/'),
            'create' => Pages\CreateCategoria::route('/create'),
            'edit' => Pages\EditCategoria::route('/{record}/edit'),
        ];
    }
}
