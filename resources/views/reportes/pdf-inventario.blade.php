<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Inventario Físico</title>
    <style>
        body { font-family: sans-serif; font-size: 10px; color: #333; margin: 0; padding: 10px; }
        .header { margin-bottom: 15px; border-bottom: 2px solid #4f46e5; padding-bottom: 8px; }
        .title { font-size: 16px; font-weight: bold; color: #1e1b4b; }
        .meta { font-size: 9px; color: #666; margin-top: 3px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th { background-color: #f3f4f6; color: #374151; font-weight: bold; padding: 6px 4px; text-align: left; border-bottom: 1px solid #d1d5db; font-size: 9px; text-transform: uppercase; }
        td { padding: 5px 4px; border-bottom: 1px solid #e5e7eb; font-size: 9px; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .font-mono { font-family: monospace; }
        .total-box { margin-top: 15px; text-align: right; font-size: 11px; font-weight: bold; }
    </style>
</head>
<body>
    <div class="header">
        <div class="title">Reporte Oficial de Inventario Físico Consolidado</div>
        <div class="meta">Fecha de emisión: {{ now()->format('d/m/Y H:i:s') }} | Sistema de Control de Inventario para Mipymes</div>
    </div>

    <table>
        <thead>
            <tr>
                <th>SKU</th>
                <th>Ítem</th>
                <th>Categoría</th>
                <th>Sucursal</th>
                <th>Área</th>
                <th>Responsable</th>
                <th class="text-right">Cantidad</th>
                <th class="text-center">Unidad</th>
                <th class="text-right">Costo Unit.</th>
                <th class="text-right">Valorización</th>
            </tr>
        </thead>
        <tbody>
            @foreach($inventarios as $inv)
                <tr>
                    <td class="font-mono">{{ $inv->item ? $inv->item->sku : '-' }}</td>
                    <td><strong>{{ $inv->item ? $inv->item->nombre : '-' }}</strong></td>
                    <td>{{ $inv->item && $inv->item->categoria ? $inv->item->categoria->nombre : '-' }}</td>
                    <td>{{ $inv->area && $inv->area->sucursal ? $inv->area->sucursal->nombre : '-' }}</td>
                    <td>{{ $inv->area ? $inv->area->nombre : '-' }}</td>
                    <td>{{ $inv->area && $inv->area->encargado ? $inv->area->encargado->name : 'Sin asignar' }}</td>
                    <td class="text-right font-mono"><strong>{{ number_format($inv->cantidad, 2) }}</strong></td>
                    <td class="text-center">{{ $inv->item && $inv->item->unidadMedida ? $inv->item->unidadMedida->abreviatura : 'u' }}</td>
                    <td class="text-right">${{ number_format($inv->item ? $inv->item->costo_unitario : 0, 2) }}</td>
                    <td class="text-right font-mono">${{ number_format($inv->cantidad * ($inv->item ? $inv->item->costo_unitario : 0), 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="total-box">
        Valor Total del Inventario Físico: ${{ number_format($totalValor, 2) }}
    </div>
</body>
</html>
