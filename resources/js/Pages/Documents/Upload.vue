<script setup lang="ts">
import { useForm, Link } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, router } from '@inertiajs/vue3'
import { ref } from 'vue'

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
</script>

<template>
  <Head title="Subir Documento" />

  <AuthenticatedLayout>
    <template #header>
      <h2 class="text-xl font-semibold leading-tight">
        Subir Documento - {{ vehicle.brand }} {{ vehicle.model }}
      </h2>
    </template>

    <div class="py-12">
      <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
          <form @submit.prevent="submit" class="p-6 space-y-6">
            <!-- Tipo -->
            <div>
              <label class="block text-sm font-medium mb-1">Tipo de documento</label>
              <select
                v-model="form.type"
                required
                class="w-full px-3 py-2 border rounded-md dark:bg-gray-700 dark:border-gray-600"
              >
                <option value="factura">Factura</option>
                <option value="itv">ITV</option>
                <option value="seguro">Seguro</option>
                <option value="impuesto">Impuesto</option>
                <option value="otro">Otro</option>
              </select>
            </div>

            <!-- Título -->
            <div>
              <label class="block text-sm font-medium mb-1">Título</label>
              <input
                v-model="form.title"
                type="text"
                placeholder="Ej: Factura cambio aceite"
                class="w-full px-3 py-2 border rounded-md dark:bg-gray-700 dark:border-gray-600"
              />
            </div>

            <!-- File upload -->
            <div>
              <label class="block text-sm font-medium mb-1">Archivo</label>
              <div
                @dragover.prevent="dragOver = true"
                @dragleave="dragOver = false"
                @drop.prevent="handleDrop"
                class="border-2 border-dashed rounded-lg p-8 text-center cursor-pointer transition"
                :class="dragOver ? 'border-blue-500 bg-blue-50 dark:bg-blue-900/20' : 'border-gray-300 dark:border-gray-600'"
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
                  <p class="text-sm text-gray-500">{{ (form.file.size / 1024).toFixed(0) }} KB</p>
                </div>
                <div v-else>
                  <p class="text-gray-500">Arrastra un archivo o haz clic para seleccionar</p>
                  <p class="text-xs text-gray-400 mt-1">PDF, JPG, PNG (máx. 20MB)</p>
                </div>
              </div>
              <p v-if="form.errors.file" class="text-red-500 text-sm mt-1">{{ form.errors.file }}</p>
            </div>

            <!-- Fecha del documento -->
            <div class="grid grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-medium mb-1">Fecha del documento</label>
                <input
                  v-model="form.document_date"
                  type="date"
                  class="w-full px-3 py-2 border rounded-md dark:bg-gray-700 dark:border-gray-600"
                />
              </div>
              <div>
                <label class="block text-sm font-medium mb-1">Fecha de caducidad</label>
                <input
                  v-model="form.expiry_date"
                  type="date"
                  class="w-full px-3 py-2 border rounded-md dark:bg-gray-700 dark:border-gray-600"
                />
              </div>
            </div>

            <!-- Importe -->
            <div>
              <label class="block text-sm font-medium mb-1">Importe (€)</label>
              <input
                v-model="form.amount"
                type="number"
                step="0.01"
                min="0"
                placeholder="0.00"
                class="w-full px-3 py-2 border rounded-md dark:bg-gray-700 dark:border-gray-600"
              />
            </div>

            <!-- Botones -->
            <div class="flex justify-end space-x-3">
              <Link
                :href="route('vehicles.show', vehicle.id)"
                class="px-4 py-2 border rounded-md hover:bg-gray-50 dark:hover:bg-gray-700"
              >
                Cancelar
              </Link>
              <button
                type="submit"
                :disabled="form.processing || !form.file"
                class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 disabled:opacity-50"
              >
                {{ form.processing ? 'Subiendo...' : 'Subir documento' }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>