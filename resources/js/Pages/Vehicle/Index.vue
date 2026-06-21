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
  fuel_type: string
  current_km: number
  is_active: boolean
}

defineProps<{
  vehicles: Vehicle[]
}>()
</script>

<template>
  <Head title="Mis Vehículos" />

  <AuthenticatedLayout>
    <template #header>
      <h2 class="text-xl font-semibold leading-tight">Mis Vehículos</h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <Card title="Lista de vehículos">
          <div class="flex justify-end mb-4">
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
              class="border dark:border-gray-700 rounded-lg p-4 hover:bg-gray-50 dark:hover:bg-gray-700 transition"
            >
              <div class="flex justify-between items-start mb-2">
                <h4 class="font-semibold text-lg">
                  {{ vehicle.brand }} {{ vehicle.model }}
                </h4>
                <Badge :variant="vehicle.is_active ? 'success' : 'gray'">
                  {{ vehicle.is_active ? 'Activo' : 'Inactivo' }}
                </Badge>
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
        </Card>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
