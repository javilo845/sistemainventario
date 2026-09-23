<?php

namespace Database\Seeders;

use App\Models\Area;
use App\Models\Categoria;
use App\Models\Empresa;
use App\Models\Item;
use App\Models\Proveedor;
use App\Models\Sucursal;
use App\Models\UnidadMedida;
use App\Models\User;
use App\Services\InventarioService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        $inventarioService = app(InventarioService::class);

        // 1. Usuarios con roles globales
        $superAdmin = User::firstOrCreate(
            ['email' => 'admin@inventario.com'],
            [
                'name' => 'Super Administrador',
                'password' => Hash::make('password'),
                'estado' => 'activo',
            ]
        );
        $superAdmin->syncRoles(['Super Administrador']);

        // 2. Empresa Demo
        $empresa = Empresa::firstOrCreate(
            ['identificacion_fiscal' => '08011990123456'],
            [
                'nombre' => 'Mipyme Soluciones Comerciales S. de R.L.',
                'direccion' => 'Blvd. Morazán, Edificio Corporativo Los Próceres, Piso 3',
                'telefono' => '+504 2234-5678',
                'correo' => 'contacto@mipymesoluciones.hn',
                'estado' => 'activo',
            ]
        );

        // 3. Administrador de la Empresa
        $adminEmpresa = User::firstOrCreate(
            ['email' => 'gerente@demo.com'],
            [
                'name' => 'Carlos Mendoza (Gerente General)',
                'password' => Hash::make('password'),
                'empresa_id' => $empresa->id,
                'estado' => 'activo',
            ]
        );
        $adminEmpresa->syncRoles(['Administrador de Empresa']);

        // Usuario Solo Lectura
        $usuarioConsulta = User::firstOrCreate(
            ['email' => 'auditor@demo.com'],
            [
                'name' => 'Lic. Sofia Ramos (Auditora Externa)',
                'password' => Hash::make('password'),
                'empresa_id' => $empresa->id,
                'estado' => 'activo',
            ]
        );
        $usuarioConsulta->syncRoles(['Consulta / Solo Lectura']);

        // 4. Encargados de Áreas
        $encargadosData = [
            ['name' => 'Juan Pérez (Encargado Bodega Central)', 'email' => 'bodega.central@demo.com'],
            ['name' => 'María Gómez (Encargada Despacho)', 'email' => 'despacho.central@demo.com'],
            ['name' => 'Luis Barahona (Encargado Tienda)', 'email' => 'tienda.central@demo.com'],
            ['name' => 'Ana Martínez (Encargada Bodega Norte)', 'email' => 'bodega.norte@demo.com'],
            ['name' => 'Roberto Díaz (Encargado Recepción Norte)', 'email' => 'recepcion.norte@demo.com'],
            ['name' => 'Elena Cruz (Encargada Exhibición Norte)', 'email' => 'tienda.norte@demo.com'],
        ];

        $encargados = [];
        foreach ($encargadosData as $enc) {
            $user = User::firstOrCreate(
                ['email' => $enc['email']],
                [
                    'name' => $enc['name'],
                    'password' => Hash::make('password'),
                    'empresa_id' => $empresa->id,
                    'estado' => 'activo',
                ]
            );
            $user->syncRoles(['Encargado de Área']);
            $encargados[] = $user;
        }

        // 5. Sucursales
        $sucursalCentral = Sucursal::firstOrCreate(
            ['empresa_id' => $empresa->id, 'nombre' => 'Sucursal Central (Tegucigalpa)'],
            [
                'direccion' => 'Av. La Paz, Frente a Embajada Americana',
                'telefono' => '+504 2231-1122',
                'estado' => 'activo',
            ]
        );

        $sucursalNorte = Sucursal::firstOrCreate(
            ['empresa_id' => $empresa->id, 'nombre' => 'Sucursal Norte (San Pedro Sula)'],
            [
                'direccion' => 'Circunvalación, Barrio Suyapa',
                'telefono' => '+504 2550-9988',
                'estado' => 'activo',
            ]
        );

        // 6. Áreas por Sucursal
        $areaBodegaCentral = Area::firstOrCreate(
            ['sucursal_id' => $sucursalCentral->id, 'nombre' => 'Bodega Central'],
            [
                'descripcion' => 'Almacén primario de mercancías y pallets',
                'encargado_id' => $encargados[0]->id,
                'estado' => 'activo',
            ]
        );

        $areaDespachoCentral = Area::firstOrCreate(
            ['sucursal_id' => $sucursalCentral->id, 'nombre' => 'Área de Despacho'],
            [
                'descripcion' => 'Preparación de pedidos y logística local',
                'encargado_id' => $encargados[1]->id,
                'estado' => 'activo',
            ]
        );

        $areaTiendaCentral = Area::firstOrCreate(
            ['sucursal_id' => $sucursalCentral->id, 'nombre' => 'Piso de Ventas Central'],
            [
                'descripcion' => 'Exhibición y atención directa a clientes',
                'encargado_id' => $encargados[2]->id,
                'estado' => 'activo',
            ]
        );

        $areaBodegaNorte = Area::firstOrCreate(
            ['sucursal_id' => $sucursalNorte->id, 'nombre' => 'Bodega Norte'],
            [
                'descripcion' => 'Almacenamiento regional para zona norte',
                'encargado_id' => $encargados[3]->id,
                'estado' => 'activo',
            ]
        );

        $areaRecepcionNorte = Area::firstOrCreate(
            ['sucursal_id' => $sucursalNorte->id, 'nombre' => 'Recepción y Control Norte'],
            [
                'descripcion' => 'Control de calidad y recepción de proveedores',
                'encargado_id' => $encargados[4]->id,
                'estado' => 'activo',
            ]
        );

        $areaTiendaNorte = Area::firstOrCreate(
            ['sucursal_id' => $sucursalNorte->id, 'nombre' => 'Piso de Ventas Norte'],
            [
                'descripcion' => 'Sala de ventas San Pedro Sula',
                'encargado_id' => $encargados[5]->id,
                'estado' => 'activo',
            ]
        );

        // 7. Categorías
        $catNombres = ['Papelería y Oficina', 'Limpieza e Higiene', 'Materiales y Repuestos', 'Alimentos y Bebidas', 'Tecnología y Accesorios'];
        $categorias = [];
        foreach ($catNombres as $nombre) {
            $categorias[] = Categoria::firstOrCreate(['empresa_id' => $empresa->id, 'nombre' => $nombre]);
        }

        // 8. Proveedores
        $prov1 = Proveedor::firstOrCreate(
            ['empresa_id' => $empresa->id, 'nombre' => 'Distribuidora Universal S.A.'],
            ['contacto' => 'Roberto Paz', 'telefono' => '9988-7766', 'correo' => 'ventas@universal.com']
        );
        $prov2 = Proveedor::firstOrCreate(
            ['empresa_id' => $empresa->id, 'nombre' => 'Comercializadora Industrial del Caribe'],
            ['contacto' => 'Glenda Torres', 'telefono' => '9544-3322', 'correo' => 'pedidos@caribeindustrial.com']
        );
        $prov3 = Proveedor::firstOrCreate(
            ['empresa_id' => $empresa->id, 'nombre' => 'Papelera y Suministros Centroamericanos'],
            ['contacto' => 'Marcos Varela', 'telefono' => '8877-6655', 'correo' => 'contacto@papelera.hn']
        );

        // Unidades de medida
        $uUnidad = UnidadMedida::where('abreviatura', 'u')->first();
        $uCaja = UnidadMedida::where('abreviatura', 'caja')->first();
        $uKg = UnidadMedida::where('abreviatura', 'kg')->first();
        $uLitro = UnidadMedida::where('abreviatura', 'L')->first();
        $uPaq = UnidadMedida::where('abreviatura', 'paq')->first();

        // 9. Catálogo de 18 Ítems con SKU
        $itemsData = [
            ['sku' => 'PAP-001', 'nombre' => 'Resma Papel Bond Carta 75g', 'cat' => 0, 'u' => $uPaq->id, 'p' => $prov3->id, 'costo' => 5.50, 'min' => 20],
            ['sku' => 'PAP-002', 'nombre' => 'Caja de Bolígrafos Azules 50u', 'cat' => 0, 'u' => $uCaja->id, 'p' => $prov3->id, 'costo' => 12.00, 'min' => 10],
            ['sku' => 'PAP-003', 'nombre' => 'Engrapadora Metálica de Uso Rudo', 'cat' => 0, 'u' => $uUnidad->id, 'p' => $prov3->id, 'costo' => 8.75, 'min' => 5],
            ['sku' => 'PAP-004', 'nombre' => 'Cinta Adhesiva Transparente 48mm', 'cat' => 0, 'u' => $uUnidad->id, 'p' => $prov3->id, 'costo' => 1.80, 'min' => 25],
            
            ['sku' => 'LIM-001', 'nombre' => 'Desinfectante Multiusos Lavanda 1 Galón', 'cat' => 1, 'u' => $uLitro->id, 'p' => $prov2->id, 'costo' => 7.20, 'min' => 15],
            ['sku' => 'LIM-002', 'nombre' => 'Jabón Líquido Antibacterial 5L', 'cat' => 1, 'u' => $uLitro->id, 'p' => $prov2->id, 'costo' => 11.50, 'min' => 8],
            ['sku' => 'LIM-003', 'nombre' => 'Toallas de Papel Interdobladas (Caja 20 paq)', 'cat' => 1, 'u' => $uCaja->id, 'p' => $prov2->id, 'costo' => 22.00, 'min' => 6],
            ['sku' => 'LIM-004', 'nombre' => 'Bolsas Negras para Basura 55 Galones (Paq 100u)', 'cat' => 1, 'u' => $uPaq->id, 'p' => $prov2->id, 'costo' => 14.30, 'min' => 10],

            ['sku' => 'REP-001', 'nombre' => 'Bombillo LED 12W Luz Blanca', 'cat' => 2, 'u' => $uUnidad->id, 'p' => $prov1->id, 'costo' => 2.40, 'min' => 30],
            ['sku' => 'REP-002', 'nombre' => 'Cable Eléctrico Calibre 12 THHN (Rollo 100m)', 'cat' => 2, 'u' => $uUnidad->id, 'p' => $prov1->id, 'costo' => 45.00, 'min' => 4],
            ['sku' => 'REP-003', 'nombre' => 'Aceite Lubricante Multiuso WD-40 11oz', 'cat' => 2, 'u' => $uUnidad->id, 'p' => $prov1->id, 'costo' => 6.80, 'min' => 12],
            ['sku' => 'REP-004', 'nombre' => 'Tornillos Autoperforantes 1-1/2 pulgada (Caja 500u)', 'cat' => 2, 'u' => $uCaja->id, 'p' => $prov1->id, 'costo' => 9.50, 'min' => 15],

            ['sku' => 'TEC-001', 'nombre' => 'Mouse Óptico USB Ergonómico', 'cat' => 4, 'u' => $uUnidad->id, 'p' => $prov1->id, 'costo' => 7.00, 'min' => 10],
            ['sku' => 'TEC-002', 'nombre' => 'Teclado Estándar USB Español', 'cat' => 4, 'u' => $uUnidad->id, 'p' => $prov1->id, 'costo' => 9.50, 'min' => 8],
            ['sku' => 'TEC-003', 'nombre' => 'Regleta Protectora de Picos 6 Tomas', 'cat' => 4, 'u' => $uUnidad->id, 'p' => $prov1->id, 'costo' => 11.20, 'min' => 10],
            ['sku' => 'TEC-004', 'nombre' => 'Cable HDMI 2.0 de Alta Velocidad 2m', 'cat' => 4, 'u' => $uUnidad->id, 'p' => $prov1->id, 'costo' => 4.50, 'min' => 15],

            ['sku' => 'ALI-001', 'nombre' => 'Café Gourmet Tostado y Molido 1 Lb', 'cat' => 3, 'u' => $uKg->id, 'p' => $prov2->id, 'costo' => 4.20, 'min' => 20],
            ['sku' => 'ALI-002', 'nombre' => 'Azúcar Blanca Especial 25 Kg', 'cat' => 3, 'u' => $uKg->id, 'p' => $prov2->id, 'costo' => 18.00, 'min' => 5],
        ];

        $itemsCreated = [];
        foreach ($itemsData as $data) {
            $item = Item::firstOrCreate(
                ['empresa_id' => $empresa->id, 'sku' => $data['sku']],
                [
                    'categoria_id' => $categorias[$data['cat']]->id,
                    'unidad_medida_id' => $data['u'],
                    'proveedor_id' => $data['p'],
                    'nombre' => $data['nombre'],
                    'costo_unitario' => $data['costo'],
                    'stock_minimo' => $data['min'],
                    'estado' => 'activo',
                ]
            );
            $itemsCreated[] = $item;
        }

        // 10. Movimientos iniciales de Entrada (poblando inventario físico en Bodegas)
        // Entradas en Bodega Central
        $inventarioService->registrarEntrada($itemsCreated[0]->id, $areaBodegaCentral->id, 80, $adminEmpresa->id, 'Factura #F-4581 Proveedor Papelera');
        $inventarioService->registrarEntrada($itemsCreated[1]->id, $areaBodegaCentral->id, 35, $adminEmpresa->id, 'Factura #F-4581 Proveedor Papelera');
        $inventarioService->registrarEntrada($itemsCreated[4]->id, $areaBodegaCentral->id, 40, $adminEmpresa->id, 'Factura #F-9021 Comercializadora Caribe');
        $inventarioService->registrarEntrada($itemsCreated[8]->id, $areaBodegaCentral->id, 100, $adminEmpresa->id, 'Factura #F-1024 Distribuidora Universal');
        $inventarioService->registrarEntrada($itemsCreated[12]->id, $areaBodegaCentral->id, 25, $adminEmpresa->id, 'Factura #F-1024 Distribuidora Universal');
        $inventarioService->registrarEntrada($itemsCreated[16]->id, $areaBodegaCentral->id, 50, $adminEmpresa->id, 'Factura #F-9021 Comercializadora Caribe');

        // Entradas en Bodega Norte
        $inventarioService->registrarEntrada($itemsCreated[0]->id, $areaBodegaNorte->id, 40, $adminEmpresa->id, 'Compra directa sede Norte');
        $inventarioService->registrarEntrada($itemsCreated[4]->id, $areaBodegaNorte->id, 25, $adminEmpresa->id, 'Compra directa sede Norte');
        $inventarioService->registrarEntrada($itemsCreated[8]->id, $areaBodegaNorte->id, 60, $adminEmpresa->id, 'Compra directa sede Norte');
        $inventarioService->registrarEntrada($itemsCreated[9]->id, $areaBodegaNorte->id, 8, $adminEmpresa->id, 'Compra directa sede Norte');

        // Entradas con stock bajo para activar alertas visuales en el dashboard
        $inventarioService->registrarEntrada($itemsCreated[2]->id, $areaBodegaCentral->id, 2, $adminEmpresa->id, 'Entrada de muestra (Stock bajo para demo)'); // min es 5
        $inventarioService->registrarEntrada($itemsCreated[6]->id, $areaBodegaCentral->id, 3, $adminEmpresa->id, 'Stock inicial limitado (Alerta demo)'); // min es 6
        $inventarioService->registrarEntrada($itemsCreated[13]->id, $areaBodegaNorte->id, 4, $adminEmpresa->id, 'Quedan pocas unidades en sede norte'); // min es 8

        // 11. Traslados entre áreas (probando reasignación automática de custodia)
        // Traslado de Bodega Central a Despacho Central
        $inventarioService->registrarTraslado(
            $itemsCreated[0]->id,
            $areaBodegaCentral->id,
            $areaDespachoCentral->id,
            20,
            $adminEmpresa->id,
            'Suministro para empaque y envíos de fin de mes'
        );

        // Traslado de Bodega Central a Piso de Ventas Central
        $inventarioService->registrarTraslado(
            $itemsCreated[8]->id,
            $areaBodegaCentral->id,
            $areaTiendaCentral->id,
            30,
            $adminEmpresa->id,
            'Colocación en estantería para exhibición de tienda'
        );

        // Traslado en Sede Norte: Bodega Norte a Piso de Ventas Norte
        $inventarioService->registrarTraslado(
            $itemsCreated[4]->id,
            $areaBodegaNorte->id,
            $areaTiendaNorte->id,
            10,
            $adminEmpresa->id,
            'Reabastecimiento de mostrador'
        );

        // 12. Salida de inventario por consumo
        $inventarioService->registrarSalida(
            $itemsCreated[0]->id,
            $areaDespachoCentral->id,
            5,
            $encargados[1]->id,
            'Uso interno de papelería en oficina de despacho'
        );

        // 13. Ajuste de inventario (auditoría)
        $inventarioService->registrarAjuste(
            $itemsCreated[8]->id,
            $areaBodegaCentral->id,
            -2,
            $adminEmpresa->id,
            'Auditoría física quincenal: 2 bombillos rotos por caída de tarima'
        );
    }
}
