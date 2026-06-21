<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Informe de Venta - {{ $vehicle->brand }} {{ $vehicle->model }}</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; }
        .header { text-align: center; border-bottom: 2px solid #333; padding-bottom: 20px; margin-bottom: 30px; }
        .score { font-size: 48px; font-weight: bold; color: {{ $score >= 70 ? '#22c55e' : ($score >= 40 ? '#f59e0b' : '#ef4444') }}; }
        .section { margin-bottom: 20px; }
        .section h3 { border-bottom: 1px solid #ddd; padding-bottom: 5px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 8px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background: #f5f5f5; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Informe de Venta</h1>
        <h2>{{ $vehicle->brand }} {{ $vehicle->model }}</h2>
        <p>Matrícula: {{ $vehicle->plate }} | Año: {{ $vehicle->year }}</p>
    </div>

    <div class="section">
        <h3>Puntuación de Salud</h3>
        <div class="score">{{ $score }}/100</div>
    </div>

    <div class="section">
        <h3>Datos del Vehículo</h3>
        <table>
            <tr><th>Marca</th><td>{{ $vehicle->brand }}</td></tr>
            <tr><th>Modelo</th><td>{{ $vehicle->model }}</td></tr>
            <tr><th>Año</th><td>{{ $vehicle->year }}</td></tr>
            <tr><th>Kilómetros</th><td>{{ number_format($vehicle->current_km) }} km</td></tr>
            <tr><th>Combustible</th><td>{{ ucfirst($vehicle->fuel_type) }}</td></tr>
            @if($vehicle->specs)
                <tr><th>Cilindrada</th><td>{{ $vehicle->specs->engine_cc }} cc</td></tr>
                <tr><th>Potencia</th><td>{{ $vehicle->specs->power_hp }} CV</td></tr>
            @endif
        </table>
    </div>

    <div class="section">
        <h3>Historial de Mantenimiento ({{ $vehicle->maintenanceEntries->count() }})</h3>
        <table>
            <thead>
                <tr><th>Fecha</th><th>Tipo</th><th>Título</th><th>Coste</th></tr>
            </thead>
            <tbody>
                @foreach($vehicle->maintenanceEntries as $entry)
                    <tr>
                        <td>{{ $entry->service_date->format('d/m/Y') }}</td>
                        <td>{{ ucfirst($entry->type) }}</td>
                        <td>{{ $entry->title }}</td>
                        <td>{{ $entry->cost ? number_format($entry->cost, 2) . ' €' : '-' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="section">
        <h3>Documentos ({{ $vehicle->documents->count() }})</h3>
        <table>
            <thead>
                <tr><th>Tipo</th><th>Título</th><th>Fecha</th><th>Vence</th></tr>
            </thead>
            <tbody>
                @foreach($vehicle->documents as $doc)
                    <tr>
                        <td>{{ ucfirst($doc->type) }}</td>
                        <td>{{ $doc->title }}</td>
                        <td>{{ $doc->document_date?->format('d/m/Y') ?? '-' }}</td>
                        <td>{{ $doc->expiry_date?->format('d/m/Y') ?? '-' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div style="margin-top: 40px; text-align: center; color: #666; font-size: 12px;">
        <p>Generado el {{ now()->format('d/m/Y H:i') }} | GarageOS</p>
    </div>
</body>
</html>
