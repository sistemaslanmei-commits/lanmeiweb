<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

#[Fillable([
    'nombre',
    'logo',
    'favicon',
    'telefono_principal',
    'whatsapp_principal',
    'email_contacto',
    'direccion_fiscal',
    'porcentaje_deltron',
    'redes_sociales',
    'banners_por_pagina',
])]
class Empresa extends Model
{
    protected $table = 'empresas';

    protected function casts(): array
    {
        return [
            'porcentaje_deltron' => 'decimal:2',
            'redes_sociales'     => 'array',
            'banners_por_pagina' => 'array',
        ];
    }

    protected static function booted(): void
    {
        static::updating(function (Empresa $empresa) {
            if ($empresa->isDirty('logo') && $empresa->getOriginal('logo')) {
                Storage::disk('public')->delete($empresa->getOriginal('logo'));
            }

            if ($empresa->isDirty('favicon') && $empresa->getOriginal('favicon')) {
                Storage::disk('public')->delete($empresa->getOriginal('favicon'));
            }

            if ($empresa->isDirty('banners_por_pagina')) {
                $oldBanners = array_values($empresa->getOriginal('banners_por_pagina') ?? []);
                $newBanners = array_values($empresa->banners_por_pagina ?? []);

                foreach (array_diff($oldBanners, $newBanners) as $removedBanner) {
                    if ($removedBanner) {
                        Storage::disk('public')->delete($removedBanner);
                    }
                }
            }
        });

        // Dentro de booted() en app/Models/Empresa.php
        static::saved(function (Empresa $empresa) {
            if ($empresa->wasChanged('porcentaje_deltron')) {
                $multiplicador = 1 + ($empresa->porcentaje_deltron / 100);

                \App\Models\DeltronProducto::query()->update([
                    'precio' => \Illuminate\Support\Facades\DB::raw("ROUND(precio_costo_soles * {$multiplicador}, 2)"),
                ]);
            }
        });

        
    }
}