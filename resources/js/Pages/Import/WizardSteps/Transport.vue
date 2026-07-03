<script setup lang="ts">
import { ref, computed } from 'vue'
import { useForm } from '@inertiajs/vue3'
import { Button } from '@/Components/ui/button'
import { Input } from '@/Components/ui/input'
import { Label } from '@/Components/ui/label'
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/Components/ui/select'
import { Textarea } from '@/Components/ui/textarea'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/Components/ui/card'
import { Alert, AlertDescription } from '@/Components/ui/alert'
import { Truck, Upload, FileText, AlertCircle, CheckCircle2 } from 'lucide-vue-next'

interface Props {
  importId: number
  documents: any[]
  stepData: any
}

const props = defineProps<Props>()

const emit = defineEmits(['step-completed'])

const form = useForm({
  transport_type: '',
  transport_provider: '',
  transport_cost: '',
  currency: 'EUR',
  transport_eta: '',
  transport_date: '',
  insurance_file: null as File | null,
  tracking_number: '',
  notes: '',
})

const uploading = ref(false)

const transportProviders = [
  { id: 'clicktrans', name: 'ClickTrans', url: 'https://www.clicktrans.es/' },
  { id: 'uship', name: 'uShip', url: 'https://www.uShip.es/' },
  { id: 'direct', name: 'Transporte directo (conductor)', url: '' },
  { id: 'self', name: 'Conducirlo yo mismo', url: '' },
]

const transportTypes = [
  { id: 'temporary_plates', name: 'Placas temporales' },
  { id: 'flatbed', name: 'Cama baja' },
  { id: 'open_trailer', name: 'Remolque abierto' },
  { id: 'enclosed_trailer', name: 'Remolque cerrado' },
]

const insuranceUploaded = computed(() => form.insurance_file !== null)

const submit = () => {
  uploading.value = true
  const formData = new FormData()

  Object.keys(form.data()).forEach(key => {
    if (key.includes('_file') && form[key] instanceof File) {
      formData.append(key, form[key])
    } else if (form[key]) {
      formData.append(key, form[key])
    }
  })

  formData.append('step', 'transport')

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
      <CardTitle>Transporte a España</CardTitle>
      <CardDescription>
        Organiza el transporte del vehículo desde Alemania hasta España
      </CardDescription>
    </CardHeader>
    <CardContent class="space-y-6">
      <!-- Tipo de transporte -->
      <div class="space-y-2">
        <Label>Tipo de transporte *</Label>
        <Select v-model="form.transport_type">
          <SelectTrigger>
            <SelectValue placeholder="Selecciona tipo de transporte" />
          </SelectTrigger>
          <SelectContent>
            <SelectItem v-for="type in transportTypes" :key="type.id" :value="type.id">
              {{ type.name }}
            </SelectItem>
          </SelectContent>
        </Select>
      </div>

      <!-- Proveedor -->
      <div class="space-y-2">
        <Label>Proveedor de transporte *</Label>
        <Select v-model="form.transport_provider">
          <SelectTrigger>
            <SelectValue placeholder="Selecciona proveedor o introduce uno propio" />
          </SelectTrigger>
          <SelectContent>
            <SelectItem v-for="provider in transportProviders" :key="provider.id" :value="provider.id">
              {{ provider.name }}
            </SelectItem>
          </SelectContent>
        </Select>
        <p v-if="form.transport_provider" class="text-sm text-muted-foreground">
          <a
            v-if="transportProviders.find(p => p.id === form.transport_provider)?.url"
            :href="transportProviders.find(p => p.id === form.transport_provider)?.url"
            target="_blank"
            rel="noopener noreferrer"
            class="text-blue-600 hover:underline"
          >
            {{ transportProviders.find(p => p.id === form.transport_provider)?.name }}
          </a>
        </p>
      </div>

      <!-- Coste y fecha -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="space-y-2">
          <Label for="transport_cost">Coste del transporte *</Label>
          <Input
            id="transport_cost"
            v-model="form.transport_cost"
            placeholder="Ej. 500"
            @input="form.transport_cost = formatPrice(form.transport_cost)"
          />
        </div>
        <div class="space-y-2">
          <Label for="transport_eta">Fecha estimada de llegada *</Label>
          <Input
            id="transport_eta"
            v-model="form.transport_eta"
            type="date"
          />
        </div>
        <div class="space-y-2">
          <Label for="transport_date">Fecha real de recogida</Label>
          <Input
            id="transport_date"
            v-model="form.transport_date"
            type="date"
          />
        </div>
      </div>

      <!-- Seguro -->
      <div class="space-y-4 pt-4 border-t">
        <h3 class="font-semibold text-lg">Seguro de transporte *</h3>
        <Alert v-if="!insuranceUploaded" variant="destructive">
          <AlertCircle class="h-4 w-4" />
          <AlertDescription>
            Debes subir el certificado de seguro de transporte
          </AlertDescription>
        </Alert>

        <div class="space-y-2">
          <Label for="insurance_file">Certificado de seguro *</Label>
          <div class="flex items-center gap-3">
            <Input
              id="insurance_file"
              type="file"
              @change="handleFileUpload($event, 'insurance_file')"
              :disabled="uploading"
            />
            <div v-if="form.insurance_file" class="flex items-center gap-2 text-sm text-green-600">
              <CheckCircle2 class="h-4 w-4" />
              <span>{{ form.insurance_file.name }}</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Tracking y notas -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="space-y-2">
          <Label for="tracking_number">Número de seguimiento (si aplica)</Label>
          <Input
            id="tracking_number"
            v-model="form.tracking_number"
            placeholder="Ej. TRK123456789"
          />
        </div>
        <div class="space-y-2">
          <Label for="notes">Notas adicionales</Label>
          <Textarea
            id="notes"
            v-model="form.notes"
            rows="3"
            placeholder="Instrucciones especiales para el conductor..."
          />
        </div>
      </div>

      <!-- Info ADAC placas temporales -->
      <Alert>
        <Truck class="h-4 w-4" />
        <AlertDescription>
          <strong>Tip:</strong> Puedes solicitar placas temporales de exportación (Ausfuhrkennzeichen)
          válidas por 12 meses a través de <a href="https://www.adac.de" target="_blank" rel="noopener noreferrer" class="text-blue-600 hover:underline">ADAC</a>
          o <a href="https://www.dat.de" target="_blank" rel="noopener noreferrer" class="text-blue-600 hover:underline">DAT</a>.
        </AlertDescription>
      </Alert>

      <!-- Action Buttons -->
      <div class="flex justify-end gap-3 pt-6 border-t">
        <Button
          type="submit"
          :disabled="!insuranceUploaded || uploading"
          @click.prevent="submit"
        >
          <Upload v-if="!uploading" class="h-4 w-4 mr-2" />
          {{ uploading ? 'Subiendo...' : 'Subir y continuar' }}
        </Button>
      </div>
    </CardContent>
  </Card>
</template>
