<script setup lang="ts">
import AppSidebarLayout from '@/layouts/app/AppSidebarLayout.vue'
import { Head, Link } from '@inertiajs/vue3'
import { Card, CardContent, CardHeader, CardTitle } from '@/Components/ui/card'
import { LineChart, Line, XAxis, YAxis, Tooltip, ResponsiveContainer } from 'recharts'
import { TrendingUp } from 'lucide-vue-next'

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

  <AppSidebarLayout>
    <template #header>
      Estadísticas
    </template>

    <div class="space-y-6">
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <Card class="border-0 shadow-lg">
          <CardHeader>
            <CardTitle>Vehículos</CardTitle>
          </CardHeader>
          <CardContent>
            <p class="text-3xl font-bold">{{ stats.total_vehicles }}</p>
          </CardContent>
        </Card>

        <Card class="border-0 shadow-lg">
          <CardHeader>
            <CardTitle>Documentos</CardTitle>
          </CardHeader>
          <CardContent>
            <p class="text-3xl font-bold">{{ stats.total_documents }}</p>
          </CardContent>
        </Card>

        <Card class="border-0 shadow-lg">
          <CardHeader>
            <CardTitle>Mantenimientos</CardTitle>
          </CardHeader>
          <CardContent>
            <p class="text-3xl font-bold">{{ stats.total_maintenance }}</p>
          </CardContent>
        </Card>

        <Card class="border-0 shadow-lg">
          <CardHeader>
            <CardTitle>Gasto total</CardTitle>
          </CardHeader>
          <CardContent>
            <p class="text-3xl font-bold">{{ stats.total_spent }} €</p>
          </CardContent>
        </Card>

        <Card class="border-0 shadow-lg">
          <CardHeader>
            <CardTitle>Coste/km</CardTitle>
          </CardHeader>
          <CardContent>
            <p class="text-3xl font-bold">{{ stats.avg_cost_per_km }} €/km</p>
          </CardContent>
        </Card>
      </div>

      <Card v-if="stats.monthly_spending?.length" class="border-0 shadow-lg">
        <CardHeader>
          <CardTitle class="flex items-center gap-2">
            <TrendingUp class="h-5 w-5" />
            Gastos mensuales
          </CardTitle>
        </CardHeader>
        <CardContent>
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
        </CardContent>
      </Card>
    </div>
  </AppSidebarLayout>
</template>
