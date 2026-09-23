<?php

namespace Database\Seeders;

use App\Models\UnidadMedida;
use Illuminate\Database\Seeder;

class UnidadesMedidaSeeder extends Seeder
{
    public function run(): void
    {
        $unidades = [
            ['nombre' => 'Unidad', 'abreviatura' => 'u'],
            ['nombre' => 'Caja', 'abreviatura' => 'caja'],
            ['nombre' => 'Kilogramo', 'abreviatura' => 'kg'],
            ['nombre' => 'Litro', 'abreviatura' => 'L'],
            ['nombre' => 'Metro', 'abreviatura' => 'm'],
            ['nombre' => 'Paquete', 'abreviatura' => 'paq'],
            ['nombre' => 'Galón', 'abreviatura' => 'gal'],
            ['nombre' => 'Bolsa', 'abreviatura' => 'bolsa'],
        ];

        foreach ($unidades as $u) {
            UnidadMedida::firstOrCreate(['abreviatura' => $u['abreviatura']], $u);
        }
    }
}
