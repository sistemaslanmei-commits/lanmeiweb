<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

#[Fillable([
    'parent_id',
    'nombreCategoria',
    'slug',
    'descripcion',
    'imagen',
    'is_active',
    'sort_order',
])]
class Categoria extends Model
{
    protected $table = 'categorias';

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        // Genera slug automáticamente si SyncDeltronCatalog crea una categoría nueva
        static::creating(function (Categoria $categoria) {
            if (empty($categoria->slug)) {
                $categoria->slug = Str::slug($categoria->nombreCategoria) . '-' . Str::random(4);
            }
        });

        static::updating(function (Categoria $categoria) {
            if ($categoria->isDirty('imagen') && $categoria->getOriginal('imagen')) {
                Storage::disk('public')->delete($categoria->getOriginal('imagen'));
            }
        });

        static::deleting(function (Categoria $categoria) {
            if ($categoria->imagen) {
                Storage::disk('public')->delete($categoria->imagen);
            }
        });
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Categoria::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Categoria::class, 'parent_id');
    }

    public function productos(): HasMany
    {
        return $this->hasMany(Producto::class, 'categoria_id');
    }

    public function productosDeltron(): HasMany
    {
        return $this->hasMany(DeltronProducto::class, 'categoria_id');
    }

    public function getTodosLosProductosAttribute()
    {
        return $this->productos->concat($this->productosDeltron);
    }
}