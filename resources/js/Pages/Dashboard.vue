<script setup lang="ts">
import { Link } from '@inertiajs/vue3'
import WebLayout from '@/layouts/WebLayout.vue'
import { Head } from '@inertiajs/vue3'
import { Card, CardContent, CardHeader, CardTitle } from '@/Components/ui/card'
import { Badge } from '@/Components/ui/badge'
import { Button } from '@/Components/ui/button'
import StatCard from '@/Components/StatCard.vue'
import EmptyState from '@/Components/EmptyState.vue'
import { Bell, Plus, Car, Wrench, Calendar, ArrowRight, Shield } from 'lucide-vue-next'
import { cn } from '@/lib/utils'

interface Vehicle {
  id: number
  plate: string
  brand: string
  model: string
  year: number
  current_km: number
  is_active: boolean
}

interface Alert {
  id: number
  type: string
  title: string
  body: string
  read_at: string | null
}

defineProps<{
  vehicles?: Vehicle[]
  alerts?: Alert[]
  stats?: {
    total_vehicles: number
    total_documents: number
    pending_alerts: number
  }
}>()
</script>

<template>
  <Head title="Dashboard - Glassmorphism" />

  <WebLayout>
    <template #header>
      <!-- Dashboard Glassmorphism -->
    </template>

    <!-- Liquid Glass Hero -->
    <div class="mb-8 flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
      <div class="glass-liquid rounded-3xl p-8">
        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-primary/90">
          Panel principal
        </p>
        <h1 class="mt-2 text-4xl font-bold tracking-tight text-foreground">
          Hola, <span class="text-gradient">conductor</span>
        </h1>
        <p class="mt-1.5 text-base text-muted-foreground">
          {{ vehicles?.length ?? 0 }} {{ (vehicles?.length ?? 0) === 1 ? 'vehículo registrado' : 'vehículos registrados' }}
          · vista general de tu flota
        </p>
      </div>
      <Button
        as-child
        class="glass-button inline-flex items-center gap-2 rounded-2xl px-6 py-3 text-sm font-semibold"
      >
        <Link :href="route('vehicles.wizard')">
          <Plus class="h-5 w-5" />
          Añadir vehículo
        </Link>
      </Button>
    </div>

    <!-- KPI Glass Grid -->
    <div class="mb-8 grid grid-cols-1 gap-6 md:grid-cols-3">
      <StatCard
        title="Vehículos"
        :value="stats?.total_vehicles ?? 0"
        :icon="Car"
        accent="primary"
        :description="(vehicles?.filter(v => v.is_active).length ?? 0) + ' activos'"
        :href="route('vehicles.index')"
      />
      <StatCard
        title="Documentos"
        :value="stats?.total_documents ?? 0"
        :icon="Shield"
        accent="accent"
        description="ITV, seguro, permiso de circulación"
      />
      <StatCard
        title="Alertas pendientes"
        :value="stats?.pending_alerts ?? 0"
        :icon="Bell"
        accent="destructive"
        :pulse="(stats?.pending_alerts ?? 0) > 0"
        description="Mantenimiento y avisos"
        :href="route('alerts.index' as any)"
      />
    </div>

    <!-- Glass Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
      <!-- Vehículos -->
      <div class="glass-panel p-6">
        <div class="flex flex-row items-center justify-between pb-4">
          <h3 class="text-xl font-bold tracking-tight text-foreground">
            Mis Vehículos
          </h3>
          <Button as-child variant="ghost" size="sm" class="rounded-xl glass-tab">
            <Link :href="route('vehicles.index')" class="flex items-center gap-1.5">
              Ver todos
              <ArrowRight class="h-4 w-4" />
            </Link>
          </Button>
        </div>
        <div class="space-y-4">
          <EmptyState
            v-if="!vehicles?.length"
            :icon="Car"
            title="Aún no tienes vehículos"
            description="Registra tu primer vehículo para empezar a llevar el control de mantenimiento, alertas y documentos."
            action-label="Añadir primer vehículo"
            :action-href="route('vehicles.create')"
            compact
          />

          <div v-else class="space-y-3">
            <Link
              v-for="vehicle in vehicles"
              :key="vehicle.id"
              :href="route('vehicles.show', vehicle.id)"
              class="glass-item group flex items-center justify-between rounded-2xl p-4"
            >
              <div class="flex items-center gap-4">
                <div class="glass-icon flex h-12 w-12 items-center justify-center rounded-2xl">
                  <Car class="h-6 w-6 text-primary" />
                </div>
                <div>
                  <p class="font-bold text-lg text-foreground group-hover:text-primary transition-colors">
                    {{ vehicle.brand }} {{ vehicle.model }}
                  </p>
                  <p class="text-sm text-muted-foreground">
                    {{ vehicle.year }} · {{ vehicle.plate }}
                  </p>
                </div>
              </div>
              <div class="flex items-center gap-5">
                <div class="text-right">
                  <p class="text-lg font-bold tabular-nums text-foreground">
                    {{ vehicle.current_km.toLocaleString() }}
                  </p>
                  <p class="text-xs uppercase tracking-wider text-muted-foreground">km</p>
                </div>
                <Badge :variant="vehicle.is_active ? 'default' : 'secondary'" class="glass-badge">
                  {{ vehicle.is_active ? 'Activo' : 'Inactivo' }}
                </Badge>
                <ArrowRight class="h-5 w-5 text-muted-foreground/50 group-hover:translate-x-1 transition-transform" />
              </div>
            </Link>
          </div>
        </div>
      </div>

      <!-- Alertas -->
      <div class="glass-panel">
        <Card class="border-0 bg-transparent">
          <CardHeader class="flex flex-row items-center justify-between pb-3">
            <CardTitle class="text-xl font-bold tracking-tight text-foreground">
              Alertas recientes
            </CardTitle>
            <Button as-child variant="ghost" size="sm" class="rounded-xl glass-tab">
              <Link :href="route('alerts.index' as any)" class="flex items-center gap-1.5">
                Ver todas
                <ArrowRight class="h-4 w-4" />
              </Link>
            </Button>
          </CardHeader>
          <CardContent>
            <EmptyState
              v-if="!alerts?.length"
              :icon="Calendar"
              title="¡Todo al día!"
              description="No tienes alertas pendientes. Tu flota está en perfecto estado."
              compact
            />

            <div v-else class="space-y-3">
              <div
                v-for="alert in alerts"
                :key="alert.id"
                class="glass-alert flex items-start gap-4 rounded-2xl p-4"
              >
                <div class="glass-alert-icon flex h-11 w-11 items-center justify-center rounded-2xl shrink-0">
                  <Wrench class="h-5 w-5 text-primary-foreground" />
                </div>
                <div class="flex-1 min-w-0">
                  <div class="flex items-center justify-between gap-2 mb-1">
                    <h4 class="font-bold text-sm text-foreground">
                      {{ alert.title }}
                    </h4>
                    <Badge variant="outline" class="text-[10px] uppercase tracking-wider shrink-0 glass-badge-outline">
                      {{ alert.type }}
                    </Badge>
                  </div>
                  <p class="text-sm text-muted-foreground">
                    {{ alert.body }}
                  </p>
                </div>
              </div>
            </div>
          </CardContent>
        </Card>
      </div>
    </div>
  </WebLayout>
