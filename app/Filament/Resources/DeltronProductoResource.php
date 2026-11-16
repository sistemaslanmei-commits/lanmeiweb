<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DeltronProductoResource\Pages;
use App\Models\DeltronProducto;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class DeltronProductoResource extends Resource
{
    protected static ?string $model = DeltronProducto::class;
    protected static ?string $navigationIcon = 'heroicon-o-server-stack';
    protected static ?string $navigationGroup = 'Catálogo';
    protected static ?string $label = 'Producto Deltron';
    protected static ?string $pluralLabel = 'Catálogo Deltron';

    public static function canCreate(): bool
    {
        return false;
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('sku')->disabled(),
            Forms\Components\TextInput::make('nombre')->disabled(),
            Forms\Components\TextInput::make('marca')->disabled(),
            Forms\Components\TextInput::make('precio_costo_usd')->label('Costo USD')->prefix('$')->disabled(),
            Forms\Components\TextInput::make('precio_costo_soles')->label('Costo S/ (+IGV)')->prefix('S/')->disabled(),
            Forms\Components\TextInput::make('precio')->label('Precio Venta Público')->prefix('S/')->disabled(),
            Forms\Components\TextInput::make('stock')->disabled(),
            Forms\Components\Toggle::make('is_active')->label('Visible en tienda'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('imagen')->label('Foto'),
                Tables\Columns\TextColumn::make('sku')->searchable()->copyable(),
                Tables\Columns\TextColumn::make('nombre')->searchable()->limit(45),
                Tables\Columns\TextColumn::make('categoria.nombreCategoria')->label('Categoría')->sortable(),
                Tables\Columns\TextColumn::make('precio_costo_soles')->label('Costo S/')->money('PEN')->sortable(),
                Tables\Columns\TextColumn::make('precio')->label('Venta S/')->money('PEN')->weight('bold')->color('success')->sortable(),
                Tables\Columns\TextColumn::make('stock')->badge()->color(fn (int $state): string => $state > 0 ? 'info' : 'danger')->sortable(),
                Tables\Columns\ToggleColumn::make('is_active')->label('Activo'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('categoria_id')
                    ->label('Categoría')
                    ->relationship('categoria', 'nombreCategoria')
                    ->searchable(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDeltronProductos::route('/'),
            'view' => Pages\ViewDeltronProducto::route('/{record}'),
        ];
    }
}