<script setup lang="ts">
import { ref, computed } from 'vue';
import { useForm } from '@inertiajs/vue3';
import { Link } from '@inertiajs/vue3';
import Input from '@/Components/ui/input/Input.vue';
import Label from '@/Components/ui/label/Label.vue';
import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from '@/Components/ui/select';
import { Button } from '@/Components/ui/button';
import { Upload, X, Loader2, Camera, Trash2, Check } from 'lucide-vue-next';
import { cn } from '@/lib/utils';

const props = defineProps<{
  modelValue: ReturnType<typeof useForm>;
}>();

const emit = defineEmits<{
  (e: 'update:modelValue', value: typeof props.modelValue): void;
}>();

const form = computed({
  get: () => props.modelValue,
  set: (value) => emit('update:modelValue', value),
});

const photoFiles = ref<File[]>([]);
const photoInput = ref<HTMLInputElement | null>(null);
const photoPreviews = ref<string[]>([]);
const selectedPhotoCategory = ref('principal');
const isUploading = ref(false);

const photoCategories = [
  { value: 'principal', label: 'Foto principal' },
  { value: 'frontal', label: 'Frente' },
  { value: 'lateral', label: 'Lateral' },
  { value: 'trasero', label: 'Atrás' },
  { value: 'interior', label: 'Interior' },
  { value: 'motor', label: 'Motor' },
  { value: 'averia', label: 'Avería' },
  { value: 'daño', label: 'Daño' },
  { value: 'documento', label: 'Documento' },
  { value: 'antes_reparacion', label: 'Antes reparación' },
  { value: 'despues_reparacion', label: 'Después reparación' },
];

const handlePhotoSelect = (e: Event) => {
  const target = e.target as HTMLInputElement;
  const files = Array.from(target.files || []);

  if (photoFiles.value.length + files.length > 10) {
    alert('Máximo 10 fotos por vehículo');
    return;
  }

  files.forEach((file) => {
    photoFiles.value.push(file);
    photoPreviews.value.push(URL.createObjectURL(file));
  });
};

const removePhoto = (index: number) => {
  photoFiles.value.splice(index, 1);
  URL.revokeObjectURL(photoPreviews.value[index]);
  photoPreviews.value.splice(index, 1);
};

const clearPhotos = () => {
  photoFiles.value.forEach((_, index) => {
    URL.revokeObjectURL(photoPreviews.value[index]);
  });
  photoFiles.value = [];
  photoPreviews.value = [];
};

const uploadPhotos = async () => {
  if (photoFiles.value.length === 0) return;

  isUploading.value = true;

  try {
    const formData = new FormData();
    photoFiles.value.forEach((file) => {
      formData.append('files[]', file);
    });
    formData.append('category', selectedPhotoCategory.value);

    const response = await fetch(route('vehicles.photos.store', form.value.id), {
      method: 'POST',
      headers: {
        Accept: 'application/json',
      },
      body: formData,
    });

    if (!response.ok) throw new Error('Upload failed');

    clearPhotos();
    alert('Fotos subidas correctamente');
  } catch (error) {
    console.error('Upload error:', error);
    alert('Error al subir las fotos');
  } finally {
    isUploading.value = false;
  }
};

const getCategoryColor = (category: string) => {
  const colors: Record<string, string> = {
    principal: 'bg-green-500',
    frontal: 'bg-blue-500',
    lateral: 'bg-cyan-500',
    trasero: 'bg-purple-500',
    interior: 'bg-yellow-500',
    motor: 'bg-red-500',
    averia: 'bg-orange-500',
    daño: 'bg-pink-500',
    documento: 'bg-indigo-500',
    antes_reparacion: 'bg-slate-500',
    despues_reparacion: 'bg-teal-500',
  };
  return colors[category] || 'bg-gray-500';
};
</script>

