<script setup lang="ts">
import { Link } from '@inertiajs/vue3'
import AppSidebarLayout from '@/layouts/app/AppSidebarLayout.vue'
import { Head } from '@inertiajs/vue3'
import { Badge } from '@/Components/ui/badge'
import { Button } from '@/Components/ui/button'
import StatCard from '@/Components/StatCard.vue'
import EmptyState from '@/Components/EmptyState.vue'
import { Bell, Plus, Car, Wrench, Calendar, Shield } from 'lucide-vue-next'

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
  <Head title="Dashboard - Minimal" />

  <AppSidebarLayout>
    <template #header>
      Dashboard Minimal
    </template>

    <!-- Space Hero -->
    <div class="mb-16 flex flex-col gap-6 sm:flex-row sm:items-end sm:justify-between">
      <div>
        <p class="text-xs font-medium uppercase tracking-[0.2em] text-muted-foreground mb-4">
          Panel principal
        </p>
        <h1 class="text-5xl md:text-6xl font-extralight tracking-tighter text-foreground">
          Hola, conductor
        </h1>
        <p class="mt-3 text-lg text-muted-foreground">
          {{ vehicles?.length ?? 0 }} {{ (vehicles?.length ?? 0) === 1 ? 'vehículo registrado' : 'vehículos registrados' }}
        </p>
      </div>
      <Button
        as-child
        variant="default"
        class="rounded-full px-10 py-4 text-base font-medium"
      >
        <Link :href="route('vehicles.wizard')">
          <Plus class="h-5 w-5" />
          Añadir vehículo
        </Link>
      </Button>
    </div>

    <!-- Space KPI Grid -->
    <div class="mb-16 grid grid-cols-1 gap-8 md:grid-cols-3">
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
        description="ITV, seguro, permiso"
      />
      <StatCard
        title="Alertas"
        :value="stats?.pending_alerts ?? 0"
        :icon="Bell"
        accent="destructive"
        :pulse="(stats?.pending_alerts ?? 0) > 0"
      />
    </div>

    <!-- Space Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
      <!-- Vehículos -->
      <div>
        <div class="flex items-center justify-between mb-6">
          <h2 class="text-3xl font-extralight tracking-tight text-foreground">
            Mis Vehículos
          </h2>
          <Link
            :href="route('vehicles.index')"
            class="text-base font-medium text-primary hover:underline"
          >
            Ver todos
          </Link>
        </div>

        <EmptyState
          v-if="!vehicles?.length"
          :icon="Car"
          title="Sin vehículos"
          description="Registra tu primer vehículo."
          action-label="Añadir vehículo"
          :action-href="route('vehicles.create')"
          compact
        />

        <div v-else class="space-y-4">
          <Link
            v-for="vehicle in vehicles"
            :key="vehicle.id"
            :href="route('vehicles.show', vehicle.id)"
            class="group flex items-center justify-between py-4 border-b border-border/30 hover:border-primary transition-colors"
          >
            <div class="flex items-center gap-6">
              <div class="flex h-12 w-12 items-center justify-center rounded-full bg-primary/10">
                <Car class="h-6 w-6 text-primary" />
              </div>
              <div>
                <p class="font-medium text-xl text-foreground">
                  {{ vehicle.brand }} {{ vehicle.model }}
                </p>
                <p class="text-base text-muted-foreground">
                  {{ vehicle.year }} · {{ vehicle.plate }}
                </p>
              </div>
            </div>
            <div class="flex items-center gap-8">
              <span class="text-xl font-mono text-foreground">
                {{ vehicle.current_km.toLocaleString() }}
              </span>
              <span class="text-sm text-muted-foreground">km</span>
              <Badge :variant="vehicle.is_active ? 'default' : 'secondary'" class="rounded-full">
                {{ vehicle.is_active ? 'Activo' : 'Inactivo' }}
              </Badge>
            </div>
          </Link>
        </div>
      </div>

      <!-- Alertas -->
      <div>
        <div class="flex items-center justify-between mb-8">
          <h2 class="text-3xl font-extralight tracking-tight text-foreground">
            Alertas
          </h2>
          <Link
            :href="route('alerts.index' as any)"
            class="text-base font-medium text-primary hover:underline"
          >
            Ver todas
          </Link>
        </div>

        <EmptyState
          v-if="!alerts?.length"
          :icon="Calendar"
          title="Todo al día"
          description="Sin alertas pendientes."
          compact
        />

        <div v-else class="space-y-4">
          <div
            v-for="alert in alerts"
            :key="alert.id"
            class="flex items-start gap-4 py-4 border-b border-border/30"
          >
            <div class="flex h-10 w-10 items-center justify-center rounded-full bg-amber-500/10">
              <Wrench class="h-5 w-5 text-amber-600" />
            </div>
            <div class="flex-1">
              <div class="flex items-center gap-3 mb-2">
                <h4 class="font-medium text-base text-foreground">
                  {{ alert.title }}
                </h4>
                <span class="text-xs uppercase text-muted-foreground">
                  {{ alert.type }}
                </span>
              </div>
              <p class="text-base text-muted-foreground">
                {{ alert.body }}
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppSidebarLayout>
</template>

<style scoped>
.border-b {
  border-bottom-width: 1px;
  border-bottom-style: solid;
}
</style>
