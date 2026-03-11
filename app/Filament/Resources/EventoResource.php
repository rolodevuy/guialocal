<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EventoResource\Pages;
use App\Models\Evento;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Illuminate\Support\Str;

class EventoResource extends Resource
{
    protected static ?string $model = Evento::class;

    protected static ?string $navigationIcon  = 'heroicon-o-calendar-days';
    protected static ?string $navigationLabel = 'Eventos';
    protected static ?string $modelLabel      = 'Evento';
    protected static ?string $pluralModelLabel = 'Eventos';
    protected static ?string $navigationGroup = 'Contenido';
    protected static ?int    $navigationSort  = 7;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Tabs::make()
                ->columnSpanFull()
                ->tabs([

                    Forms\Components\Tabs\Tab::make('Contenido')
                        ->icon('heroicon-o-document-text')
                        ->schema([
                            Forms\Components\TextInput::make('titulo')
                                ->label('Título')
                                ->required()
                                ->live(onBlur: true)
                                ->afterStateUpdated(fn ($state, $set) => $set('slug', Str::slug($state)))
                                ->columnSpanFull(),

                            Forms\Components\TextInput::make('slug')
                                ->label('Slug')
                                ->required()
                                ->unique(ignoreRecord: true)
                                ->prefix('/eventos/')
                                ->columnSpanFull(),

                            Forms\Components\Textarea::make('descripcion')
                                ->label('Descripción')
                                ->helperText('Breve descripción del evento (máx. 500 caracteres).')
                                ->rows(4)
                                ->maxLength(500)
                                ->columnSpanFull(),
                        ]),

                    Forms\Components\Tabs\Tab::make('Fecha y hora')
                        ->icon('heroicon-o-clock')
                        ->schema([
                            Forms\Components\DatePicker::make('fecha_inicio')
                                ->label('Fecha de inicio')
                                ->required()
                                ->native(false),

                            Forms\Components\DatePicker::make('fecha_fin')
                                ->label('Fecha de fin')
                                ->helperText('Dejá vacío si es un solo día.')
                                ->native(false)
                                ->after('fecha_inicio')
                                ->nullable(),

                            Forms\Components\TimePicker::make('hora_inicio')
                                ->label('Hora de inicio')
                                ->seconds(false)
                                ->native(false)
                                ->nullable(),

                            Forms\Components\TimePicker::make('hora_fin')
                                ->label('Hora de fin')
                                ->seconds(false)
                                ->native(false)
                                ->nullable(),
                        ])
                        ->columns(2),

                    Forms\Components\Tabs\Tab::make('Imagen')
                        ->icon('heroicon-o-photo')
                        ->schema([
                            SpatieMediaLibraryFileUpload::make('portada')
                                ->collection('portada')
                                ->image()
                                ->imageEditor()
                                ->maxSize(3072)
                                ->helperText('Recomendado: 1200x630px. Máx 3MB. Se optimiza automáticamente.')
                                ->columnSpanFull(),
                        ]),

                    Forms\Components\Tabs\Tab::make('Relaciones')
                        ->icon('heroicon-o-building-storefront')
                        ->schema([
                            Forms\Components\Select::make('lugar_id')
                                ->label('Negocio / Lugar donde se realiza')
                                ->relationship('lugar', 'nombre')
                                ->searchable()
                                ->preload()
                                ->placeholder('Sin negocio asociado')
                                ->nullable(),
                        ]),

                    Forms\Components\Tabs\Tab::make('Configuración')
                        ->icon('heroicon-o-cog-6-tooth')
                        ->schema([
                            Forms\Components\Toggle::make('publicado')
                                ->label('Publicado')
                                ->helperText('Visible en el sitio público'),
                        ]),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                SpatieMediaLibraryImageColumn::make('portada')
                    ->collection('portada')
                    ->circular()
                    ->defaultImageUrl(fn ($r) => 'https://ui-avatars.com/api/?name=' . urlencode($r->titulo) . '&background=f59e0b&color=fff')
                    ->label(''),

                Tables\Columns\TextColumn::make('titulo')
                    ->label('Título')
                    ->searchable()
                    ->sortable()
                    ->limit(50),

                Tables\Columns\TextColumn::make('fecha_inicio')
                    ->label('Inicio')
                    ->date('d/m/Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('fecha_fin')
                    ->label('Fin')
                    ->date('d/m/Y')
                    ->placeholder('—')
                    ->sortable(),

                Tables\Columns\TextColumn::make('lugar.nombre')
                    ->label('Lugar')
                    ->placeholder('—')
                    ->badge()
                    ->color('gray'),

                Tables\Columns\IconColumn::make('publicado')
                    ->label('Publicado')
                    ->boolean()
                    ->sortable(),
            ])
            ->defaultSort('fecha_inicio', 'asc')
            ->filters([
                Tables\Filters\TernaryFilter::make('publicado')->label('Publicado'),
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
            'index'  => Pages\ListEventos::route('/'),
            'create' => Pages\CreateEvento::route('/create'),
            'edit'   => Pages\EditEvento::route('/{record}/edit'),
        ];
    }
}
