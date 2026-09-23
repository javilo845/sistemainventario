<?php

namespace App\Policies;

use App\Models\Empresa;
use App\Models\User;

class EmpresaPolicy
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
        return $user->hasRole('Super Administrador');
    }

    public function view(User $user, Empresa $empresa): bool
    {
        return $user->empresa_id === $empresa->id;
    }

    public function create(User $user): bool
    {
        return $user->hasRole('Super Administrador');
    }

    public function update(User $user, Empresa $empresa): bool
    {
        return $user->hasRole('Administrador de Empresa') && $user->empresa_id === $empresa->id;
    }

    public function delete(User $user, Empresa $empresa): bool
    {
        return $user->hasRole('Super Administrador');
    }
}
