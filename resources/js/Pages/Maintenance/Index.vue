<script setup lang="ts">
import { Link } from '@inertiajs/vue3'
import AppSidebarLayout from '@/layouts/app/AppSidebarLayout.vue'
import { Head } from '@inertiajs/vue3'
import { Card, CardContent, CardHeader, CardTitle } from '@/Components/ui/card'
import { Badge } from '@/Components/ui/badge'
import { Wrench, Calendar, Fuel, CheckCircle2 } from 'lucide-vue-next'

interface Vehicle {
  id: number
  plate: string
  brand: string
  model: string
}

interface MaintenanceEntry {
  id: number
  type: string
  title: string
  description: string | null
  km_at_service: number
  service_date: string
  cost: number | null
  is_verified: boolean
  workshop?: { name: string } | null
}

defineProps<{
  vehicle: Vehicle
  entries: MaintenanceEntry[]
  stats: {
    total_cost: number
    cost_per_km: number
    entries_count: number
  }
}>()

const typeLabels: Record<string, string> = {
  aceite: 'Aceite',
  filtros: 'Filtros',
  neumaticos: 'Neumáticos',
  frenos: 'Frenos',
  distribucion: 'Distribución',
  embrague: 'Embrague',
  bateria: 'Batería',
  itv: 'ITV',
  revision_general: 'Revisión General',
  otro: 'Otro',
}
</script>

<template>
  <Head title="Mantenimiento" />

  <AppSidebarLayout>
    <template #header>
      Mantenimiento - {{ vehicle.brand }} {{ vehicle.model }}
    </template>

    <!-- Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
      <Card class="border-0 shadow-lg bg-linear-to-br from-blue-50 to-blue-100/50 dark:from-blue-950/50 dark:to-blue-900/30">
        <CardContent class="pt-6">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm font-medium text-muted-foreground">Coste Total</p>
              <p class="text-2xl font-bold text-foreground">{{ stats.total_cost?.toLocaleString() ?? 0 }} €</p>
            </div>
            <div class="p-2 bg-blue-500 rounded-lg">
              <Wrench class="h-5 w-5 text-white" />
            </div>
          </div>
        </CardContent>
      </Card>

      <Card class="border-0 shadow-lg bg-linear-to-br from-green-50 to-green-100/50 dark:from-green-950/50 dark:to-green-900/30">
        <CardContent class="pt-6">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm font-medium text-muted-foreground">Coste por Km</p>
              <p class="text-2xl font-bold text-foreground">{{ stats.cost_per_km?.toFixed(2) ?? 0 }} €/km</p>
            </div>
            <div class="p-2 bg-green-500 rounded-lg">
              <Fuel class="h-5 w-5 text-white" />
            </div>
          </div>
        </CardContent>
      </Card>

      <Card class="border-0 shadow-lg bg-linear-to-br from-amber-50 to-amber-100/50 dark:from-amber-950/50 dark:to-amber-900/30">
        <CardContent class="pt-6">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm font-medium text-muted-foreground">Entradas</p>
              <p class="text-2xl font-bold text-foreground">{{ stats.entries_count }}</p>
            </div>
            <div class="p-2 bg-amber-500 rounded-lg">
              <Calendar class="h-5 w-5 text-white" />
            </div>
          </div>
        </CardContent>
      </Card>
    </div>

    <!-- Timeline -->
    <Card class="border-0 shadow-lg">
      <CardHeader class="pb-3">
        <CardTitle class="text-lg font-semibold">Historial de Mantenimiento</CardTitle>
      </CardHeader>
      <CardContent>
        <div v-if="!entries.length" class="text-center py-12">
          <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-muted mb-4">
            <Wrench class="h-8 w-8 text-muted-foreground" />
          </div>
          <h3 class="font-medium text-foreground mb-1">No hay entradas de mantenimiento</h3>
          <p class="text-sm text-muted-foreground">Añade tu primer registro</p>
        </div>

        <div v-else class="relative">
          <div class="absolute left-4 top-0 bottom-0 w-0.5 bg-border"></div>
          <div class="space-y-6">
            <div
              v-for="entry in entries"
              :key="entry.id"
              class="relative pl-10"
            >
              <div class="absolute -left-3 w-7 h-7 rounded-full bg-primary border-4 border-background"></div>
              <div class="rounded-xl border border-border bg-muted/30 p-4">
                <div class="flex justify-between items-start">
                  <div>
                    <Badge variant="outline" class="text-xs uppercase">
                      {{ typeLabels[entry.type] }}
                    </Badge>
                    <h4 class="font-medium mt-1">{{ entry.title }}</h4>
                    <p v-if="entry.description" class="text-sm text-muted-foreground mt-1">{{ entry.description }}</p>
                  </div>
                  <div class="text-right">
                    <p class="text-sm text-muted-foreground">{{ entry.service_date }}</p>
                    <p v-if="entry.cost" class="font-medium">{{ entry.cost.toLocaleString() }} €</p>
                  </div>
                </div>
                <div class="flex flex-wrap gap-3 mt-2 text-xs text-muted-foreground">
                  <span class="flex items-center gap-1">
                    <Calendar class="h-3 w-3" />
                    {{ entry.km_at_service.toLocaleString() }} km
                  </span>
                  <span v-if="entry.workshop">{{ entry.workshop.name }}</span>
                  <span v-if="entry.is_verified" class="flex items-center gap-1 text-green-600">
                    <CheckCircle2 class="h-3 w-3" />
                    Verificado
                  </span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </CardContent>
    </Card>
  </AppSidebarLayout>
</template>
