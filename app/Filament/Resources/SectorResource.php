<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SectorResource\Pages;
use App\Models\Sector;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class SectorResource extends Resource
{
    protected static ?string $model = Sector::class;

    protected static ?string $navigationIcon = 'heroicon-o-squares-2x2';

    protected static ?string $navigationLabel = 'Sectores';

    protected static ?string $modelLabel = 'Sector';

    protected static ?string $pluralModelLabel = 'Sectores';

    protected static ?int $navigationSort = 0;

    public static function form(Form $form): Form
    {
        return $form
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

                Forms\Components\Textarea::make('descripcion')
                    ->label('Descripción')
                    ->rows(3)
                    ->columnSpanFull(),

                Forms\Components\Select::make('icono')
                    ->label('Ícono')
                    ->searchable()
                    ->placeholder('Buscá un ícono...')
                    ->options([
                        'Comercio' => [
                            'shopping-bag'    => 'shopping-bag — Bolsa de compras',
                            'shopping-cart'   => 'shopping-cart — Carrito de compras',
                            'building-storefront' => 'building-storefront — Tienda',
                            'banknotes'       => 'banknotes — Dinero / Finanzas',
                            'briefcase'       => 'briefcase — Negocios / Profesional',
                            'tag'             => 'tag — Etiqueta / Ofertas',
                        ],
                        'Gastronomía y Ocio' => [
                            'utensils'        => 'utensils — Restaurantes / Gastronomía',
                            'coffee'          => 'coffee — Cafés y Bares',
                            'cake'            => 'cake — Panaderías / Confiterías',
                            'wine'            => 'wine — Vinotecas / Bares',
                            'musical-note'    => 'musical-note — Música / Espectáculos',
                            'bolt'            => 'bolt — Deporte / Gimnasio',
                            'trophy'          => 'trophy — Deportes / Clubes',
                            'sparkles'        => 'sparkles — Estética / Belleza',
                        ],
                        'Turismo y Alojamiento' => [
                            'building-office'  => 'building-office — Hotel / Alojamiento',
                            'globe-alt'        => 'globe-alt — Turismo / Agencias',
                            'map-pin'          => 'map-pin — Puntos turísticos',
                            'sun'              => 'sun — Playa / Sol',
                            'camera'           => 'camera — Fotografía / Turismo',
                            'map'              => 'map — Mapa / Guía',
                        ],
                        'General' => [
                            'squares-2x2'     => 'squares-2x2 — Cuadrícula / General',
                            'star'            => 'star — Estrella / Destacado',
                            'heart'           => 'heart — Favorito / Salud',
                            'home'            => 'home — Hogar',
                            'light-bulb'      => 'light-bulb — Ideas / Servicios',
                        ],
                    ]),

                Forms\Components\Select::make('color_preset')
                    ->label('Color')
                    ->options([
                        'amber'   => 'Amber (dorado)',
                        'rose'    => 'Rose (rosado)',
                        'sky'     => 'Sky (celeste)',
                        'emerald' => 'Emerald (verde)',
                        'violet'  => 'Violet (violeta)',
                        'orange'  => 'Orange (naranja)',
                    ])
                    ->helperText('Color base para diferenciar visualmente el sector.')
                    ->afterStateHydrated(function (Forms\Components\Select $component, ?Sector $record) {
                        if (! $record?->color_classes) {
                            return;
                        }
                        // Inferir preset desde la clase bg (ej: "bg-amber-100" → "amber")
                        $bg = $record->color_classes['bg'] ?? '';
                        if (preg_match('/bg-(\w+)-/', $bg, $matches)) {
                            $component->state($matches[1]);
                        }
                    })
                    ->afterStateUpdated(function (Set $set, ?string $state) {
                        if ($state) {
                            $set('color_classes', self::buildColorClasses($state));
                        }
                    })
                    ->reactive(),

                Forms\Components\Hidden::make('color_classes'),

                Forms\Components\TextInput::make('orden')
                    ->label('Orden')
                    ->numeric()
                    ->default(0)
                    ->minValue(0),

                Forms\Components\Toggle::make('activo')
                    ->label('Activo')
                    ->default(true),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nombre')
                    ->label('Nombre')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('icono')
                    ->label('Ícono')
                    ->badge()
                    ->color('info'),

                Tables\Columns\TextColumn::make('categorias_count')
                    ->label('Categorías')
                    ->counts('categorias')
                    ->sortable()
                    ->alignCenter(),

                Tables\Columns\TextColumn::make('orden')
                    ->label('Orden')
                    ->sortable()
                    ->alignCenter(),

                Tables\Columns\IconColumn::make('activo')
                    ->label('Activo')
                    ->boolean()
                    ->alignCenter(),
            ])
            ->defaultSort('orden')
            ->actions([
                Tables\Actions\EditAction::make(),
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
            'index'  => Pages\ListSectores::route('/'),
            'create' => Pages\CreateSector::route('/create'),
            'edit'   => Pages\EditSector::route('/{record}/edit'),
        ];
    }

    /**
     * Genera el array de clases Tailwind a partir de un color base.
     */
    public static function buildColorClasses(string $color): array
    {
        return [
            'bg'         => "bg-{$color}-100",
            'bg_light'   => "bg-{$color}-50",
            'text'       => "text-{$color}-600",
            'text_hover' => "text-{$color}-700",
            'border'     => "border-{$color}-200",
            'icon'       => "text-{$color}-500",
        ];
    }
}
