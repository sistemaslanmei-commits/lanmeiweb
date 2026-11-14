<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

#[Fillable([
    'categoria_id',
    'marca_id',
    'sku',
    'nombre',
    'slug',
    'precio',
    'stock',
    'descripcion_corta',
    'descripcion',
    'especificaciones_tecnicas',
    'imagen',
    'imagenes',
    'ficha_tecnica_pdf',
    'is_featured',
    'is_active',
    'sort_order',
])]
class Producto extends Model
{
    protected $table = 'productos';

    protected function casts(): array
    {
        return [
            'especificaciones_tecnicas' => 'array',
            'imagenes'                  => 'array',
            'precio'                    => 'decimal:2',
            'is_featured'               => 'boolean',
            'is_active'                 => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::updating(function (Producto $producto) {
            if ($producto->isDirty('ficha_tecnica_pdf') && $producto->getOriginal('ficha_tecnica_pdf')) {
                Storage::disk('public')->delete($producto->getOriginal('ficha_tecnica_pdf'));
            }

            $oldImage = $producto->getOriginal('imagen');
            if ($producto->isDirty('imagen') && $oldImage && !str_starts_with($oldImage, 'http')) {
                Storage::disk('public')->delete($oldImage);
            }

            if ($producto->isDirty('imagenes')) {
                $oldImages = $producto->getOriginal('imagenes') ?? [];
                $newImages = $producto->imagenes ?? [];

                foreach (array_diff($oldImages, $newImages) as $removedImage) {
                    Storage::disk('public')->delete($removedImage);
                }
            }
        });

        static::deleting(function (Producto $producto) {
            if ($producto->ficha_tecnica_pdf) {
                Storage::disk('public')->delete($producto->ficha_tecnica_pdf);
            }

            if ($producto->imagen && !str_starts_with($producto->imagen, 'http')) {
                Storage::disk('public')->delete($producto->imagen);
            }

            if (!empty($producto->imagenes) && is_array($producto->imagenes)) {
                foreach ($producto->imagenes as $galleryImage) {
                    Storage::disk('public')->delete($galleryImage);
                }
            }
        });
    }

    public function categoria(): BelongsTo
    {
        return $this->belongsTo(Categoria::class, 'categoria_id');
    }

    public function marca(): BelongsTo
    {
        return $this->belongsTo(Marca::class, 'marca_id');
    }
}