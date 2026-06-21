<script setup lang="ts">
interface Vehicle {
  id: number
  plate: string
  brand: string
  model: string
  year: number
  fuel_type: string
  current_km: number
  specs?: {
    engine_cc: number | null
    power_hp: number | null
  } | null
  documents?: Array<{
    type: string
    expiry_date: string | null
  }>
}

defineProps<{
  vehicle: Vehicle
}>()
</script>

<template>
  <div class="min-h-screen bg-gray-100 dark:bg-gray-900 py-12">
    <div class="max-w-2xl mx-auto px-4">
      <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
        <h1 class="text-2xl font-bold mb-4">
          {{ vehicle.brand }} {{ vehicle.model }}
        </h1>

        <div class="grid grid-cols-2 gap-4 mb-6">
          <div>
            <span class="text-gray-500">Matrícula:</span>
            <span class="font-medium">{{ vehicle.plate }}</span>
          </div>
          <div>
            <span class="text-gray-500">Año:</span>
            <span class="font-medium">{{ vehicle.year }}</span>
          </div>
          <div>
            <span class="text-gray-500">Km:</span>
            <span class="font-medium">{{ vehicle.current_km.toLocaleString() }}</span>
          </div>
          <div>
            <span class="text-gray-500">Combustible:</span>
            <span class="font-medium capitalize">{{ vehicle.fuel_type }}</span>
          </div>
        </div>

        <div v-if="vehicle.specs" class="mb-6">
          <h2 class="text-lg font-semibold mb-2">Especificaciones</h2>
          <p v-if="vehicle.specs.engine_cc">{{ vehicle.specs.engine_cc }} cc</p>
          <p v-if="vehicle.specs.power_hp">{{ vehicle.specs.power_hp }} CV</p>
        </div>

        <div v-if="vehicle.documents?.length" class="mb-6">
          <h2 class="text-lg font-semibold mb-2">ITV</h2>
          <div
            v-for="doc in vehicle.documents"
            :key="doc.type"
            class="text-sm"
          >
            <span class="text-gray-500">Vence:</span>
            {{ doc.expiry_date }}
          </div>
        </div>

        <p class="text-sm text-gray-500 mt-8">
          Escanea el QR para ver la información del vehículo
        </p>
      </div>
    </div>
  </div>
</template>
