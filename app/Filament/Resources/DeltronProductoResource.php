<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DeltronProductoResource\Pages;
use App\Filament\Resources\DeltronProductoResource\RelationManagers;
use App\Models\DeltronProducto;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class DeltronProductoResource extends Resource
{
    protected static ?string $model = DeltronProducto::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
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
            'index' => Pages\ListDeltronProductos::route('/'),
            'create' => Pages\CreateDeltronProducto::route('/create'),
            'view' => Pages\ViewDeltronProducto::route('/{record}'),
            'edit' => Pages\EditDeltronProducto::route('/{record}/edit'),
        ];
    }
}
