<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: 'Helvetica', 'Arial', sans-serif; color: #333; line-height: 1.6; margin: 0; padding: 0; }
        .page { padding: 40px; position: relative; }
        .header { display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid #0a3a2f; padding-bottom: 20px; margin-bottom: 30px; }
        .logo { font-size: 22px; font-weight: bold; color: #0a3a2f; }
        .report-type { text-align: right; font-size: 12px; color: #666; }

        .title { margin-bottom: 30px; }
        .title h1 { font-size: 24px; color: #0a3a2f; margin-bottom: 5px; }
        .title p { font-size: 14px; color: #666; }

        .section { margin-bottom: 25px; page-break-inside: avoid; }
        .section-title { font-size: 15px; font-weight: bold; color: #0a3a2f; background: #f0f4f2; padding: 8px 12px; border-left: 5px solid #0a3a2f; margin-bottom: 15px; text-transform: uppercase; }

        .grid { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        .grid td { padding: 10px; border: 1px solid #eee; font-size: 13px; }
        .label { font-weight: bold; color: #666; background: #fafafa; width: 30%; }
        .value { color: #000; width: 70%; }

        .status-box { display: flex; gap: 10px; margin-bottom: 20px; }
        .status-item { flex: 1; padding: 15px; border: 1px solid #ddd; border-radius: 8px; text-align: center; }
        .status-item.verified { border-color: #0a3a2f; background: #f0fdf4; }
        .status-item.pending { border-color: #ddd; background: #fff; }
        .status-label { font-size: 11px; color: #666; display: block; margin-bottom: 5px; }
        .status-value { font-weight: bold; font-size: 13px; }

        .notes-area { padding: 15px; background: #fffbe6; border: 1px solid #ffe58f; border-radius: 8px; font-size: 13px; font-style: italic; }

        .footer { position: absolute; bottom: 30px; left: 40px; right: 40px; border-top: 1px solid #eee; padding-top: 15px; font-size: 10px; color: #999; text-align: center; }
    </style>
</head>
<body>
    <div class="page">
        <div class="header">
            <div class="logo">GarageOS <span style="font-weight: normal; font-size: 14px;">Audit Report</span></div>
            <div class="report-type">
                Informe Técnico Detallado<br>
                ID: {{ $certificate_id }}<br>
                Fecha: {{ $date }}
            </div>
        </div>

        <div class="title">
            <h1>Análisis Exhaustivo de Vehículo</h1>
            <p>Este documento contiene la auditoría técnica y legal completa realizada por el equipo de GarageOS.</p>
        </div>

        <div class="section">
            <div class="section-title">1. Identificación Técnica</div>
            <table class="grid">
                <tr><td class="label">VIN / Bastidor</td><td class="value"><strong>{{ $import->vin ?? 'NO PROPORCIONADO' }}</strong></td></tr>
                <tr><td class="label">Marca y Modelo</td><td class="value">{{ $import->brand }} {{ $import->model }}</td></tr>
                <tr><td class="label">Año de Fabricación</td><td class="value">{{ $import->year }}</td></tr>
                <tr><td class="label">Matrícula Original</td><td class="value">{{ $import->plate_original }}</td></tr>
                <tr><td class="label">País de Origen</td><td class="value">{{ $import->origin_country }}</td></tr>
            </table>
        </div>

        <div class="section">
            <div class="section-title">2. Matriz de Verificación</div>
            <div class="status-box">
                <div class="status-item {{ $verification->vin_verified ? 'verified' : 'pending' }}">
                    <span class="status-label">VIN</span>
                    <span class="status-value">{{ $verification->vin_verified ? 'VERIFICADO' : 'PENDIENTE' }}</span>
                </div>
                <div class="status-item {{ $verification->ownership_verified ? 'verified' : 'pending' }}">
                    <span class="status-label">TITULARIDAD</span>
                    <span class="status-value">{{ $verification->ownership_verified ? 'VERIFICADO' : 'PENDIENTE' }}</span>
                </div>
                <div class="status-item {{ $verification->technical_data_verified ? 'verified' : 'pending' }}">
                    <span class="status-label">TÉCNICO (CoC)</span>
                    <span class="status-value">{{ $verification->technical_data_verified ? 'VERIFICADO' : 'PENDIENTE' }}</span>
                </div>
                <div class="status-item {{ $verification->itv_verified ? 'verified' : 'pending' }}">
                    <span class="status-label">ITV</span>
                    <span class="status-value">{{ $verification->itv_verified ? 'VERIFICADO' : 'PENDIENTE' }}</span>
                </div>
                <div class="status-item {{ $verification->legal_status_verified ? 'verified' : 'pending' }}">
                    <span class="status-label">LEGAL</span>
                    <span class="status-value">{{ $verification->legal_status_verified ? 'VERIFICADO' : 'PENDIENTE' }}</span>
                </div>
            </div>
        </div>

        <div class="section">
            <div class="section-title">3. Observaciones del Auditor</div>
            <div class="notes-area">
                {{ $verification->notes ?? 'Sin observaciones adicionales proporcionadas por el auditor.' }}
            </div>
        </div>

        <div class="section">
            <div class="section-title">4. Veredicto Final</div>
            <table class="grid">
                <tr>
                    <td class="label">Estado de Importación</td>
                    <td class="value" style="font-size: 16px; font-weight: bold; color: {{ $verification->overall_status === 'approved' ? '#059669' : '#dc2626' }}">
                        {{ strtoupper($verification->overall_status) }}
                    </td>
                </tr>
                <tr>
                    <td class="label">Auditor Responsable</td>
                    <td class="value">{{ $verification->auditor->name }}</td>
                </tr>
                <tr>
                    <td class="label">Fecha de Auditoría</td>
                    <td class="value">{{ $verification->verified_at->format('d/m/Y H:i') }}</td>
                </tr>
            </table>
        </div>

        <div class="footer">
            Este informe es un documento técnico interno de GarageOS. La validez del mismo está sujeta a la verificación del VIN en nuestro portal oficial.<br>
            © {{ date('Y') }} GarageOS - Auditoría de Importaciones Profesionales.
        </div>
    </div>
</body>
</html>
