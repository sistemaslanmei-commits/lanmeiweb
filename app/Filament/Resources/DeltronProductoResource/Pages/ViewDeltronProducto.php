<?php

namespace App\Filament\Resources\DeltronProductoResource\Pages;

use App\Filament\Resources\DeltronProductoResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewDeltronProducto extends ViewRecord
{
    protected static string $resource = DeltronProductoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
