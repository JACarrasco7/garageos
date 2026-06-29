<script setup lang="ts">
import { Link } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head } from '@inertiajs/vue3'
import { Card, CardContent, CardHeader, CardTitle, CardDescription, CardFooter } from '@/Components/ui/card'
import { Badge } from '@/Components/ui/badge'
import { Button } from '@/Components/ui/button'
import { Download, ArrowLeft, Eye, Calendar } from 'lucide-vue-next'

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
  token: string
  score: number
  views: number
  created_at: string
}

defineProps<{
  report: Report
  vehicle: Vehicle
}>()

const scoreVariant = (score: number) => {
  if (score >= 70) return 'default'
  if (score >= 40) return 'secondary'
  return 'destructive'
}
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
          <CardHeader class="text-center">
            <CardTitle class="text-2xl">{{ vehicle.brand }} {{ vehicle.model }}</CardTitle>
            <CardDescription>{{ vehicle.plate }} • {{ vehicle.year }}</CardDescription>
          </CardHeader>

          <CardContent class="text-center mb-8">
            <p class="text-sm text-muted-foreground mb-2">Puntuación de Salud</p>
            <div class="text-6xl font-bold" :class="report.score >= 70 ? 'text-green-600 dark:text-green-400' : report.score >= 40 ? 'text-amber-600 dark:text-amber-400' : 'text-destructive'">
              {{ report.score }}/100
            </div>
            <Badge :variant="scoreVariant(report.score)" class="mt-2">
              {{ report.score >= 70 ? 'Excelente' : report.score >= 40 ? 'Regular' : 'Necesita atención' }}
            </Badge>
          </CardContent>

          <CardFooter class="border-t pt-4">
            <div class="flex justify-between text-sm text-muted-foreground mb-4 w-full">
              <span class="flex items-center gap-1"><Eye class="h-4 w-4" /> {{ report.views }}</span>
              <span class="flex items-center gap-1"><Calendar class="h-4 w-4" /> {{ new Date(report.created_at).toLocaleDateString() }}</span>
            </div>

            <div class="flex justify-center space-x-4 w-full">
              <Button as-child>
                <a :href="route('marketplace.download', report)" target="_blank">
                  <Download class="mr-2 h-4 w-4" />
                  Descargar PDF
                </a>
              </Button>
              <Button as-child variant="ghost">
                <Link :href="route('vehicles.show', vehicle.id)">
                  <ArrowLeft class="mr-2 h-4 w-4" />
                  Volver al vehículo
                </Link>
              </Button>
            </div>
          </CardFooter>
        </Card>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
