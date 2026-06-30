<script setup lang="ts">
defineProps<{ imports: any }>()

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
  <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-2xl font-bold">Importaciones de Vehículos</h1>
      <a :href="route('imports.create')" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
        Nueva Importación
      </a>
    </div>

    <div class="bg-white shadow rounded-lg">
      <table class="min-w-full divide-y divide-gray-200">
        <thead>
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Matrícula Original</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Matrícula Nueva</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Vehículo</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Acciones</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
          <tr v-for="vehicleImport in imports.data" :key="vehicleImport.id">
            <td class="px-6 py-4">{{ vehicleImport.plate_original }}</td>
            <td class="px-6 py-4">{{ vehicleImport.plate_new || '-' }}</td>
            <td class="px-6 py-4">{{ vehicleImport.brand }} {{ vehicleImport.model }}</td>
            <td class="px-6 py-4">
              <span :class="statusClass(vehicleImport.status)">{{ vehicleImport.status }}</span>
            </td>
            <td class="px-6 py-4">
              <a :href="route('imports.show', vehicleImport.id)" class="text-blue-600 hover:underline">Ver</a>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>