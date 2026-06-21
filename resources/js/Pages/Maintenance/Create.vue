<script setup lang="ts">
import { useForm, Link } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, router } from '@inertiajs/vue3'

interface Vehicle {
  id: number
  plate: string
  brand: string
  model: string
  current_km: number
}

const props = defineProps<{
  vehicle: Vehicle
}>()

const form = useForm({
  type: 'otro',
  title: '',
  description: '',
  km_at_service: props.vehicle.current_km,
  service_date: new Date().toISOString().split('T')[0],
  cost: '',
  notes: '',
})

const submit = () => {
  form.post(route('maintenance.store', props.vehicle.id), {
    preserveScroll: true,
    onSuccess: () => {
      router.visit(route('maintenance.index', props.vehicle.id))
    },
  })
}
</script>

<template>
  <Head title="Añadir Mantenimiento" />

  <AuthenticatedLayout>
    <template #header>
      <h2 class="text-xl font-semibold leading-tight">
        Añadir Mantenimiento - {{ vehicle.brand }} {{ vehicle.model }}
      </h2>
    </template>

    <div class="py-12">
      <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
          <form @submit.prevent="submit" class="p-6 space-y-6">
            <!-- Tipo -->
            <div>
              <label class="block text-sm font-medium mb-1">Tipo</label>
              <select
                v-model="form.type"
                required
                class="w-full px-3 py-2 border rounded-md dark:bg-gray-700 dark:border-gray-600"
              >
                <option value="aceite">Aceite</option>
                <option value="filtros">Filtros</option>
                <option value="neumaticos">Neumáticos</option>
                <option value="frenos">Frenos</option>
                <option value="distribucion">Distribución</option>
                <option value="embrague">Embrague</option>
                <option value="bateria">Batería</option>
                <option value="itv">ITV</option>
                <option value="revision_general">Revisión General</option>
                <option value="otro">Otro</option>
              </select>
            </div>

            <!-- Título -->
            <div>
              <label class="block text-sm font-medium mb-1">Título</label>
              <input
                v-model="form.title"
                type="text"
                required
                placeholder="Ej: Cambio de aceite y filtros"
                class="w-full px-3 py-2 border rounded-md dark:bg-gray-700 dark:border-gray-600"
              />
            </div>

            <!-- Descripción -->
            <div>
              <label class="block text-sm font-medium mb-1">Descripción</label>
              <textarea
                v-model="form.description"
                rows="3"
                placeholder="Detalles del servicio realizado..."
                class="w-full px-3 py-2 border rounded-md dark:bg-gray-700 dark:border-gray-600"
              ></textarea>
            </div>

            <!-- Fecha y Km -->
            <div class="grid grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-medium mb-1">Fecha</label>
                <input
                  v-model="form.service_date"
                  type="date"
                  required
                  class="w-full px-3 py-2 border rounded-md dark:bg-gray-700 dark:border-gray-600"
                />
              </div>
              <div>
                <label class="block text-sm font-medium mb-1">Km en el servicio</label>
                <input
                  v-model="form.km_at_service"
                  type="number"
                  required
                  min="0"
                  class="w-full px-3 py-2 border rounded-md dark:bg-gray-700 dark:border-gray-600"
                />
              </div>
            </div>

            <!-- Coste -->
            <div>
              <label class="block text-sm font-medium mb-1">Coste (€)</label>
              <input
                v-model="form.cost"
                type="number"
                step="0.01"
                min="0"
                placeholder="0.00"
                class="w-full px-3 py-2 border rounded-md dark:bg-gray-700 dark:border-gray-600"
              />
            </div>

            <!-- Notas -->
            <div>
              <label class="block text-sm font-medium mb-1">Notas técnicas</label>
              <textarea
                v-model="form.notes"
                rows="2"
                placeholder="Observaciones, piezas usadas, etc."
                class="w-full px-3 py-2 border rounded-md dark:bg-gray-700 dark:border-gray-600"
              ></textarea>
            </div>

            <!-- Botones -->
            <div class="flex justify-end space-x-3">
              <Link
                :href="route('maintenance.index', vehicle.id)"
                class="px-4 py-2 border rounded-md hover:bg-gray-50 dark:hover:bg-gray-700"
              >
                Cancelar
              </Link>
              <button
                type="submit"
                :disabled="form.processing"
                class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 disabled:opacity-50"
              >
                {{ form.processing ? 'Guardando...' : 'Guardar entrada' }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
