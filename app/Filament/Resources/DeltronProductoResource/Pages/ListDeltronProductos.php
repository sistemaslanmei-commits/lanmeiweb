<?php

namespace App\Filament\Resources\DeltronProductoResource\Pages;

use App\Filament\Resources\DeltronProductoResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListDeltronProductos extends ListRecords
{
    protected static string $resource = DeltronProductoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
