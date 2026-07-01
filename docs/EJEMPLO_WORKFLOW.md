# Ejemplo: Dashboard de Vehículos

Este ejemplo muestra cómo usar OpenDesigner + Copilot para crear una página de GarageOS.

## Paso 1: Generar diseño con OpenDesigner

**Prompt en OpenDesigner:**
```
Dashboard principal de GarageOS con estadísticas de vehículos.
Cards: total vehículos, activos, mantenimiento pendiente, alertas.
Grid responsive con iconos y números grandes.
Paleta: Nürburgring Green (#0A3A2F), McLaren Orange (#FF8000).
Glassmorphism en header y cards.
```

## Paso 2: HTML/Tailwind generado por OpenDesigner

```html
<div class="min-h-screen bg-background">
  <!-- Header -->
  <header class="sticky top-0 z-30 h-16 border-b border-border/50 bg-card/90 backdrop-blur-xl px-6">
    <div class="flex items-center justify-between">
      <div class="flex items-center gap-3">
        <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-gradient-to-br from-primary to-primary/80">
          <svg class="h-5 w-5 text-primary-foreground" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3" />
          </svg>
        </div>
        <span class="font-bold text-lg">GarageOS</span>
      </div>
    </div>
  </header>

  <!-- Main -->
  <main class="container mx-auto max-w-7xl px-6 py-8">
    <h1 class="mb-6 text-3xl font-bold text-foreground">Dashboard</h1>

    <!-- Stats Grid -->
    <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-4">
      <!-- Card 1 -->
      <div class="group rounded-xl border border-border/50 bg-card p-6 shadow-sm hover:-translate-y-1 hover:shadow-lg hover:shadow-primary/5 transition-all duration-300">
        <div class="flex items-center justify-between">
          <div class="h-12 w-12 rounded-lg bg-gradient-to-br from-primary/10 to-accent/10 flex items-center justify-center">
            <svg class="h-6 w-6 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3" />
            </svg>
          </div>
          <span class="rounded-full bg-accent px-2 py-1 text-xs font-semibold text-accent-foreground">+12%</span>
        </div>
        <div class="mt-4">
          <p class="text-sm text-muted-foreground">Total Vehículos</p>
          <p class="text-3xl font-bold text-foreground">24</p>
        </div>
      </div>

      <!-- Card 2 -->
      <div class="group rounded-xl border border-border/50 bg-card p-6 shadow-sm hover:-translate-y-1 hover:shadow-lg hover:shadow-primary/5 transition-all duration-300">
        <div class="flex items-center justify-between">
          <div class="h-12 w-12 rounded-lg bg-gradient-to-br from-primary/10 to-accent/10 flex items-center justify-center">
            <svg class="h-6 w-6 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
          </div>
        </div>
        <div class="mt-4">
          <p class="text-sm text-muted-foreground">Activos</p>
          <p class="text-3xl font-bold text-foreground">18</p>
        </div>
      </div>

      <!-- Card 3 -->
      <div class="group rounded-xl border border-border/50 bg-card p-6 shadow-sm hover:-translate-y-1 hover:shadow-lg hover:shadow-primary/5 transition-all duration-300">
        <div class="flex items-center justify-between">
          <div class="h-12 w-12 rounded-lg bg-gradient-to-br from-primary/10 to-accent/10 flex items-center justify-center">
            <svg class="h-6 w-6 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
          </div>
          <span class="rounded-full bg-destructive px-2 py-1 text-xs font-semibold text-destructive-foreground">3</span>
        </div>
        <div class="mt-4">
          <p class="text-sm text-muted-foreground">Mantenimiento</p>
          <p class="text-3xl font-bold text-foreground">5</p>
        </div>
      </div>

      <!-- Card 4 -->
      <div class="group rounded-xl border border-border/50 bg-card p-6 shadow-sm hover:-translate-y-1 hover:shadow-lg hover:shadow-primary/5 transition-all duration-300">
        <div class="flex items-center justify-between">
          <div class="h-12 w-12 rounded-lg bg-gradient-to-br from-primary/10 to-accent/10 flex items-center justify-center">
            <svg class="h-6 w-6 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
            </svg>
          </div>
        </div>
        <div class="mt-4">
          <p class="text-sm text-muted-foreground">Alertas</p>
          <p class="text-3xl font-bold text-foreground">2</p>
        </div>
      </div>
    </div>
  </main>
</div>
```

