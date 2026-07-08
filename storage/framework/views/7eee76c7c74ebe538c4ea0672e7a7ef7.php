<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title inertia><?php echo e(config('app.name', 'Laravel')); ?></title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        <?php echo app('Tighten\Ziggy\BladeRouteGenerator')->generate(); ?>
        <?php echo app('Illuminate\Foundation\Vite')(['resources/js/app.ts', "resources/js/Pages/{$page['component']}.vue"]); ?>
        <?php $__inertiaSsrResponse = app(\Inertia\Ssr\SsrState::class)->setPage($page)->dispatch();  if ($__inertiaSsrResponse) { echo $__inertiaSsrResponse->head; } ?>

        <!-- Liquid Glass SVG filter (refracción real para .liquid-glass) -->
        <svg style="position:absolute;width:0;height:0;pointer-events:none" aria-hidden="true" focusable="false">
            <defs>
                <filter id="liquid-glass" x="-10%" y="-10%" width="120%" height="120%">
                    <feTurbulence type="fractalNoise" baseFrequency="0.012 0.018" numOctaves="2" seed="3" />
                    <feDisplacementMap in="SourceGraphic" scale="6" />
                    <feGaussianBlur stdDeviation="0.4" />
                </filter>
            </defs>
        </svg>
    </head>
    <body class="font-sans antialiased bg-motorspace">
        <?php $__inertiaSsrResponse = app(\Inertia\Ssr\SsrState::class)->setPage($page)->dispatch();  if ($__inertiaSsrResponse) { echo $__inertiaSsrResponse->body; } else { ?><script data-page="app" type="application/json"><?php echo json_encode($page); ?></script><div id="app"></div><?php } ?>
    </body>
</html>
<?php /**PATH C:\laragon\www\app_garage\resources\views/app.blade.php ENDPATH**/ ?>