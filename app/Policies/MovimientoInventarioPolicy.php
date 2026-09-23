<?php

namespace App\Policies;

use App\Models\MovimientoInventario;
use App\Models\User;

class MovimientoInventarioPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['Super Administrador', 'Administrador de Empresa', 'Encargado de Área', 'Consulta / Solo Lectura']);
    }

    public function view(User $user, MovimientoInventario $mov): bool
    {
        if ($user->hasRole('Super Administrador')) {
            return true;
        }

        return $mov->item->empresa_id === $user->empresa_id;
    }

    // Por regla de negocio, los movimientos nunca se editan ni se eliminan
    public function update(User $user, MovimientoInventario $mov): bool
    {
        return false;
    }

    public function delete(User $user, MovimientoInventario $mov): bool
    {
        return false;
    }
}
