<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Area extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'areas';

    protected $fillable = [
        'sucursal_id',
        'nombre',
        'descripcion',
        'encargado_id',
        'estado',
    ];

    public function sucursal(): BelongsTo
    {
        return $this->belongsTo(Sucursal::class);
    }

    public function encargado(): BelongsTo
    {
        return $this->belongsTo(User::class, 'encargado_id');
    }

    public function inventarios(): HasMany
    {
        return $this->hasMany(InventarioArea::class);
    }

    public function movimientosOrigen(): HasMany
    {
        return $this->hasMany(MovimientoInventario::class, 'area_origen_id');
    }

    public function movimientosDestino(): HasMany
    {
        return $this->hasMany(MovimientoInventario::class, 'area_destino_id');
    }
}
