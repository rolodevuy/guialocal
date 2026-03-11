<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FichaResource\Pages;
use App\Models\Ficha;
use App\Models\Lugar;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;

class FichaResource extends Resource
{
    protected static ?string $model = Ficha::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-storefront';

    protected static ?string $navigationLabel = 'Fichas';

    protected static ?string $modelLabel = 'Ficha';

    protected static ?string $pluralModelLabel = 'Fichas';

    protected static ?string $navigationGroup = 'Directorio';

    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Tabs::make('Ficha')
                    ->tabs([
                        Forms\Components\Tabs\Tab::make('Lugar y descripción')
                            ->icon('heroicon-o-information-circle')
                            ->schema([
                                Forms\Components\Select::make('lugar_id')
                                    ->label('Lugar')
                                    ->options(Lugar::orderBy('nombre')->pluck('nombre', 'id'))
                                    ->required()
                                    ->searchable()
                                    ->columnSpanFull(),
                                Forms\Components\Textarea::make('descripcion')
                                    ->label('Descripción')
                                    ->rows(4)
                                    ->columnSpanFull(),
                            ]),

                        Forms\Components\Tabs\Tab::make('Contacto')
                            ->icon('heroicon-o-phone')
                            ->schema([
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
                                    ->maxLength(255)
                                    ->prefix('https://')
                                    ->placeholder('www.ejemplo.com.uy')
                                    ->dehydrateStateUsing(function (?string $state): ?string {
                                        if (! $state) return null;
                                        $state = preg_replace('#^https?://#i', '', $state);
                                        return $state;
                                    })
                                    ->columnSpanFull(),

                                Forms\Components\Repeater::make('redes_sociales')
                                    ->label('Redes sociales')
                                    ->schema([
                                        Forms\Components\Select::make('red')
                                            ->label('Red')
                                            ->options([
                                                'instagram' => 'Instagram',
                                                'facebook'  => 'Facebook',
                                                'tiktok'    => 'TikTok',
                                                'youtube'   => 'YouTube',
                                                'twitter'   => 'X / Twitter',
                                                'linkedin'  => 'LinkedIn',
                                                'whatsapp'  => 'WhatsApp',
                                            ])
                                            ->required()
                                            ->native(false),
                                        Forms\Components\TextInput::make('url')
                                            ->label('URL del perfil')
                                            ->url()
                                            ->required()
                                            ->placeholder('https://instagram.com/mi_negocio'),
                                    ])
                                    ->columns(2)
                                    ->addActionLabel('+ Agregar red social')
                                    ->reorderable(false)
                                    ->defaultItems(0)
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
                                    ->addActionLabel('+ Agregar franja')
                                    ->reorderable(false)
                                    ->defaultItems(0)
                                    ->columnSpanFull(),

                                Forms\Components\Section::make('Fechas especiales')
                                    ->description('Feriados, vacaciones u horarios puntuales.')
                                    ->icon('heroicon-o-calendar-days')
                                    ->collapsible()
                                    ->collapsed()
                                    ->schema([
                                        Forms\Components\Repeater::make('horarios_especiales')
                                            ->label('')
                                            ->schema([
                                                Forms\Components\TextInput::make('nombre')
                                                    ->label('Descripción')
                                                    ->placeholder('Ej: Navidad, Feriado patrio...')
                                                    ->required()
                                                    ->columnSpan(2),
                                                Forms\Components\DatePicker::make('fecha')
                                                    ->label('Fecha')
                                                    ->required()
                                                    ->native(false)
                                                    ->displayFormat('d/m/Y'),
                                                Forms\Components\Toggle::make('se_repite')
                                                    ->label('Se repite anualmente')
                                                    ->default(false),
                                                Forms\Components\Toggle::make('activo')
                                                    ->label('Activo')
                                                    ->default(true),
                                                Forms\Components\Toggle::make('cerrado')
                                                    ->label('Cerrado ese día')
                                                    ->default(false)
                                                    ->live(),
                                                Forms\Components\TimePicker::make('apertura')
                                                    ->label('Apertura')
                                                    ->seconds(false)
                                                    ->visible(fn (Get $get) => !$get('cerrado')),
                                                Forms\Components\TimePicker::make('cierre')
                                                    ->label('Cierre')
                                                    ->seconds(false)
                                                    ->visible(fn (Get $get) => !$get('cerrado')),
                                            ])
                                            ->columns(2)
                                            ->addActionLabel('+ Agregar fecha especial')
                                            ->reorderable(false)
                                            ->defaultItems(0)
                                            ->columnSpanFull(),
                                    ]),
                            ]),

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
                                Forms\Components\Select::make('estado')
                                    ->label('Estado')
                                    ->options(Ficha::ESTADOS)
                                    ->default('activa')
                                    ->required()
                                    ->native(false)
                                    ->helperText('pendiente = esperando revisión admin | activa = visible | rechazada | suspendida')
                                    ->columnSpanFull(),
                                Forms\Components\Toggle::make('featured')
                                    ->label('Destacado')
                                    ->helperText('Aparece en la home y resultados prioritarios.'),
                                Forms\Components\Toggle::make('activo')
                                    ->label('Activo')
                                    ->default(true)
                                    ->helperText('Solo las fichas activas son visibles en el sitio.'),

                                Forms\Components\Select::make('user_id')
                                    ->label('Propietario (acceso al panel)')
                                    ->options(User::orderBy('name')->pluck('name', 'id'))
                                    ->searchable()
                                    ->nullable()
                                    ->placeholder('Sin propietario asignado')
                                    ->helperText('Usuario que puede gestionar esta ficha desde /panel')
                                    ->columnSpanFull(),
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
                                    ->helperText('Recomendado: 300x300px. Máx 1MB. Se optimiza automáticamente.')
                                    ->columnSpanFull(),
                                SpatieMediaLibraryFileUpload::make('portada')
                                    ->label('Imagen de portada')
                                    ->collection('portada')
                                    ->image()
                                    ->imageEditor()
                                    ->maxSize(2048)
                                    ->helperText('Recomendado: 1200x400px. Máx 2MB. Se optimiza automáticamente.')
                                    ->columnSpanFull(),
                                SpatieMediaLibraryFileUpload::make('galeria')
                                    ->label('Galería')
                                    ->collection('galeria')
                                    ->image()
                                    ->multiple()
                                    ->reorderable()
                                    ->maxFiles(10)
                                    ->maxSize(2048)
                                    ->helperText('Hasta 10 imágenes (máx 2MB c/u). Se optimizan automáticamente.')
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
                    ->defaultImageUrl(fn () => 'https://ui-avatars.com/api/?name=F&background=f59e0b&color=fff&size=64'),
                Tables\Columns\TextColumn::make('lugar.nombre')
                    ->label('Lugar')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('lugar.categoria.nombre')
                    ->label('Categoría')
                    ->badge()
                    ->sortable(),
                Tables\Columns\TextColumn::make('lugar.zona.nombre')
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
                Tables\Columns\TextColumn::make('featured_score')
                    ->label('Score')
                    ->alignCenter()
                    ->sortable()
                    ->badge()
                    ->color(fn (int $state): string => match (true) {
                        $state >= 80 => 'warning',
                        $state >= 20 => 'success',
                        default      => 'gray',
                    }),
                Tables\Columns\TextColumn::make('estado')
                    ->label('Estado')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'activa'     => 'success',
                        'pendiente'  => 'warning',
                        'rechazada'  => 'danger',
                        'suspendida' => 'gray',
                        default      => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => Ficha::ESTADOS[$state] ?? $state),
                Tables\Columns\IconColumn::make('activo')
                    ->label('Activo')
                    ->boolean()
                    ->alignCenter()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('visitas')
                    ->label('Visitas')
                    ->alignCenter()
                    ->sortable()
                    ->numeric()
                    ->badge()
                    ->color(fn (int $state): string => match (true) {
                        $state >= 500 => 'success',
                        $state >= 100 => 'info',
                        $state >= 10  => 'gray',
                        default       => 'gray',
                    })
                    ->toggleable(),
                Tables\Columns\TextColumn::make('propietario.name')
                    ->label('Propietario')
                    ->placeholder('—')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Actualizado')
                    ->dateTime('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('estado')
                    ->label('Estado')
                    ->options(Ficha::ESTADOS),
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
                    Tables\Actions\BulkAction::make('activar')
                        ->label('Activar y publicar')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->modalDescription('Se pondrán en estado "activa" y visibles al público.')
                        ->action(function ($records) {
                            $lugarIds = $records->pluck('lugar_id')->filter()->unique();
                            $records->each->update(['activo' => true, 'estado' => 'activa']);
                            Lugar::whereIn('id', $lugarIds)->update(['activo' => true]);
                        })
                        ->deselectRecordsAfterCompletion(),
                    Tables\Actions\BulkAction::make('desactivar')
                        ->label('Desactivar seleccionadas')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->action(fn ($records) => $records->each->update(['activo' => false]))
                        ->deselectRecordsAfterCompletion(),
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('updated_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListFichas::route('/'),
            'create' => Pages\CreateFicha::route('/create'),
            'edit'   => Pages\EditFicha::route('/{record}/edit'),
        ];
    }
}
