<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Certificado de Importación - GarageOS</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; }
        .header { text-align: center; border-bottom: 2px solid #333; padding-bottom: 20px; margin-bottom: 30px; }
        .section { margin-bottom: 25px; }
        .section h2 { color: #2563eb; border-bottom: 1px solid #ddd; padding-bottom: 5px; }
        .vehicle-info table { width: 100%; }
        .vehicle-info td { padding: 8px 0; }
        .label { font-weight: bold; width: 200px; }
        .plate { font-size: 24px; font-weight: bold; color: #2563eb; }
        .valuation { background: #f8fafc; padding: 15px; border-radius: 5px; }
        .total { font-size: 20px; font-weight: bold; color: #059669; }
    </style>
</head>
<body>
    <div class="header">
        <h1>CERTIFICADO DE IMPORTACIÓN</h1>
        <p>GarageOS - Vehículo Importado Alemania → España</p>
    </div>

    <div class="section vehicle-info">
        <h2>Datos del Vehículo</h2>
        <table>
            <tr>
                <td class="label">Marca:</td>
                <td><?php echo e($import->brand); ?></td>
            </tr>
            <tr>
                <td class="label">Modelo:</td>
                <td><?php echo e($import->model); ?></td>
            </tr>
            <tr>
                <td class="label">Año:</td>
                <td><?php echo e($import->year); ?></td>
            </tr>
            <tr>
                <td class="label">Matrícula Original (DE):</td>
                <td><?php echo e($import->plate_original ?? 'Pendiente'); ?></td>
            </tr>
            <tr>
                <td class="label">Matrícula Española (ES):</td>
                <td class="plate"><?php echo e($plate_new); ?></td>
            </tr>
            <tr>
                <td class="label">País Origen:</td>
                <td><?php echo e($import->origin_country); ?></td>
            </tr>
        </table>
    </div>

    <div class="section valuation">
        <h2>Valoración y Costes Estimados</h2>
        <table>
            <tr>
                <td>Valor estimado vehículo:</td>
                <td><?php echo e(number_format($valuation['valuation']->estimated_value, 2)); ?>€</td>
            </tr>
            <tr>
                <td>IEDMT (impuesto CO₂):</td>
                <td><?php echo e(number_format($valuation['taxes']['iedmt'], 2)); ?>€</td>
            </tr>
            <tr>
                <td>ITP estimado:</td>
                <td><?php echo e(number_format($valuation['taxes']['itp_estimate'], 2)); ?>€</td>
            </tr>
            <tr>
                <td class="total">Coste total estimado:</td>
                <td class="total"><?php echo e(number_format($valuation['total_cost'], 2)); ?>€</td>
            </tr>
        </table>
    </div>

    <div class="section">
        <h2>Información</h2>
        <p><strong>Fecha certificado:</strong> <?php echo e(now()->format('d/m/Y')); ?></p>
        <p><strong>Estado importación:</strong> <?php echo e($import->current_step->getLabel()); ?></p>
        <p>Este certificado es informativo y no sustituye la documentación oficial.</p>
    </div>
</body>
<?php /**PATH C:\laragon\www\app_garage\resources\views/pdf/import-certificate.blade.php ENDPATH**/ ?>