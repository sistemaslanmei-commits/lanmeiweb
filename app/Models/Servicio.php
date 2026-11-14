<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

#[Fillable([
    'titulo',
    'slug',
    'descripcion_corta',
    'contenido',
    'icono',
    'imagen_principal',
    'imagenes',
    'orden',
    'is_active',
])]
class Servicio extends Model
{
    protected $table = 'servicios';

    protected function casts(): array
    {
        return [
            'imagenes'  => 'array',
            'is_active' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::updating(function (Servicio $servicio) {
            if ($servicio->isDirty('imagen_principal') && $servicio->getOriginal('imagen_principal')) {
                Storage::disk('public')->delete($servicio->getOriginal('imagen_principal'));
            }

            if ($servicio->isDirty('imagenes')) {
                $oldImages = $servicio->getOriginal('imagenes') ?? [];
                $newImages = $servicio->imagenes ?? [];

                foreach (array_diff($oldImages, $newImages) as $removedImage) {
                    Storage::disk('public')->delete($removedImage);
                }
            }
        });

        static::deleting(function (Servicio $servicio) {
            if ($servicio->imagen_principal) {
                Storage::disk('public')->delete($servicio->imagen_principal);
            }

            if (!empty($servicio->imagenes) && is_array($servicio->imagenes)) {
                foreach ($servicio->imagenes as $galleryImage) {
                    Storage::disk('public')->delete($galleryImage);
                }
            }
        });
    }
}