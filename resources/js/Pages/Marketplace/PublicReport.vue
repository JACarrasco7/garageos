<script setup lang="ts">
import { Head } from '@inertiajs/vue3'
import { Card, CardContent, CardHeader, CardTitle, CardDescription, CardFooter } from '@/Components/ui/card'
import { Badge } from '@/Components/ui/badge'
import { Eye, Calendar } from 'lucide-vue-next'

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

  <div class="bg-background min-h-screen py-12">
    <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
      <Card>
        <CardHeader class="text-center">
          <CardTitle class="text-2xl">Informe de Venta</CardTitle>
          <CardDescription class="text-xl">{{ vehicle.brand }} {{ vehicle.model }}</CardDescription>
          <p class="text-muted-foreground">{{ vehicle.plate }} • {{ vehicle.year }}</p>
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

        <CardFooter class="flex justify-center gap-6 text-muted-foreground">
          <span class="flex items-center gap-1"><Calendar class="h-4 w-4" /> {{ new Date(report.created_at).toLocaleDateString() }}</span>
          <span class="flex items-center gap-1"><Eye class="h-4 w-4" /> {{ report.views }} vistas</span>
        </CardFooter>
      </Card>
    </div>
  </div>
</template>
