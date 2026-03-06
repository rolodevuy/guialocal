<?php

namespace App\Filament\Resources;

use App\Filament\Resources\NegocioResource\Pages;
use App\Models\Categoria;
use App\Models\Negocio;
use App\Models\Zona;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Illuminate\Support\Str;

class NegocioResource extends Resource
{
    protected static ?string $model = Negocio::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-storefront';

    protected static ?string $navigationLabel = 'Negocios';

    protected static ?string $modelLabel = 'Negocio';

    protected static ?string $pluralModelLabel = 'Negocios';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Tabs::make('Negocio')
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
                                Forms\Components\Textarea::make('descripcion')
                                    ->label('Descripción')
                                    ->rows(4)
                                    ->columnSpanFull(),
                                Forms\Components\Select::make('categoria_id')
                                    ->label('Categoría')
                                    ->options(Categoria::orderBy('nombre')->pluck('nombre', 'id'))
                                    ->required()
                                    ->searchable(),
                                Forms\Components\Select::make('zona_id')
                                    ->label('Zona')
                                    ->options(Zona::orderBy('nombre')->pluck('nombre', 'id'))
                                    ->required()
                                    ->searchable(),
                            ])
                            ->columns(2),

                        Forms\Components\Tabs\Tab::make('Contacto')
                            ->icon('heroicon-o-phone')
                            ->schema([
                                Forms\Components\TextInput::make('direccion')
                                    ->label('Dirección')
                                    ->maxLength(255)
                                    ->columnSpanFull(),
                                Forms\Components\TextInput::make('telefono')
                                    ->label('Teléfono')
                                    ->tel()
                                    ->maxLength(50),
                                Forms\Components\TextInput::make('email')
                                    ->label('Email')
                                    ->email()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('sitio_web')
                                    ->label('Sitio web')
                                    ->url()
                                    ->maxLength(255)
                                    ->prefix('https://')
                                    ->columnSpanFull(),
                            ])
                            ->columns(2),

                        Forms\Components\Tabs\Tab::make('Horarios')
                            ->icon('heroicon-o-clock')
                            ->schema([
                                Forms\Components\Repeater::make('horarios')
                                    ->label('')
                                    ->schema([
                                        Forms\Components\Select::make('dia_inicio')
                                            ->label('Desde')
                                            ->options([
                                                'Lunes'     => 'Lunes',
                                                'Martes'    => 'Martes',
                                                'Miércoles' => 'Miércoles',
                                                'Jueves'    => 'Jueves',
                                                'Viernes'   => 'Viernes',
                                                'Sábado'    => 'Sábado',
                                                'Domingo'   => 'Domingo',
                                            ])
                                            ->required()
                                            ->native(false),

                                        Forms\Components\Select::make('dia_fin')
                                            ->label('Hasta')
                                            ->options([
                                                'Lunes'     => 'Lunes',
                                                'Martes'    => 'Martes',
                                                'Miércoles' => 'Miércoles',
                                                'Jueves'    => 'Jueves',
                                                'Viernes'   => 'Viernes',
                                                'Sábado'    => 'Sábado',
                                                'Domingo'   => 'Domingo',
                                            ])
                                            ->placeholder('Solo ese día')
                                            ->native(false),

                                        Forms\Components\TimePicker::make('apertura')
                                            ->label('Apertura')
                                            ->seconds(false)
                                            ->visible(fn (Get $get) => !$get('cerrado')),

                                        Forms\Components\TimePicker::make('cierre')
                                            ->label('Cierre')
                                            ->seconds(false)
                                            ->visible(fn (Get $get) => !$get('cerrado')),

                                        Forms\Components\Toggle::make('cerrado')
                                            ->label('Cerrado ese día')
                                            ->live()
                                            ->columnSpan(2),
                                    ])
                                    ->columns(2)
                                    ->addButtonLabel('+ Agregar franja')
                                    ->reorderable(false)
                                    ->defaultItems(0)
                                    ->columnSpanFull(),
                            ]),

                        Forms\Components\Tabs\Tab::make('Ubicación')
                            ->icon('heroicon-o-map-pin')
                            ->schema([
                                // Mapa interactivo: click o drag para fijar posición
                                Forms\Components\View::make('filament.forms.components.map-picker')
                                    ->columnSpanFull()
                                    ->dehydrated(false),
                                Forms\Components\TextInput::make('lat')
                                    ->label('Latitud')
                                    ->numeric()
                                    ->step(0.0000001)
                                    ->readOnly()
                                    ->helperText('Se actualiza al hacer click en el mapa.'),
                                Forms\Components\TextInput::make('lng')
                                    ->label('Longitud')
                                    ->numeric()
                                    ->step(0.0000001)
                                    ->readOnly()
                                    ->helperText('Se actualiza al hacer click en el mapa.'),
                            ])
                            ->columns(2),

                        Forms\Components\Tabs\Tab::make('Configuración')
                            ->icon('heroicon-o-cog-6-tooth')
                            ->schema([
                                Forms\Components\Select::make('plan')
                                    ->label('Plan')
                                    ->options([
                                        'gratuito' => 'Gratuito',
                                        'basico'   => 'Básico',
                                        'premium'  => 'Premium',
                                    ])
                                    ->default('gratuito')
                                    ->required(),
                                Forms\Components\Toggle::make('featured')
                                    ->label('Destacado')
                                    ->helperText('Aparece en la home y resultados prioritarios.'),
                                Forms\Components\Toggle::make('activo')
                                    ->label('Activo')
                                    ->default(true)
                                    ->helperText('Solo los negocios activos son visibles en el sitio.'),
                            ]),

                        Forms\Components\Tabs\Tab::make('Imágenes')
                            ->icon('heroicon-o-photo')
                            ->schema([
                                SpatieMediaLibraryFileUpload::make('logo')
                                    ->label('Logo')
                                    ->collection('logo')
                                    ->image()
                                    ->imageEditor()
                                    ->maxSize(1024)
                                    ->helperText('Logo del negocio. Se muestra en la tarjeta de detalle. Máx 1MB.')
                                    ->columnSpanFull(),
                                SpatieMediaLibraryFileUpload::make('portada')
                                    ->label('Imagen de portada')
                                    ->collection('portada')
                                    ->image()
                                    ->imageEditor()
                                    ->maxSize(2048)
                                    ->helperText('Imagen principal del negocio. Máx 2MB.')
                                    ->columnSpanFull(),
                                SpatieMediaLibraryFileUpload::make('galeria')
                                    ->label('Galería')
                                    ->collection('galeria')
                                    ->image()
                                    ->multiple()
                                    ->reorderable()
                                    ->maxFiles(10)
                                    ->maxSize(2048)
                                    ->helperText('Hasta 10 imágenes. Podés reordenarlas arrastrando.')
                                    ->columnSpanFull(),
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                SpatieMediaLibraryImageColumn::make('portada')
                    ->label('')
                    ->collection('portada')
                    ->circular()
                    ->defaultImageUrl(fn () => 'https://ui-avatars.com/api/?name=N&background=f59e0b&color=fff&size=64'),
                Tables\Columns\TextColumn::make('nombre')
                    ->label('Negocio')
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
                Tables\Columns\TextColumn::make('plan')
                    ->label('Plan')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'premium'  => 'warning',
                        'basico'   => 'success',
                        'gratuito' => 'gray',
                        default    => 'gray',
                    }),
                Tables\Columns\IconColumn::make('featured')
                    ->label('Dest.')
                    ->boolean()
                    ->alignCenter()
                    ->toggleable(),
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
                Tables\Filters\SelectFilter::make('plan')
                    ->label('Plan')
                    ->options([
                        'gratuito' => 'Gratuito',
                        'basico'   => 'Básico',
                        'premium'  => 'Premium',
                    ]),
                Tables\Filters\TernaryFilter::make('featured')
                    ->label('Destacado'),
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

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListNegocios::route('/'),
            'create' => Pages\CreateNegocio::route('/create'),
            'edit'   => Pages\EditNegocio::route('/{record}/edit'),
        ];
    }
}
