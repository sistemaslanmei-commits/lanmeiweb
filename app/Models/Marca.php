<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

#[Fillable([
    'nombre',
    'slug',
    'descripcion',
    'logo',
    'is_active',
])]
class Marca extends Model
{
    protected $table = 'marcas';

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::updating(function (Marca $marca) {
            if ($marca->isDirty('logo') && $marca->getOriginal('logo')) {
                Storage::disk('public')->delete($marca->getOriginal('logo'));
            }
        });

        static::deleting(function (Marca $marca) {
            if ($marca->logo) {
                Storage::disk('public')->delete($marca->logo);
            }
        });
    }

    public function productos(): HasMany
    {
        return $this->hasMany(Producto::class, 'marca_id');
    }
}