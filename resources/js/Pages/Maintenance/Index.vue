<script setup lang="ts">
import { Link } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head } from '@inertiajs/vue3'

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

  <AuthenticatedLayout>
    <template #header>
      <h2 class="text-xl font-semibold leading-tight">
        Mantenimiento - {{ vehicle.brand }} {{ vehicle.model }}
      </h2>
    </template>

    <div class="py-12">
      <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
        <!-- Stats -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
          <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
            <div class="text-sm text-gray-500">Coste Total</div>
            <div class="text-3xl font-bold">{{ stats.total_cost?.toLocaleString() ?? 0 }} €</div>
          </div>
          <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
            <div class="text-sm text-gray-500">Coste por Km</div>
            <div class="text-3xl font-bold">{{ stats.cost_per_km?.toFixed(2) ?? 0 }} €/km</div>
          </div>
          <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
            <div class="text-sm text-gray-500">Entradas</div>
            <div class="text-3xl font-bold">{{ stats.entries_count }}</div>
          </div>
        </div>

        <!-- Timeline -->
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6">
            <h3 class="text-lg font-medium mb-4">Historial de Mantenimiento</h3>

            <div v-if="!entries.length" class="text-center py-8 text-gray-500">
              <p>No hay entradas de mantenimiento</p>
            </div>

            <div v-else class="relative">
              <div class="absolute left-4 top-0 bottom-0 w-0.5 bg-gray-200 dark:bg-gray-700"></div>
              <div class="space-y-6">
                <div
                  v-for="entry in entries"
                  :key="entry.id"
                  class="relative pl-10"
                >
                  <div class="absolute left-2 w-5 h-5 rounded-full bg-blue-500 border-2 border-white dark:border-gray-800"></div>
                  <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                    <div class="flex justify-between items-start">
                      <div>
                        <span class="text-xs text-gray-500 uppercase">{{ typeLabels[entry.type] }}</span>
                        <h4 class="font-medium">{{ entry.title }}</h4>
                        <p v-if="entry.description" class="text-sm text-gray-500 mt-1">{{ entry.description }}</p>
                      </div>
                      <div class="text-right">
                        <span class="text-sm text-gray-500">{{ entry.service_date }}</span>
                        <div v-if="entry.cost" class="font-medium">{{ entry.cost.toLocaleString() }} €</div>
                      </div>
                    </div>
                    <div class="flex gap-4 mt-2 text-xs text-gray-500">
                      <span>{{ entry.km_at_service.toLocaleString() }} km</span>
                      <span v-if="entry.workshop">{{ entry.workshop.name }}</span>
                      <span v-if="entry.is_verified" class="text-green-500">✓ Verificado</span>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>