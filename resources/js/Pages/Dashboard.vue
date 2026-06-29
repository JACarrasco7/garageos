<script setup lang="ts">
import { Link } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head } from '@inertiajs/vue3'
import Card from '@/Components/Card.vue'
import Badge from '@/Components/Badge.vue'

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

    <div class="flex justify-end mb-4">
      <Link :href="route('dashboard.stats')" class="text-sm text-blue-600 hover:underline">
        Ver estadísticas →
      </Link>
    </div>

    <div class="py-12">
      <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
        <!-- Stats -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
          <Card padding>
            <div class="flex items-center">
              <div class="p-3 bg-blue-100 dark:bg-blue-900/50 rounded-lg">
                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2v12zM15 19v-6a2 2 0 00-2-2h-2a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2v12z" />
                </svg>
              </div>
              <div class="ml-4">
                <p class="text-sm text-gray-500">Vehículos</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ stats?.total_vehicles ?? 0 }}</p>
              </div>
            </div>
            <Link :href="route('vehicles.index')" class="text-sm text-blue-600 hover:underline mt-4 inline-block">
              Ver todos →
            </Link>
          </Card>

          <Card padding>
            <div class="flex items-center">
              <div class="p-3 bg-green-100 dark:bg-green-900/50 rounded-lg">
                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
              </div>
              <div class="ml-4">
                <p class="text-sm text-gray-500">Documentos</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ stats?.total_documents ?? 0 }}</p>
              </div>
            </div>
          </Card>

          <Card padding>
            <div class="flex items-center">
              <div class="p-3 bg-orange-100 dark:bg-orange-900/50 rounded-lg">
                <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
              </div>
              <div class="ml-4">
                <p class="text-sm text-gray-500">Alertas pendientes</p>
                <p class="text-2xl font-bold" :class="stats?.pending_alerts ? 'text-orange-500' : 'text-gray-900 dark:text-gray-100'">
                  {{ stats?.pending_alerts ?? 0 }}
                </p>
              </div>
            </div>
          </Card>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
          <!-- Vehículos -->
          <Card title="Mis Vehículos" :hover="false">
            <template #default>
              <div class="flex justify-end mb-4">
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
                    <Badge :variant="vehicle.is_active ? 'success' : 'gray'">
                      {{ vehicle.is_active ? 'Activo' : 'Inactivo' }}
                    </Badge>
                  </div>
                  <div class="text-sm text-gray-500 mt-1">
                    {{ vehicle.current_km.toLocaleString() }} km
                  </div>
                </Link>
              </div>
            </template>
          </Card>

          <!-- Alertas -->
          <Card title="Alertas Recientes" :hover="false">
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
                  <Badge variant="warning" size="sm">
                    {{ alert.type }}
                  </Badge>
                </div>
                <p class="text-sm text-gray-500 mt-1">{{ alert.body }}</p>
              </div>
            </div>
          </Card>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