## Paso 3: Convertir a Vue 3 + Inertia + shadcn-vue

**Prompt para Copilot:**
```
Convierte este HTML a una página Vue 3 con Inertia y shadcn-vue para GarageOS.
- Usa components: Card, Badge
- Usa iconos: lucide-vue-next (Car, CheckCircle, Clock, Bell)
- Implementa en resources/js/Pages/Vehicle/Dashboard.vue
- Usa el layout AppSidebarLayout
```

**Resultado en resources/js/Pages/Vehicle/Dashboard.vue:**
```vue
<script setup lang="ts">
import AppSidebarLayout from '@/layouts/app/AppSidebarLayout.vue'
import { Head } from '@inertiajs/vue3'
import { Card, CardContent } from '@/Components/ui/card'
import { Badge } from '@/Components/ui/badge'
import { Car, CheckCircle, Clock, Bell } from 'lucide-vue-next'

interface StatCard {
  title: string
  value: number
  icon: any
  badge?: string
  badgeVariant?: 'default' | 'destructive'
}

const stats: StatCard[] = [
  {
    title: 'Total Vehículos',
    value: 24,
    icon: Car,
    badge: '+12%',
  },
  {
    title: 'Activos',
    value: 18,
    icon: CheckCircle,
  },
  {
    title: 'Mantenimiento',
    value: 5,
    icon: Clock,
    badge: '3',
    badgeVariant: 'destructive',
  },
  {
    title: 'Alertas',
    value: 2,
    icon: Bell,
  },
]
</script>

<template>
  <Head title="Dashboard" />

  <AppSidebarLayout>
    <template #header>
      Dashboard
    </template>

    <div class="container mx-auto max-w-7xl px-6 py-8">
      <h1 class="mb-6 text-3xl font-bold text-foreground">
        Dashboard
      </h1>

      <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-4">
        <Card
          v-for="stat in stats"
          :key="stat.title"
          class="group hover:-translate-y-1 hover:shadow-lg hover:shadow-primary/5 transition-all duration-300 border-border/50"
        >
          <CardContent class="p-6">
            <div class="flex items-center justify-between">
              <div class="h-12 w-12 rounded-lg bg-gradient-to-br from-primary/10 to-accent/10 flex items-center justify-center">
                <component :is="stat.icon" class="h-6 w-6 text-primary" />
              </div>
              <Badge v-if="stat.badge" :variant="stat.badgeVariant">
                {{ stat.badge }}
              </Badge>
            </div>
            <div class="mt-4">
              <p class="text-sm text-muted-foreground">
                {{ stat.title }}
              </p>
              <p class="text-3xl font-bold text-foreground">
                {{ stat.value }}
              </p>
            </div>
          </CardContent>
        </Card>
      </div>
    </div>
  </AppSidebarLayout>
</template>
```

## Paso 4: Añadir ruta

```php
// routes/web.php
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');
});
```

## Paso 5: Crear Controller

```bash
php artisan make:controller DashboardController
```

```php
// app/Http/Controllers/DashboardController.php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        return Inertia::render('Vehicle/Dashboard', [
            'stats' => [
                ['title' => 'Total Vehículos', 'value' => 24, 'badge' => '+12%'],
                ['title' => 'Activos', 'value' => 18],
                ['title' => 'Mantenimiento', 'value' => 5, 'badge' => '3'],
                ['title' => 'Alertas', 'value' => 2],
            ],
        ]);
    }
}
```

## Resumen del Flujo

1. **OpenDesigner**: Genera HTML/Tailwind con el design system
2. **Copilot**: Convierte a Vue 3 + Inertia + shadcn-vue
3. **Integración**: Conecta con el backend de Laravel
4. **Resultado**: Página funcional en GarageOS

## Archivos Clave

- [DESIGN_SYSTEM.md](DESIGN_SYSTEM.md) - Tokens y patrones
- [resources/css/app.css](resources/css/app.css) - Variables CSS
- [docs/OPENDESIGNER_WORKFLOW.md](OPENDESIGNER_WORKFLOW.md) - Workflow completo
