<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Link } from '@inertiajs/vue3'

interface Vehicle {
  id: number
  plate: string
  brand: string
  model: string
  year: number
  fuel_type: string
  color: string
  current_km: number
  photo: string | null
  specs?: {
    engine_cc: number | null
    power_hp: number | null
    transmission: string | null
  } | null
}

defineProps<{
  vehicle: Vehicle
}>()
</script>

<template>
  <AuthenticatedLayout>
    <template #header>
      <h2 class="text-xl font-semibold leading-tight">
        {{ vehicle.brand }} {{ vehicle.model }}
      </h2>
    </template>

    <div class="py-12">
      <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">
        <!-- Información básica -->
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6">
            <div class="flex items-start gap-6">
              <div class="flex-1">
                <h3 class="text-lg font-medium mb-4">Datos del vehículo</h3>
                <dl class="grid grid-cols-2 gap-4 text-sm">
                  <div>
                    <dt class="text-gray-500">Matrícula</dt>
                    <dd class="font-medium">{{ vehicle.plate }}</dd>
                  </div>
                  <div>
                    <dt class="text-gray-500">Año</dt>
                    <dd class="font-medium">{{ vehicle.year }}</dd>
                  </div>
                  <div>
                    <dt class="text-gray-500">Combustible</dt>
                    <dd class="font-medium capitalize">{{ vehicle.fuel_type }}</dd>
                  </div>
                  <div>
                    <dt class="text-gray-500">Km actuales</dt>
                    <dd class="font-medium">{{ vehicle.current_km.toLocaleString() }} km</dd>
                  </div>
                  <div v-if="vehicle.color">
                    <dt class="text-gray-500">Color</dt>
                    <dd class="font-medium">{{ vehicle.color }}</dd>
                  </div>
                </dl>
              </div>
            </div>
          </div>
        </div>

        <!-- Especificaciones -->
        <div v-if="vehicle.specs" class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6">
            <h3 class="text-lg font-medium mb-4">Especificaciones</h3>
            <dl class="grid grid-cols-3 gap-4 text-sm">
              <div v-if="vehicle.specs.engine_cc">
                <dt class="text-gray-500">Cilindrada</dt>
                <dd class="font-medium">{{ vehicle.specs.engine_cc }} cc</dd>
              </div>
              <div v-if="vehicle.specs.power_hp">
                <dt class="text-gray-500">Potencia</dt>
                <dd class="font-medium">{{ vehicle.specs.power_hp }} CV</dd>
              </div>
              <div v-if="vehicle.specs.transmission">
                <dt class="text-gray-500">Transmisión</dt>
                <dd class="font-medium capitalize">{{ vehicle.specs.transmission }}</dd>
              </div>
            </dl>
          </div>
        </div>

        <!-- Acciones -->
        <div class="flex gap-3">
          <Link
            :href="route('vehicles.index')"
            class="px-4 py-2 border rounded-md hover:bg-gray-50 dark:hover:bg-gray-700"
          >
            ← Volver
          </Link>
          <Link
            :href="route('documents.index', { vehicle: vehicle.id })"
            class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700"
          >
            Ver documentos
          </Link>
          <Link
            :href="route('maintenance.index', { vehicle: vehicle.id })"
            class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700"
          >
            Ver mantenimiento
          </Link>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>