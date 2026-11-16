<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EmpresaResource\Pages;
use App\Models\Empresa;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class EmpresaResource extends Resource
{
    protected static ?string $model = Empresa::class;
    protected static ?string $navigationIcon = 'heroicon-o-building-office-2';
    protected static ?string $navigationGroup = 'Configuración Institucional';
    protected static ?string $label = 'Empresa';
    protected static ?string $pluralLabel = 'Configuración de Empresa';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Identidad y Contacto')->schema([
                Forms\Components\TextInput::make('nombre')->required()->default('LANMEI'),
                Forms\Components\FileUpload::make('logo')->image()->directory('empresa'),
                Forms\Components\FileUpload::make('favicon')->image()->directory('empresa'),
                Forms\Components\TextInput::make('telefono_principal')->tel(),
                Forms\Components\TextInput::make('whatsapp_principal')->label('WhatsApp (ej. 51905467384)'),
                Forms\Components\TextInput::make('email_contacto')->email(),
                Forms\Components\Textarea::make('direccion_fiscal')->columnSpanFull(),
            ])->columns(2),

            Forms\Components\Section::make('Configuración Comercial Deltron')->schema([
                Forms\Components\TextInput::make('porcentaje_deltron')
                    ->label('Porcentaje de Ganancia Deltron (%)')
                    ->numeric()
                    ->suffix('%')
                    ->default(25.00)
                    ->required()
                    ->helperText('Al guardar un nuevo porcentaje, todos los precios del catálogo Deltron se recalcularán automáticamente.'),
            ]),

            Forms\Components\Section::make('Redes Sociales y Banners Dinámicos')->schema([
                Forms\Components\KeyValue::make('redes_sociales')
                    ->keyLabel('Red Social (facebook, instagram, tiktok, linkedin)')
                    ->valueLabel('URL completa'),
                Forms\Components\KeyValue::make('banners_por_pagina')
                    ->keyLabel('Página (inicio, nosotros, servicios, catalogo)')
                    ->valueLabel('Ruta o texto del banner'),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('logo'),
                Tables\Columns\TextColumn::make('nombre')->weight('bold'),
                Tables\Columns\TextColumn::make('whatsapp_principal')->label('WhatsApp'),
                Tables\Columns\TextColumn::make('porcentaje_deltron')->label('Margen Deltron')->suffix('%'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEmpresas::route('/'),
            'create' => Pages\CreateEmpresa::route('/create'),
            'edit' => Pages\EditEmpresa::route('/{record}/edit'),
        ];
    }
}