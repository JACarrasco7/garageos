<script setup lang="ts">
import { ref, computed } from 'vue'
import { router } from '@inertiajs/vue3'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/Components/ui/card'
import { Badge } from '@/Components/ui/badge'
import { Button } from '@/Components/ui/button'
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/Components/ui/tabs'
import { Checkbox } from '@/Components/ui/checkbox'
import { Label } from '@/Components/ui/label'
import { Alert, AlertDescription, AlertTitle } from '@/Components/ui/alert'
import {
  Car,
  Truck,
  Search,
  Calculator,
  FileCheck,
  CreditCard,
  CheckCircle2,
  AlertCircle,
  Clock,
  Upload,
  FileText,
  ArrowRight,
  ArrowLeft,
  AlertTriangle,
  ExternalLink,
  Copy,
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
  documents_count: number
  verified_documents_count: number
  step_completion: Record<string, boolean>
  can_advance_to: { value: string; label: string } | null
  temporary_plates: any[]
}

interface StepData {
  id: string
  label: string
  order: number
  description: string
  requiredDocs: string[]
  estimatedTime: string
  estimatedCost: string
  urls: Record<string, string>
}

const props = defineProps<{
  import: VehicleImport
  steps: StepData[]
}>()

const activeTab = ref(props.import.current_step)

const steps = computed<StepData[]>(() => [
  {
    id: 'purchase',
    label: '1. Compra en Alemania',
    order: 1,
    description: 'Obtener documentación necesaria del vehículo en Alemania',
    requiredDocs: ['compraventa', 'coc', 'ficha_tecnica_origen', 'tarjeta_itv_origen'],
    estimatedTime: '1 día',
    estimatedCost: '0-100€',
    urls: {
      'ADAC - Contrato': 'https://www.adac.de/rund-ums-fahrzeug/autoverkauf-kauf/kaufvertrag/',
      'DAT Check': 'https://www.dat.de/',
      'Carfax': 'https://www.carfax.eu/',
      'Mobile.de': 'https://www.mobile.de/',
    }
  },
  {
    id: 'transport',
    label: '2. Transporte a España',
    order: 2,
    description: 'Llevar el vehículo a España (placas temporales o transportista)',
    requiredDocs: ['seguro_transporte'],
    estimatedTime: '1-3 días',
    estimatedCost: '150-1.500€',
    urls: {
      'ADAC - Placas temporales': 'https://www.adac.de/rund-ums-fahrzeug/zulassung/kennzeichen/ausfuerrkennzeichen/',
      'ClickTrans': 'https://www.clicktrans.es/',
      'uShip': 'https://www.uShip.es/',
    }
  },
  {
    id: 'itv_inspection',
    label: '3. ITV de Importación',
    order: 3,
    description: 'Pasar la ITV de importación en España (plazo: 30 días desde llegada)',
    requiredDocs: ['ficha_itv_es'],
    estimatedTime: '1 día',
    estimatedCost: '60-80€',
    urls: {
      'ITV General': 'https://www.itv.es/',
      'Citas ITV': 'https://www.itv.es/itv-web/',
    }
  },
  {
    id: 'taxes',
    label: '4. Impuestos',
    order: 4,
    description: 'Liquidar IEDMT, IVA/ITP e IVTM',
    requiredDocs: ['modelo_576', 'modelo_309_300', 'modelo_itp', 'justificante_ivtm'],
    estimatedTime: '5-7 días',
    estimatedCost: '1.000-3.000€',
    urls: {
      'Modelo 576 (IEDMT)': 'https://sede.agenciatributaria.gob.es/acciona05i/Inicio.html',
      'Tramos CO₂ (BOE)': 'https://www.boe.es/buscar/act.php?id=BOE-A-2023-12921',
    }
  },
  {
    id: 'dgt_registration',
    label: '5. Matriculación DGT',
    order: 5,
    description: 'Matricular el vehículo en la DGT',
    requiredDocs: ['permiso_circulacion'],
    estimatedTime: '2-3 días',
    estimatedCost: '52€ + seguro',
    urls: {
      'Matriculación DGT': 'https://sede.dgt.gob.es/es/tramites-y-multas/vehiculo/matriculacion/matriculacion-ordinaria/',
    }
  },
  {
    id: 'plates',
    label: '6. Placas Físicas',
    order: 6,
    description: 'Colocar placas españolas',
    requiredDocs: [],
    estimatedTime: '1 día',
    estimatedCost: '30-50€',
    urls: {}
  }
])

