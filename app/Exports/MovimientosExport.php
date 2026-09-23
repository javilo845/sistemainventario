<?php

namespace App\Exports;

use App\Models\MovimientoInventario;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class MovimientosExport implements FromCollection, WithHeadings, WithMapping
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
            'Fecha y Hora',
            'Tipo',
            'SKU',
            'Ítem',
            'Cantidad',
            'Unidad',
            'Área Origen',
            'Área Destino',
            'Usuario Responsable',
            'Motivo / Observación',
        ];
    }

    /**
     * @param MovimientoInventario $mov
     */
    public function map($mov): array
    {
        return [
            $mov->created_at->format('d/m/Y H:i:s'),
            strtoupper($mov->tipo),
            $mov->item ? $mov->item->sku : 'N/A',
            $mov->item ? $mov->item->nombre : 'N/A',
            $mov->cantidad,
            $mov->item && $mov->item->unidadMedida ? $mov->item->unidadMedida->abreviatura : 'u',
            $mov->areaOrigen ? $mov->areaOrigen->nombre : '-',
            $mov->areaDestino ? $mov->areaDestino->nombre : '-',
            $mov->usuario ? $mov->usuario->name : 'N/A',
            $mov->motivo ?: '-',
        ];
    }
}
