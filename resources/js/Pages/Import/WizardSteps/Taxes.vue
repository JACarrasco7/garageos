<script setup lang="ts">
import { ref, computed } from 'vue'
import { useForm } from '@inertiajs/vue3'
import { Button } from '@/Components/ui/button'
import { Input } from '@/Components/ui/input'
import { Label } from '@/Components/ui/label'
import { Textarea } from '@/Components/ui/textarea'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/Components/ui/card'
import { Alert, AlertDescription } from '@/Components/ui/alert'
import { Calculator, Upload, FileText, AlertCircle, CheckCircle2, Info } from 'lucide-vue-next'

interface Props {
  importId: number
  documents: any[]
  stepData: any
  vehicleData: {
    co2_emissions?: number | null
    year?: number
  }
}

const props = defineProps<Props>()

const emit = defineEmits(['step-completed'])

const form = useForm({
  iedmt_paid: false,
  iedmt_amount: '',
  iedmt_file: null as File | null,
  itp_paid: false,
  itp_amount: '',
  itp_file: null as File | null,
  ivtm_paid: false,
  ivtm_amount: '',
  ivtm_file: null as File | null,
  tax_provider: '',
  tax_provider_cost: '',
  notes: '',
})

const uploading = ref(false)

const iedmtBands = [
  { co2: 120, rate: 0 },
  { co2: 160, rate: 4.75 },
  { co2: 200, rate: 11.25 },
  { co2: Infinity, rate: 16.00 },
]

const countryRates = {
  DE: { vat: 19, label: 'Alemania' },
  FR: { vat: 20, label: 'Francia' },
  IT: { vat: 22, label: 'Italia' },
  NL: { vat: 21, label: 'Holanda' },
  ES: { vat: 21, label: 'España' },
}

const calculateIedmt = () => {
  if (!props.vehicleData.co2_emissions) return 'N/A'
  const co2 = props.vehicleData.co2_emissions
  const band = iedmtBands.find(b => co2 <= b.co2) || iedmtBands[iedmtBands.length - 1]
  return `€${band.rate}/g CO₂`
}

const calculateVat = () => {
  const country = props.stepData?.origin_country || 'ES'
  const rate = countryRates[country]?.vat || 21
  return `IVA (${rate}%)`
}

const requiredDocs = computed(() => {
  const docs = []
  if (form.iedmt_paid) docs.push(form.iedmt_file)
  if (form.itp_paid) docs.push(form.itp_file)
  if (form.ivtm_paid) docs.push(form.ivtm_file)
  return docs.filter(Boolean).length
})