const currentStepData = computed(() => steps.value.find(s => s.id === activeTab.value))

const progressPercentage = computed(() => {
  const stepIndex = steps.value.findIndex(s => s.id === props.import.current_step)
  return Math.round((stepIndex / steps.value.length) * 100)
})

const isStepCompleted = (stepId: string): boolean => {
  return props.import.step_completion[stepId] || false
}

const isStepActive = (stepOrder: number): boolean => {
  return stepOrder <= props.import.current_step_order
}

const canAdvance = computed(() => props.import.can_advance_to !== null)

const daysUntilItvDeadline = computed(() => {
  if (!props.import.itv_deadline) return null
  const deadline = new Date(props.import.itv_deadline)
  const today = new Date()
  return Math.ceil((deadline.getTime() - today.getTime()) / (1000 * 60 * 60 * 24))
})

const itvDeadlineStatus = computed(() => {
  const days = daysUntilItvDeadline.value
  if (days === null) return null
  if (days < 0) return { status: 'expired', color: 'bg-red-600', text: 'Expirado' }
  if (days <= 7) return { status: 'urgent', color: 'bg-orange-600', text: `¡Solo ${days} días!` }
  if (days <= 14) return { status: 'warning', color: 'bg-yellow-600', text: `${days} días restantes` }
  return { status: 'ok', color: 'bg-green-600', text: `${days} días restantes` }
})

const totalEstimatedCost = computed(() => {
  const costs: Record<string, string> = {
    purchase: '0-100',
    transport: '150-1500',
    itv_inspection: '60-80',
    taxes: '1000-3000',
    dgt_registration: '52-1200',
    plates: '30-50'
  }

  const completedSteps = steps.value.filter(s => isStepCompleted(s.id))
  let totalMin = 0
  let totalMax = 0

  completedSteps.forEach(step => {
    const range = costs[step.id as keyof typeof costs].split('-').map(n => parseInt(n.trim()))
    totalMin += range[0] || 0
    totalMax += range[1] || 0
  })

  return { min: totalMin, max: totalMax }
})

const advanceToNextStep = () => {
  if (!props.import.can_advance_to) return

  router.patch(route('import.update-step', props.import.id), {
    step: props.import.can_advance_to.value
  }, {
    preserveScroll: true,
    onSuccess: () => {
      activeTab.value = props.import.can_advance_to!.value
    }
  })
}

const goToStep = (stepId: string) => {
  const step = steps.value.find(s => s.id === stepId)
  if (!step || !isStepActive(step.order)) return

  router.patch(route('import.update-step', props.import.id), { step: stepId }, {
    preserveScroll: true,
    onSuccess: () => { activeTab.value = stepId }
  })
}

const getStepIcon = (step: StepData) => {
  const icons = {
    purchase: Car,
    transport: Truck,
    itv_inspection: Search,
    taxes: Calculator,
    dgt_registration: FileCheck,
    plates: CreditCard
  }
  return icons[step.id as keyof typeof icons] || FileText
}

const getStepStatusBadge = (step: StepData) => {
  if (isStepCompleted(step.id)) {
    return { variant: 'default' as const, class: 'bg-green-600 hover:bg-green-700', icon: CheckCircle2, text: 'Completado' }
  }
  if (activeTab.value === step.id) {
    return { variant: 'secondary' as const, class: 'bg-blue-600', icon: Clock, text: 'En curso' }
  }
  return { variant: 'outline' as const, class: '', icon: AlertCircle, text: 'Pendiente' }
}

const copyToClipboard = (text: string) => {
  navigator.clipboard.writeText(text)
}
</script>

