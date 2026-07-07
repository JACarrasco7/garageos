<script setup lang="ts">
import { ref, computed } from 'vue'
import { Head, router } from '@inertiajs/vue3'
import WebLayout from '@/layouts/WebLayout.vue'
import PageCard from '@/Components/PageCard.vue'
import { CardContent, CardHeader, CardTitle } from '@/Components/ui/card'
import { Button } from '@/Components/ui/button'
import { Badge } from '@/Components/ui/badge'
import { Check, AlertCircle, Upload, FileText, Calendar, ShieldCheck, MapPin, Star } from 'lucide-vue-next'
import { useForm } from '@inertiajs/vue3'

interface ImportDocument {
  id: number
  type: string
  type_label: string
  is_verified: boolean
  file_url: string
  step: string
}

interface VehicleImport {
  id: number
  plate_original: string
  plate_new: string | null
  brand: string
  model: string
  year: number
  current_step: Step
  status: string
  arrival_date: string | null
  itv_deadline: string | null
  import_documents: ImportDocument[]
}

interface Step {
  id: string
  label: string
  order: number
  required_docs: string[]
}

const props = defineProps<{
  vehicleImport: VehicleImport
  steps: Step[]
}>()

const uploadingDoc = ref<number | null>(null)
const uploadForm = useForm({
  step: '',
  type: '',
  file: null as File | null
})

const currentStepData = computed(() => {
  return props.steps.find(s => s.id === props.vehicleImport.current_step?.id)
})

const documentTypes: Record<string, string[]> = {
  purchase: ['compraventa', 'coc', 'ficha_tecnica_origen', 'tarjeta_itv_origen'],
  transport: ['seguro_transporte'],
  itv_inspection: ['ficha_itv_es'],
  taxes: ['modelo_576', 'modelo_309_300', 'modelo_itp', 'justificante_ivtm'],
  dgt_registration: ['permiso_circulacion'],
  plates: []
}

const documentLabels: Record<string, string> = {
  compraventa: 'Contrato de Compraventa',
  coc: 'Certificado de Conformidad (COC)',
  ficha_tecnica_origen: 'Ficha Técnica Alemana',
  tarjeta_itv_origen: 'Tarjeta ITV/TÜV Alemana',
  seguro_transporte: 'Seguro de Transporte',
  ficha_itv_es: 'Ficha Técnica ITV Española',
  modelo_576: 'Modelo 576 (IEDMT)',
  modelo_309_300: 'Modelo 309/300 (IVA)',
  modelo_itp: 'Liquidación ITP',
  justificante_ivtm: 'Justificante IVTM',
  permiso_circulacion: 'Permiso de Circulación',
  otro: 'Otro Documento'
}

const getStepIcon = (stepId: string) => {
  const icons: Record<string, any> = {
    purchase: FileText,
    transport: ShieldCheck,
    itv_inspection: Calendar,
    taxes: FileText,
    dgt_registration: ShieldCheck,
    plates: Check
  }
  return icons[stepId] || FileText
}

const isDocumentUploaded = (stepId: string, docType: string): boolean => {
  return props.vehicleImport.import_documents.some(
    d => d.step === stepId && d.type === docType && d.is_verified
  )
}

const handleUpload = (step: string, type: string) => {
  uploadForm.step = step
  uploadForm.type = type
  const input = document.getElementById(`file-${step}-${type}`)
  input?.click()
}

const submitDocument = () => {
  if (!uploadForm.file || !uploadForm.step || !uploadForm.type) return

  uploadForm.post(route('import.upload', props.vehicleImport.id), {
    forceFormData: true,
    onSuccess: () => {
      uploadForm.reset('file')
    }
  })
}

const providers = ref<{
  id: number
  name: string
  address: string
  city: string
  phone: string
  email: string
  rating: number
  distance: number
  services: string[]
}[]>([])

const loadProviders = () => {
  router.get(route('import.providers', props.vehicleImport.id))
}
</script>

