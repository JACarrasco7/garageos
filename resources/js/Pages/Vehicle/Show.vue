<script setup lang="ts">
import { Link } from '@inertiajs/vue3'
import { Head } from '@inertiajs/vue3'
import { ref } from 'vue'
import AppSidebarLayout from '@/layouts/app/AppSidebarLayout.vue'
import { Card, CardContent, CardHeader, CardTitle } from '@/Components/ui/card'
import { Badge } from '@/Components/ui/badge'
import { Button } from '@/Components/ui/button'
import { Separator } from '@/Components/ui/separator'
import {
  ArrowLeft,
  Pencil,
  FileText,
  Wrench,
  TrendingUp,
  Calendar,
  Fuel,
  Car,
  Image as ImageIcon,
} from 'lucide-vue-next'
import { cn } from '@/lib/utils'

interface Vehicle {
  id: number
  plate: string
  brand: string
  model: string
  year: number
  registration_date: string | null
  fuel_type: string
  eco_label: string | null
  emissions_co2: number | null
  official_consumption: number | null
  color: string
  current_km: number
  photo: string | null
  specs?: {
    engine_cc: number | null
    power_hp: number | null
    torque_nm: number | null
    transmission: string | null
    drive: string | null
    doors: number | null
    seats: number | null
  } | null
}

const props = defineProps<{
  vehicle: Vehicle
}>()

const activeTab = ref('info')

const tabs = [
  { id: 'info', label: 'Información', icon: Car },
  { id: 'specs', label: 'Ficha técnica', icon: Wrench },
  { id: 'photos', label: 'Fotos', icon: ImageIcon },
  { id: 'documents', label: 'Documentos', icon: FileText },
  { id: 'maintenance', label: 'Mantenimiento', icon: Wrench },
]

const ecoLabelColors: Record<string, string> = {
  ECO: 'bg-green-500 text-white',
  C: 'bg-blue-500 text-white',
  B: 'bg-yellow-500 text-black',
  Zero: 'bg-sky-600 text-white',
}
</script>