</template>

<style scoped>
.glass-liquid {
  background: rgba(255, 255, 255, 0.12);
  backdrop-filter: blur(32px);
  border: 1px solid rgba(255, 255, 255, 0.25);
  box-shadow: 0 8px 48px rgba(0, 0, 0, 0.25);
}

.glass-button {
  background: rgba(255, 255, 255, 0.15);
  backdrop-filter: blur(20px);
  border: 1px solid rgba(255, 255, 255, 0.3);
  box-shadow: 0 8px 32px rgba(0, 0, 0, 0.25);
  transition: all 0.3s ease;
}

.glass-button:hover {
  background: rgba(255, 255, 255, 0.25);
  transform: translateY(-2px);
  box-shadow: 0 12px 40px rgba(0, 0, 0, 0.35);
}

.glass-kpi {
  background: rgba(255, 255, 255, 0.1);
  backdrop-filter: blur(24px);
  border-radius: 28px;
  border: 1px solid rgba(255, 255, 255, 0.2);
  padding: 3px;
  box-shadow: 0 8px 32px rgba(0, 0, 0, 0.15);
}

.glass-panel {
  background: rgba(255, 255, 255, 0.12);
  backdrop-filter: blur(32px);
  border-radius: 32px;
  border: 1px solid rgba(255, 255, 255, 0.2);
  box-shadow: 0 12px 48px rgba(0, 0, 0, 0.2);
  padding: 3px;
}

.glass-item {
  background: rgba(255, 255, 255, 0.08);
  backdrop-filter: blur(16px);
  border: 1px solid rgba(255, 255, 255, 0.15);
  transition: all 0.3s ease;
}

.glass-item:hover {
  background: rgba(255, 255, 255, 0.18);
  border-color: rgba(var(--primary), 0.5);
}

.glass-icon {
  background: rgba(var(--primary), 0.25);
  backdrop-filter: blur(12px);
}

.glass-badge {
  background: rgba(255, 255, 255, 0.2);
  backdrop-filter: blur(10px);
}

.glass-alert {
  background: rgba(245, 158, 11, 0.12);
  backdrop-filter: blur(16px);
  border: 1px solid rgba(245, 158, 11, 0.35);
}

.glass-alert:hover {
  background: rgba(245, 158, 11, 0.2);
}

.glass-alert-icon {
  background: linear-gradient(135deg, rgb(245, 158, 11), rgb(219, 127, 27));
  box-shadow: 0 6px 20px rgba(245, 158, 11, 0.4);
}

.glass-tab {
  background: rgba(255, 255, 255, 0.12);
  backdrop-filter: blur(12px);
}

.glass-badge-outline {
  background: rgba(255, 255, 255, 0.15);
  backdrop-filter: blur(8px);
}
</style>
