<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Informe de Venta - <?php echo e($vehicle->brand); ?> <?php echo e($vehicle->model); ?></title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; }
        .header { text-align: center; border-bottom: 2px solid #333; padding-bottom: 20px; margin-bottom: 30px; }
        .score { font-size: 48px; font-weight: bold; color: <?php echo e($score >= 70 ? '#22c55e' : ($score >= 40 ? '#f59e0b' : '#ef4444')); ?>; }
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
        <h2><?php echo e($vehicle->brand); ?> <?php echo e($vehicle->model); ?></h2>
        <p>Matrícula: <?php echo e($vehicle->plate); ?> | Año: <?php echo e($vehicle->year); ?></p>
    </div>

    <div class="section">
        <h3>Puntuación de Salud</h3>
        <div class="score"><?php echo e($score); ?>/100</div>
    </div>

    <div class="section">
        <h3>Datos del Vehículo</h3>
        <table>
            <tr><th>Marca</th><td><?php echo e($vehicle->brand); ?></td></tr>
            <tr><th>Modelo</th><td><?php echo e($vehicle->model); ?></td></tr>
            <tr><th>Año</th><td><?php echo e($vehicle->year); ?></td></tr>
            <tr><th>Kilómetros</th><td><?php echo e(number_format($vehicle->current_km)); ?> km</td></tr>
            <tr><th>Combustible</th><td><?php echo e(ucfirst($vehicle->fuel_type->value)); ?></td></tr>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($vehicle->specs): ?>
                <tr><th>Cilindrada</th><td><?php echo e($vehicle->specs->engine_cc); ?> cc</td></tr>
                <tr><th>Potencia</th><td><?php echo e($vehicle->specs->power_hp); ?> CV</td></tr>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </table>
    </div>

    <div class="section">
        <h3>Historial de Mantenimiento (<?php echo e($vehicle->maintenanceEntries->count()); ?>)</h3>
        <table>
            <thead>
                <tr><th>Fecha</th><th>Tipo</th><th>Título</th><th>Coste</th></tr>
            </thead>
            <tbody>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $vehicle->maintenanceEntries; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $entry): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                    <tr>
                        <td><?php echo e($entry->service_date->format('d/m/Y')); ?></td>
                        <td><?php echo e(ucfirst($entry->type)); ?></td>
                        <td><?php echo e($entry->title); ?></td>
                        <td><?php echo e($entry->cost ? number_format($entry->cost, 2) . ' €' : '-'); ?></td>
                    </tr>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
            </tbody>
        </table>
    </div>

    <div class="section">
        <h3>Documentos (<?php echo e($vehicle->documents->count()); ?>)</h3>
        <table>
            <thead>
                <tr><th>Tipo</th><th>Título</th><th>Fecha</th><th>Vence</th></tr>
            </thead>
            <tbody>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $vehicle->documents; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $doc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                    <tr>
                        <td><?php echo e(ucfirst($doc->type)); ?></td>
                        <td><?php echo e($doc->title); ?></td>
                        <td><?php echo e($doc->document_date?->format('d/m/Y') ?? '-'); ?></td>
                        <td><?php echo e($doc->expiry_date?->format('d/m/Y') ?? '-'); ?></td>
                    </tr>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
            </tbody>
        </table>
    </div>

    <div style="margin-top: 40px; text-align: center; color: #666; font-size: 12px;">
        <p>Generado el <?php echo e(now()->format('d/m/Y H:i')); ?> | GarageOS</p>
    </div>
</body>
</html>
<?php /**PATH C:\laragon\www\app_garage\resources\views/pdf/sale-report.blade.php ENDPATH**/ ?>