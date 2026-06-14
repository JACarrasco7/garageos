<script setup lang="ts">
import { Link } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'

interface Vehicle {
  id: number
  plate: string
  brand: string
  model: string
  year: number
  fuel_type: string
  current_km: number
  is_active: boolean
}

defineProps<{
  vehicles: Vehicle[]
}>()
</script>

<template>
  <AuthenticatedLayout>
    <template #header>
      <h2 class="text-xl font-semibold leading-tight">Mis Vehículos</h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6 text-gray-900 dark:text-gray-100">
            <div class="flex justify-between items-center mb-6">
              <h3 class="text-lg font-medium">Lista de vehículos</h3>
              <Link
                :href="route('vehicles.create')"
                class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700"
              >
                Añadir vehículo
              </Link>
            </div>

            <div v-if="vehicles.length === 0" class="text-center py-8">
              <p class="text-gray-500">No tienes vehículos registrados</p>
            </div>

            <div v-else class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
              <div
                v-for="vehicle in vehicles"
                :key="vehicle.id"
                class="border dark:border-gray-700 rounded-lg p-4"
              >
                <div class="flex justify-between items-start mb-2">
                  <h4 class="font-semibold text-lg">
                    {{ vehicle.brand }} {{ vehicle.model }}
                  </h4>
                  <span
                    :class="[
                      'px-2 py-1 text-xs rounded',
                      vehicle.is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'
                    ]"
                  >
                    {{ vehicle.is_active ? 'Activo' : 'Inactivo' }}
                  </span>
                </div>
                <p class="text-sm text-gray-600 dark:text-gray-400">
                  {{ vehicle.plate }} • {{ vehicle.year }}
                </p>
                <p class="text-sm text-gray-600 dark:text-gray-400">
                  {{ vehicle.current_km.toLocaleString() }} km
                </p>
                <Link
                  :href="route('vehicles.show', vehicle.id)"
                  class="mt-3 inline-block text-blue-600 hover:underline text-sm"
                >
                  Ver detalles →
                </Link>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>