<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Factura - GarageOS</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; }
        .header { text-align: center; border-bottom: 2px solid #333; padding-bottom: 20px; margin-bottom: 30px; }
        .invoice-details { margin-bottom: 30px; }
        .invoice-details th { text-align: right; padding-right: 10px; }
        .invoice-details td { padding: 5px 0; }
        .items table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .items th, .items td { padding: 10px; text-align: left; border-bottom: 1px solid #ddd; }
        .items th { background: #f5f5f5; }
        .total { text-align: right; font-size: 18px; font-weight: bold; }
    </style>
</head>
<body>
    <div class="header">
        <h1>FACTURA</h1>
        <p>GarageOS - Comisión por Servicio</p>
    </div>

    <div class="invoice-details">
        <table>
            <tr>
                <th>Fecha:</th>
                <td><?php echo e($fee->created_at->format('d/m/Y')); ?></td>
            </tr>
            <tr>
                <th>Factura #:</th>
                <td><?php echo e($fee->id); ?></td>
            </tr>
            <tr>
                <th>Proveedor:</th>
                <td><?php echo e($account->business_profile['name'] ?? $user->name); ?><br><?php echo e($user->email); ?></td>
            </tr>
        </table>
    </div>

    <div class="items">
        <table>
            <thead>
                <tr>
                    <th>Descripción</th>
                    <th>Importe</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><?php echo e($fee->description); ?></td>
                    <td><?php echo e(number_format($fee->amount, 2)); ?> <?php echo e(strtoupper($fee->currency)); ?></td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="total">
        Total: <?php echo e(number_format($fee->amount, 2)); ?> <?php echo e(strtoupper($fee->currency)); ?>

    </div>

    <div style="margin-top: 40px; font-size: 12px; color: #666;">
        <p>Comisión plataforma: <?php echo e($fee->platform_fee_percent); ?>%</p>
        <p>Estado: <?php echo e($fee->processed_at ? 'Pagada' : 'Pendiente'); ?></p>
    </div>
</body>
<?php /**PATH C:\laragon\www\app_garage\resources\views/pdf/invoice.blade.php ENDPATH**/ ?>