<template>
  <Head :title="`Importación: ${vehicleImport.brand} ${vehicleImport.model}`" />

  <WebLayout>
    <template #header>
      <div class="flex items-center justify-between">
        <div>
          <h2 class="text-2xl font-bold">{{ vehicleImport.brand }} {{ vehicleImport.model }} ({{ vehicleImport.year }})</h2>
          <p class="text-sm text-muted-foreground">
            Matrícula: {{ vehicleImport.plate_original }} → {{ vehicleImport.plate_new || 'Pendiente asignación' }}
          </p>
        </div>
        <Badge :variant="vehicleImport.status === 'completed' ? 'success' : 'secondary'">
          {{ vehicleImport.status }}
        </Badge>
      </div>
    </template>

    <div class="space-y-6">
      <!-- Progress Stepper -->
      <PageCard>
        <template #title>
          <CardHeader>
            <CardTitle>Progreso del Trámite</CardTitle>
          </CardHeader>
        </template>
        <CardContent>
          <div class="flex items-center justify-between">
            <div
              v-for="(step, index) in steps"
              :key="step.id"
              class="flex flex-col items-center relative"
              :class="{ 'opacity-50': step.order > vehicleImport.current_step?.order }"
            >
              <div
                class="w-10 h-10 rounded-full flex items-center justify-center text-sm font-semibold mb-2"
                :class="step.order <= vehicleImport.current_step?.order
                  ? 'bg-primary text-primary-foreground'
                  : 'bg-muted text-muted-foreground'"
              >
                {{ step.order }}
              </div>
              <span class="text-xs text-center max-w-[120px]">{{ step.label }}</span>

              <div
                v-if="index < steps.length - 1"
                class="absolute top-5 left-full w-full h-0.5 bg-border"
              ></div>
            </div>
          </div>
        </CardContent>
      </PageCard>

      <!-- Document Checklist -->
      <PageCard>
        <template #title>
          <CardHeader>
            <CardTitle>Documentos requeridos</CardTitle>
          </CardHeader>
        </template>
        <CardContent class="space-y-4">
          <div
            v-for="step in steps"
            :key="step.id"
            class="border rounded-lg p-4"
          >
            <div class="flex items-center justify-between mb-3">
              <div class="flex items-center gap-3">
                <component :is="getStepIcon(step.id)" class="h-5 w-5 text-primary" />
                <h3 class="font-medium">{{ step.label }}</h3>
              </div>

              <div class="flex items-center gap-2">
                <Badge variant="outline">
                  {{ step.required_docs.length }} documentos
                </Badge>
              </div>
            </div>

            <div v-if="step.required_docs.length === 0" class="text-sm text-muted-foreground">
              No se requieren documentos para este paso
            </div>

            <div v-else class="space-y-3">
              <div
                v-for="docType in step.required_docs"
                :key="docType"
                class="flex items-center justify-between p-3 bg-muted/50 rounded-lg"
              >
                <div class="flex items-center gap-3">
                  <FileText class="h-4 w-4 text-muted-foreground" />
                  <div>
                    <p class="text-sm font-medium">{{ documentLabels[docType] || docType }}</p>
                    <p v-if="isDocumentUploaded(step.id, docType)" class="text-xs text-green-600">
                      Documento verificado
                    </p>
                  </div>
                </div>

                <div class="flex items-center gap-2">
                  <input
                    type="file"
                    :id="`file-${step.id}-${docType}`"
                    class="hidden"
                    @change="handleUpload(step.id, docType)"
                  />
                  <Button
                    size="sm"
                    variant="outline"
                    @click="handleUpload(step.id, docType)"
                  >
                    <Upload class="h-4 w-4 mr-2" />
                    Subir
                  </Button>
                </div>
              </div>
            </div>
          </div>
        </CardContent>
      </PageCard>

      <!-- Timeline Info -->
      <PageCard>
        <template #title>
          <CardHeader>
            <CardTitle>Información del Proceso</CardTitle>
          </CardHeader>
        </template>
        <CardContent class="space-y-4">
          <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
            <div>
              <p class="text-muted-foreground">Fecha de compra</p>
              <p class="font-medium">-</p>
            </div>
            <div>
              <p class="text-muted-foreground">Fecha de llegada</p>
              <p class="font-medium">{{ vehicleImport.arrival_date || '-' }}</p>
            </div>
            <div>
              <p class="text-muted-foreground">Plazo ITV</p>
              <p class="font-medium">{{ vehicleImport.itv_deadline || '-' }}</p>
            </div>
          </div>
        </CardContent>
      </PageCard>

      <!-- Service Providers -->
      <PageCard>
        <template #title>
          <CardHeader>
            <div class="flex items-center justify-between">
              <CardTitle>Proveedores de Servicios</CardTitle>
              <Button size="sm" variant="outline" @click="loadProviders">
                Actualizar
              </Button>
            </div>
          </CardHeader>
        </template>
        <CardContent class="space-y-4">
          <div v-if="providers.length === 0" class="text-center py-8 text-muted-foreground">
            <MapPin class="h-12 w-12 mx-auto mb-2 opacity-50" />
            <p>Haz clic en "Actualizar" para ver proveedores cercanos</p>
          </div>

          <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <div
              v-for="provider in providers"
              :key="provider.id"
              class="border rounded-lg p-4 hover:bg-muted/50 transition-colors"
            >
              <div class="flex items-start justify-between mb-2">
                <h4 class="font-medium">{{ provider.name }}</h4>
                <div class="flex items-center gap-1 text-xs">
                  <Star class="h-3 w-3 fill-yellow-400 text-yellow-400" />
                  <span>{{ provider.rating }}</span>
                </div>
              </div>

              <div class="space-y-2 text-sm text-muted-foreground">
                <p class="flex items-center gap-1">
                  <MapPin class="h-3 w-3" />
                  {{ provider.address }}, {{ provider.city }}
                </p>
                <p v-if="provider.phone">📞 {{ provider.phone }}</p>
                <p v-if="provider.email">✉️ {{ provider.email }}</p>
                <p class="text-xs">
                  📍 {{ provider.distance.toFixed(1) }} km
                </p>
              </div>

              <div class="mt-3 flex flex-wrap gap-1">
                <Badge
                  v-for="service in provider.services"
                  :key="service"
                  variant="outline"
                  class="text-xs"
                >
                  {{ service }}
                </Badge>
              </div>
            </div>
          </div>
        </CardContent>
      </PageCard>
    </div>
  </WebLayout>
</template>