<template>
  <div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
      <div>
        <h1 class="text-3xl font-bold">Importación {{ import.origin_country === 'DE' ? 'Alemania' : 'Extranjero' }} → España</h1>
        <p class="text-muted-foreground mt-1">
          {{ import.brand }} {{ import.model }} ({{ import.year }})
          <span v-if="import.plate_original" class="ml-2 px-2 py-1 bg-muted rounded text-sm">
            {{ import.plate_original }}
          </span>
        </p>
      </div>
      <div class="flex items-center gap-3">
        <Badge :variant="canAdvance ? 'default' : 'outline'" class="text-sm px-4 py-2">
          Progreso: {{ progressPercentage }}%
        </Badge>
        <Badge variant="outline" class="text-sm px-4 py-2">
          {{ import.documents_count }}/{{ import.verified_documents_count }} documentos
        </Badge>
      </div>
    </div>

    <!-- Progress Bar -->
    <div class="bg-card rounded-lg p-6 border">
      <div class="flex items-center gap-4 mb-4">
        <div class="h-3 flex-1 bg-muted rounded-full overflow-hidden">
          <div class="h-full bg-gradient-to-r from-blue-600 to-purple-600 transition-all duration-500" :style="{ width: `${progressPercentage}%` }" />
        </div>
        <span class="text-sm font-medium whitespace-nowrap min-w-[120px]">
          Paso {{ currentStepData?.order }} de 6
        </span>
      </div>
      <div class="flex flex-wrap gap-2">
        <Badge
          v-for="step in steps"
          :key="step.id"
          :class="[
            'cursor-pointer transition-all',
            isStepCompleted(step.id) ? 'bg-green-600 hover:bg-green-700' :
            activeTab === step.id ? 'bg-blue-600' :
            isStepActive(step.order) ? 'bg-muted' : 'opacity-50 bg-muted'
          ]"
          @click="goToStep(step.id)"
        >
          {{ step.label }}
        </Badge>
      </div>
    </div>

    <!-- ITV Deadline Alert -->
    <Alert v-if="import.arrival_date && itvDeadlineStatus" :class="itvDeadlineStatus.color">
      <AlertTriangle class="h-4 w-4" />
      <AlertTitle class="font-semibold">{{ itvDeadlineStatus.text }}</AlertTitle>
      <AlertDescription>
        Tienes {{ daysUntilItvDeadline }} días desde la llegada para pasar la ITV de importación.
        <span v-if="itvDeadlineStatus.status === 'urgent'" class="font-semibold">¡Actúa ahora!</span>
      </AlertDescription>
    </Alert>

    <!-- Import Steps -->
    <Tabs v-model="activeTab" class="w-full">
      <TabsList class="grid w-full grid-cols-6">
        <TabsTrigger
          v-for="step in steps"
          :key="step.id"
          :value="step.id"
          :disabled="!isStepActive(step.order)"
          class="flex flex-col items-center gap-1 py-3 text-xs"
        >
          <component :is="getStepIcon(step)" class="h-4 w-4" />
          <span class="hidden sm:inline">{{ step.order }}</span>
        </TabsTrigger>
      </TabsList>

      <TabsContent v-for="step in steps" :key="step.id" :value="step.id" class="mt-6">
        <Card>
          <CardHeader>
            <div class="flex items-start justify-between">
              <div>
                <CardTitle class="flex items-center gap-2 text-2xl">
                  <component :is="getStepIcon(step)" class="h-6 w-6" />
                  {{ step.label }}
                </CardTitle>
                <CardDescription class="mt-2 text-base">{{ step.description }}</CardDescription>
              </div>
              <Badge :variant="getStepStatusBadge(step).variant" :class="getStepStatusBadge(step).class">
                <component :is="getStepStatusBadge(step).icon" class="h-4 w-4 mr-1" />
                {{ getStepStatusBadge(step).text }}
              </Badge>
            </div>
          </CardHeader>
          <CardContent class="space-y-6">
            <!-- Time and Cost -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div class="flex items-center gap-3 p-4 bg-muted/50 rounded-lg">
                <Clock class="h-5 w-5 text-blue-600" />
                <div>
                  <p class="text-sm text-muted-foreground">Tiempo estimado</p>
                  <p class="font-semibold text-lg">{{ step.estimatedTime }}</p>
                </div>
              </div>
              <div class="flex items-center gap-3 p-4 bg-muted/50 rounded-lg">
                <Calculator class="h-5 w-5 text-green-600" />
                <div>
                  <p class="text-sm text-muted-foreground">Coste estimado</p>
                  <p class="font-semibold text-lg">{{ step.estimatedCost }}</p>
                </div>
              </div>
            </div>

            <!-- External Links -->
            <div v-if="Object.keys(step.urls).length > 0" class="space-y-3">
              <h3 class="font-semibold text-lg flex items-center gap-2">
                <ExternalLink class="h-5 w-5" />
                Enlaces útiles
              </h3>
              <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                <a
                  v-for="(url, label) in step.urls"
                  :key="label"
                  :href="url"
                  target="_blank"
                  rel="noopener noreferrer"
                  class="flex items-center justify-between p-4 rounded-lg border hover:bg-accent transition-colors group"
                >
                  <div class="flex-1 min-w-0">
                    <span class="font-medium text-sm block truncate">{{ label }}</span>
                    <span class="text-xs text-muted-foreground block truncate">{{ url }}</span>
                  </div>
                  <div class="flex items-center gap-2 ml-2">
                    <Button variant="ghost" size="sm" @click.prevent="copyToClipboard(url)">
                      <Copy class="h-4 w-4" />
                    </Button>
                    <ExternalLink class="h-4 w-4 text-muted-foreground group-hover:text-foreground" />
                  </div>
                </a>
              </div>
            </div>

            <!-- Step Navigation -->
            <div class="flex items-center justify-between pt-6 border-t">
              <Button
                v-if="step.order > 1"
                variant="outline"
                @click="activeTab = steps[step.order - 2].id"
              >
                <ArrowLeft class="h-4 w-4 mr-2" />
                Paso anterior
              </Button>
              <div v-else></div>

              <div v-if="activeTab === step.id" class="flex items-center gap-3">
                <div v-if="step.requiredDocs.length > 0" class="text-sm text-muted-foreground">
                  {{ isStepCompleted(step.id) ? '✓' : '○' }}
                  {{ step.requiredDocs.filter(d => isStepCompleted(step.id)).length }}/{{ step.requiredDocs.length }} documentos
                </div>
                <Button v-if="step.id !== 'plates'" :disabled="!canAdvance" @click="advanceToNextStep">
                  Siguiente paso
                  <ArrowRight class="h-4 w-4 ml-2" />
                </Button>
                <Badge v-else variant="default" class="bg-green-600 text-base px-4 py-2">
                  <CheckCircle2 class="h-5 w-5 mr-2" />
                  Importación completada
                </Badge>
              </div>
            </div>
          </CardContent>
        </Card>
      </TabsContent>
    </Tabs>

    <!-- Total Cost Summary -->
    <Card>
      <CardHeader>
        <CardTitle class="flex items-center gap-2">
          <Calculator class="h-5 w-5" />
          Costes totales acumulados
        </CardTitle>
      </CardHeader>
      <CardContent>
        <div class="flex items-end gap-2">
          <span class="text-4xl font-bold">{{ totalEstimatedCost.min }}€</span>
          <span v-if="totalEstimatedCost.max > totalEstimatedCost.min" class="text-2xl text-muted-foreground mb-1">
            - {{ totalEstimatedCost.max }}€
          </span>
        </div>
        <p class="text-sm text-muted-foreground mt-2">
          Coste estimado de los pasos completados (sin incluir el precio del vehículo)
        </p>
      </CardContent>
    </Card>
  </div>
