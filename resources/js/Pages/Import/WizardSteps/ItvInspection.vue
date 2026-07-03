<script setup lang="ts">
import { ref, computed } from 'vue'
import { useForm } from '@inertiajs/vue3'
import { Button } from '@/Components/ui/button'
import { Input } from '@/Components/ui/input'
import { Label } from '@/Components/ui/label'
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/Components/ui/select'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/Components/ui/card'
import { Alert, AlertDescription } from '@/Components/ui/alert'
import { Search, Upload, FileText, AlertCircle, CheckCircle2, Calendar } from 'lucide-vue-next'

interface Props {
  importId: number
  documents: any[]
  stepData: any
  itvDeadline?: string | null
}

const props = defineProps<Props>()

const emit = defineEmits(['step-completed'])

const form = useForm({
  itv_station: '',
  itv_date: '',
  itv_time: '',
  itv_result: '',
  itv_certificate_file: null as File | null,
  homologation_required: false,
  homologation_provider: '',
  homologation_cost: '',
  notes: '',
})

const uploading = ref(false)

const itvStations = [
  { id: 'itv_01', name: 'ITV Madrid - Centro', city: 'Madrid' },
  { id: 'itv_02', name: 'ITV Barcelona - Norte', city: 'Barcelona' },
  { id: 'itv_03', name: 'ITV Valencia - Sur', city: 'Valencia' },
  { id: 'itv_04', name: 'ITV Sevilla - Este', city: 'Sevilla' },
  { id: 'itv_05', name: 'ITV Bilbao - Centro', city: 'Bilbao' },
]

const itvResults = [
  { id: 'passed', label: 'Favorable (sin reparaciones)', color: 'bg-green-600' },
  { id: 'passed_repairs', label: 'Favorable con reparaciones', color: 'bg-yellow-600' },
  { id: 'failed', label: 'Desfavorable (vehículo no apto)', color: 'bg-red-600' },
]

const certificateUploaded = computed(() => form.itv_certificate_file !== null)
const canSubmit = computed(() => certificateUploaded.value && form.itv_result)

const submit = () => {
  uploading.value = true
  const formData = new FormData()

  Object.keys(form.data()).forEach(key => {
    if (key.includes('_file') && form[key] instanceof File) {
      formData.append(key, form[key])
    } else if (form[key] !== null && form[key] !== '') {
      formData.append(key, form[key])
    }
  })

  formData.append('step', 'itv_inspection')

  form.post(`/imports/${props.importId}/upload`, {
    forceFormData: true,
    onSuccess: () => {
      uploading.value = false
      emit('step-completed')
    },
    onError: () => {
      uploading.value = false
    },
  })
}

const handleFileUpload = (event: Event, field: string) => {
  const target = event.target as HTMLInputElement
  if (target.files && target.files[0]) {
    form[field] = target.files[0]
  }
}

const formatPrice = (value: string) => {
  return value.replace(/\D/g, '').replace(/\B(?=(\d{3})+(?!\d))/g, '.')
}
</script>

