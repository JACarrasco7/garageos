<script setup lang="ts">
import { useForm, Link } from '@inertiajs/vue3'
import { Head, router } from '@inertiajs/vue3'
import { ref, onMounted } from 'vue'
import AppSidebarLayout from '@/layouts/app/AppSidebarLayout.vue'
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/Components/ui/card'
import { Button } from '@/Components/ui/button'
import { Input } from '@/Components/ui/input'
import { Label } from '@/Components/ui/label'
import { Textarea } from '@/Components/ui/textarea'
import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from '@/Components/ui/select'
import { ArrowLeft, Upload, FileText, Camera, X } from 'lucide-vue-next'

interface Vehicle {
  id: number
  plate: string
  brand: string
  model: string
}

const props = defineProps<{
  vehicle: Vehicle
}>()

const form = useForm({
  type: 'otro',
  title: '',
  file: null as File | null,
  document_date: '',
  expiry_date: '',
  amount: '',
})

const fileInput = ref<HTMLInputElement | null>(null)
const dragOver = ref(false)
const cameraPreview = ref<string | null>(null)
const isMobile = ref(false)

onMounted(() => {
  isMobile.value = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)
})

const handleFileChange = (e: Event) => {
  const target = e.target as HTMLInputElement
  if (target.files?.length) {
    form.file = target.files[0]
  }
}

const handleDrop = (e: DragEvent) => {
  dragOver.value = false
  if (e.dataTransfer?.files.length) {
    form.file = e.dataTransfer.files[0]
  }
}

const handleCameraCapture = async () => {
  try {
    if (isMobile.value) {
      const { Camera, CameraResultType } = await import('@capacitor/camera')

      const image = await Camera.getPhoto({
        quality: 90,
        allowEditing: false,
        resultType: CameraResultType.Base64,
      })

      cameraPreview.value = image.base64String || null

      const response = await fetch(`data:image/jpeg;base64,${image.base64String}`)
      const blob = await response.blob()
      form.file = new File([blob], `camera-${Date.now()}.jpg`, { type: 'image/jpeg' })
    } else {
      fileInput.value?.click()
    }
  } catch (error) {
    console.error('Camera capture error:', error)
    alert('No se pudo acceder a la cámara. Intenta subir el archivo directamente.')
  }
}

const removeCameraPhoto = () => {
  cameraPreview.value = null
  form.file = null
}

const submit = () => {
  form.post(route('documents.store', props.vehicle.id), {
    preserveScroll: true,
    onSuccess: () => {
      form.reset()
      cameraPreview.value = null
      router.visit(route('documents.index', props.vehicle.id))
    },
  })
}

const typeOptions = [
  { value: 'factura', label: 'Factura' },
  { value: 'itv', label: 'ITV' },
  { value: 'seguro', label: 'Seguro' },
  { value: 'impuesto', label: 'Impuesto' },
  { value: 'otro', label: 'Otro' },
]
</script>

