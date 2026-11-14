<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'nombre_sucursal',
    'direccion',
    'ciudad',
    'telefono',
    'whatsapp',
    'horario_atencion',
    'mapa_embed_url',
    'es_principal',
    'is_active',
])]
class Tienda extends Model
{
    protected $table = 'tiendas';

    protected function casts(): array
    {
        return [
            'es_principal' => 'boolean',
            'is_active'    => 'boolean',
        ];
    }
}