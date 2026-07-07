<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: 'Helvetica', 'Arial', sans-serif; color: #333; line-height: 1.5; margin: 0; padding: 0; }
        .page { padding: 40px; position: relative; }
        .header { display: flex; justify-content: space-between; align-items: center; border-bottom: 3px solid #0a3a2f; padding-bottom: 20px; margin-bottom: 30px; }
        .logo { font-size: 24px; font-weight: bold; color: #0a3a2f; }
        .cert-id { text-align: right; font-size: 12px; color: #666; }

        .title { text-align: center; margin-bottom: 40px; }
        .title h1 { font-size: 28px; color: #0a3a2f; text-transform: uppercase; margin-bottom: 5px; }
        .title p { font-size: 14px; color: #666; }

        .section { margin-bottom: 30px; }
        .section-title { font-size: 16px; font-weight: bold; color: #0a3a2f; border-left: 4px solid #0a3a2f; padding-left: 10px; margin-bottom: 15px; text-transform: uppercase; }

        .grid { width: 100%; border-collapse: collapse; }
        .grid td { padding: 8px 0; border-bottom: 1px solid #eee; font-size: 13px; }
        .label { font-weight: bold; color: #666; width: 30%; }
        .value { text-align: right; color: #000; }

        .verification-box { margin-top: 30px; padding: 20px; background: #f9f9f9; border: 1px solid #ddd; border-radius: 10px; text-align: center; }
        .status-badge { font-size: 20px; font-weight: bold; color: #0a3a2f; text-transform: uppercase; margin-bottom: 10px; }
        .score-bar { height: 10px; background: #ddd; border-radius: 5px; margin: 10px auto; width: 80%; overflow: hidden; }
        .score-fill { height: 100%; background: #0a3a2f; }

        .footer { position: absolute; bottom: 40px; left: 40px; right: 40px; border-top: 1px solid #eee; padding-top: 20px; font-size: 10px; color: #999; text-align: center; }
        .seal { position: absolute; top: 150px; right: 50px; width: 100px; height: 100px; border: 4px double #0a3a2f; border-radius: 50%; display: flex; align-items: center; justify-content: center; text-align: center; font-size: 10px; font-weight: bold; color: #0a3a2f; transform: rotate(-15deg); opacity: 0.6; }
    </style>
</head>
<body>
    <div class="page">
        <div class="seal">GARAGEOS<br>VERIFIED<br>CERTIFIED</div>

        <div class="header">
            <div class="logo">GarageOS <span style="font-weight: normal; font-size: 14px;">Trust-Seal</span></div>
            <div class="cert-id">
                ID Certificado: {{ $certificate_id }}<br>
                Fecha de Emisión: {{ $date }}
            </div>
        </div>

        <div class="title">
            <h1>Certificado de Verificación de Vehículo</h1>
            <p>Este documento certifica que el vehículo ha sido sometido a un proceso de auditoría técnica y legal.</p>
        </div>

        <div class="section">
            <div class="section-title">Identidad del Vehículo</div>
            <table class="grid">
                <tr><td class="label">Marca</td><td class="value">{{ $import->brand }}</td></tr>
                <tr><td class="label">Modelo</td><td class="value">{{ $import->model }}</td></tr>
                <tr><td class="label">Año</td><td class="value">{{ $import->year }}</td></tr>
                <tr><td class="label">VIN / Bastidor</td><td class="value">{{ $import->vin ?? 'No proporcionado' }}</td></tr>
                <tr><td class="label">Matrícula Original</td><td class="value">{{ $import->plate_original }}</td></tr>
            </table>
        </div>

        <div class="section">
            <div class="section-title">Auditoría de Verificación</div>
            <table class="grid">
                <tr>
                    <td class="label">Verificación de VIN</td>
                    <td class="value">{{ $verification->vin_verified ? '✅ Verificado' : '❌ Pendiente' }}</td>
                </tr>
                <tr>
                    <td class="label">Titularidad y DNI</td>
                    <td class="value">{{ $verification->ownership_verified ? '✅ Verificado' : '❌ Pendiente' }}</td>
                </tr>
                <tr>
                    <td class="label">Datos Técnicos (CoC)</td>
                    <td class="value">{{ $verification->technical_data_verified ? '✅ Verificado' : '❌ Pendiente' }}</td>
                </tr>
                <tr>
                    <td class="label">ITV de Importación</td>
                    <td class="value">{{ $verification->itv_verified ? '✅ Verificado' : '❌ Pendiente' }}</td>
                </tr>
                <tr>
                    <td class="label">Estatus Legal (Cargas)</td>
                    <td class="value">{{ $verification->legal_status_verified ? '✅ Verificado' : '❌ Pendiente' }}</td>
                </tr>
            </table>
        </div>

        <div class="verification-box">
            <div class="status-badge">{{ $verification->overall_status }}</div>
            <div class="score-bar">
                <div class="score-fill" style="width: {{ $score }}%;"></div>
            </div>
            <p style="font-size: 12px; color: #666;">Índice de Confianza: {{ $score }}%</p>
        </div>

        <div class="footer">
            Este certificado es válido únicamente si es verificado a través del portal oficial de GarageOS.<br>
            © {{ date('Y') }} GarageOS - Gestión Inteligente de Importaciones.
        </div>
    </div>
</body>
</html>
