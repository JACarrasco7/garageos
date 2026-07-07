<script setup lang="ts">
import { Link } from '@inertiajs/vue3'
import AppSidebarLayout from '@/layouts/app/AppSidebarLayout.vue'
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
  <Head title="Dashboard" />

  <AppSidebarLayout>
    <template #header>
      Dashboard
    </template>

    <!-- Hero Welcome -->
    <div data-tour="dashboard" class="mb-8 flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between animate-in-up">
      <div>
        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-primary/80">
          Panel principal
        </p>
        <h1
          class="mt-2 text-3xl font-bold tracking-tight md:text-4xl text-foreground"
        >
          Hola, <span class="text-gradient">conductor</span>
        </h1>
        <p class="mt-1.5 text-base text-muted-foreground">
          {{ vehicles?.length ?? 0 }} {{ (vehicles?.length ?? 0) === 1 ? 'vehículo registrado' : 'vehículos registrados' }}
          · vista general de tu flota
        </p>
      </div>
      <Button
        as-child
        :class="cn(
          'inline-flex items-center gap-2 rounded-xl px-4 py-2.5 text-sm font-semibold shadow-lg transition-all duration-300 hover:scale-[1.02] active:scale-95',
          'bg-gradient-to-r from-primary to-primary/85 text-primary-foreground shadow-primary/30',
        )"
      >
        <Link :href="route('vehicles.wizard')">
          <Plus class="h-4 w-4" />
          Añadir vehículo
        </Link>
      </Button>
    </div>

    <!-- KPI Grid -->
    <div class="mb-8 grid grid-cols-1 gap-4 md:grid-cols-3 animate-in-up delay-100">
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
      />
    </div>

    <!-- Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
      <!-- Vehículos -->
      <Card
        data-tour="vehicles"
        :class="cn(
          'border backdrop-blur-xl animate-in-up delay-200',
          'bg-card/80 border-border shadow-sm',
        )"
      >
        <CardHeader class="flex flex-row items-center justify-between pb-3">
          <CardTitle class="text-lg font-semibold tracking-tight text-foreground">
            Mis Vehículos
          </CardTitle>
          <Button as-child variant="ghost" size="sm" class="rounded-lg text-primary hover:bg-primary/10">
            <Link :href="route('vehicles.index')" class="flex items-center gap-1.5">
              Ver todos
              <ArrowRight class="h-3.5 w-3.5" />
            </Link>
          </Button>
        </CardHeader>
        <CardContent>
          <EmptyState
            v-if="!vehicles?.length"
            :icon="Car"
            title="Aún no tienes vehículos"
            description="Registra tu primer vehículo para empezar a llevar el control de mantenimiento, alertas y documentos."
            action-label="Añadir primer vehículo"
            :action-href="route('vehicles.create')"
            compact
          />

          <div v-else class="space-y-2">
            <Link
              v-for="vehicle in vehicles"
              :key="vehicle.id"
              :href="route('vehicles.show', vehicle.id)"
              :class="cn(
                'group flex items-center justify-between rounded-xl border p-4 transition-all duration-300',
                'border-border bg-card/50 hover:border-primary/40 hover:bg-primary/5',
              )"
            >
              <div class="flex items-center gap-3">
                <div
                  class="flex h-10 w-10 items-center justify-center rounded-xl transition-transform duration-300 group-hover:scale-105 bg-primary/10 text-primary"
                >
                  <Car class="h-5 w-5" />
                </div>
                <div>
                  <p class="font-semibold transition-colors text-foreground group-hover:text-primary">
                    {{ vehicle.brand }} {{ vehicle.model }}
                  </p>
                  <p class="text-xs text-muted-foreground">
                    {{ vehicle.year }} · {{ vehicle.plate }}
                  </p>
                </div>
              </div>
              <div class="flex items-center gap-4">
                <div class="text-right">
                  <p class="text-sm font-semibold tabular-nums text-foreground">
                    {{ vehicle.current_km.toLocaleString() }}
                  </p>
                  <p class="text-[10px] uppercase tracking-wider text-muted-foreground">km</p>
                </div>
                <Badge :variant="vehicle.is_active ? 'default' : 'secondary'">
                  {{ vehicle.is_active ? 'Activo' : 'Inactivo' }}
                </Badge>
                <ArrowRight class="h-4 w-4 transition-transform duration-300 group-hover:translate-x-1 text-muted-foreground/30" />
              </div>
            </Link>
          </div>
        </CardContent>
      </Card>

      <!-- Alertas -->
      <Card
        data-tour="alerts"
        :class="cn(
          'border backdrop-blur-xl animate-in-up delay-300',
          'bg-card/80 border-border shadow-sm',
        )"
      >
        <CardHeader class="flex flex-row items-center justify-between pb-3">
          <CardTitle class="text-lg font-semibold tracking-tight text-foreground">
            Alertas recientes
          </CardTitle>
          <Button as-child variant="ghost" size="sm" class="rounded-lg text-primary hover:bg-primary/10">
            <Link :href="route('alerts.index' as any)" class="flex items-center gap-1.5">
              Ver todas
              <ArrowRight class="h-3.5 w-3.5" />
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

          <div v-else class="space-y-2">
            <div
              v-for="alert in alerts"
              :key="alert.id"
              :class="cn(
                'flex items-start gap-3 rounded-xl border p-4 transition-all duration-300',
                'border-amber-500/20 bg-amber-500/5 hover:border-amber-500/40 hover:bg-amber-500/10',
              )"
            >
              <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-gradient-to-br from-amber-500 to-amber-600 shrink-0 shadow-md shadow-amber-500/30">
                <Wrench class="h-4 w-4 text-primary-foreground" />
              </div>
              <div class="flex-1 min-w-0">
                <div class="flex items-center justify-between gap-2 mb-1">
                  <h4 class="font-semibold text-sm text-foreground">
                    {{ alert.title }}
                  </h4>
                  <Badge variant="outline" class="text-[10px] uppercase tracking-wider shrink-0">
                    {{ alert.type }}
                  </Badge>
                </div>
                <p class="text-xs text-muted-foreground">
                  {{ alert.body }}
                </p>
              </div>
            </div>
          </div>
        </CardContent>
      </Card>
    </div>
  </AppSidebarLayout>
</template>
