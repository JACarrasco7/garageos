<script setup lang="ts">
import { useForm } from '@inertiajs/vue3'

const form = useForm({
  plate_original: '',
  brand: '',
  model: '',
  year: '',
  engine_cc: '',
  power_kw: '',
})

const submit = () => {
  form.post(route('imports.store'))
}
</script>

<template>
  <div class="max-w-2xl mx-auto py-6 sm:px-6 lg:px-8">
    <h1 class="text-2xl font-bold mb-6">Importar Vehículo desde Alemania</h1>

    <form @submit.prevent="submit" class="space-y-6">
      <div>
        <label class="block text-sm font-medium">Matrícula Alemán</label>
        <input v-model="form.plate_original" type="text" required
          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
          placeholder="B-XX-1234" />
        <p class="text-xs text-gray-500 mt-1">Formato: B-XX-1234</p>
      </div>

      <div class="grid grid-cols-2 gap-4">
        <div>
          <label class="block text-sm font-medium">Marca</label>
          <input v-model="form.brand" type="text" required
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" />
        </div>
        <div>
          <label class="block text-sm font-medium">Modelo</label>
          <input v-model="form.model" type="text" required
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" />
        </div>
      </div>

      <div>
        <label class="block text-sm font-medium">Año</label>
        <input v-model="form.year" type="number" required
          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" />
      </div>

      <div class="grid grid-cols-2 gap-4">
        <div>
          <label class="block text-sm font-medium">Cilindrada (cc)</label>
          <input v-model="form.engine_cc" type="number"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" />
        </div>
        <div>
          <label class="block text-sm font-medium">Potencia (kW)</label>
          <input v-model="form.power_kw" type="number"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" />
        </div>
      </div>

      <button type="submit" :disabled="form.processing"
        class="w-full py-2 bg-blue-600 text-white rounded hover:bg-blue-700 disabled:opacity-50">
        {{ form.processing ? 'Procesando...' : 'Crear Solicitud' }}
      </button>
    </form>
  </div>
</template>