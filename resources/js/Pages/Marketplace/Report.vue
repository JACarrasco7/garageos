<script setup lang="ts">
import { Link } from '@inertiajs/vue3'
import WebLayout from '@/layouts/WebLayout.vue'
import { Head } from '@inertiajs/vue3'
import { Card, CardContent, CardHeader, CardTitle, CardDescription, CardFooter } from '@/Components/ui/card'
import { Badge } from '@/Components/ui/badge'
import { Button } from '@/Components/ui/button'
import { Download, ArrowLeft } from 'lucide-vue-next'

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

  <WebLayout>
    <template #header>
      Informe de Venta
    </template>

    <div class="max-w-3xl mx-auto">
      <Card>
        <CardHeader class="text-center">
          <CardTitle class="text-2xl">{{ vehicle.brand }} {{ vehicle.model }}</CardTitle>
          <CardDescription>{{ vehicle.plate }}</CardDescription>
        </CardHeader>

        <CardContent class="text-center mb-8">
          <p class="text-sm text-muted-foreground mb-2">Puntuación de Salud</p>
          <div class="text-6xl font-bold" :class="report.score >= 70 ? 'text-green-600 dark:text-green-400' : report.score >= 40 ? 'text-amber-600 dark:text-amber-400' : 'text-destructive'">
            {{ report.score }}/100
          </div>
          <Badge :variant="report.score >= 70 ? 'default' : report.score >= 40 ? 'secondary' : 'destructive'" class="mt-2">
            {{ report.score >= 70 ? 'Excelente' : report.score >= 40 ? 'Regular' : 'Necesita atención' }}
          </Badge>
        </CardContent>

        <CardFooter class="flex justify-center space-x-4">
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
        </CardFooter>
      </Card>
    </div>
  </WebLayout>
</template>