<template>
  <Card>
    <CardHeader>
      <CardTitle>ITV de Importación</CardTitle>
      <CardDescription>
        Programa y pasa la ITV de importación en España (plazo: 30 días desde llegada)
      </CardDescription>
    </CardHeader>
    <CardContent class="space-y-6">
      <!-- Deadline Alert -->
      <Alert v-if="itvDeadline" class="border-orange-500">
        <Calendar class="h-4 w-4" />
        <AlertDescription>
          <strong>Fecha límite:</strong> {{ new Date(itvDeadline).toLocaleDateString('es-ES') }}
          - Tienes 30 días desde la llegada para pasar la ITV
        </AlertDescription>
      </Alert>

      <!-- Estación ITV -->
      <div class="space-y-2">
        <Label>Estación ITV *</Label>
        <Select v-model="form.itv_station">
          <SelectTrigger>
            <SelectValue placeholder="Selecciona estación ITV" />
          </SelectTrigger>
          <SelectContent>
            <SelectItem v-for="station in itvStations" :key="station.id" :value="station.id">
              {{ station.name }} ({{ station.city }})
            </SelectItem>
          </SelectContent>
        </Select>
      </div>

      <!-- Fecha y hora -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="space-y-2">
          <Label for="itv_date">Fecha de la cita *</Label>
          <Input
            id="itv_date"
            v-model="form.itv_date"
            type="date"
            :min="new Date().toISOString().split('T')[0]"
          />
        </div>
        <div class="space-y-2">
          <Label for="itv_time">Hora de la cita</Label>
          <Input
            id="itv_time"
            v-model="form.itv_time"
            type="time"
          />
        </div>
      </div>

      <!-- Resultado ITV -->
      <div class="space-y-2">
        <Label>Resultado de la ITV *</Label>
        <Select v-model="form.itv_result">
          <SelectTrigger>
            <SelectValue placeholder="Selecciona resultado" />
          </SelectTrigger>
          <SelectContent>
            <SelectItem v-for="result in itvResults" :key="result.id" :value="result.id">
              {{ result.label }}
            </SelectItem>
          </SelectContent>
        </Select>
      </div>

      <!-- Certificado ITV -->
      <div class="space-y-4 pt-4 border-t">
        <h3 class="font-semibold text-lg">Documentación</h3>
        <Alert v-if="!certificateUploaded" variant="destructive">
          <AlertCircle class="h-4 w-4" />
          <AlertDescription>
            Debes subir el certificado de la ITV para continuar
          </AlertDescription>
        </Alert>

        <div class="space-y-2">
          <Label for="itv_certificate_file">Certificado de ITV *</Label>
          <div class="flex items-center gap-3">
            <Input
              id="itv_certificate_file"
              type="file"
              accept=".pdf,.jpg,.jpeg,.png"
              @change="handleFileUpload($event, 'itv_certificate_file')"
              :disabled="uploading"
            />
            <div v-if="form.itv_certificate_file" class="flex items-center gap-2 text-sm text-green-600">
              <CheckCircle2 class="h-4 w-4" />
              <span>{{ form.itv_certificate_file.name }}</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Homologación -->
      <div class="space-y-4 pt-4 border-t">
        <h3 class="font-semibold text-lg">Homologación (si es necesaria)</h3>
        <div class="flex items-center gap-2">
          <input
            id="homologation_required"
            v-model="form.homologation_required"
            type="checkbox"
            class="rounded border-gray-300"
          />
          <Label for="homologation_required" class="cursor-pointer">
            Este vehículo requiere homologación
          </Label>
        </div>

        <div v-if="form.homologation_required" class="space-y-4 pl-6 border-l-2 border-gray-300">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="space-y-2">
              <Label>Proveedor de homologación</Label>
              <Input
                v-model="form.homologation_provider"
                placeholder="Ej. Gestoría ABC"
              />
            </div>
            <div class="space-y-2">
              <Label>Coste estimado</Label>
              <Input
                v-model="form.homologation_cost"
                placeholder="Ej. 800"
                @input="form.homologation_cost = formatPrice(form.homologation_cost)"
              />
            </div>
          </div>
        </div>
      </div>

      <!-- Notas -->
      <div class="space-y-2">
        <Label for="notes">Notas adicionales</Label>
        <textarea
          id="notes"
          v-model="form.notes"
          rows="3"
          class="w-full rounded-md border border-input bg-background px-3 py-2 text-sm placeholder:text-muted-foreground focus:outline-none focus:ring-2 focus:ring-ring"
          placeholder="Observaciones sobre la inspección..."
        />
      </div>

      <!-- Enlace útil -->
      <Alert>
        <Search class="h-4 w-4" />
        <AlertDescription>
          <strong>Pedir cita ITV:</strong> Visita <a href="https://www.itv.es/itv-web/" target="_blank" rel="noopener noreferrer" class="text-blue-600 hover:underline">itv.es</a>
          para programar tu cita. Documentación necesaria: documento de identidad, factura de compra, certificado de conformidad, permiso de circulación original.
        </AlertDescription>
      </Alert>

      <!-- Action Buttons -->
      <div class="flex justify-end gap-3 pt-6 border-t">
        <Button
          type="submit"
          :disabled="!canSubmit || uploading"
          @click.prevent="submit"
        >
          <Upload v-if="!uploading" class="h-4 w-4 mr-2" />
          {{ uploading ? 'Subiendo...' : 'Subir y continuar' }}
        </Button>
      </div>
    </CardContent>
  </Card>
</template>
