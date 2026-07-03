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
  invoice_file: null as File | null,
  coc_file: null as File | null,
  technical_data_file: null as File | null,
  original_itv_file: null as File | null,
})

const uploading = ref(false)
const uploadProgress = ref(0)

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
