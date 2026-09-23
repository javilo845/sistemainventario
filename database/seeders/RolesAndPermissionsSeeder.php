<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Lista de permisos
        $permissions = [
            'gestionar empresas',
            'gestionar usuarios',
            'gestionar sucursales',
            'gestionar areas',
            'gestionar categorias',
            'gestionar unidades',
            'gestionar proveedores',
            'gestionar items',
            'ver inventario',
            'registrar entrada',
            'registrar salida',
            'registrar traslado',
            'registrar ajuste',
            'ver dashboard',
            'ver reportes',
            'exportar reportes',
        ];

        foreach ($permissions as $permission) {
            Permission::findOrCreate($permission, 'web');
        }

        // 1. Super Administrador (tiene todos los permisos)
        $superAdmin = Role::findOrCreate('Super Administrador', 'web');
        $superAdmin->givePermissionTo(Permission::all());

        // 2. Administrador de Empresa
        $adminEmpresa = Role::findOrCreate('Administrador de Empresa', 'web');
        $adminEmpresa->givePermissionTo([
            'gestionar usuarios',
            'gestionar sucursales',
            'gestionar areas',
            'gestionar categorias',
            'gestionar unidades',
            'gestionar proveedores',
            'gestionar items',
            'ver inventario',
            'registrar entrada',
            'registrar salida',
            'registrar traslado',
            'registrar ajuste',
            'ver dashboard',
            'ver reportes',
            'exportar reportes',
        ]);

        // 3. Encargado de Área
        $encargadoArea = Role::findOrCreate('Encargado de Área', 'web');
        $encargadoArea->givePermissionTo([
            'ver inventario',
            'registrar entrada',
            'registrar salida',
            'registrar traslado',
            'ver dashboard',
            'ver reportes',
        ]);

        // 4. Consulta / Solo Lectura
        $consulta = Role::findOrCreate('Consulta / Solo Lectura', 'web');
        $consulta->givePermissionTo([
            'ver inventario',
            'ver dashboard',
            'ver reportes',
        ]);
    }
}