<template>
  <div class="space-y-6">
    <div class="space-y-4">
      <h3 class="text-lg font-semibold text-foreground">Fotos del vehículo</h3>
      <p class="text-sm text-muted-foreground">
        Sube hasta 10 fotos. Puedes añadir más fotos después desde la página del vehículo.
      </p>
    </div>

    <div class="space-y-4">
      <div class="flex gap-4">
        <div class="flex-1 space-y-2">
          <Label for="photo-category">Categoría de las fotos</Label>
          <Select v-model="selectedPhotoCategory">
            <SelectTrigger id="photo-category">
              <SelectValue placeholder="Selecciona categoría" />
            </SelectTrigger>
            <SelectContent>
              <SelectItem
                v-for="cat in photoCategories"
                :key="cat.value"
                :value="cat.value"
              >
                {{ cat.label }}
              </SelectItem>
            </SelectContent>
          </Select>
        </div>

        <div class="flex items-end">
          <Button
            type="button"
            variant="outline"
            size="lg"
            @click="photoInput?.click()"
            :disabled="photoFiles.length >= 10 || isUploading"
            class="w-full"
          >
            <Upload class="mr-2 h-4 w-4" />
            {{ photoFiles.length >= 10 ? 'Máximo 10' : `Añadir fotos (${photoFiles.length}/10)` }}
          </Button>
          <input
            ref="photoInput"
            type="file"
            class="hidden"
            multiple
            accept="image/jpeg,image/png,image/webp"
            @change="handlePhotoSelect"
          />
        </div>
      </div>

      <div v-if="photoPreviews.length > 0" class="space-y-3">
        <div class="flex items-center justify-between">
          <span class="text-sm text-muted-foreground">
            {{ photoPreviews.length }} foto(s) seleccionada(s)
          </span>
          <div class="flex gap-2">
            <Button
              type="button"
              variant="ghost"
              size="sm"
              @click="clearPhotos"
              :disabled="isUploading"
            >
              Limpiar
            </Button>
            <Button
              type="button"
              size="sm"
              @click="uploadPhotos"
              :disabled="isUploading"
            >
              <Loader2 v-if="isUploading" class="mr-2 h-4 w-4 animate-spin" />
              {{ isUploading ? 'Subiendo...' : 'Subir' }}
            </Button>
          </div>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-3">
          <div
            v-for="(preview, index) in photoPreviews"
            :key="index"
            class="relative aspect-square rounded-lg overflow-hidden border-2"
            :class="getCategoryColor(selectedPhotoCategory)"
          >
            <img
              :src="preview"
              alt="Preview"
              class="w-full h-full object-cover"
            />
            <Button
              type="button"
              size="icon"
              variant="destructive"
              @click="removePhoto(index)"
              :disabled="isUploading"
              class="absolute top-1 right-1 h-6 w-6"
            >
              <X class="h-3 w-3" />
            </Button>
            <div
              v-if="!isUploading"
              class="absolute bottom-1 left-1 bg-black/50 text-white text-xs px-2 py-0.5 rounded"
            >
              {{ selectedPhotoCategory }}
            </div>
          </div>
        </div>
      </div>

      <div v-else class="border-2 border-dashed rounded-lg p-8 text-center">
        <Upload class="h-12 w-12 mx-auto text-muted-foreground mb-3" />
        <p class="text-sm text-muted-foreground">
          No has seleccionado ninguna foto
        </p>
        <p class="text-xs text-muted-foreground mt-1">
          Haz clic en "Añadir fotos" para subir fotos de tu vehículo
        </p>
      </div>
    </div>

    <div v-if="isUploading" class="bg-muted p-4 rounded-lg">
      <div class="flex items-center gap-3">
        <Loader2 class="h-5 w-5 animate-spin text-primary" />
        <div class="flex-1">
          <p class="text-sm font-medium">Subiendo fotos...</p>
          <p class="text-xs text-muted-foreground">
            Por favor, espera a que se complete la subida antes de continuar
          </p>
        </div>
      </div>
    </div>

    <div class="border-t pt-4">
      <p class="text-sm text-muted-foreground mb-2">
        Tips para mejores fotos:
      </p>
      <ul class="text-xs text-muted-foreground space-y-1 ml-4">
        <li>• Usa buena iluminación natural</li>
        <li>• Limpia el vehículo antes de fotografiar</li>
        <li>• Captura ángulos frontales y laterales completos</li>
        <li>• Incluye fotos del motor y el interior</li>
        <li>• Para fotos de averías, asegúrate de capturar los detalles del daño</li>
      </ul>
    </div>
  </div>
</template>
