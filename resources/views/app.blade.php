<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title inertia>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @routes
        @vite(['resources/js/app.ts', "resources/js/Pages/{$page['component']}.vue"])
        @inertiaHead

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
        @inertia
    </body>
</html>
