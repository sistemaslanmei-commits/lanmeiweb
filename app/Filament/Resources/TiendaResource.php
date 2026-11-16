<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TiendaResource\Pages;
use App\Models\Tienda;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class TiendaResource extends Resource
{
    protected static ?string $model = Tienda::class;
    protected static ?string $navigationIcon = 'heroicon-o-map-pin';
    protected static ?string $navigationGroup = 'Configuración Institucional';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('nombre_sucursal')->required(),
            Forms\Components\TextInput::make('ciudad')->default('Lima')->required(),
            Forms\Components\TextInput::make('direccion')->required()->columnSpanFull(),
            Forms\Components\TextInput::make('telefono')->tel(),
            Forms\Components\TextInput::make('whatsapp')->tel(),
            Forms\Components\TextInput::make('horario_atencion')->placeholder('Lun - Sáb: 9:00 AM - 7:00 PM'),
            Forms\Components\Textarea::make('mapa_embed_url')->label('URL Embed Google Maps (src del iframe)')->columnSpanFull(),
            Forms\Components\Toggle::make('es_principal')->label('Sede Principal'),
            Forms\Components\Toggle::make('is_active')->label('Activa')->default(true),
        ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nombre_sucursal')->searchable(),
                Tables\Columns\TextColumn::make('ciudad')->sortable(),
                Tables\Columns\TextColumn::make('direccion')->limit(40),
                Tables\Columns\TextColumn::make('whatsapp'),
                Tables\Columns\IconColumn::make('es_principal')->label('Principal')->boolean(),
                Tables\Columns\ToggleColumn::make('is_active')->label('Activa'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTiendas::route('/'),
            'create' => Pages\CreateTienda::route('/create'),
            'edit' => Pages\EditTienda::route('/{record}/edit'),
        ];
    }
}