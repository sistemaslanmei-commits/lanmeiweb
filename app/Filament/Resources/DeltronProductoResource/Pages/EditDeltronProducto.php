<?php

namespace App\Filament\Resources\DeltronProductoResource\Pages;

use App\Filament\Resources\DeltronProductoResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditDeltronProducto extends EditRecord
{
    protected static string $resource = DeltronProductoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
