<script setup lang="ts">
import { Link, useForm } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head } from '@inertiajs/vue3'
import Card from '@/Components/Card.vue'
import PrimaryButton from '@/Components/PrimaryButton.vue'

interface Vehicle {
  id: number
  plate: string
  brand: string
  model: string
  year: number
  current_km: number
}

const props = defineProps<{
  vehicle: Vehicle
}>()

const form = useForm({})

const submit = () => {
  form.post(route('marketplace.store', { vehicle: props.vehicle.id }))
}
</script>

<template>
  <Head title="Generar Informe de Venta" />

  <AuthenticatedLayout>
    <template #header>
      <h2 class="text-xl font-semibold leading-tight">Generar Informe de Venta</h2>
    </template>

    <div class="py-12">
      <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
        <Card title="Informe para venta">
          <div class="space-y-4">
            <div class="border-b pb-4">
              <h4 class="font-medium text-lg">{{ vehicle.brand }} {{ vehicle.model }}</h4>
              <p class="text-gray-500">{{ vehicle.plate }} • {{ vehicle.year }}</p>
              <p class="text-sm text-gray-500">{{ vehicle.current_km.toLocaleString() }} km</p>
            </div>

            <div>
              <h5 class="font-medium mb-2">El informe incluirá:</h5>
              <ul class="text-sm text-gray-600 space-y-1">
                <li>• Puntuación de salud del vehículo</li>
                <li>• Historial de mantenimientos</li>
                <li>• Documentación disponible</li>
                <li>• PDF descargable con certificado</li>
              </ul>
            </div>

            <div class="flex justify-end space-x-3 pt-4">
              <Link
                :href="route('vehicles.show', vehicle.id)"
                class="px-4 py-2 text-gray-600 hover:underline"
              >
                Cancelar
              </Link>
              <PrimaryButton @click="submit" :disabled="form.processing">
                {{ form.processing ? 'Generando...' : 'Generar Informe' }}
              </PrimaryButton>
            </div>
          </div>
        </Card>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
