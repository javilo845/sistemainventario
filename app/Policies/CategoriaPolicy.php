<?php

namespace App\Policies;

use App\Models\Categoria;
use App\Models\User;

class CategoriaPolicy
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

    public function view(User $user, Categoria $categoria): bool
    {
        return $user->empresa_id === $categoria->empresa_id;
    }

    public function create(User $user): bool
    {
        return $user->hasRole('Administrador de Empresa');
    }

    public function update(User $user, Categoria $categoria): bool
    {
        return $user->hasRole('Administrador de Empresa') && $user->empresa_id === $categoria->empresa_id;
    }

    public function delete(User $user, Categoria $categoria): bool
    {
        return $user->hasRole('Administrador de Empresa') && $user->empresa_id === $categoria->empresa_id;
    }
}
