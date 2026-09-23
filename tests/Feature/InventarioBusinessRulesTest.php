<?php

namespace Tests\Feature;

use App\Models\Area;
use App\Models\Categoria;
use App\Models\Empresa;
use App\Models\Item;
use App\Models\Sucursal;
use App\Models\UnidadMedida;
use App\Models\User;
use App\Services\InventarioService;
use Exception;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InventarioBusinessRulesTest extends TestCase
{
    use RefreshDatabase;

    protected Empresa $empresa;
    protected Sucursal $sucursal;
    protected Area $areaOrigen;
    protected Area $areaDestino;
    protected Item $item;
    protected User $user;
    protected InventarioService $inventarioService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->inventarioService = app(InventarioService::class);

        $this->empresa = Empresa::create([
            'nombre' => 'Test Company',
            'identificacion_fiscal' => '0801999912345',
            'estado' => 'activo',
        ]);

        $this->user = User::create([
            'name' => 'Admin Test',
            'email' => 'admin@test.com',
            'password' => bcrypt('password'),
            'empresa_id' => $this->empresa->id,
            'estado' => 'activo',
        ]);

        $this->sucursal = Sucursal::create([
            'empresa_id' => $this->empresa->id,
            'nombre' => 'Sucursal Principal',
            'estado' => 'activo',
        ]);

        $this->areaOrigen = Area::create([
            'sucursal_id' => $this->sucursal->id,
            'nombre' => 'Bodega Origen',
            'encargado_id' => $this->user->id,
            'estado' => 'activo',
        ]);

        $this->areaDestino = Area::create([
            'sucursal_id' => $this->sucursal->id,
            'nombre' => 'Bodega Destino',
            'encargado_id' => $this->user->id,
            'estado' => 'activo',
        ]);

        $categoria = Categoria::create([
            'empresa_id' => $this->empresa->id,
            'nombre' => 'General',
        ]);

        $unidad = UnidadMedida::create([
            'nombre' => 'Unidad',
            'abreviatura' => 'u',
        ]);

        $this->item = Item::create([
            'empresa_id' => $this->empresa->id,
            'categoria_id' => $categoria->id,
            'unidad_medida_id' => $unidad->id,
            'nombre' => 'Articulo de Prueba',
            'sku' => 'TEST-001',
            'costo_unitario' => 10.00,
            'stock_minimo' => 5,
            'estado' => 'activo',
        ]);
    }

    public function test_entrada_incrementa_stock_correctamente(): void
    {
        $this->inventarioService->registrarEntrada(
            $this->item->id,
            $this->areaOrigen->id,
            50.0,
            $this->user->id,
            'Entrada de prueba'
        );

        $stock = $this->inventarioService->obtenerStock($this->item->id, $this->areaOrigen->id);
        $this->assertEquals(50.0, $stock);
    }

    public function test_traslado_valida_stock_suficiente_en_area_origen(): void
    {
        $this->inventarioService->registrarEntrada(
            $this->item->id,
            $this->areaOrigen->id,
            20.0,
            $this->user->id
        );

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Stock insuficiente en el área origen');

        // Intentar trasladar más de lo disponible (30 > 20)
        $this->inventarioService->registrarTraslado(
            $this->item->id,
            $this->areaOrigen->id,
            $this->areaDestino->id,
            30.0,
            $this->user->id
        );
    }

    public function test_traslado_actualiza_stock_en_ambas_areas_atomicamente(): void
    {
        $this->inventarioService->registrarEntrada(
            $this->item->id,
            $this->areaOrigen->id,
            50.0,
            $this->user->id
        );

        $this->inventarioService->registrarTraslado(
            $this->item->id,
            $this->areaOrigen->id,
            $this->areaDestino->id,
            20.0,
            $this->user->id,
            'Traslado exitoso'
        );

        $stockOrigen = $this->inventarioService->obtenerStock($this->item->id, $this->areaOrigen->id);
        $stockDestino = $this->inventarioService->obtenerStock($this->item->id, $this->areaDestino->id);

        $this->assertEquals(30.0, $stockOrigen);
        $this->assertEquals(20.0, $stockDestino);
    }

    public function test_ajuste_requiere_motivo_obligatorio(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('El motivo del ajuste es estrictamente obligatorio');

        $this->inventarioService->registrarAjuste(
            $this->item->id,
            $this->areaOrigen->id,
            5.0,
            $this->user->id,
            '   '
        );
    }
}
