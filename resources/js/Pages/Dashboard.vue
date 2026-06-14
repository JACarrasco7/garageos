<script setup lang="ts">
import { Link } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head } from '@inertiajs/vue3'

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

  <AuthenticatedLayout>
    <template #header>
      <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
        Dashboard
      </h2>
    </template>

    <div class="py-12">
      <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
        <!-- Stats -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
          <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
            <div class="text-sm text-gray-500">Vehículos</div>
            <div class="text-3xl font-bold">{{ stats?.total_vehicles ?? 0 }}</div>
            <Link :href="route('vehicles.index')" class="text-sm text-blue-600 hover:underline mt-2 inline-block">
              Ver todos →
            </Link>
          </div>
          <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
            <div class="text-sm text-gray-500">Documentos</div>
            <div class="text-3xl font-bold">{{ stats?.total_documents ?? 0 }}</div>
          </div>
          <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
            <div class="text-sm text-gray-500">Alertas pendientes</div>
            <div class="text-3xl font-bold" :class="stats?.pending_alerts ? 'text-orange-500' : ''">
              {{ stats?.pending_alerts ?? 0 }}
            </div>
          </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
          <!-- Vehículos -->
          <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
              <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-medium">Mis Vehículos</h3>
                <Link
                  :href="route('vehicles.create')"
                  class="text-sm text-blue-600 hover:underline"
                >
                  + Añadir
                </Link>
              </div>

              <div v-if="!vehicles?.length" class="text-center py-8 text-gray-500">
                <p>No tienes vehículos registrados</p>
                <Link
                  :href="route('vehicles.create')"
                  class="mt-2 inline-block px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 text-sm"
                >
                  Añadir primer vehículo
                </Link>
              </div>

              <div v-else class="space-y-3">
                <Link
                  v-for="vehicle in vehicles"
                  :key="vehicle.id"
                  :href="route('vehicles.show', vehicle.id)"
                  class="block p-3 border dark:border-gray-700 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition"
                >
                  <div class="flex justify-between items-start">
                    <div>
                      <span class="font-medium">{{ vehicle.brand }} {{ vehicle.model }}</span>
                      <span class="text-sm text-gray-500 ml-2">{{ vehicle.plate }}</span>
                    </div>
                    <span class="text-sm text-gray-500">{{ vehicle.year }}</span>
                  </div>
                  <div class="text-sm text-gray-500 mt-1">
                    {{ vehicle.current_km.toLocaleString() }} km
                  </div>
                </Link>
              </div>
            </div>
          </div>

          <!-- Alertas -->
          <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
              <h3 class="text-lg font-medium mb-4">Alertas Recientes</h3>

              <div v-if="!alerts?.length" class="text-center py-8 text-gray-500">
                <p>✅ No hay alertas pendientes</p>
              </div>

              <div v-else class="space-y-3">
                <div
                  v-for="alert in alerts"
                  :key="alert.id"
                  class="p-3 border dark:border-gray-700 rounded-lg bg-orange-50 dark:bg-orange-900/20 border-orange-200 dark:border-orange-800"
                >
                  <div class="flex justify-between items-start">
                    <span class="font-medium text-sm">{{ alert.title }}</span>
                    <span class="text-xs px-2 py-0.5 rounded bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200 capitalize">
                      {{ alert.type }}
                    </span>
                  </div>
                  <p class="text-sm text-gray-500 mt-1">{{ alert.body }}</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>