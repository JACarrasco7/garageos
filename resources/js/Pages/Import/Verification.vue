<script setup lang="ts">
import { ref, computed } from 'vue'
import { useForm, router } from '@inertiajs/vue3'
import {
  CheckCircle2,
  AlertCircle,
  FileText,
  ShieldCheck,
  Download,
  Search,
  ClipboardCheck
} from 'lucide-vue-next'
import { Button } from '@/Components/ui/button'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/Components/ui/card'
import { Label } from '@/Components/ui/label'
import { Checkbox } from '@/Components/ui/checkbox'
import { Textarea } from '@/Components/ui/textarea'
import { Badge } from '@/Components/ui/badge'
import { Alert, AlertDescription, AlertTitle } from '@/Components/ui/alert'
import { cn } from '@/lib/utils'

interface Verification {
  id: number
  vin_verified: boolean
  ownership_verified: boolean
  technical_data_verified: boolean
  itv_verified: boolean
  legal_status_verified: boolean
  notes: string | null
  overall_status: string
}

interface Evaluation {
  score: number
  status: string
  points: Record<string, boolean>
}

interface VehicleImport {
  id: number
  brand: string
  model: string
  year: number
  vin: string | null
  plate_original: string | null
}

const props = defineProps<{
  import: VehicleImport
  verification: Verification
  evaluation: Evaluation
}>()

const form = useForm({
  vin_verified: props.verification.vin_verified,
  ownership_verified: props.verification.ownership_verified,
  technical_data_verified: props.verification.technical_data_verified,
  itv_verified: props.verification.itv_verified,
  legal_status_verified: props.verification.legal_status_verified,
  notes: props.verification.notes || '',
})

const isGenerating = ref(false)

const updateVerification = () => {
  form.patch(route('import.verify.update', props.import.id), {
    preserveScroll: true,
  })
}

const generateCertificate = async () => {
  isGenerating.value = true
  try {
    const response = await router.post(route('import.verify.report', props.import.id), {}, {
      onSuccess: (page) => {
        // The controller returns JSON, but Inertia handles it.
        // In a real scenario, we'd handle the PDF URL.
        alert('Certificado de Confianza generado con éxito.')
      }
    })
  } finally {
    isGenerating.value = false
  }
}

const generateDetailedReport = async () => {
  isGenerating.value = true
  try {
    await router.post(route('import.verify.detailed', props.import.id), {}, {
      onSuccess: () => {
        alert('Informe Técnico Detallado generado con éxito.')
      }
    })
  } finally {
    isGenerating.value = false
  }
}

const statusColors = {
  CERTIFIED: 'bg-green-600 text-white',
  VERIFIED: 'bg-blue-600 text-white',
  PARTIAL: 'bg-yellow-500 text-black',
  UNVERIFIED: 'bg-red-600 text-white',
}

const verificationItems = [
  { key: 'vin_verified', label: 'VIN / Bastidor', description: 'Verificación física del número de bastidor contra documentación.' },
  { key: 'ownership_verified', label: 'Titularidad y DNI', description: 'Validación de identidad del vendedor y títulos de propiedad.' },
  { key: 'technical_data_verified', label: 'Datos Técnicos (CoC)', description: 'Revisión del Certificado de Conformidad y ficha técnica.' },
  { key: 'itv_verified', label: 'ITV de Importación', description: 'Confirmación de superación de la inspección técnica.' },
  { key: 'legal_status_verified', label: 'Estatus Legal', description: 'Comprobación de ausencia de cargas, embargos o robos.' },
]
</script>

