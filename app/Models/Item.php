<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Item extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'items';

    protected $fillable = [
        'empresa_id',
        'categoria_id',
        'unidad_medida_id',
        'proveedor_id',
        'nombre',
        'sku',
        'descripcion',
        'imagen',
        'costo_unitario',
        'stock_minimo',
        'estado',
    ];

    protected function casts(): array
    {
        return [
            'costo_unitario' => 'decimal:2',
            'stock_minimo' => 'integer',
        ];
    }

    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class);
    }

    public function categoria(): BelongsTo
    {
        return $this->belongsTo(Categoria::class);
    }

    public function unidadMedida(): BelongsTo
    {
        return $this->belongsTo(UnidadMedida::class, 'unidad_medida_id');
    }

    public function proveedor(): BelongsTo
    {
        return $this->belongsTo(Proveedor::class);
    }

    public function inventarios(): HasMany
    {
        return $this->hasMany(InventarioArea::class, 'item_id');
    }

    public function areas(): BelongsToMany
    {
        return $this->belongsToMany(Area::class, 'inventario_area', 'item_id', 'area_id')
            ->withPivot('cantidad')
            ->withTimestamps();
    }

    public function movimientos(): HasMany
    {
        return $this->hasMany(MovimientoInventario::class, 'item_id');
    }

    public function getStockTotalAttribute(): float
    {
        return (float) $this->inventarios()->sum('cantidad');
    }

    public function isBajoStockMinimo(): bool
    {
        return $this->stock_total < $this->stock_minimo;
    }
}
