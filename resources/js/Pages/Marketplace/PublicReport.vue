<script setup lang="ts">
import { Head } from '@inertiajs/vue3'

interface Vehicle {
  id: number
  plate: string
  brand: string
  model: string
  year: number
  current_km: number
}

interface Report {
  id: number
  score: number
  views: number
  created_at: string
}

defineProps<{
  report: Report
  vehicle: Vehicle
}>()
</script>

<template>
  <Head title="Informe de Venta" />

  <div class="min-h-screen bg-gray-100 dark:bg-gray-900 py-12">
    <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
      <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
        <div class="text-center mb-6 p-6">
          <h1 class="text-2xl font-bold">Informe de Venta</h1>
          <h2 class="text-xl">{{ vehicle.brand }} {{ vehicle.model }}</h2>
          <p class="text-gray-500">{{ vehicle.plate }} • {{ vehicle.year }}</p>
        </div>

        <div class="text-center mb-8 p-6">
          <p class="text-sm text-gray-500 mb-2">Puntuación de Salud</p>
          <div class="text-6xl font-bold" :class="report.score >= 70 ? 'text-green-500' : report.score >= 40 ? 'text-amber-500' : 'text-red-500'">
            {{ report.score }}/100
          </div>
        </div>

        <div class="p-6 text-center text-gray-500">
          <p>Informe generado el {{ new Date(report.created_at).toLocaleDateString() }}</p>
          <p>Visto {{ report.views }} veces</p>
        </div>
      </div>
    </div>
  </div>
</template>