<template>
  <div class="space-y-6 max-w-5xl mx-auto p-4">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
      <div>
        <h1 class="text-3xl font-bold flex items-center gap-3">
          <ShieldCheck class="h-8 w-8 text-primary" />
          Auditoría de Verificación
        </h1>
        <p class="text-muted-foreground">
          Vehículo: {{ import.brand }} {{ import.model }} ({{ import.year }})
          <span v-if="import.vin" class="ml-2 font-mono text-xs bg-muted px-2 py-1 rounded">VIN: {{ import.vin }}</span>
        </p>
      </div>
      <div class="flex items-center gap-3">
        <Badge :class="[statusColors[evaluation.status]]" class="text-sm px-4 py-1">
          {{ evaluation.status }}
        </Badge>
        <div class="text-right">
          <span class="text-xs font-medium text-muted-foreground">Score de Confianza</span>
          <div class="text-xl font-bold">{{ evaluation.score }}%</div>
        </div>
      </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
      <!-- Left Column: Verification Checks -->
      <div class="lg:col-span-2 space-y-6">
        <Card>
          <CardHeader>
            <CardTitle class="flex items-center gap-2">
              <ClipboardCheck class="h-5 w-5" />
              Checklist de Auditoría
            </CardTitle>
            <CardDescription>
              Marque los puntos verificados basándose en la evidencia documental y física.
            </CardDescription>
          </CardHeader>
          <CardContent class="space-y-6">
            <div class="space-y-4">
              <div
                v-for="item in verificationItems"
                :key="item.key"
                class="flex items-start gap-4 p-4 rounded-xl border transition-colors hover:bg-muted/30"
                :class="form[item.key] ? 'border-green-200 bg-green-50/30' : 'border-border'"
              >
                <Checkbox
                  :id="item.key"
                  v-model:checked="form[item.key]"
                  @update:checked="updateVerification"
                />
                <div class="grid flex-1 gap-1.5 leading-none">
                  <Label :for="item.key" class="text-sm font-semibold leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70">
                    {{ item.label }}
                  </Label>
                  <p class="text-sm text-muted-foreground">
                    {{ item.description }}
                  </p>
                </div>
                <div v-if="form[item.key]" class="text-green-600">
                  <CheckCircle2 class="h-5 w-5" />
                </div>
              </div>
            </div>

            <div class="space-y-2 pt-4">
              <Label for="notes" class="text-sm font-semibold">Observaciones del Auditor</Label>
              <Textarea
                id="notes"
                v-model="form.notes"
                placeholder="Añada cualquier detalle relevante sobre la verificación..."
                @blur="updateVerification"
                class="min-h-[120px]"
              />
            </div>
          </CardContent>
        </Card>
      </div>

      <!-- Right Column: Reports & Actions -->
      <div class="space-y-6">
        <Card class="border-primary/20 bg-primary/5">
          <CardHeader>
            <CardTitle class="text-lg flex items-center gap-2">
              <FileText class="h-5 w-5 text-primary" />
              Certificación
            </CardTitle>
            <CardDescription>
              Genere los documentos oficiales de GarageOS.
            </CardDescription>
          </CardHeader>
          <CardContent class="space-y-4">
            <div class="space-y-3">
              <Button
                class="w-full justify-between group"
                :disabled="isGenerating || evaluation.score < 40"
                @click="generateCertificate"
              >
                <div class="flex items-center gap-2">
                  <ShieldCheck class="h-4 w-4" />
                  <span>Certificado de Confianza</span>
                </div>
                <Download class="h-4 w-4 opacity-0 group-hover:opacity-100 transition-opacity" />
              </Button>

              <Button
                variant="outline"
                class="w-full justify-between group"
                :disabled="isGenerating"
                @click="generateDetailedReport"
              >
                <div class="flex items-center gap-2">
                  <Search class="h-4 w-4" />
                  <span>Informe Técnico Detallado</span>
                </div>
                <Download class="h-4 w-4 opacity-0 group-hover:opacity-100 transition-opacity" />
              </Button>
            </div>

            <Alert v-if="evaluation.score < 70" variant="warning" class="mt-4">
              <AlertCircle class="h-4 w-4" />
              <AlertTitle class="text-xs">Aviso de Calidad</AlertTitle>
              <AlertDescription class="text-xs">
                El score actual es bajo. Se recomienda completar más verificaciones para obtener el sello de "CERTIFIED".
              </AlertDescription>
            </Alert>
          </CardContent>
        </Card>

        <Card>
          <CardHeader>
            <CardTitle class="text-sm">Resumen de Auditoría</CardTitle>
          </CardHeader>
          <CardContent class="space-y-3">
            <div class="flex justify-between text-xs">
              <span class="text-muted-foreground">Progreso de Verificación</span>
              <span class="font-medium">{{ evaluation.score }}%</span>
            </div>
            <div class="h-2 w-full bg-muted rounded-full overflow-hidden">
              <div
                class="h-full bg-primary transition-all duration-500"
                :style="{ width: `${evaluation.score}%` }"
              />
            </div>
            <div class="grid grid-cols-2 gap-2 pt-2">
              <div class="p-2 rounded bg-muted text-center">
                <div class="text-[10px] text-muted-foreground uppercase">Estado</div>
                <div class="text-xs font-bold">{{ evaluation.status }}</div>
              </div>
              <div class="p-2 rounded bg-muted text-center">
                <div class="text-[10px] text-muted-foreground uppercase">Auditor</div>
                <div class="text-xs font-bold truncate">GarageOS Pro</div>
              </div>
            </div>
          </CardContent>
        </Card>
      </div>
    </div>
  </div>
</template>
