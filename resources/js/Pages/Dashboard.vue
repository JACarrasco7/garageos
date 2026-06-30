<script setup lang="ts">
import { Link } from '@inertiajs/vue3'
import AppSidebarLayout from '@/layouts/app/AppSidebarLayout.vue'
import { Head } from '@inertiajs/vue3'
import { Card, CardContent, CardHeader, CardTitle } from '@/Components/ui/card'
import { Badge } from '@/Components/ui/badge'
import { Button } from '@/Components/ui/button'
import { BarChart3, FileText, Bell, Plus, Car, Wrench, Calendar, ArrowRight } from 'lucide-vue-next'

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

    <!-- Welcome Section -->
    <div class="mb-6">
      <h1 class="text-2xl font-bold text-foreground">Bienvenido a GarageOS</h1>
      <p class="text-muted-foreground">Gestiona tus vehículos de forma inteligente</p>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
      <Card class="border-0 shadow-lg bg-linear-to-br from-blue-50 to-blue-100/50 dark:from-blue-950/50 dark:to-blue-900/30">
        <CardContent class="pt-6">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm font-medium text-muted-foreground">Vehículos</p>
              <p class="text-3xl font-bold text-foreground">{{ stats?.total_vehicles ?? 0 }}</p>
            </div>
            <div class="p-3 bg-blue-500 rounded-xl">
              <Car class="h-6 w-6 text-white" />
            </div>
          </div>
          <Button as-child variant="link" size="sm" class="mt-4 px-0">
            <Link :href="route('vehicles.index')">Ver todos <ArrowRight class="ml-1 h-3 w-3" /></Link>
          </Button>
        </CardContent>
      </Card>

      <Card class="border-0 shadow-lg bg-linear-to-br from-green-50 to-green-100/50 dark:from-green-950/50 dark:to-green-900/30">
        <CardContent class="pt-6">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm font-medium text-muted-foreground">Documentos</p>
              <p class="text-3xl font-bold text-foreground">{{ stats?.total_documents ?? 0 }}</p>
            </div>
            <div class="p-3 bg-green-500 rounded-xl">
              <FileText class="h-6 w-6 text-white" />
            </div>
          </div>
        </CardContent>
      </Card>

      <Card class="border-0 shadow-lg bg-linear-to-br from-amber-50 to-amber-100/50 dark:from-amber-950/50 dark:to-amber-900/30">
        <CardContent class="pt-6">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm font-medium text-muted-foreground">Alertas pendientes</p>
              <p class="text-3xl font-bold" :class="stats?.pending_alerts ? 'text-amber-600 dark:text-amber-400' : 'text-foreground'">
                {{ stats?.pending_alerts ?? 0 }}
              </p>
            </div>
            <div class="p-3 bg-amber-500 rounded-xl">
              <Bell class="h-6 w-6 text-white" />
            </div>
          </div>
        </CardContent>
      </Card>
    </div>

    <!-- Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
      <!-- Vehículos -->
      <Card class="border-0 shadow-lg">
        <CardHeader class="flex flex-row items-center justify-between pb-3">
          <CardTitle class="text-lg font-semibold">Mis Vehículos</CardTitle>
          <Button as-child variant="outline" size="sm" class="rounded-lg">
            <Link :href="route('vehicles.create')">
              <Plus class="mr-2 h-4 w-4" />
              Añadir
            </Link>
          </Button>
        </CardHeader>
        <CardContent>
          <div v-if="!vehicles?.length" class="text-center py-12">
            <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-muted mb-4">
              <Car class="h-8 w-8 text-muted-foreground" />
            </div>
            <h3 class="font-medium text-foreground mb-1">No tienes vehículos registrados</h3>
            <p class="text-sm text-muted-foreground mb-4">Comienza añadiendo tu primer vehículo</p>
            <Button as-child>
              <Link :href="route('vehicles.create')">Añadir primer vehículo</Link>
            </Button>
          </div>

          <div v-else class="space-y-3">
            <Link
              v-for="vehicle in vehicles"
              :key="vehicle.id"
              :href="route('vehicles.show', vehicle.id)"
              class="flex items-center justify-between p-4 rounded-xl border border-border hover:bg-accent transition-colors group"
            >
              <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-primary/10">
                  <Car class="h-5 w-5 text-primary" />
                </div>
                <div>
                  <p class="font-medium text-foreground group-hover:text-primary transition-colors">
                    {{ vehicle.brand }} {{ vehicle.model }}
                  </p>
                  <p class="text-sm text-muted-foreground">{{ vehicle.plate }}</p>
                </div>
              </div>
              <div class="text-right">
                <Badge :variant="vehicle.is_active ? 'default' : 'secondary'" class="mb-1">
                  {{ vehicle.is_active ? 'Activo' : 'Inactivo' }}
                </Badge>
                <p class="text-xs text-muted-foreground">{{ vehicle.current_km.toLocaleString() }} km</p>
              </div>
            </Link>
          </div>
        </CardContent>
      </Card>

      <!-- Alertas -->
      <Card class="border-0 shadow-lg">
        <CardHeader class="pb-3">
          <CardTitle class="text-lg font-semibold">Alertas Recientes</CardTitle>
        </CardHeader>
        <CardContent>
          <div v-if="!alerts?.length" class="text-center py-12">
            <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-green-100 dark:bg-green-900/30 mb-4">
              <Calendar class="h-8 w-8 text-green-600" />
            </div>
            <h3 class="font-medium text-foreground mb-1">¡Todo al día!</h3>
            <p class="text-sm text-muted-foreground">No hay alertas pendientes</p>
          </div>

          <div v-else class="space-y-3">
            <div
              v-for="alert in alerts"
              :key="alert.id"
              class="flex items-start gap-3 p-4 rounded-xl border border-amber-200 dark:border-amber-800 bg-amber-50/50 dark:bg-amber-900/20"
            >
              <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-amber-500 shrink-0">
                <Wrench class="h-4 w-4 text-white" />
              </div>
              <div class="flex-1">
                <div class="flex items-center justify-between mb-1">
                  <h4 class="font-medium text-sm text-foreground">{{ alert.title }}</h4>
                  <Badge variant="outline" class="text-xs">
                    {{ alert.type }}
                  </Badge>
                </div>
                <p class="text-sm text-muted-foreground">{{ alert.body }}</p>
              </div>
            </div>
          </div>
        </CardContent>
      </Card>
    </div>
  </AppSidebarLayout>
</template>
