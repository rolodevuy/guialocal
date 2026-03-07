<?php

namespace App\Filament\Resources;

use App\Filament\Resources\GuiaResource\Pages;
use App\Models\Guia;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Illuminate\Support\Str;

class GuiaResource extends Resource
{
    protected static ?string $model = Guia::class;

    protected static ?string $navigationIcon   = 'heroicon-o-book-open';
    protected static ?string $navigationLabel  = 'Guías';
    protected static ?string $modelLabel       = 'Guía';
    protected static ?string $pluralModelLabel = 'Guías';
    protected static ?int    $navigationSort   = 6;

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
                                ->prefix('/guias/')
                                ->columnSpanFull(),

                            Forms\Components\Textarea::make('intro')
                                ->label('Introducción')
                                ->helperText('Breve descripción visible en el listado y como meta description.')
                                ->rows(3)
                                ->maxLength(400)
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

                    Forms\Components\Tabs\Tab::make('Negocios')
                        ->icon('heroicon-o-building-storefront')
                        ->schema([
                            Forms\Components\Select::make('lugares')
                                ->label('Negocios incluidos en esta guía')
                                ->relationship('lugares', 'nombre')
                                ->multiple()
                                ->searchable()
                                ->preload()
                                ->columnSpanFull()
                                ->helperText('Seleccioná los negocios que aparecerán en esta guía.'),

                            Forms\Components\Select::make('categoria_id')
                                ->label('Categoría principal')
                                ->relationship('categoria', 'nombre')
                                ->searchable()
                                ->preload()
                                ->placeholder('Sin categoría')
                                ->nullable(),
                        ]),

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

                Tables\Columns\TextColumn::make('lugares_count')
                    ->label('Negocios')
                    ->counts('lugares')
                    ->badge()
                    ->color('gray'),

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
            'index'  => Pages\ListGuias::route('/'),
            'create' => Pages\CreateGuia::route('/create'),
            'edit'   => Pages\EditGuia::route('/{record}/edit'),
        ];
    }
}
