<script setup lang="ts">
import { Link, useForm } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head } from '@inertiajs/vue3'
import Card from '@/Components/Card.vue'
import PrimaryButton from '@/Components/PrimaryButton.vue'

const form = useForm({
  file: null as File | null,
})

const submit = () => {
  form.post(route('vehicles.import.store'))
}

const handleFile = (event: Event) => {
  const target = event.target as HTMLInputElement
  form.file = target.files?.[0] ?? null
}
</script>

<template>
  <Head title="Importar Vehículos" />

  <AuthenticatedLayout>
    <template #header>
      <h2 class="text-xl font-semibold leading-tight">Importar Vehículos</h2>
    </template>

    <div class="py-12">
      <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
        <Card title="Importar desde CSV">
          <form @submit.prevent="submit" class="space-y-6">
            <div>
              <label class="block text-sm font-medium mb-2">Archivo CSV</label>
              <input
                type="file"
                accept=".csv,.txt"
                @change="handleFile"
                class="block w-full text-sm border-gray-300 rounded-md"
                required
              />
              <p class="text-xs text-gray-500 mt-1">
                Columnas: plate, brand, model, year, fuel_type, current_km
              </p>
            </div>

            <div class="flex justify-end space-x-3">
              <Link
                :href="route('vehicles.index')"
                class="px-4 py-2 text-gray-600 hover:underline"
              >
                Cancelar
              </Link>
              <PrimaryButton :disabled="form.processing">
                {{ form.processing ? 'Importando...' : 'Importar' }}
              </PrimaryButton>
            </div>
          </form>
        </Card>
      </div>
    </div>
  </AuthenticatedLayout>
</template>