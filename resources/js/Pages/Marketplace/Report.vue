<script setup lang="ts">
import { Link } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head } from '@inertiajs/vue3'
import Card from '@/Components/Card.vue'
import Badge from '@/Components/Badge.vue'

interface Vehicle {
  id: number
  plate: string
  brand: string
  model: string
}

interface Report {
  id: number
  score: number
  views: number
  created_at: string
}

defineProps<{
  vehicle: Vehicle
  report: Report
}>()
</script>

<template>
  <Head title="Informe de Venta" />

  <AuthenticatedLayout>
    <template #header>
      <h2 class="text-xl font-semibold leading-tight">Informe de Venta</h2>
    </template>

    <div class="py-12">
      <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
        <Card>
          <div class="text-center mb-6">
            <h3 class="text-2xl font-bold">{{ vehicle.brand }} {{ vehicle.model }}</h3>
            <p class="text-gray-500">{{ vehicle.plate }}</p>
          </div>

          <div class="text-center mb-8">
            <p class="text-sm text-gray-500 mb-2">Puntuación de Salud</p>
            <div class="text-6xl font-bold" :class="report.score >= 70 ? 'text-green-500' : report.score >= 40 ? 'text-amber-500' : 'text-red-500'">
              {{ report.score }}/100
            </div>
            <Badge :variant="report.score >= 70 ? 'success' : report.score >= 40 ? 'warning' : 'danger'" class="mt-2">
              {{ report.score >= 70 ? 'Excelente' : report.score >= 40 ? 'Regular' : 'Necesita atención' }}
            </Badge>
          </div>

          <div class="flex justify-center space-x-4">
            <a
              :href="route('marketplace.download', report)"
              class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700"
            >
              Descargar PDF
            </a>
            <Link
              :href="route('vehicles.show', vehicle.id)"
              class="px-6 py-2 text-gray-600 hover:underline"
            >
              Volver al vehículo
            </Link>
          </div>
        </Card>
      </div>
    </div>
  </AuthenticatedLayout>
</template>