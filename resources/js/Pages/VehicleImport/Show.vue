<script setup lang="ts">
import { useForm } from '@inertiajs/vue3'

interface VehicleImport {
  id: number
  plate_original: string
  plate_new: string | null
  brand: string
  model: string
  year: number
  status: string
  rejection_reason: string | null
}

defineProps<{ vehicleImport: VehicleImport }>()

const processForm = useForm({})

function statusClass(status: string): string {
  const classes: Record<string, string> = {
    pending: 'text-yellow-600',
    processing: 'text-blue-600',
    approved: 'text-green-600',
    rejected: 'text-red-600',
  }
  return classes[status] || 'text-gray-600'
}
</script>

<template>
  <div class="max-w-2xl mx-auto py-6 sm:px-6 lg:px-8">
    <h1 class="text-2xl font-bold mb-6">Detalle Importación</h1>

    <div class="bg-white shadow rounded-lg p-6 space-y-4">
      <div>
        <span class="text-sm text-gray-500">Matrícula Original (Alemania)</span>
        <p class="font-semibold">{{ vehicleImport.plate_original }}</p>
      </div>

      <div>
        <span class="text-sm text-gray-500">Matrícula Asignada (España)</span>
        <p class="font-semibold text-green-600">{{ vehicleImport.plate_new || 'Pendiente' }}</p>
      </div>

      <div>
        <span class="text-sm text-gray-500">Vehículo</span>
        <p class="font-semibold">{{ vehicleImport.brand }} {{ vehicleImport.model }} ({{ vehicleImport.year }})</p>
      </div>

      <div>
        <span class="text-sm text-gray-500">Estado</span>
        <span :class="statusClass(vehicleImport.status)" class="font-semibold">{{ vehicleImport.status }}</span>
      </div>

      <div v-if="vehicleImport.rejection_reason" class="bg-red-50 p-4 rounded">
        <span class="text-sm text-red-600">Motivo rechazo: {{ vehicleImport.rejection_reason }}</span>
      </div>

      <div class="pt-4 border-t">
        <button v-if="vehicleImport.status === 'approved'"
          @click="processForm.post(route('imports.process', vehicleImport.id))"
          :disabled="processForm.processing"
          class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 disabled:opacity-50">
          {{ processForm.processing ? 'Procesando...' : 'Procesar Importación' }}
        </button>
      </div>
    </div>
  </div>
</template>