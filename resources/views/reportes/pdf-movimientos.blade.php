<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Movimientos de Inventario</title>
    <style>
        body { font-family: sans-serif; font-size: 10px; color: #333; margin: 0; padding: 10px; }
        .header { margin-bottom: 15px; border-bottom: 2px solid #4f46e5; padding-bottom: 8px; }
        .title { font-size: 16px; font-weight: bold; color: #1e1b4b; }
        .meta { font-size: 9px; color: #666; margin-top: 3px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th { background-color: #f3f4f6; color: #374151; font-weight: bold; padding: 6px 4px; text-align: left; border-bottom: 1px solid #d1d5db; font-size: 9px; text-transform: uppercase; }
        td { padding: 5px 4px; border-bottom: 1px solid #e5e7eb; font-size: 9px; }
        .text-right { text-align: right; }
        .font-mono { font-family: monospace; }
        .badge { font-weight: bold; text-transform: uppercase; font-size: 8px; padding: 2px 4px; }
    </style>
</head>
<body>
    <div class="header">
        <div class="title">Auditoría y Bitácora de Movimientos de Inventario</div>
        <div class="meta">Fecha de emisión: {{ now()->format('d/m/Y H:i:s') }} | Trazabilidad Inmutable</div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Fecha/Hora</th>
                <th>Tipo</th>
                <th>SKU</th>
                <th>Ítem</th>
                <th class="text-right">Cantidad</th>
                <th>Origen</th>
                <th>Destino</th>
                <th>Usuario</th>
                <th>Observación</th>
            </tr>
        </thead>
        <tbody>
            @foreach($movimientos as $mov)
                <tr>
                    <td class="font-mono">{{ $mov->created_at->format('d/m/Y H:i') }}</td>
                    <td><strong>{{ strtoupper($mov->tipo) }}</strong></td>
                    <td class="font-mono">{{ $mov->item ? $mov->item->sku : '-' }}</td>
                    <td>{{ $mov->item ? $mov->item->nombre : '-' }}</td>
                    <td class="text-right font-mono"><strong>{{ number_format($mov->cantidad, 2) }}</strong></td>
                    <td>{{ $mov->areaOrigen ? $mov->areaOrigen->nombre : '-' }}</td>
                    <td>{{ $mov->areaDestino ? $mov->areaDestino->nombre : '-' }}</td>
                    <td>{{ $mov->usuario ? $mov->usuario->name : '-' }}</td>
                    <td>{{ $mov->motivo ?: '-' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
