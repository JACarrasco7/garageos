<script setup lang="ts">
import { useForm, Link } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'

interface Garage {
  id: number
  name: string
}

defineProps<{
  garages: Garage[]
}>()

const form = useForm({
  garage_id: garages[0]?.id || '',
  plate: '',
  vin: '',
  brand: '',
  model: '',
  year: new Date().getFullYear(),
  fuel_type: 'gasolina',
  color: '',
  current_km: 0,
})

const submit = () => {
  form.post(route('vehicles.store'))
}
</script>

<template>
  <AuthenticatedLayout>
    <template #header>
      <h2 class="text-xl font-semibold leading-tight">Añadir Vehículo</h2>
    </template>

    <div class="py-12">
      <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
          <form @submit.prevent="submit" class="p-6 space-y-6">
            <div>
              <label class="block text-sm font-medium mb-1">Matrícula</label>
              <input
                v-model="form.plate"
                type="text"
                required
                maxlength="10"
                class="w-full px-3 py-2 border rounded-md dark:bg-gray-700 dark:border-gray-600"
              />
              <p v-if="form.errors.plate" class="text-red-500 text-sm mt-1">{{ form.errors.plate }}</p>
            </div>

            <div>
              <label class="block text-sm font-medium mb-1">Marca</label>
              <input
                v-model="form.brand"
                type="text"
                required
                maxlength="50"
                class="w-full px-3 py-2 border rounded-md dark:bg-gray-700 dark:border-gray-600"
              />
            </div>

            <div>
              <label class="block text-sm font-medium mb-1">Modelo</label>
              <input
                v-model="form.model"
                type="text"
                required
                maxlength="80"
                class="w-full px-3 py-2 border rounded-md dark:bg-gray-700 dark:border-gray-600"
              />
            </div>

            <div>
              <label class="block text-sm font-medium mb-1">Año</label>
              <input
                v-model="form.year"
                type="number"
                required
                :min="1900"
                :max="new Date().getFullYear() + 1"
                class="w-full px-3 py-2 border rounded-md dark:bg-gray-700 dark:border-gray-600"
              />
            </div>

            <div>
              <label class="block text-sm font-medium mb-1">Combustible</label>
              <select
                v-model="form.fuel_type"
                required
                class="w-full px-3 py-2 border rounded-md dark:bg-gray-700 dark:border-gray-600"
              >
                <option value="gasolina">Gasolina</option>
                <option value="diesel">Diesel</option>
                <option value="hibrido">Híbrido</option>
                <option value="electrico">Eléctrico</option>
                <option value="glp">GLP</option>
              </select>
            </div>

            <div>
              <label class="block text-sm font-medium mb-1">Km actuales</label>
              <input
                v-model="form.current_km"
                type="number"
                required
                min="0"
                class="w-full px-3 py-2 border rounded-md dark:bg-gray-700 dark:border-gray-600"
              />
            </div>

            <div class="flex justify-end space-x-3">
              <Link
                :href="route('vehicles.index')"
                class="px-4 py-2 border rounded-md hover:bg-gray-50 dark:hover:bg-gray-700"
              >
                Cancelar
              </Link>
              <button
                type="submit"
                :disabled="form.processing"
                class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 disabled:opacity-50"
              >
                Guardar vehículo
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>