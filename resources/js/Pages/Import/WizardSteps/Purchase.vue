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
import { Upload, FileText, AlertCircle, CheckCircle2 } from 'lucide-vue-next'

interface Props {
  importId: number
  documents: any[]
  stepData: any
}

const props = defineProps<Props>()

const emit = defineEmits(['step-completed'])

const form = useForm({
  seller_name: '',
  seller_contact: '',
  vehicle_price: '',
  currency: 'EUR',
  purchase_country: 'DE',
  purchase_date: '',
  vin: '',
  brand: '',
  model: '',
  year: '',
  engine_cc: '',
  power_kw: '',
  co2_emissions: '',
  invoice_file: null as File | null,
  coc_file: null as File | null,
  technical_data_file: null as File | null,
  original_itv_file: null as File | null,
})

const uploading = ref(false)
const uploadProgress = ref(0)
const decoding = ref(false)

const decodeVin = async () => {
  if (!form.vin || form.vin.length !== 17) return

  decoding.value = true
  try {
    const response = await fetch(route('vin.decode'), {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
      },
      body: JSON.stringify({ vin: form.vin }),
    })

    const data = await response.json()
    if (data.success) {
      form.brand = data.data.make || ''
      form.model = data.data.model || ''
      form.year = data.data.year?.toString() || ''
      form.engine_cc = data.data.engine_cc?.toString() || ''
      form.power_kw = data.data.power_kw?.toString() || ''
      form.co2_emissions = data.data.co2_emissions?.toString() || ''
    }
  } catch (e) {
    // Silencioso - el usuario rellenará manualmente
  } finally {
    decoding.value = false
  }
}

const documentTypes = [
  { key: 'invoice_file', label: 'Factura de compra', required: true },
  { key: 'coc_file', label: 'Certificado de Conformidad (CoC)', required: true },
  { key: 'technical_data_file', label: 'Ficha técnica original', required: true },
  { key: 'original_itv_file', label: 'Tarjeta ITV original', required: true },
]

