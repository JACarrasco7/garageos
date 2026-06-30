<script setup lang="ts">
defineProps<{ import: Object }>()
</script>

<template>
  <div class="max-w-2xl mx-auto py-6 sm:px-6 lg:px-8">
    <h1 class="text-2xl font-bold mb-6">Detalle Importación</h1>

    <div class="bg-white shadow rounded-lg p-6 space-y-4">
      <div>
        <span class="text-sm text-gray-500">Matrícula Original (Alemania)</span>
        <p class="font-semibold">{{ import.plate_original }}</p>
      </div>

      <div>
        <span class="text-sm text-gray-500">Matrícula Asignada (España)</span>
        <p class="font-semibold text-green-600">{{ import.plate_new || 'Pendiente' }}</p>
      </div>

      <div>
        <span class="text-sm text-gray-500">Vehículo</span>
        <p class="font-semibold">{{ import.brand }} {{ import.model }} ({{ import.year }})</p>
      </div>

      <div>
        <span class="text-sm text-gray-500">Estado</span>
        <span :class="statusClass(import.status)" class="font-semibold">{{ import.status }}</span>
      </div>

      <div v-if="import.rejection_reason" class="bg-red-50 p-4 rounded">
        <span class="text-sm text-red-600">Motivo rechazo: {{ import.rejection_reason }}</span>
      </div>

      <div class="pt-4 border-t">
        <button v-if="import.status === 'approved'"
          @click="$inertia.post(route('imports.process', import.id))"
          class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">
          Procesar Importación
        </button>
      </div>
    </div>
  </div>
</template>

<script>
function statusClass(status) {
  return {
    pending: 'text-yellow-600',
    processing: 'text-blue-600',
    approved: 'text-green-600',
    rejected: 'text-red-600',
  }[status] || 'text-gray-600'
}
</script>