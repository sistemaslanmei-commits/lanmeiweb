<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductoResource\Pages;
use App\Models\Producto;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class ProductoResource extends Resource
{
    protected static ?string $model = Producto::class;
    protected static ?string $navigationIcon = 'heroicon-o-cube';
    protected static ?string $navigationGroup = 'Catálogo';
    protected static ?string $label = 'Producto Propio';
    protected static ?string $pluralLabel = 'Productos Propios';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Información General')->schema([
                Forms\Components\TextInput::make('nombre')
                    ->required()
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn ($state, Forms\Set $set) => $set('slug', Str::slug($state))),
                Forms\Components\TextInput::make('slug')->required()->unique(ignoreRecord: true),
                Forms\Components\TextInput::make('sku')->unique(ignoreRecord: true),
                Forms\Components\Select::make('categoria_id')
                    ->relationship('categoria', 'nombreCategoria')
                    ->searchable()
                    ->preload(),
                Forms\Components\Select::make('marca_id')
                    ->relationship('marca', 'nombre')
                    ->searchable()
                    ->preload(),
                Forms\Components\TextInput::make('precio')->numeric()->prefix('S/')->required(),
                Forms\Components\TextInput::make('stock')->numeric()->default(0),
            ])->columns(2),

            Forms\Components\Section::make('Multimedia y Ficha Técnica PDF')->schema([
                Forms\Components\FileUpload::make('imagen')
                    ->label('Imagen Principal')
                    ->image()
                    ->directory('productos'),
                Forms\Components\FileUpload::make('ficha_tecnica_pdf')
                    ->label('Ficha Técnica (PDF)')
                    ->directory('fichas_tecnicas')
                    ->acceptedFileTypes(['application/pdf'])
                    ->maxSize(10240),
                Forms\Components\FileUpload::make('galeria')
                    ->label('Galería de Imágenes')
                    ->image()
                    ->multiple()
                    ->reorderable()
                    ->directory('productos/galeria')
                    ->columnSpanFull(),
            ])->columns(2),

            Forms\Components\Section::make('Detalles y Especificaciones')->schema([
                Forms\Components\Textarea::make('descripcion_corta')->rows(2),
                Forms\Components\RichEditor::make('descripcion'),
                Forms\Components\KeyValue::make('especificaciones_tecnicas')
                    ->keyLabel('Característica (ej. Procesador)')
                    ->valueLabel('Valor (ej. Intel Core i7)'),
                Forms\Components\Toggle::make('is_featured')->label('Producto Destacado'),
                Forms\Components\Toggle::make('is_active')->label('Activo')->default(true),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('imagen'),
                Tables\Columns\TextColumn::make('sku')->searchable(),
                Tables\Columns\TextColumn::make('nombre')->searchable()->limit(40),
                Tables\Columns\TextColumn::make('categoria.nombreCategoria')->label('Categoría'),
                Tables\Columns\TextColumn::make('precio')->money('PEN')->sortable(),
                Tables\Columns\TextColumn::make('stock')->sortable(),
                Tables\Columns\ToggleColumn::make('is_active')->label('Activo'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProductos::route('/'),
            'create' => Pages\CreateProducto::route('/create'),
            'edit' => Pages\EditProducto::route('/{record}/edit'),
        ];
    }
}