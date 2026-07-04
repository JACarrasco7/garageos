<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Informe de Vehículo - {{ $vehicle->brand }} {{ $vehicle->model }}</title>
    <style>
        body { font-family: system-ui, sans-serif; margin: 0; padding: 20px; }
        .header { border-bottom: 2px solid #e5e7eb; padding-bottom: 20px; margin-bottom: 30px; }
        .title { font-size: 24px; font-weight: bold; color: #1f2937; }
        .subtitle { color: #6b7280; margin-top: 5px; }
        .section { margin-bottom: 25px; }
        .section-title { font-size: 18px; font-weight: 600; color: #374151; margin-bottom: 15px; }
        .grid { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; }
        .label { color: #6b7280; font-size: 12px; }
        .value { font-weight: 500; margin-top: 3px; }
        .badge { display: inline-block; padding: 2px 8px; border-radius: 9999px; font-size: 12px; }
        .badge-success { background: #d1fae5; color: #065f46; }
        .badge-warning { background: #fef3c7; color: #92400e; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { text-align: left; padding: 8px; border-bottom: 1px solid #e5e7eb; }
        th { background: #f9fafb; font-weight: 600; }
        .stats-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 15px; }
        .stat-card { background: #f9fafb; padding: 15px; border-radius: 8px; text-align: center; }
        .stat-value { font-size: 24px; font-weight: bold; color: #1f2937; }
        .stat-label { font-size: 12px; color: #6b7280; margin-top: 5px; }
    </style>
</head>
<body>
    <div class="header">
        <div class="title">{{ $vehicle->brand }} {{ $vehicle->model }} ({{ $vehicle->year }})</div>
        <div class="subtitle">Matrícula: {{ $vehicle->plate }} | Informe generado: {{ now()->format('d/m/Y') }}</div>
    </div>

    <div class="section">
        <div class="section-title">Estadísticas</div>
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-value">{{ number_format($stats['total_maintenance_cost'] ?? 0) }} €</div>
                <div class="stat-label">Gasto total mantenimiento</div>
            </div>
            <div class="stat-card">
                <div class="stat-value">{{ number_format($stats['avg_monthly_km'] ?? 0) }} km</div>
                <div class="stat-label">Media mensual</div>
            </div>
            <div class="stat-card">
                <div class="stat-value">{{ $stats['maintenance_count'] ?? 0 }}</div>
                <div class="stat-label">Intervenciones</div>
            </div>
        </div>
    </div>

    <div class="section">
        <div class="section-title">Especificaciones técnicas</div>
        <div class="grid">
            <div>
                <div class="label">Motor</div>
                <div class="value">{{ $vehicle->specs->engine_displacement ?? '-' }} cc</div>
            </div>
            <div>
                <div class="label">Potencia</div>
                <div class="value">{{ $vehicle->specs->power_hp ?? '-' }} CV</div>
            </div>
            <div>
                <div class="label">Cambio</div>
                <div class="value">{{ $vehicle->specs->transmission ?? '-' }}</div>
            </div>
            <div>
                <div class="label">Tracción</div>
                <div class="value">{{ $vehicle->specs->drive ?? '-' }}</div>
            </div>
        </div>
    </div>

    <div class="section">
        <div class="section-title">Documentos ({{ $stats['document_count'] ?? 0 }})</div>
        @if($vehicle->documents->isNotEmpty())
        <table>
            <thead>
                <tr>
                    <th>Tipo</th>
                    <th>Fecha</th>
                    <th>Importe</th>
                </tr>
            </thead>
            <tbody>
                @foreach($vehicle->documents as $doc)
                <tr>
                    <td>{{ $doc->type }}</td>
                    <td>{{ $doc->date?->format('d/m/Y') ?? '-' }}</td>
                    <td>{{ $doc->amount ? number_format($doc->amount, 2).' €' : '-' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @else
        <p style="color: #6b7280;">Sin documentos registrados</p>
        @endif
    </div>

    <div class="section">
        <div class="section-title">Últimas revisiones</div>
        @if($vehicle->maintenanceEntries->isNotEmpty())
        <table>
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Tipo</th>
                    <th>Kilometraje</th>
                    <th>Coste</th>
                </tr>
            </thead>
            <tbody>
                @foreach($vehicle->maintenanceEntries as $entry)
                <tr>
                    <td>{{ $entry->date->format('d/m/Y') }}</td>
                    <td>{{ $entry->type }}</td>
                    <td>{{ number_format($entry->km) }} km</td>
                    <td>{{ number_format($entry->cost, 2) }} €</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @else
        <p style="color: #6b7280;">Sin revisiones registradas</p>
        @endif
    </div>
</body>
</html>
