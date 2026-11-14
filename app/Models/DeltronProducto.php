<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'sku',
    'nombre',
    'precio',
    'precio_costo_soles',
    'precio_costo_usd',
    'stock',
    'imagen',
    'categoria_id',
    'marca',
    'especificaciones_html',
])]
class DeltronProducto extends Model
{
    protected $table = 'deltron_productos';

    protected function casts(): array
    {
        return [
            'precio'             => 'decimal:2',
            'precio_costo_soles' => 'decimal:2',
            'precio_costo_usd'   => 'decimal:2',
        ];
    }

    public function categoria(): BelongsTo
    {
        return $this->belongsTo(Categoria::class, 'categoria_id');
    }
}