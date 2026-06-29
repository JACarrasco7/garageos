<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, Link } from '@inertiajs/vue3'
import Card from '@/Components/Card.vue'
import { LineChart, Line, XAxis, YAxis, Tooltip, ResponsiveContainer } from 'recharts'

interface Stats {
  total_vehicles: number
  total_documents: number
  total_maintenance: number
  total_spent: number
  avg_cost_per_km: number
  monthly_spending?: Array<{ month: string; amount: number }>
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

        <Card v-if="stats.monthly_spending?.length" title="Gastos mensuales" class="mt-6">
          <div class="h-64">
            <ResponsiveContainer width="100%" height="100%">
              <LineChart :data="stats.monthly_spending">
                <XAxis dataKey="month" />
                <YAxis />
                <Tooltip />
                <Line type="monotone" dataKey="amount" stroke="#3b82f6" />
              </LineChart>
            </ResponsiveContainer>
          </div>
        </Card>
      </div>
    </div>
  </AuthenticatedLayout>
</template>