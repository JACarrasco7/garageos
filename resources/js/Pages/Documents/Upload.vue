<script setup lang="ts">
import { useForm, Link } from '@inertiajs/vue3'
import { Head, router } from '@inertiajs/vue3'
import { ref } from 'vue'
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
import { ArrowLeft, Upload, FileText } from 'lucide-vue-next'

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

const submit = () => {
  form.post(route('documents.store', props.vehicle.id), {
    preserveScroll: true,
    onSuccess: () => {
      form.reset()
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
              <div
                @dragover.prevent="dragOver = true"
                @dragleave="dragOver = false"
                @drop.prevent="handleDrop"
                class="border-2 border-dashed rounded-lg p-8 text-center cursor-pointer transition"
                :class="dragOver ? 'border-primary bg-primary/10' : 'border-border'"
                @click="fileInput?.click()"
              >
                <input
                  ref="fileInput"
                  type="file"
                  class="hidden"
                  @change="handleFileChange"
                  accept=".pdf,.jpg,.jpeg,.png"
                />
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