const requiredDocsUploaded = computed(() => {
  return documentTypes
    .filter(doc => doc.required)
    .every(doc => form[doc.key] !== null)
})

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

  formData.append('step', 'purchase')

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
      <CardTitle>Datos de la compra</CardTitle>
      <CardDescription>
        Introduce la información de la compra del vehículo en Alemania
      </CardDescription>
    </CardHeader>
    <CardContent class="space-y-6">
      <!-- VIN y Decodificación -->
      <div class="space-y-4 p-4 rounded-xl bg-primary/5 border border-primary/20">
        <div class="flex items-center gap-2 text-primary font-semibold">
          <FileText class="h-4 w-4" />
          <span>Identificación del Vehículo (VIN)</span>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
          <div class="space-y-2 md:col-span-2">
            <Label for="vin">Número de Bastidor (VIN) *</Label>
            <Input
              id="vin"
              v-model="form.vin"
              placeholder="WAUZZZ8V8KA000000"
              maxlength="17"
              @blur="decodeVin"
            />
          </div>
          <div class="flex items-end">
            <Button
              type="button"
              variant="outline"
              size="sm"
              :disabled="form.vin.length !== 17 || decoding"
              @click="decodeVin"
            >
              {{ decoding ? 'Decodificando...' : 'Autocompletar' }}
            </Button>
          </div>
        </div>
        <p class="text-xs text-muted-foreground">
          Introduce el VIN y pulsa "Autocompletar" para rellenar marca, modelo y datos técnicos automáticamente.
        </p>
      </div>

      <!-- Vendedor -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="space-y-2">
          <Label for="seller_name">Nombre del vendedor *</Label>
          <Input
            id="seller_name"
            v-model="form.seller_name"
            placeholder="Ej. Autohaus Müller GmbH"
          />
        </div>
        <div class="space-y-2">
          <Label for="seller_contact">Contacto (teléfono/email) *</Label>
          <Input
            id="seller_contact"
            v-model="form.seller_contact"
            placeholder="Ej. +49 123 456789"
          />
        </div>
      </div>

      <!-- Precio -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="space-y-2 md:col-span-2">
          <Label for="vehicle_price">Precio del vehículo *</Label>
          <Input
            id="vehicle_price"
            v-model="form.vehicle_price"
            placeholder="Ej. 15000"
            @input="form.vehicle_price = formatPrice(form.vehicle_price)"
          />
        </div>
        <div class="space-y-2">
          <Label for="currency">Moneda</Label>
          <Select v-model="form.currency">
            <SelectTrigger>
              <SelectValue />
            </SelectTrigger>
            <SelectContent>
              <SelectItem value="EUR">EUR €</SelectItem>
              <SelectItem value="USD">USD $</SelectItem>
              <SelectItem value="GBP">GBP £</SelectItem>
            </SelectContent>
          </Select>
        </div>
      </div>

      <!-- Fecha y país -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="space-y-2">
          <Label for="purchase_date">Fecha de compra *</Label>
          <Input
            id="purchase_date"
            v-model="form.purchase_date"
            type="date"
          />
        </div>
        <div class="space-y-2">
          <Label for="purchase_country">País de compra</Label>
          <Select v-model="form.purchase_country">
            <SelectTrigger>
              <SelectValue />
            </SelectTrigger>
            <SelectContent>
              <SelectItem value="DE">Alemania</SelectItem>
              <SelectItem value="AT">Austria</SelectItem>
              <SelectItem value="BE">Bélgica</SelectItem>
              <SelectItem value="FR">Francia</SelectItem>
              <SelectItem value="NL">Países Bajos</SelectItem>
              <SelectItem value="IT">Italia</SelectItem>
            </SelectContent>
          </Select>
        </div>
      </div>

      <!-- Datos técnicos (autocompletados) -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4 pt-4 border-t">
        <div class="space-y-2">
          <Label for="brand">Marca</Label>
          <Input id="brand" v-model="form.brand" placeholder="Ej. BMW" />
        </div>
        <div class="space-y-2">
          <Label for="model">Modelo</Label>
          <Input id="model" v-model="form.model" placeholder="Ej. Serie 3" />
        </div>
        <div class="space-y-2">
          <Label for="year">Año</Label>
          <Input id="year" v-model="form.year" type="number" placeholder="2020" />
        </div>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="space-y-2">
          <Label for="engine_cc">Cilindrada (cc)</Label>
          <Input id="engine_cc" v-model="form.engine_cc" placeholder="2000" />
        </div>
        <div class="space-y-2">
          <Label for="power_kw">Potencia (kW)</Label>
          <Input id="power_kw" v-model="form.power_kw" placeholder="140" />
        </div>
        <div class="space-y-2">
          <Label for="co2_emissions">CO2 (g/km)</Label>
          <Input id="co2_emissions" v-model="form.co2_emissions" placeholder="120" />
        </div>
      </div>

      <!-- Documentos requeridos -->
      <div class="space-y-4 pt-4 border-t">
        <h3 class="font-semibold text-lg">Documentos obligatorios</h3>
        <Alert v-if="!requiredDocsUploaded" variant="destructive">
          <AlertCircle class="h-4 w-4" />
          <AlertDescription>
            Debes subir todos los documentos obligatorios para continuar
          </AlertDescription>
        </Alert>

        <div class="space-y-3">
          <div v-for="doc in documentTypes" :key="doc.key" class="space-y-2">
            <Label :for="doc.key">
              {{ doc.label }}
              <span v-if="doc.required" class="text-red-500">*</span>
            </Label>
            <div class="flex items-center gap-3">
              <Input
                :id="doc.key"
                type="file"
                @change="handleFileUpload($event, doc.key)"
                :disabled="uploading"
              />
              <div v-if="form[doc.key]" class="flex items-center gap-2 text-sm text-green-600">
                <CheckCircle2 class="h-4 w-4" />
                <span>{{ form[doc.key].name }}</span>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Action Buttons -->
      <div class="flex justify-end gap-3 pt-6 border-t">
        <Button
          type="submit"
          :disabled="!requiredDocsUploaded || uploading"
          @click.prevent="submit"
        >
          <Upload v-if="!uploading" class="h-4 w-4 mr-2" />
          {{ uploading ? 'Subiendo...' : 'Subir y continuar' }}
        </Button>
      </div>
    </CardContent>
  </Card>
</template>
