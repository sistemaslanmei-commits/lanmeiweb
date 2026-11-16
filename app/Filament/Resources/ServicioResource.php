<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ServicioResource\Pages;
use App\Models\Servicio;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class ServicioResource extends Resource
{
    protected static ?string $model = Servicio::class;
    protected static ?string $navigationIcon = 'heroicon-o-wrench-screwdriver';
    protected static ?string $navigationGroup = 'Configuración Institucional';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('titulo')
                ->required()
                ->live(onBlur: true)
                ->afterStateUpdated(fn ($state, Forms\Set $set) => $set('slug', Str::slug($state))),
            Forms\Components\TextInput::make('slug')->required()->unique(ignoreRecord: true),
            Forms\Components\TextInput::make('icono')->label('Clase de Icono (ej. bi bi-laptop)'),
            Forms\Components\TextInput::make('orden')->numeric()->default(0),
            Forms\Components\Textarea::make('descripcion_corta')->columnSpanFull(),
            Forms\Components\RichEditor::make('contenido')->columnSpanFull(),
            Forms\Components\FileUpload::make('imagen_principal')->image()->directory('servicios'),
            Forms\Components\FileUpload::make('galeria')->image()->multiple()->reorderable()->directory('servicios/galeria'),
            Forms\Components\Toggle::make('is_active')->label('Activo')->default(true),
        ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('imagen_principal')->label('Imagen'),
                Tables\Columns\TextColumn::make('titulo')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('orden')->sortable(),
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
            'index' => Pages\ListServicios::route('/'),
            'create' => Pages\CreateServicio::route('/create'),
            'edit' => Pages\EditServicio::route('/{record}/edit'),
        ];
    }
}