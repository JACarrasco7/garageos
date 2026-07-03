<script setup lang="ts">
import { ref, computed } from 'vue'
import { useForm } from '@inertiajs/vue3'
import { Button } from '@/Components/ui/button'
import { Input } from '@/Components/ui/input'
import { Label } from '@/Components/ui/label'
import { Textarea } from '@/Components/ui/textarea'
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/Components/ui/select'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/Components/ui/card'
import { Alert, AlertDescription } from '@/Components/ui/alert'
import { FileCheck, Upload, FileText, AlertCircle, CheckCircle2, Info } from 'lucide-vue-next'

interface Props {
  importId: number
  documents: any[]
  stepData: any
}

const props = defineProps<Props>()

const emit = defineEmits(['step-completed'])

const form = useForm({
  dgt_provider: '',
  dgt_provider_contact: '',
  dgt_file_number: '',
  dgt_submission_date: '',
  dgt_submission_file: null as File | null,
  permit_circulation_file: null as File | null,
  vehicle_card_file: null as File | null,
  insurance_policy_file: null as File | null,
  notes: '',
})

const uploading = ref(false)

const providers = [
  { id: 'self', name: 'Presentación directa (DGT online)' },
  { id: 'gestoria', name: 'A través de gestoría' },
]

const requiredFiles = computed(() => {
  return [
    { name: 'Justificante presentación DGT', field: 'dgt_submission_file' },
    { name: 'Permiso de circulación', field: 'permit_circulation_file' },
    { name: 'Tarjeta ITV', field: 'vehicle_card_file' },
    { name: 'Póliza de seguro', field: 'insurance_policy_file' },
  ]
})

const allRequiredUploaded = computed(() => {
  return requiredFiles.value.every(f => (form as any)[f.field] !== null)
})

const canSubmit = computed(() => {
  return form.dgt_file_number && allRequiredUploaded.value
})

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

  formData.append('step', 'dgt_registration')

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
</script>

<template>
  <Card>
    <CardHeader>
      <CardTitle>Matriculación DGT</CardTitle>
      <CardDescription>
        Matricular el vehículo en la Dirección General de Tráfico
      </CardDescription>
    </CardHeader>
    <CardContent class="space-y-6">
      <!-- Proveedor -->
      <div class="space-y-2">
        <Label>Forma de presentación *</Label>
        <Select v-model="form.dgt_provider">
          <SelectTrigger>
            <SelectValue placeholder="Selecciona forma de presentación" />
          </SelectTrigger>
          <SelectContent>
            <SelectItem value="self">Presentación directa (DGT online)</SelectItem>
            <SelectItem value="gestoria">A través de gestoría</SelectItem>
          </SelectContent>
        </Select>
      </div>

      <!-- Datos de gestoría -->
      <div v-if="form.dgt_provider === 'gestoria'" class="space-y-4 p-4 border rounded-lg">
        <h3 class="font-semibold">Datos de la gestoría</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div class="space-y-2">
            <Label>Nombre de la gestoría</Label>
            <Input
              v-model="form.dgt_provider"
              placeholder="Ej. Gestoría ABC"
            />
          </div>
          <div class="space-y-2">
            <Label>Contacto (teléfono/email)</Label>
            <Input
              v-model="form.dgt_provider_contact"
              placeholder="Ej. +34 912 345 678"
            />
          </div>
        </div>
      </div>

      <!-- Expediente DGT -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="space-y-2">
          <Label for="dgt_file_number">Número de expediente DGT *</Label>
          <Input
            id="dgt_file_number"
            v-model="form.dgt_file_number"
            placeholder="Ej. 2024-12345678"
          />
        </div>
        <div class="space-y-2">
          <Label for="dgt_submission_date">Fecha de presentación *</Label>
          <Input
            id="dgt_submission_date"
            v-model="form.dgt_submission_date"
            type="date"
          />
        </div>
      </div>

      <!-- Documentos requeridos -->
      <div class="space-y-4 pt-4 border-t">
        <h3 class="font-semibold text-lg">Documentación obligatoria</h3>
        <Alert v-if="!allRequiredUploaded" variant="destructive">
          <AlertCircle class="h-4 w-4" />
          <AlertDescription>
            Debes subir todos los documentos obligatorios para continuar
          </AlertDescription>
        </Alert>

        <div class="space-y-3">
          <div v-for="doc in requiredFiles" :key="doc.name" class="space-y-2">
            <Label :for="doc.name.replace(/\s+/g, '_')">
              {{ doc.name }} *
            </Label>
            <div class="flex items-center gap-3">
              <Input
                :id="doc.name.replace(/\s+/g, '_')"
                type="file"
                accept=".pdf,.jpg,.jpeg,.png"
                @change="handleFileUpload($event, doc.field)"
                :disabled="uploading"
              />
              <div v-if="(form as any)[doc.field]" class="flex items-center gap-2 text-sm text-green-600">
                <CheckCircle2 class="h-4 w-4" />
                <span>{{ (form as any)[doc.field].name }}</span>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Notas -->
      <div class="space-y-2">
        <Label for="notes">Notas adicionales</Label>
        <Textarea
          id="notes"
          v-model="form.notes"
          rows="3"
          placeholder="Detalles sobre el proceso de matriculación..."
        />
      </div>

      <!-- Info -->
      <Alert>
        <Info class="h-4 w-4" />
        <AlertDescription>
          <strong>Documentos necesarios para matricular:</strong> DNI/NIE del titular, factura de compra,
          justificante del pago de impuestos (IEDMT/ITP), certificado ITV favorable, permiso de circulación original,
          tarjeta de inspección técnica, póliza de seguro obligatorio.
        </AlertDescription>
      </Alert>

      <!-- Enlace útil -->
      <Alert>
        <FileCheck class="h-4 w-4" />
        <AlertDescription>
          <strong>Matriculación online:</strong>
          <a href="https://sede.dgt.gob.es/es/tramites-y-multas/vehiculo/matriculacion/matriculacion-ordinaria/" target="_blank" rel="noopener noreferrer" class="text-blue-600 hover:underline ml-2">Sede Electrónica DGT</a>
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
