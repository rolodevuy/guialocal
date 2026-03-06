<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ArticuloResource\Pages;
use App\Models\Articulo;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Illuminate\Support\Str;

class ArticuloResource extends Resource
{
    protected static ?string $model = Articulo::class;

    protected static ?string $navigationIcon  = 'heroicon-o-newspaper';
    protected static ?string $navigationLabel = 'Artículos';
    protected static ?string $modelLabel      = 'Artículo';
    protected static ?string $pluralModelLabel = 'Artículos';
    protected static ?int    $navigationSort  = 5;

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
                                ->prefix('/articulos/')
                                ->columnSpanFull(),

                            Forms\Components\Textarea::make('extracto')
                                ->label('Extracto')
                                ->helperText('Breve descripción para listados y meta description (máx. 300 caracteres).')
                                ->rows(3)
                                ->maxLength(300)
                                ->columnSpanFull(),

                            Forms\Components\RichEditor::make('cuerpo')
                                ->label('Contenido')
                                ->toolbarButtons([
                                    'bold', 'italic', 'underline', 'strike',
                                    'h2', 'h3',
                                    'bulletList', 'orderedList',
                                    'link',
                                    'blockquote',
                                    'redo', 'undo',
                                ])
                                ->columnSpanFull(),
                        ]),

                    Forms\Components\Tabs\Tab::make('Imagen')
                        ->icon('heroicon-o-photo')
                        ->schema([
                            SpatieMediaLibraryFileUpload::make('portada')
                                ->collection('portada')
                                ->image()
                                ->imageEditor()
                                ->maxSize(3072)
                                ->columnSpanFull(),
                        ]),

                    Forms\Components\Tabs\Tab::make('Relaciones')
                        ->icon('heroicon-o-link')
                        ->schema([
                            Forms\Components\Select::make('categoria_id')
                                ->label('Categoría relacionada')
                                ->relationship('categoria', 'nombre')
                                ->searchable()
                                ->preload()
                                ->placeholder('Sin categoría')
                                ->nullable(),

                            Forms\Components\Select::make('negocio_id')
                                ->label('Negocio relacionado')
                                ->relationship('negocio', 'nombre')
                                ->searchable()
                                ->placeholder('Sin negocio')
                                ->nullable(),
                        ])
                        ->columns(2),

                    Forms\Components\Tabs\Tab::make('Configuración')
                        ->icon('heroicon-o-cog-6-tooth')
                        ->schema([
                            Forms\Components\Toggle::make('publicado')
                                ->label('Publicado')
                                ->helperText('Visible en el sitio público'),

                            Forms\Components\DateTimePicker::make('publicado_en')
                                ->label('Fecha de publicación')
                                ->native(false)
                                ->nullable(),
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
                    ->limit(55),

                Tables\Columns\TextColumn::make('categoria.nombre')
                    ->label('Categoría')
                    ->badge()
                    ->color('warning')
                    ->placeholder('—'),

                Tables\Columns\IconColumn::make('publicado')
                    ->label('Publicado')
                    ->boolean()
                    ->sortable(),

                Tables\Columns\TextColumn::make('publicado_en')
                    ->label('Fecha')
                    ->date('d/m/Y')
                    ->sortable()
                    ->placeholder('—'),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\TernaryFilter::make('publicado')->label('Publicado'),
                Tables\Filters\SelectFilter::make('categoria')
                    ->relationship('categoria', 'nombre')
                    ->label('Categoría'),
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
            'index'  => Pages\ListArticulos::route('/'),
            'create' => Pages\CreateArticulo::route('/create'),
            'edit'   => Pages\EditArticulo::route('/{record}/edit'),
        ];
    }
}
