<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Link } from '@inertiajs/vue3'

interface Vehicle {
  id: number
  plate: string
  brand: string
  model: string
}

interface Document {
  id: number
  type: string
  title: string
  file_path: string
  expiry_date: string | null
  amount: number | null
}

defineProps<{
  vehicle: Vehicle
  documents: Record<string, Document[]>
}>()

const documentLabels: Record<string, string> = {
  factura: 'Facturas',
  itv: 'ITV',
  seguro: 'Seguros',
  impuesto: 'Impuestos',
  otro: 'Otros',
}
</script>

<template>
  <AuthenticatedLayout>
    <template #header>
      <h2 class="text-xl font-semibold leading-tight">
        Documentos - {{ vehicle.brand }} {{ vehicle.model }}
      </h2>
    </template>

    <div class="py-12">
      <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6">
            <div class="flex justify-between items-center mb-6">
              <h3 class="text-lg font-medium">Documentos del vehículo</h3>
              <button
                @click="$emit('open-upload')"
                class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700"
              >
                Subir documento
              </button>
            </div>

            <div v-for="(group, type) in documents" :key="type" class="mb-6">
              <h4 class="font-medium text-gray-700 dark:text-gray-300 mb-3">
                {{ documentLabels[type] || type }}
              </h4>
              <div class="space-y-2">
                <div
                  v-for="doc in group"
                  :key="doc.id"
                  class="flex justify-between items-center p-3 border dark:border-gray-700 rounded"
                >
                  <div>
                    <p class="font-medium">{{ doc.title }}</p>
                    <p v-if="doc.expiry_date" class="text-sm text-gray-500">
                      Vence: {{ doc.expiry_date }}
                    </p>
                  </div>
                  <a
                    :href="`/storage/${doc.file_path}`"
                    target="_blank"
                    class="text-blue-600 hover:underline"
                  >
                    Ver
                  </a>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>