<template>
  <Head :title="`${props.vehicle.brand} ${props.vehicle.model}`" />

  <AppSidebarLayout>
    <template #header>
      {{ props.vehicle.brand }} {{ props.vehicle.model }}
    </template>

    <div class="max-w-4xl space-y-6">
      <!-- Tabs -->
      <div class="flex flex-wrap gap-2 border-b border-border pb-2">
        <Button
          v-for="tab in tabs"
          :key="tab.id"
          :variant="activeTab === tab.id ? 'default' : 'ghost'"
          size="sm"
          @click="activeTab = tab.id"
          class="rounded-lg"
        >
          <component :is="tab.icon" class="mr-2 h-4 w-4" />
          {{ tab.label }}
        </Button>
      </div>

      <!-- Tab: Información -->
      <Card v-show="activeTab === 'info'" class="glass-surface border-0">
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
            <div v-if="props.vehicle.registration_date">
              <dt class="text-muted-foreground">Matriculación</dt>
              <dd class="font-medium">{{ props.vehicle.registration_date }}</dd>
            </div>
            <div>
              <dt class="text-muted-foreground">Combustible</dt>
              <dd class="font-medium capitalize">{{ props.vehicle.fuel_type }}</dd>
            </div>
            <div v-if="props.vehicle.eco_label">
              <dt class="text-muted-foreground">Etiqueta ambiental</dt>
              <dd class="font-medium">
                <Badge :class="ecoLabelColors[props.vehicle.eco_label]">
                  {{ props.vehicle.eco_label }}
                </Badge>
              </dd>
            </div>
            <div v-if="props.vehicle.emissions_co2">
              <dt class="text-muted-foreground">Emisiones CO2</dt>
              <dd class="font-medium">{{ props.vehicle.emissions_co2 }} g/km</dd>
            </div>
            <div v-if="props.vehicle.official_consumption">
              <dt class="text-muted-foreground">Consumo oficial</dt>
              <dd class="font-medium">{{ props.vehicle.official_consumption }} l/100km</dd>
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

      <!-- Tab: Ficha técnica -->
      <Card v-show="activeTab === 'specs'" class="glass-surface border-0">
        <CardHeader class="pb-3">
          <CardTitle class="text-lg font-semibold">Especificaciones</CardTitle>
        </CardHeader>
        <CardContent>
          <dl v-if="props.vehicle.specs" class="grid grid-cols-3 gap-4 text-sm">
            <div v-if="props.vehicle.specs.engine_cc">
              <dt class="text-muted-foreground">Cilindrada</dt>
              <dd class="font-medium">{{ props.vehicle.specs.engine_cc }} cc</dd>
            </div>
            <div v-if="props.vehicle.specs.power_hp">
              <dt class="text-muted-foreground">Potencia</dt>
              <dd class="font-medium">{{ props.vehicle.specs.power_hp }} CV</dd>
            </div>
            <div v-if="props.vehicle.specs.torque_nm">
              <dt class="text-muted-foreground">Par motor</dt>
              <dd class="font-medium">{{ props.vehicle.specs.torque_nm }} Nm</dd>
            </div>
            <div v-if="props.vehicle.specs.transmission">
              <dt class="text-muted-foreground">Transmisión</dt>
              <dd class="font-medium capitalize">{{ props.vehicle.specs.transmission }}</dd>
            </div>
            <div v-if="props.vehicle.specs.drive">
              <dt class="text-muted-foreground">Tracción</dt>
              <dd class="font-medium">{{ props.vehicle.specs.drive.toUpperCase() }}</dd>
            </div>
            <div v-if="props.vehicle.specs.doors">
              <dt class="text-muted-foreground">Puertas</dt>
              <dd class="font-medium">{{ props.vehicle.specs.doors }}</dd>
            </div>
            <div v-if="props.vehicle.specs.seats">
              <dt class="text-muted-foreground">Asientos</dt>
              <dd class="font-medium">{{ props.vehicle.specs.seats }}</dd>
            </div>
          </dl>
          <p v-else class="text-muted-foreground">No hay especificaciones registradas.</p>
        </CardContent>
      </Card>

      <!-- Tab: Fotos -->
      <Card v-show="activeTab === 'photos'" class="glass-surface border-0">
        <CardHeader class="pb-3">
          <CardTitle class="flex items-center gap-2 text-lg font-semibold">
            <ImageIcon class="h-5 w-5" />
            Fotos del vehículo
          </CardTitle>
        </CardHeader>
        <CardContent>
          <div class="flex flex-col items-center justify-center py-8">
            <ImageIcon class="h-16 w-16 text-muted-foreground/50 mb-4" />
            <p class="text-muted-foreground mb-4">Gestiona las fotos de tu vehículo</p>
            <Button as-child>
              <Link :href="route('vehicles.photos.index', props.vehicle.id)">
                Ver galería de fotos
              </Link>
            </Button>
          </div>
        </CardContent>
      </Card>

      <!-- Tab: Documentos -->
      <Card v-show="activeTab === 'documents'" class="glass-surface border-0">
        <CardHeader class="pb-3">
          <CardTitle class="flex items-center gap-2 text-lg font-semibold">
            <FileText class="h-5 w-5" />
            Documentos
          </CardTitle>
        </CardHeader>
        <CardContent>
          <div class="flex flex-col items-center justify-center py-8">
            <FileText class="h-16 w-16 text-muted-foreground/50 mb-4" />
            <p class="text-muted-foreground mb-4">Gestiona los documentos de tu vehículo</p>
            <Button as-child>
              <Link :href="route('documents.index', { vehicle: props.vehicle.id })">
                Ver documentos
              </Link>
            </Button>
          </div>
        </CardContent>
      </Card>

      <!-- Tab: Mantenimiento -->
      <Card v-show="activeTab === 'maintenance'" class="glass-surface border-0">
        <CardHeader class="pb-3">
          <CardTitle class="flex items-center gap-2 text-lg font-semibold">
            <Wrench class="h-5 w-5" />
            Mantenimiento
          </CardTitle>
        </CardHeader>
        <CardContent>
          <div class="flex flex-col items-center justify-center py-8">
            <Wrench class="h-16 w-16 text-muted-foreground/50 mb-4" />
            <p class="text-muted-foreground mb-4">Historial de mantenimiento</p>
            <Button as-child>
              <Link :href="route('maintenance.index', { vehicle: props.vehicle.id })">
                Ver mantenimiento
              </Link>
            </Button>
          </div>
        </CardContent>
      </Card>

      <Separator />

      <div class="flex flex-wrap gap-3">
        <Button as-child variant="ghost" size="sm" class="rounded-lg">
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