</template>
</script>

<template>
  <div class="space-y-6">
    <div class="flex items-center justify-between">
      <div>
        <h1 class="text-3xl font-bold">Importación Alemania → España</h1>
        <p class="text-muted-foreground mt-1">Sigue los 6 pasos para matricular tu vehículo</p>
      </div>
      <Badge variant="outline" class="text-lg px-4 py-2">
        Progreso: {{ progressPercentage }}%
      </Badge>
    </div>

    <div class="bg-card rounded-lg p-6 border">
      <div class="flex items-center gap-4 mb-4">
        <div class="h-2 flex-1 bg-muted rounded-full overflow-hidden">
          <div
            class="h-full bg-primary transition-all duration-500"
            :style="{ width: `${progressPercentage}%` }"
          />
        </div>
        <span class="text-sm font-medium whitespace-nowrap">
          Paso {{ currentStepData?.order }} de {{ steps.length }}
        </span>
      </div>
      <div class="flex flex-wrap gap-2">
        <Badge
          v-for="step in steps"
          :key="step.id"
          :variant="isStepCompleted(step.id) ? 'default' : 'outline'"
          :class="{
            'opacity-50': !isStepActive(step.id),
            'bg-green-600': isStepCompleted(step.id) && step.id !== 'plates'
          }"
        >
          {{ step.label }}
        </Badge>
      </div>
    </div>

    <Tabs v-model="activeTab" class="w-full">
      <TabsList class="grid w-full grid-cols-6">
        <TabsTrigger
          v-for="step in steps"
          :key="step.id"
          :value="step.id"
          :disabled="!isStepActive(step.id)"
          class="flex flex-col items-center gap-1 py-3 text-xs"
        >
          <component :is="getStepIcon(step)" class="h-4 w-4" />
          <span class="hidden sm:inline">{{ step.order }}</span>
        </TabsTrigger>
      </TabsList>

      <TabsContent v-for="step in steps" :key="step.id" :value="step.id" class="mt-6 space-y-6">
        <Card>
          <CardHeader>
            <CardTitle class="flex items-center gap-2">
              <component :is="getStepIcon(step)" class="h-5 w-5" />
              {{ step.label }}
            </CardTitle>
            <CardDescription>{{ step.description }}</CardDescription>
          </CardHeader>
          <CardContent class="space-y-4">
            <div class="grid grid-cols-2 gap-4">
              <div class="flex items-center gap-2 text-sm">
                <Clock class="h-4 w-4 text-muted-foreground" />
                <span class="text-muted-foreground">Tiempo estimado:</span>
                <span class="font-medium">{{ step.estimatedTime }}</span>
              </div>
              <div class="flex items-center gap-2 text-sm">
                <Calculator class="h-4 w-4 text-muted-foreground" />
                <span class="text-muted-foreground">Coste estimado:</span>
                <span class="font-medium">{{ step.estimatedCost }}</span>
              </div>
            </div>

            <div v-if="step.requiredDocs.length > 0" class="space-y-3">
              <h3 class="font-medium flex items-center gap-2">
                <FileText class="h-4 w-4" />
                Documentos requeridos
              </h3>
              <div class="space-y-2">
                <div
                  v-for="doc in step.requiredDocs"
                  :key="doc"
                  class="flex items-center justify-between p-3 rounded-lg border bg-muted/50"
                >
                  <div class="flex items-center gap-3">
                    <Checkbox
                      :id="doc"
                      v-model="documents[doc]"
                      :disabled="!isStepActive(step.id)"
                      @update:modelValue="() => {}"
                    />
                    <Label :for="doc" class="cursor-pointer">
                      {{ doc.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase()) }}
                    </Label>
                  </div>
                  <div class="flex items-center gap-2">
                    <CheckCircle2
                      v-if="documents[doc]"
                      class="h-5 w-5 text-green-600"
                    />
                    <AlertCircle
                      v-else
                      class="h-5 w-5 text-muted-foreground"
                    />
                  </div>
                </div>
              </div>
            </div>

            <div v-if="Object.keys(step.urls).length > 0" class="space-y-3">
              <h3 class="font-medium">Enlaces útiles</h3>
              <div class="space-y-2">
                <a
                  v-for="(url, label) in step.urls"
                  :key="label"
                  :href="url"
                  target="_blank"
                  rel="noopener noreferrer"
                  class="block p-3 rounded-lg border hover:bg-accent transition-colors"
                >
                  <span class="text-sm font-medium">{{ label }}</span>
                  <span class="block text-xs text-muted-foreground mt-1">{{ url }}</span>
                </a>
              </div>
            </div>

            <div v-if="activeTab === step.id" class="flex items-center justify-between pt-4 border-t">
              <div class="text-sm text-muted-foreground">
                Completado: {{ step.requiredDocs.filter(d => documents[d]).length }}/{{ step.requiredDocs.length }} documentos
              </div>
              <Button
                v-if="step.id !== 'plates'"
                :disabled="!canAdvance"
                @click="advanceToNextStep"
              >
                Siguiente paso
              </Button>
              <Badge v-else variant="default" class="bg-green-600">
                Importación completada
              </Badge>
            </div>
          </CardContent>
        </Card>
      </TabsContent>
    </Tabs>

    <Card>
      <CardHeader>
        <CardTitle class="flex items-center gap-2">
          <Calculator class="h-5 w-5" />
          Costes totales acumulados
        </CardTitle>
      </CardHeader>
      <CardContent>
        <div class="text-3xl font-bold">
          {{ totalEstimatedCost.toFixed(2) }}€
        </div>
        <p class="text-sm text-muted-foreground mt-1">
          Coste estimado de los pasos completados (sin incluir el precio del vehículo)
        </p>
      </CardContent>
    </Card>
  </div>
</template>