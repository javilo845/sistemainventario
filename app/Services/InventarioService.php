<?php

namespace App\Services;

use App\Models\Area;
use App\Models\InventarioArea;
use App\Models\Item;
use App\Models\MovimientoInventario;
use Exception;
use Illuminate\Support\Facades\DB;

class InventarioService
{
    /**
     * Obtener el stock físico actual de un ítem en una área determinada.
     */
    public function obtenerStock(int $itemId, int $areaId): float
    {
        $inv = InventarioArea::where('item_id', $itemId)
            ->where('area_id', $areaId)
            ->first();

        return $inv ? (float) $inv->cantidad : 0.0;
    }

    /**
     * Registrar ENTRADA de inventario.
     * Incrementa el stock en el área de destino.
     */
    public function registrarEntrada(int $itemId, int $areaDestinoId, float $cantidad, int $usuarioId, ?string $motivo = null): MovimientoInventario
    {
        if ($cantidad <= 0) {
            throw new Exception('La cantidad ingresada debe ser mayor a cero.');
        }

        return DB::transaction(function () use ($itemId, $areaDestinoId, $cantidad, $usuarioId, $motivo) {
            // Actualizar o crear registro en inventario_area
            $inv = InventarioArea::firstOrCreate(
                ['item_id' => $itemId, 'area_id' => $areaDestinoId],
                ['cantidad' => 0]
            );

            $inv->increment('cantidad', $cantidad);

            // Registrar en bitácora inmutable
            return MovimientoInventario::create([
                'item_id' => $itemId,
                'tipo' => 'entrada',
                'cantidad' => $cantidad,
                'area_origen_id' => null,
                'area_destino_id' => $areaDestinoId,
                'usuario_id' => $usuarioId,
                'motivo' => $motivo ?: 'Entrada de inventario',
                'created_at' => now(),
            ]);
        });
    }

    /**
     * Registrar SALIDA de inventario.
     * Descuenta stock del área de origen previa validación estricta de saldo disponible.
     */
    public function registrarSalida(int $itemId, int $areaOrigenId, float $cantidad, int $usuarioId, ?string $motivo = null): MovimientoInventario
    {
        if ($cantidad <= 0) {
            throw new Exception('La cantidad a retirar debe ser mayor a cero.');
        }

        return DB::transaction(function () use ($itemId, $areaOrigenId, $cantidad, $usuarioId, $motivo) {
            $inv = InventarioArea::where('item_id', $itemId)
                ->where('area_id', $areaOrigenId)
                ->lockForUpdate()
                ->first();

            $stockActual = $inv ? (float) $inv->cantidad : 0.0;

            if ($stockActual < $cantidad) {
                throw new Exception("Stock insuficiente en el área origen. Disponible: {$stockActual}, Solicitado: {$cantidad}.");
            }

            $inv->decrement('cantidad', $cantidad);

            return MovimientoInventario::create([
                'item_id' => $itemId,
                'tipo' => 'salida',
                'cantidad' => $cantidad,
                'area_origen_id' => $areaOrigenId,
                'area_destino_id' => null,
                'usuario_id' => $usuarioId,
                'motivo' => $motivo ?: 'Salida de inventario',
                'created_at' => now(),
            ]);
        });
    }

