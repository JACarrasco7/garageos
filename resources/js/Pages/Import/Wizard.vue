<script setup lang="ts">
import { ref, computed } from 'vue'
import { router } from '@inertiajs/vue3'
import { Badge } from '@/Components/ui/badge'
import { Button } from '@/Components/ui/button'
import { Alert, AlertDescription, AlertTitle } from '@/Components/ui/alert'
import { Tabs, TabsList, TabsTrigger, TabsContent } from '@/Components/ui/tabs'
import {
  Car, Truck, Search, Calculator, FileCheck, CreditCard,
  CheckCircle2, Clock, AlertTriangle, Trophy
} from 'lucide-vue-next'
import Purchase from './WizardSteps/Purchase.vue'
import Transport from './WizardSteps/Transport.vue'
import ItvInspection from './WizardSteps/ItvInspection.vue'
import Taxes from './WizardSteps/Taxes.vue'
import DgtRegistration from './WizardSteps/DgtRegistration.vue'
import Plates from './WizardSteps/Plates.vue'

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

const props = defineProps<{
  vehicleImport: VehicleImport
}>()

const activeTab = ref(props.vehicleImport.current_step)

const importSteps = [
  { id: 'purchase', label: '1. Compra', order: 1, icon: Car },
  { id: 'transport', label: '2. Transporte', order: 2, icon: Truck },
  { id: 'itv_inspection', label: '3. ITV', order: 3, icon: Search },
  { id: 'taxes', label: '4. Impuestos', order: 4, icon: Calculator },
  { id: 'dgt_registration', label: '5. Matriculación', order: 5, icon: FileCheck },
  { id: 'plates', label: '6. Placas', order: 6, icon: CreditCard },
]

const currentStepData = computed(() => importSteps.find(s => s.id === activeTab.value))

const progressPercentage = computed(() => {
  return props.vehicleImport.progress_percentage
})

const isStepCompleted = (stepId: string): boolean => {
  return props.vehicleImport.step_completion[stepId] || false
}

const isStepActive = (stepOrder: number): boolean => {
  return stepOrder <= props.vehicleImport.current_step_order
}

const canAdvance = computed(() => props.vehicleImport.can_advance_to !== null)

const daysUntilItvDeadline = computed(() => {
  if (!props.vehicleImport.itv_deadline) return null
  const deadline = new Date(props.vehicleImport.itv_deadline)
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

const advanceToNextStep = () => {
  if (!props.vehicleImport.can_advance_to) return

  router.patch(route('import.update-step', props.vehicleImport.id), {
    step: props.vehicleImport.can_advance_to.value
  }, {
    preserveScroll: true,
    onSuccess: () => {
      activeTab.value = props.vehicleImport.can_advance_to!.value
    }
  })
}

const goToStep = (stepId: string) => {
  const step = importSteps.find(s => s.id === stepId)
  if (!step || !isStepActive(step.order)) return

  router.patch(route('import.update-step', props.vehicleImport.id), { step: stepId }, {
    preserveScroll: true,
    onSuccess: () => { activeTab.value = stepId }
  })
}

const handleStepCompleted = () => {
  if (props.vehicleImport.can_advance_to) {
    advanceToNextStep()
  }
}

const handleImportFinished = () => {
  router.visit(route('imports.show', props.vehicleImport.id))
}

const generateCertificate = () => {
  router.post(route('import.certificate', props.vehicleImport.id), {}, {
    onSuccess: (response: any) => {
      if (response?.props?.certificate_url) {
        window.open(response.props.certificate_url, '_blank')
      }
    }
  })
}

const getStepIcon = (step: any) => {
  return step.icon
}

const getStepStatusBadge = (step: any) => {
  if (isStepCompleted(step.id)) {
    return { variant: 'default' as const, class: 'bg-green-600 hover:bg-green-700', icon: CheckCircle2, text: 'Completado' }
  }
  if (activeTab.value === step.id) {
    return { variant: 'secondary' as const, class: 'bg-blue-600', icon: Clock, text: 'En curso' }
  }
  return { variant: 'outline' as const, class: '', icon: Clock, text: 'Pendiente' }
}
</script>

<template>
  <div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
      <div>
        <h1 class="text-3xl font-bold">Importación {{ vehicleImport.origin_country === 'DE' ? 'Alemania' : 'Extranjero' }} → España</h1>
        <p class="text-muted-foreground mt-1">
          {{ vehicleImport.brand }} {{ vehicleImport.model }} ({{ vehicleImport.year }})
          <span v-if="vehicleImport.plate_original" class="ml-2 px-2 py-1 bg-muted rounded text-sm">
            {{ vehicleImport.plate_original }}
          </span>
        </p>
      </div>
      <div class="flex items-center gap-3">
        <Badge :variant="canAdvance ? 'default' : 'outline'" class="text-sm px-4 py-2">
          Progreso: {{ progressPercentage }}%
        </Badge>
        <Badge variant="outline" class="text-sm px-4 py-2">
          {{ vehicleImport.documents_count }}/{{ vehicleImport.verified_documents_count }} documentos
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
          v-for="step in importSteps"
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
    <Alert v-if="vehicleImport.arrival_date && itvDeadlineStatus" :class="itvDeadlineStatus.color">
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
          v-for="step in importSteps"
          :key="step.id"
          :value="step.id"
          :disabled="!isStepActive(step.order)"
          class="flex flex-col items-center gap-1 py-3 text-xs"
        >
          <component :is="getStepIcon(step)" class="h-4 w-4" />
          <span class="hidden sm:inline">{{ step.order }}</span>
        </TabsTrigger>
      </TabsList>

      <TabsContent value="purchase" class="mt-6">
        <Purchase
          :import-id="vehicleImport.id"
          :documents="[]"
          :step-data="{}"
          @step-completed="handleStepCompleted"
        />
      </TabsContent>

      <TabsContent value="transport" class="mt-6">
        <Transport
          :import-id="vehicleImport.id"
          :documents="[]"
          :step-data="{}"
          @step-completed="handleStepCompleted"
        />
      </TabsContent>

      <TabsContent value="itv_inspection" class="mt-6">
        <ItvInspection
          :import-id="vehicleImport.id"
          :documents="[]"
          :step-data="{}"
          :itv-deadline="vehicleImport.itv_deadline"
          @step-completed="handleStepCompleted"
        />
      </TabsContent>

      <TabsContent value="taxes" class="mt-6">
        <Taxes
          :import-id="vehicleImport.id"
          :documents="[]"
          :step-data="{}"
          :vehicle-data="{ co2_emissions: vehicleImport.co2_emissions, year: vehicleImport.year }"
          @step-completed="handleStepCompleted"
        />
      </TabsContent>

      <TabsContent value="dgt_registration" class="mt-6">
        <DgtRegistration
          :import-id="vehicleImport.id"
          :documents="[]"
          :step-data="{}"
          @step-completed="handleStepCompleted"
        />
      </TabsContent>

      <TabsContent value="plates" class="mt-6">
        <Plates
          :import-id="vehicleImport.id"
          :documents="[]"
          :step-data="{}"
          :new-plate="vehicleImport.plate_new"
          @step-completed="handleStepCompleted"
          @import-finished="handleImportFinished"
        />
      </TabsContent>
    </Tabs>

    <!-- Certificate Generation -->
    <div v-if="vehicleImport.current_step_order >= 6" class="flex justify-center pt-4">
      <Button
        variant="outline"
        @click="generateCertificate"
      >
        <Trophy class="h-4 w-4 mr-2" />
        Generar Certificado de Importación
      </Button>
    </div>
  </div>
</template>
