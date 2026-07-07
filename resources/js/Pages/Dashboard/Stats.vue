<script setup lang="ts">
import AppSidebarLayout from '@/layouts/app/AppSidebarLayout.vue'
import { Head, Link } from '@inertiajs/vue3'
import { Card, CardContent, CardHeader, CardTitle } from '@/Components/ui/card'
import { Line } from 'vue-chartjs'
import {
  Chart as ChartJS,
  CategoryScale,
  LinearScale,
  PointElement,
  LineElement,
  Title,
  Tooltip,
  Legend,
  Filler,
} from 'chart.js'
import { TrendingUp } from 'lucide-vue-next'
import { computed } from 'vue'

ChartJS.register(
  CategoryScale,
  LinearScale,
  PointElement,
  LineElement,
  Title,
  Tooltip,
  Legend,
  Filler,
)

interface Stats {
  total_vehicles: number
  total_documents: number
  total_maintenance: number
  total_spent: number
  avg_cost_per_km: number
  monthly_spending?: Array<{ month: string; amount: number }>
}

const props = defineProps<{
  stats: Stats
}>()

const chartData = computed(() => ({
  labels: props.stats.monthly_spending?.map(m => m.month) ?? [],
  datasets: [
    {
      label: 'Gasto (€)',
      data: props.stats.monthly_spending?.map(m => m.amount) ?? [],
      borderColor: 'rgb(var(--primary))',
      backgroundColor: 'rgba(var(--primary), 0.1)',
      fill: true,
      tension: 0.4,
    },
  ],
}))

const chartOptions = {
  responsive: true,
  maintainAspectRatio: false,
  plugins: {
    legend: { display: false },
  },
  scales: {
    y: { beginAtZero: true },
  },
}
</script>

<template>
  <Head title="Estadísticas" />

  <AppSidebarLayout>
    <template #header>
      Estadísticas
    </template>

    <div class="space-y-6">
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <Card class="glass-panel border-0">
          <CardHeader>
            <CardTitle>Vehículos</CardTitle>
          </CardHeader>
          <CardContent>
            <p class="text-3xl font-bold">{{ stats.total_vehicles }}</p>
          </CardContent>
        </Card>

        <Card class="glass-panel border-0">
          <CardHeader>
            <CardTitle>Documentos</CardTitle>
          </CardHeader>
          <CardContent>
            <p class="text-3xl font-bold">{{ stats.total_documents }}</p>
          </CardContent>
        </Card>

        <Card class="glass-panel border-0">
          <CardHeader>
            <CardTitle>Mantenimientos</CardTitle>
          </CardHeader>
          <CardContent>
            <p class="text-3xl font-bold">{{ stats.total_maintenance }}</p>
          </CardContent>
        </Card>

        <Card class="glass-panel border-0">
          <CardHeader>
            <CardTitle>Gasto total</CardTitle>
          </CardHeader>
          <CardContent>
            <p class="text-3xl font-bold">{{ stats.total_spent }} €</p>
          </CardContent>
        </Card>

        <Card class="glass-panel border-0">
          <CardHeader>
            <CardTitle>Coste/km</CardTitle>
          </CardHeader>
          <CardContent>
            <p class="text-3xl font-bold">{{ stats.avg_cost_per_km }} €/km</p>
          </CardContent>
        </Card>
      </div>

      <Card v-if="stats.monthly_spending?.length" class="glass-panel border-0">
        <CardHeader>
          <CardTitle class="flex items-center gap-2">
            <TrendingUp class="h-5 w-5" />
            Gastos mensuales
          </CardTitle>
        </CardHeader>
        <CardContent>
          <div class="h-64">
            <Line :data="chartData" :options="chartOptions" />
          </div>
        </CardContent>
      </Card>
    </div>
  </AppSidebarLayout>
</template>

<style scoped>
.glass-panel {
  background: rgba(255, 255, 255, 0.08);
  backdrop-filter: blur(20px);
  border-radius: 24px;
  border: 1px solid rgba(255, 255, 255, 0.15);
  box-shadow: 0 4px 16px rgba(0, 0, 0, 0.08);
}
</style>

ChartJS.register(
  CategoryScale,
  LinearScale,
  PointElement,
  LineElement,
  Title,
  Tooltip,
  Legend,
  Filler,
)

interface Stats {
  total_vehicles: number
  total_documents: number
  total_maintenance: number
  total_spent: number
  avg_cost_per_km: number
  monthly_spending?: Array<{ month: string; amount: number }>
}

const props = defineProps<{
  stats: Stats
}>()

const chartData = computed(() => ({
  labels: props.stats.monthly_spending?.map(m => m.month) ?? [],
  datasets: [
    {
      label: 'Gasto (€)',
      data: props.stats.monthly_spending?.map(m => m.amount) ?? [],
      borderColor: '#3b82f6',
      backgroundColor: 'rgba(59, 130, 246, 0.1)',
      fill: true,
      tension: 0.4,
    },
  ],
}))

const chartOptions = {
  responsive: true,
  maintainAspectRatio: false,
  plugins: {
    legend: { display: false },
  },
  scales: {
    y: { beginAtZero: true },
  },
}
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
            <Line :data="chartData" :options="chartOptions" />
          </div>
        </CardContent>
      </Card>
    </div>
  </AppSidebarLayout>
</template>
