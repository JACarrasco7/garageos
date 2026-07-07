<script setup lang="ts">
import { Link } from '@inertiajs/vue3'
import WebLayout from '@/layouts/WebLayout.vue'
import { Head } from '@inertiajs/vue3'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/Components/ui/card'
import { Badge } from '@/Components/ui/badge'
import { Button } from '@/Components/ui/button'
import {
  Car,
  Plus,
  Clock,
  CheckCircle2,
  AlertCircle,
  Truck,
  Search,
  Calculator,
  FileCheck,
  CreditCard,
} from 'lucide-vue-next'

interface VehicleImport {
  id: number
  plate_original: string | null
  plate_new: string | null
  brand: string
  model: string
  year: number
  co2_emissions: number | null
  origin_country: string
  purchase_date: string | null
  arrival_date: string | null
  itv_deadline: string | null
  current_step: string
  current_step_label: string
  current_step_order: number
  needs_homologation: boolean
  status: string
  progress_percentage: number
}

defineProps<{
  imports: {
    data: VehicleImport[]
    links: any[]
    meta: any
  }
}>()

const getStepIcon = (step: string) => {
  const icons = {
    purchase: Car,
    transport: Truck,
    itv_inspection: Search,
    taxes: Calculator,
    dgt_registration: FileCheck,
    plates: CreditCard
  }
  return icons[step as keyof typeof icons] || Clock
}

const getStatusBadge = (vehicleImport: VehicleImport) => {
  if (vehicleImport.status === 'completed') {
    return { variant: 'default' as const, class: 'bg-green-600', text: 'Completado' }
  }
  if (vehicleImport.status === 'rejected') {
    return { variant: 'destructive' as const, class: '', text: 'Rechazado' }
  }
  return { variant: 'secondary' as const, class: 'bg-blue-600', text: 'En progreso' }
}
</script>

<template>
  <Head title="Importaciones" />
  <WebLayout>
    <template #header>
      <div class="flex items-center justify-between">
        <div>
          <h1 class="text-3xl font-bold">Mis Importaciones</h1>
          <p class="text-muted-foreground mt-1">Gestiona tus trámites de importación DE→ES</p>
        </div>
        <Link :href="route('imports.create')">
          <Button>
            <Plus class="h-4 w-4 mr-2" />
            Nueva importación
          </Button>
        </Link>
      </div>
    </template>

    <div class="space-y-6">
      <div v-if="imports.data.length === 0" class="text-center py-12">
        <Car class="h-12 w-12 mx-auto text-muted-foreground mb-4" />
        <h3 class="text-lg font-semibold">No tienes importaciones</h3>
        <p class="text-muted-foreground mt-2">Comienza una nueva importación desde Alemania</p>
      </div>

      <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        <Card v-for="importItem in imports.data" :key="importItem.id">
          <CardHeader>
            <div class="flex items-start justify-between">
              <div>
                <CardTitle class="flex items-center gap-2">
                  <component :is="getStepIcon(importItem.current_step)" class="h-5 w-5" />
                  {{ importItem.brand }} {{ importItem.model }}
                </CardTitle>
                <CardDescription class="mt-1">
                  {{ importItem.year }}
                  <span v-if="importItem.plate_original" class="ml-2 text-xs">
                    {{ importItem.plate_original }}
                  </span>
                </CardDescription>
              </div>
              <Badge :variant="getStatusBadge(importItem).variant" :class="getStatusBadge(importItem).class">
                {{ getStatusBadge(importItem).text }}
              </Badge>
            </div>
          </CardHeader>
          <CardContent>
            <div class="space-y-3">
              <div>
                <div class="flex justify-between text-sm mb-1">
                  <span class="text-muted-foreground">Progreso</span>
                  <span class="font-medium">{{ importItem.progress_percentage }}%</span>
                </div>
                <div class="h-2 bg-muted rounded-full overflow-hidden">
                  <div class="h-full bg-primary transition-all" :style="{ width: `${importItem.progress_percentage}%` }" />
                </div>
              </div>
              <div class="text-sm">
                <span class="text-muted-foreground">Paso actual:</span>
                <span class="ml-2 font-medium">{{ importItem.current_step_label }}</span>
              </div>
              <Link :href="route('import.wizard', importItem.id)">
                <Button variant="outline" class="w-full">
                  Ver detalles
                </Button>
              </Link>
            </div>
          </CardContent>
        </Card>
      </div>
    </div>
  </WebLayout>
</template>
