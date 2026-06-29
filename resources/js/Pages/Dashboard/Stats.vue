<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, Link } from '@inertiajs/vue3'
import Card from '@/Components/Card.vue'

interface Stats {
  total_vehicles: number
  total_documents: number
  total_maintenance: number
  total_spent: number
  avg_cost_per_km: number
}

defineProps<{
  stats: Stats
}>()
</script>

<template>
  <Head title="Estadísticas" />

  <AuthenticatedLayout>
    <template #header>
      <h2 class="text-xl font-semibold leading-tight">Estadísticas</h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
          <Card title="Vehículos">
            <p class="text-3xl font-bold">{{ stats.total_vehicles }}</p>
          </Card>

          <Card title="Documentos">
            <p class="text-3xl font-bold">{{ stats.total_documents }}</p>
          </Card>

          <Card title="Mantenimientos">
            <p class="text-3xl font-bold">{{ stats.total_maintenance }}</p>
          </Card>

          <Card title="Gasto total">
            <p class="text-3xl font-bold">{{ stats.total_spent }} €</p>
          </Card>

          <Card title="Coste/km">
            <p class="text-3xl font-bold">{{ stats.avg_cost_per_km }} €/km</p>
          </Card>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>