<script setup lang="ts">
import { ref, computed } from 'vue'
import { useForm } from '@inertiajs/vue3'
import { Button } from '@/Components/ui/button'
import { Input } from '@/Components/ui/input'
import { Label } from '@/Components/ui/label'
import { Textarea } from '@/Components/ui/textarea'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/Components/ui/card'
import { Alert, AlertDescription } from '@/Components/ui/alert'
import { CreditCard, Upload, CheckCircle2, Crown, Trophy, AlertCircle } from 'lucide-vue-next'

interface Props {
  importId: number
  documents: any[]
  stepData: any
  newPlate?: string | null
}

const props = defineProps<Props>()

const emit = defineEmits(['step-completed', 'import-finished'])

const form = useForm({
  plates_provider: '',
  plates_cost: '',
  plates_installation_date: '',
  plates_photo_file: null as File | null,
  completed: false,
  final_notes: '',
})

const uploading = ref(false)

const platesProviders = [
  { id: 'taller', name: 'Taller oficial' },
  { id: 'shop', name: 'Tienda de accesorios' },
  { id: 'online', name: 'Proveedor online' },
]

const platesInstalled = computed(() => form.plates_installation_date !== '')
const photoUploaded = computed(() => form.plates_photo_file !== null)
const canSubmit = computed(() => platesInstalled.value && photoUploaded.value)

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

  formData.append('step', 'plates')
  formData.append('completed', 'true')

  form.post(`/imports/${props.importId}/upload`, {
    forceFormData: true,
    onSuccess: () => {
      uploading.value = false
      emit('step-completed')
      emit('import-finished')
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
      <CardTitle class="flex items-center gap-2">
        <Trophy class="h-6 w-6 text-yellow-500" />
        Placas Físicas - Último Paso
      </CardTitle>
      <CardDescription>
        Coloca las placas españolas y completa el proceso de importación
      </CardDescription>
    </CardHeader>
    <CardContent class="space-y-6">
      <!-- Celebración -->
      <Alert class="border-green-500 bg-green-50 dark:bg-green-950/20">
        <Crown class="h-5 w-5 text-green-600" />
        <AlertDescription class="text-green-800 dark:text-green-200">
          <strong>¡Felicidades!</strong> Estás en el último paso de la importación. Una vez colocadas las placas,
          tu vehículo estará oficialmente matriculado en España.
        </AlertDescription>
      </Alert>

      <!-- Nueva matrícula -->
      <div v-if="newPlate" class="space-y-2 p-4 bg-blue-50 dark:bg-blue-950/20 rounded-lg border border-blue-200 dark:border-blue-800">
        <Label class="font-semibold text-blue-900 dark:text-blue-100">Tu nueva matrícula española:</Label>
        <div class="text-3xl font-bold text-blue-700 dark:text-blue-300 tracking-wider">
          {{ newPlate }}
        </div>
      </div>

      <!-- Proveedor -->
      <div class="space-y-2">
        <Label>Proveedor de placas</Label>
        <select v-model="form.plates_provider" class="w-full rounded-md border border-input bg-background px-3 py-2 text-sm">
          <option value="">Selecciona proveedor...</option>
          <option v-for="provider in platesProviders" :key="provider.id" :value="provider.id">
            {{ provider.name }}
          </option>
        </select>
      </div>

      <!-- Coste e instalación -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="space-y-2">
          <Label for="plates_cost">Coste de placas (€)</Label>
          <Input
            id="plates_cost"
            v-model="form.plates_cost"
            placeholder="Ej. 35"
            @input="form.plates_cost = formatPrice(form.plates_cost)"
          />
        </div>
        <div class="space-y-2">
          <Label for="plates_installation_date">Fecha de instalación *</Label>
          <Input
            id="plates_installation_date"
            v-model="form.plates_installation_date"
            type="date"
            :min="new Date().toISOString().split('T')[0]"
          />
        </div>
      </div>

      <!-- Foto de placas -->
      <div class="space-y-4 pt-4 border-t">
        <h3 class="font-semibold text-lg">Foto de las placas instaladas *</h3>
        <div class="space-y-2">
          <Label for="plates_photo_file">Sube una foto del vehículo con las placas nuevas *</Label>
          <div class="flex items-center gap-3">
            <Input
              id="plates_photo_file"
              type="file"
              accept="image/*"
              @change="handleFileUpload($event, 'plates_photo_file')"
              :disabled="uploading"
            />
            <div v-if="form.plates_photo_file" class="flex items-center gap-2 text-sm text-green-600">
              <CheckCircle2 class="h-4 w-4" />
              <span>{{ form.plates_photo_file.name }}</span>
            </div>
          </div>
          <p class="text-sm text-muted-foreground">
            Recomendamos que la foto muestre claramente las placas delanteras y traseras
          </p>
        </div>
      </div>

      <!-- Notas finales -->
      <div class="space-y-2">
        <Label for="final_notes">Notas finales (opcional)</Label>
        <Textarea
          id="final_notes"
          v-model="form.final_notes"
          rows="3"
          placeholder="Comentarios sobre el proceso de importación..."
        />
      </div>

      <!-- Resumen costes -->
      <div class="p-4 bg-muted rounded-lg space-y-2">
        <h3 class="font-semibold">Resumen de costes estimados de importación</h3>
        <ul class="text-sm space-y-1 text-muted-foreground">
          <li>✓ Compra (documentación): 0-100€</li>
          <li>✓ Transporte: 150-1.500€</li>
          <li>✓ ITV de importación: 60-80€</li>
          <li>✓ Impuestos (IEDMT + ITP + IVTM): 1.000-3.000€</li>
          <li>✓ Matriculación DGT: 52€ + seguro</li>
          <li>✓ Placas físicas: 30-50€</li>
          <li class="pt-2 border-t font-medium">
            Total estimado: 1.292 - 4.782€ (sin incluir precio del vehículo)
          </li>
        </ul>
      </div>

      <!-- Alerta validación -->
      <Alert v-if="!canSubmit" variant="destructive">
        <AlertCircle class="h-4 w-4" />
        <AlertDescription>
          Debes indicar la fecha de instalación y subir una foto de las placas para completar la importación
        </AlertDescription>
      </Alert>

      <!-- Action Buttons -->
      <div class="flex justify-end gap-3 pt-6 border-t">
        <Button
          type="submit"
          :disabled="!canSubmit || uploading"
          @click.prevent="submit"
          class="bg-green-600 hover:bg-green-700"
        >
          <CreditCard v-if="!uploading" class="h-4 w-4 mr-2" />
          {{ uploading ? 'Procesando...' : 'Finalizar Importación' }}
        </Button>
      </div>
    </CardContent>
  </Card>
</template>