<template>
  <Head title="Subir Documento" />

  <AppSidebarLayout>
    <template #header>
      Subir Documento - {{ vehicle.brand }} {{ vehicle.model }}
    </template>

    <div class="max-w-2xl mx-auto space-y-6">
      <Card class="border-0 shadow-lg">
        <CardHeader>
          <CardTitle>Subir Documento</CardTitle>
          <CardDescription>Arrastra o selecciona el archivo</CardDescription>
        </CardHeader>
        <CardContent>
          <form @submit.prevent="submit" class="space-y-6">
            <!-- Tipo -->
            <div class="space-y-2">
              <Label for="type">Tipo de documento</Label>
              <Select v-model="form.type">
                <SelectTrigger id="type">
                  <SelectValue placeholder="Selecciona tipo" />
                </SelectTrigger>
                <SelectContent>
                  <SelectItem v-for="opt in typeOptions" :key="opt.value" :value="opt.value">
                    {{ opt.label }}
                  </SelectItem>
                </SelectContent>
              </Select>
            </div>

            <!-- Título -->
            <div class="space-y-2">
              <Label for="title">Título</Label>
              <Input
                id="title"
                v-model="form.title"
                type="text"
                placeholder="Ej: Factura cambio aceite"
              />
            </div>

            <!-- File upload -->
            <div class="space-y-2">
              <Label>Archivo</Label>

              <div class="flex gap-2 mb-3">
                <Button
                  type="button"
                  variant="outline"
                  size="sm"
                  @click="handleCameraCapture"
                  class="flex-1"
                >
                  <Camera class="w-4 h-4 mr-1" />
                  Escanear con cámara
                </Button>
                <Button
                  type="button"
                  variant="outline"
                  size="sm"
                  @click="fileInput?.click()"
                  class="flex-1"
                >
                  <Upload class="w-4 h-4 mr-1" />
                  Subir archivo
                </Button>
              </div>

              <!-- Camera preview -->
              <div v-if="cameraPreview" class="relative rounded-lg overflow-hidden border">
                <img :src="cameraPreview" alt="Vista previa de cámara" class="w-full h-auto" />
                <Button
                  type="button"
                  variant="destructive"
                  size="icon"
                  class="absolute top-2 right-2"
                  @click="removeCameraPhoto"
                >
                  <X class="w-4 h-4" />
                </Button>
              </div>

              <!-- File input hidden -->
              <input
                ref="fileInput"
                type="file"
                class="hidden"
                @change="handleFileChange"
                accept=".pdf,.jpg,.jpeg,.png"
              />

              <!-- Drag & drop zone (solo si no hay preview) -->
              <div
                v-if="!cameraPreview"
                @dragover.prevent="dragOver = true"
                @dragleave="dragOver = false"
                @drop.prevent="handleDrop"
                class="border-2 border-dashed rounded-lg p-8 text-center cursor-pointer transition"
                :class="dragOver ? 'border-primary bg-primary/10' : 'border-border'"
                @click="fileInput?.click()"
              >
                <div v-if="form.file">
                  <p class="font-medium">{{ form.file.name }}</p>
                  <p class="text-sm text-muted-foreground">{{ (form.file.size / 1024).toFixed(0) }} KB</p>
                </div>
                <div v-else class="space-y-2">
                  <FileText class="h-8 w-8 mx-auto text-muted-foreground" />
                  <p class="text-muted-foreground">Arrastra un archivo o haz clic para seleccionar</p>
                  <p class="text-xs text-muted-foreground">PDF, JPG, PNG (máx. 20MB)</p>
                </div>
              </div>
              <p v-if="form.errors.file" class="text-sm text-destructive">{{ form.errors.file }}</p>
            </div>

            <!-- Fecha del documento -->
            <div class="grid grid-cols-2 gap-4">
              <div class="space-y-2">
                <Label for="document_date">Fecha del documento</Label>
                <Input id="document_date" v-model="form.document_date" type="date" />
              </div>
              <div class="space-y-2">
                <Label for="expiry_date">Fecha de caducidad</Label>
                <Input id="expiry_date" v-model="form.expiry_date" type="date" />
              </div>
            </div>

            <!-- Importe -->
            <div class="space-y-2">
              <Label for="amount">Importe (€)</Label>
              <Input
                id="amount"
                v-model="form.amount"
                type="number"
                step="0.01"
                min="0"
                placeholder="0.00"
              />
            </div>

            <!-- Botones -->
            <div class="flex justify-end space-x-3 pt-4">
              <Button as-child variant="ghost">
                <Link :href="route('vehicles.show', vehicle.id)">
                  <ArrowLeft class="w-4 h-4 mr-1" />
                  Cancelar
                </Link>
              </Button>
              <Button type="submit" :disabled="form.processing || !form.file">
                <Upload class="w-4 h-4 mr-1" />
                {{ form.processing ? 'Subiendo...' : 'Subir documento' }}
              </Button>
            </div>
          </form>
        </CardContent>
      </Card>
    </div>
  </AppSidebarLayout>
</template>