const canSubmit = computed(() => {
  const anyTaxPaid = form.iedmt_paid || form.itp_paid || form.ivtm_paid
  const allUploaded = requiredDocs.value >= (form.iedmt_paid ? 1 : 0) +
                                    (form.itp_paid ? 1 : 0) +
                                    (form.ivtm_paid ? 1 : 0)
  return anyTaxPaid && allUploaded
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

  formData.append('step', 'taxes')

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
      <CardTitle>Impuestos de Importación</CardTitle>
      <CardDescription>
        Liquidar IEDMT, IVA/ITP e IVTM para poder matricular el vehículo
      </CardDescription>
    </CardHeader>
    <CardContent class="space-y-6">
      <!-- Info país origen -->
      <Alert>
        <Info class="h-4 w-4" />
        <AlertDescription>
          <strong>País de origen:</strong> {{ countryRates[stepData?.origin_country || 'ES']?.label || 'España' }}
          | <strong>IVA aplicable:</strong> {{ calculateVat() }}
        </AlertDescription>
      </Alert>

      <!-- Info CO2 -->
      <Alert>
        <Info class="h-4 w-4" />
        <AlertDescription>
          <strong>IEDMT (Impuesto Especial sobre Determinados Medios de Transporte):</strong>
          Basado en emisiones CO₂ del vehículo. <strong>Tarifa estimada: {{ calculateIedmt() }}</strong>
        </AlertDescription>
      </Alert>

      <!-- IEDMT -->
      <div class="space-y-4 p-4 border rounded-lg">
        <div class="flex items-center justify-between">
          <div>
            <h3 class="font-semibold text-lg">IEDMT</h3>
            <p class="text-sm text-muted-foreground">Impuesto especial sobre vehículos de transporte</p>
          </div>
          <div class="flex items-center gap-2">
            <Label for="iedmt_paid" class="cursor-pointer">Pagado</Label>
            <input
              id="iedmt_paid"
              v-model="form.iedmt_paid"
              type="checkbox"
              class="rounded border-gray-300"
            />
          </div>
        </div>

        <div v-if="form.iedmt_paid" class="space-y-4 pt-4 border-t">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="space-y-2">
              <Label for="iedmt_amount">Importe pagado (€)</Label>
              <Input
                id="iedmt_amount"
                v-model="form.iedmt_amount"
                placeholder="Ej. 1500"
                @input="form.iedmt_amount = formatPrice(form.iedmt_amount)"
              />
            </div>
            <div class="space-y-2">
              <Label for="iedmt_file">Justificante Modelo 576 *</Label>
              <div class="flex items-center gap-3">
                <Input
                  id="iedmt_file"
                  type="file"
                  accept=".pdf,.jpg,.jpeg,.png"
                  @change="handleFileUpload($event, 'iedmt_file')"
                  :disabled="uploading"
                />
                <div v-if="form.iedmt_file" class="flex items-center gap-2 text-sm text-green-600">
                  <CheckCircle2 class="h-4 w-4" />
                  <span>{{ form.iedmt_file.name }}</span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- ITP -->
      <div class="space-y-4 p-4 border rounded-lg">
        <div class="flex items-center justify-between">
          <div>
            <h3 class="font-semibold text-lg">ITP (Impuesto de Transmisiones Patrimoniales)</h3>
            <p class="text-sm text-muted-foreground">Impuesto de cambio de titularidad (aplica a vehículos de segunda mano)</p>
          </div>
          <div class="flex items-center gap-2">
            <Label for="itp_paid" class="cursor-pointer">Pagado</Label>
            <input
              id="itp_paid"
              v-model="form.itp_paid"
              type="checkbox"
              class="rounded border-gray-300"
            />
          </div>
        </div>

        <div v-if="form.itp_paid" class="space-y-4 pt-4 border-t">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="space-y-2">
              <Label for="itp_amount">Importe pagado (€)</Label>
              <Input
                id="itp_amount"
                v-model="form.itp_amount"
                placeholder="Ej. 800"
                @input="form.itp_amount = formatPrice(form.itp_amount)"
              />
            </div>
            <div class="space-y-2">
              <Label for="itp_file">Justificante Modelo ITP *</Label>
              <div class="flex items-center gap-3">
                <Input
                  id="itp_file"
                  type="file"
                  accept=".pdf,.jpg,.jpeg,.png"
                  @change="handleFileUpload($event, 'itp_file')"
                  :disabled="uploading"
                />
                <div v-if="form.itp_file" class="flex items-center gap-2 text-sm text-green-600">
                  <CheckCircle2 class="h-4 w-4" />
                  <span>{{ form.itp_file.name }}</span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- IVTM -->
      <div class="space-y-4 p-4 border rounded-lg">
        <div class="flex items-center justify-between">
          <div>
            <h3 class="font-semibold text-lg">IVTM (Impuesto de Vehículos de Tracción Mecánica)</h3>
            <p class="text-sm text-muted-foreground">Impuesto municipal anual (se paga al matricular)</p>
          </div>
          <div class="flex items-center gap-2">
            <Label for="ivtm_paid" class="cursor-pointer">Pagado</Label>
            <input
              id="ivtm_paid"
              v-model="form.ivtm_paid"
              type="checkbox"
              class="rounded border-gray-300"
            />
          </div>
        </div>

        <div v-if="form.ivtm_paid" class="space-y-4 pt-4 border-t">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="space-y-2">
              <Label for="ivtm_amount">Importe pagado (€)</Label>
              <Input
                id="ivtm_amount"
                v-model="form.ivtm_amount"
                placeholder="Ej. 100"
                @input="form.ivtm_amount = formatPrice(form.ivtm_amount)"
              />
            </div>
            <div class="space-y-2">
              <Label for="ivtm_file">Justificante IVTM *</Label>
              <div class="flex items-center gap-3">
                <Input
                  id="ivtm_file"
                  type="file"
                  accept=".pdf,.jpg,.jpeg,.png"
                  @change="handleFileUpload($event, 'ivtm_file')"
                  :disabled="uploading"
                />
                <div v-if="form.ivtm_file" class="flex items-center gap-2 text-sm text-green-600">
                  <CheckCircle2 class="h-4 w-4" />
                  <span>{{ form.ivtm_file.name }}</span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Gestoría -->
      <div class="space-y-4 pt-4 border-t">
        <h3 class="font-semibold text-lg">Gestión por gestoría (opcional)</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div class="space-y-2">
            <Label>Nombre de la gestoría</Label>
            <Input
              v-model="form.tax_provider"
              placeholder="Ej. Gestoría Import Autos S.L."
            />
          </div>
          <div class="space-y-2">
            <Label>Coste de los servicios (€)</Label>
            <Input
              v-model="form.tax_provider_cost"
              placeholder="Ej. 500"
              @input="form.tax_provider_cost = formatPrice(form.tax_provider_cost)"
            />
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
          placeholder="Detalles sobre los pagos de impuestos..."
        />
      </div>

      <!-- Alerta validación -->
      <Alert v-if="!canSubmit" variant="destructive">
        <AlertCircle class="h-4 w-4" />
        <AlertDescription>
          Debes marcar al menos un impuesto como pagado y subir el justificante correspondiente
        </AlertDescription>
      </Alert>

      <!-- Enlaces útiles -->
      <Alert>
        <Calculator class="h-4 w-4" />
        <AlertDescription>
          <strong>Enlaces útiles:</strong>
          <a href="https://sede.agenciatributaria.gob.es/acciona05i/Inicio.html" target="_blank" rel="noopener noreferrer" class="text-blue-600 hover:underline ml-2">Sede AEAT (Modelo 576)</a> |
          <a href="https://www.boe.es/buscar/act.php?id=BOE-A-2023-12921" target="_blank" rel="noopener noreferrer" class="text-blue-600 hover:underline ml-2">Tramos CO₂ (BOE)</a>
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
