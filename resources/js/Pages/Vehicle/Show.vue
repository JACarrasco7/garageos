<script setup lang="ts">
import { Link } from '@inertiajs/vue3'
import AppSidebarLayout from '@/layouts/app/AppSidebarLayout.vue'
import { Head } from '@inertiajs/vue3'
import { Card, CardContent, CardHeader, CardTitle } from '@/Components/ui/card'
import { Badge } from '@/Components/ui/badge'
import { Button } from '@/Components/ui/button'
import { Separator } from '@/Components/ui/separator'
import { ArrowLeft, Pencil, FileText, Wrench, TrendingUp, Calendar, Fuel, Car } from 'lucide-vue-next'

interface Vehicle {
  id: number
  plate: string
  brand: string
  model: string
  year: number
  fuel_type: string
  color: string
  current_km: number
  photo: string | null
  specs?: {
    engine_cc: number | null
    power_hp: number | null
    transmission: string | null
  } | null
}

const props = defineProps<{
  vehicle: Vehicle
}>()
</script>

<template>
  <Head :title="`${props.vehicle.brand} ${props.vehicle.model}`" />

  <AppSidebarLayout>
    <template #header>
      {{ props.vehicle.brand }} {{ props.vehicle.model }}
    </template>

    <div class="max-w-4xl space-y-6">
      <Card class="border-0 shadow-lg">
        <CardHeader class="pb-3">
          <CardTitle class="flex items-center gap-2 text-lg font-semibold">
            <Car class="h-5 w-5" />
            Datos del vehículo
          </CardTitle>
        </CardHeader>
        <CardContent>
          <dl class="grid grid-cols-2 gap-4 text-sm">
            <div>
              <dt class="text-muted-foreground">Matrícula</dt>
              <dd class="font-medium">{{ props.vehicle.plate }}</dd>
            </div>
            <div>
              <dt class="text-muted-foreground">Año</dt>
              <dd class="font-medium">{{ props.vehicle.year }}</dd>
            </div>
            <div>
              <dt class="text-muted-foreground">Combustible</dt>
              <dd class="font-medium capitalize">{{ props.vehicle.fuel_type }}</dd>
            </div>
            <div>
              <dt class="text-muted-foreground">Km actuales</dt>
              <dd class="font-medium">{{ props.vehicle.current_km.toLocaleString() }} km</dd>
            </div>
            <div v-if="props.vehicle.color">
              <dt class="text-muted-foreground">Color</dt>
              <dd class="font-medium">{{ props.vehicle.color }}</dd>
            </div>
          </dl>
        </CardContent>
      </Card>

      <Card v-if="props.vehicle.specs" class="border-0 shadow-lg">
        <CardHeader class="pb-3">
          <CardTitle class="text-lg font-semibold">Especificaciones</CardTitle>
        </CardHeader>
        <CardContent>
          <dl class="grid grid-cols-3 gap-4 text-sm">
            <div v-if="props.vehicle.specs.engine_cc">
              <dt class="text-muted-foreground">Cilindrada</dt>
              <dd class="font-medium">{{ props.vehicle.specs.engine_cc }} cc</dd>
            </div>
            <div v-if="props.vehicle.specs.power_hp">
              <dt class="text-muted-foreground">Potencia</dt>
              <dd class="font-medium">{{ props.vehicle.specs.power_hp }} CV</dd>
            </div>
            <div v-if="props.vehicle.specs.transmission">
              <dt class="text-muted-foreground">Transmisión</dt>
              <dd class="font-medium capitalize">{{ props.vehicle.specs.transmission }}</dd>
            </div>
          </dl>
        </CardContent>
      </Card>

      <Separator />

      <div class="flex flex-wrap gap-3">
        <Button as-child variant="outline" size="sm" class="rounded-lg">
          <Link :href="route('vehicles.index')">
            <ArrowLeft class="mr-2 h-4 w-4" />
            Volver
          </Link>
        </Button>
        <Button as-child size="sm" class="rounded-lg">
          <Link :href="route('vehicles.edit', props.vehicle.id)">
            <Pencil class="mr-2 h-4 w-4" />
            Editar
          </Link>
        </Button>
        <Button as-child variant="secondary" size="sm" class="rounded-lg">
          <Link :href="route('documents.index', { vehicle: props.vehicle.id })">
            <FileText class="mr-2 h-4 w-4" />
            Documentos
          </Link>
        </Button>
        <Button as-child variant="secondary" size="sm" class="rounded-lg">
          <Link :href="route('maintenance.index', { vehicle: props.vehicle.id })">
            <Wrench class="mr-2 h-4 w-4" />
            Mantenimiento
          </Link>
        </Button>
        <Button as-child variant="outline" size="sm" class="rounded-lg">
          <Link :href="route('marketplace.generate', props.vehicle.id)">
            <TrendingUp class="mr-2 h-4 w-4" />
            Informe de venta
          </Link>
        </Button>
      </div>
    </div>
  </AppSidebarLayout>
</template>
