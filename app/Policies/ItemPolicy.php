<?php

namespace App\Policies;

use App\Models\Item;
use App\Models\User;

class ItemPolicy
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

    public function view(User $user, Item $item): bool
    {
        return $user->empresa_id === $item->empresa_id;
    }

    public function create(User $user): bool
    {
        return $user->hasRole('Administrador de Empresa');
    }

    public function update(User $user, Item $item): bool
    {
        return $user->hasRole('Administrador de Empresa') && $user->empresa_id === $item->empresa_id;
    }

    public function delete(User $user, Item $item): bool
    {
        return $user->hasRole('Administrador de Empresa') && $user->empresa_id === $item->empresa_id;
    }
}