    /**
     * Registrar TRASLADO de inventario entre áreas.
     * Mueve stock del área origen al área destino de forma atómica.
     * Reasigna automáticamente el responsable del stock trasladado al encargado del área destino.
     */
    public function registrarTraslado(int $itemId, int $areaOrigenId, int $areaDestinoId, float $cantidad, int $usuarioId, ?string $motivo = null): MovimientoInventario
    {
        if ($areaOrigenId === $areaDestinoId) {
            throw new Exception('El área de origen y el área de destino no pueden ser iguales.');
        }

        if ($cantidad <= 0) {
            throw new Exception('La cantidad a trasladar debe ser mayor a cero.');
        }

        return DB::transaction(function () use ($itemId, $areaOrigenId, $areaDestinoId, $cantidad, $usuarioId, $motivo) {
            $areaOrigen = Area::with('sucursal')->findOrFail($areaOrigenId);
            $areaDestino = Area::with('sucursal')->findOrFail($areaDestinoId);

            // Validar que pertenezcan a la misma empresa
            if ($areaOrigen->sucursal->empresa_id !== $areaDestino->sucursal->empresa_id) {
                throw new Exception('Los traslados solo pueden realizarse entre áreas de la misma empresa.');
            }

            // 1. Bloquear y validar saldo en origen
            $invOrigen = InventarioArea::where('item_id', $itemId)
                ->where('area_id', $areaOrigenId)
                ->lockForUpdate()
                ->first();

            $stockDisponible = $invOrigen ? (float) $invOrigen->cantidad : 0.0;

            if ($stockDisponible < $cantidad) {
                throw new Exception("Stock insuficiente en el área origen '{$areaOrigen->nombre}'. Disponible: {$stockDisponible}, Solicitado: {$cantidad}.");
            }

            // 2. Descontar de origen
            $invOrigen->decrement('cantidad', $cantidad);

            // 3. Incrementar en destino
            $invDestino = InventarioArea::firstOrCreate(
                ['item_id' => $itemId, 'area_id' => $areaDestinoId],
                ['cantidad' => 0]
            );
            $invDestino->increment('cantidad', $cantidad);

            // Nota de custodia del responsable
            $responsableDestino = $areaDestino->encargado ? $areaDestino->encargado->name : 'Área sin encargado asignado';
            $obs = ($motivo ? $motivo . ' | ' : '') . "Custodia reasignada a: {$responsableDestino}";

            // 4. Registrar en bitácora inmutable
            return MovimientoInventario::create([
                'item_id' => $itemId,
                'tipo' => 'traslado',
                'cantidad' => $cantidad,
                'area_origen_id' => $areaOrigenId,
                'area_destino_id' => $areaDestinoId,
                'usuario_id' => $usuarioId,
                'motivo' => $obs,
                'created_at' => now(),
            ]);
        });
    }

    /**
     * Registrar AJUSTE manual de inventario (físico vs sistema).
     * El motivo es estrictamente obligatorio.
     * Puede ser positivo (aumenta) o negativo (disminuye saldo).
     */
    public function registrarAjuste(int $itemId, int $areaId, float $cantidadAjuste, int $usuarioId, string $motivo): MovimientoInventario
    {
        if (trim($motivo) === '') {
            throw new Exception('El motivo del ajuste es estrictamente obligatorio.');
        }

        if ($cantidadAjuste == 0) {
            throw new Exception('La cantidad de ajuste no puede ser cero.');
        }

        return DB::transaction(function () use ($itemId, $areaId, $cantidadAjuste, $usuarioId, $motivo) {
            $inv = InventarioArea::firstOrCreate(
                ['item_id' => $itemId, 'area_id' => $areaId],
                ['cantidad' => 0]
            );

            $stockActual = (float) $inv->cantidad;

            if ($cantidadAjuste < 0 && abs($cantidadAjuste) > $stockActual) {
                throw new Exception("No es posible realizar un ajuste negativo mayor al stock actual ({$stockActual}).");
            }

            $inv->cantidad = $stockActual + $cantidadAjuste;
            $inv->save();

            return MovimientoInventario::create([
                'item_id' => $itemId,
                'tipo' => 'ajuste',
                'cantidad' => $cantidadAjuste,
                'area_origen_id' => $cantidadAjuste < 0 ? $areaId : null,
                'area_destino_id' => $cantidadAjuste > 0 ? $areaId : null,
                'usuario_id' => $usuarioId,
                'motivo' => "[Ajuste manual] " . $motivo,
                'created_at' => now(),
            ]);
        });
    }
}
