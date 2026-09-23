<?php

namespace App\Exports;

use App\Models\InventarioArea;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class InventarioExport implements FromCollection, WithHeadings, WithMapping
{
    public function __construct(
        protected $query
    ) {}

    public function collection()
    {
        return $this->query->get();
    }

    public function headings(): array
    {
        return [
            'SKU',
            'Ítem',
            'Categoría',
            'Sucursal',
            'Área',
            'Encargado Responsable',
            'Cantidad Física',
            'Unidad',
            'Costo Unitario ($)',
            'Valor Total ($)',
        ];
    }

    /**
     * @param InventarioArea $inv
     */
    public function map($inv): array
    {
        $valorTotal = $inv->cantidad * ($inv->item ? $inv->item->costo_unitario : 0);

        return [
            $inv->item ? $inv->item->sku : 'N/A',
            $inv->item ? $inv->item->nombre : 'N/A',
            $inv->item && $inv->item->categoria ? $inv->item->categoria->nombre : 'N/A',
            $inv->area && $inv->area->sucursal ? $inv->area->sucursal->nombre : 'N/A',
            $inv->area ? $inv->area->nombre : 'N/A',
            $inv->area && $inv->area->encargado ? $inv->area->encargado->name : 'Sin asignar',
            $inv->cantidad,
            $inv->item && $inv->item->unidadMedida ? $inv->item->unidadMedida->abreviatura : 'u',
            $inv->item ? $inv->item->costo_unitario : 0,
            $valorTotal,
        ];
    }
}
