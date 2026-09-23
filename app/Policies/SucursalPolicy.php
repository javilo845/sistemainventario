<?php

namespace App\Policies;

use App\Models\Sucursal;
use App\Models\User;

class SucursalPolicy
{
    public function before(User $user, string $ability): ?bool
    {
        if ($user->hasRole('Super Administrador')) {
            return true;
        }
        return null;
    }

    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['Super Administrador', 'Administrador de Empresa', 'Encargado de Área', 'Consulta / Solo Lectura']);
    }

    public function view(User $user, Sucursal $sucursal): bool
    {
        return $user->empresa_id === $sucursal->empresa_id;
    }

    public function create(User $user): bool
    {
        return $user->hasRole('Administrador de Empresa');
    }

    public function update(User $user, Sucursal $sucursal): bool
    {
        return $user->hasRole('Administrador de Empresa') && $user->empresa_id === $sucursal->empresa_id;
    }

    public function delete(User $user, Sucursal $sucursal): bool
    {
        return $user->hasRole('Administrador de Empresa') && $user->empresa_id === $sucursal->empresa_id;
    }
}
