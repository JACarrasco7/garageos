<script setup lang="ts">
import { ref, computed } from 'vue';
import { router, Link } from '@inertiajs/vue3';
import { Head } from '@inertiajs/vue3';
import AppSidebarLayout from '@/layouts/app/AppSidebarLayout.vue';
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/Components/ui/card';
import { Button } from '@/Components/ui/button';
import { Input } from '@/Components/ui/input';
import { Label } from '@/Components/ui/label';
import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from '@/Components/ui/select';
import { Badge } from '@/Components/ui/badge';
import {
  ArrowLeft,
  Upload,
  X,
  Image as ImageIcon,
  GripVertical,
  ChevronDown,
} from 'lucide-vue-next';
import { cn } from '@/lib/utils';

interface Photo {
  id: number;
  file_path: string;
  url: string;
  thumbnail_url: string;
  category: string;
  caption: string | null;
  sort_order: number;
  file_type: string;
  file_size: number | null;
}

interface Vehicle {
  id: number;
  plate: string;
  brand: string;
  model: string;
}

interface Category {
  value: string;
  label: string;
}

const props = defineProps<{
  vehicle: Vehicle;
  photos: Photo[];
  categories: Category[];
}>();

const selectedCategory = ref<string>('principal');
const selectedPhotos = ref<number[]>([]);
const uploading = ref(false);
const dragOver = ref(false);
const fileInput = ref<HTMLInputElement>();

const photosByCategory = computed(() => {
  return props.categories.reduce((acc, cat) => {
    acc[cat.value] = props.photos.filter((p) => p.category === cat.value);
    return acc;
  }, {} as Record<string, Photo[]>);
});

const currentPhotos = computed(() => {
  return photosByCategory.value[selectedCategory.value] || [];
});

const handleFileChange = (e: Event) => {
  const target = e.target as HTMLInputElement;
  if (target.files?.length) {
    uploadFiles(Array.from(target.files));
  }
};

const handleDrop = (e: DragEvent) => {
  dragOver.value = false;
  if (e.dataTransfer?.files.length) {
    uploadFiles(Array.from(e.dataTransfer.files));
  }
};

const uploadFiles = (files: File[]) => {
  uploading.value = true;

  const formData = new FormData();
  files.forEach((file) => formData.append('files[]', file));
  formData.append('category', selectedCategory.value);

  router.post(
    route('vehicles.photos.store', props.vehicle.id),
    formData,
    {
      forceFormData: true,
      onSuccess: () => {
        uploading.value = false;
      },
      onError: () => {
        uploading.value = false;
      },
    }
  );
};

const selectAll = () => {
  if (selectedPhotos.value.length === currentPhotos.value.length) {
    selectedPhotos.value = [];
  } else {
    selectedPhotos.value = currentPhotos.value.map((p) => p.id);
  }
};

const deleteSelected = () => {
  if (!selectedPhotos.value.length) return;

  if (confirm(`¿Eliminar ${selectedPhotos.value.length} fotos?`)) {
    selectedPhotos.value.forEach((photoId) => {
      router.delete(
        route('vehicles.photos.destroy', [props.vehicle.id, photoId])
      );
    });
    selectedPhotos.value = [];
  }
};

const getCategoryBadgeColor = (category: string) => {
  const colors: Record<string, string> = {
    principal: 'bg-primary text-primary-foreground',
    frontal: 'bg-blue-500',
    lateral: 'bg-green-500',
    trasero: 'bg-purple-500',
    interior: 'bg-yellow-500',
    motor: 'bg-red-500',
    averia: 'bg-orange-500',
    daño: 'bg-pink-500',
    documento: 'bg-cyan-500',
    antes_reparacion: 'bg-slate-500',
    despues_reparacion: 'bg-teal-500',
  };
  return colors[category] || 'bg-gray-500';
};

const formatFileSize = (bytes: number | null) => {
  if (!bytes) return '-';
  const mb = bytes / 1024 / 1024;
  return mb < 1 ? `${(bytes / 1024).toFixed(0)} KB` : `${mb.toFixed(1)} MB`;
};
</script>

<template>
  <Head title="Galería de Fotos" />

  <AppSidebarLayout>
    <template #header>
      <div class="flex items-center justify-between">
        <div>
          <h1 class="text-2xl font-bold text-foreground">Galería de Fotos</h1>
          <p class="text-sm text-muted-foreground">
            {{ vehicle.brand }} {{ vehicle.model }} ({{ vehicle.plate }})
          </p>
        </div>
        <Button variant="ghost" size="sm" as-child>
          <Link :href="route('vehicles.show', vehicle.id)">
            <ArrowLeft class="mr-2 h-4 w-4" />
            Volver
          </Link>
        </Button>
      </div>
    </template>

    <div class="mx-auto max-w-6xl space-y-6">
      <!-- Controles de subida -->
      <Card class="border-0 shadow-lg">
        <CardHeader>
          <CardTitle>Subir fotos</CardTitle>
          <CardDescription>
            Arrastra fotos o haz clic para seleccionar (máx. 10 a la vez)
          </CardDescription>
        </CardHeader>
        <CardContent>
          <div class="mb-4 flex items-center gap-4">
            <div class="flex-1">
              <Label for="category">Categoría</Label>
              <Select v-model="selectedCategory">
                <SelectTrigger id="category">
                  <SelectValue placeholder="Seleccionar" />
                </SelectTrigger>
                <SelectContent>
                  <SelectItem
                    v-for="cat in categories"
                    :key="cat.value"
                    :value="cat.value"
                  >
                    {{ cat.label }}
                  </SelectItem>
                </SelectContent>
              </Select>
            </div>

            <div
              class="flex flex-1 cursor-pointer items-center justify-center rounded-lg border-2 border-dashed p-6 transition hover:border-primary"
              :class="dragOver ? 'border-primary bg-primary/5' : 'border-border'"
              @dragover.prevent="dragOver = true"
              @dragleave="dragOver = false"
              @drop.prevent="handleDrop"
              @click="fileInput?.click()"
            >
              <input
                ref="fileInput"
                type="file"
                class="hidden"
                multiple
                accept="image/jpeg,image/png,image/webp"
                @change="handleFileChange"
              />
              <div class="flex items-center gap-2">
                <Upload class="h-5 w-5 text-muted-foreground" />
                <span class="text-sm text-muted-foreground">
                  {{ uploading ? 'Subiendo...' : 'Seleccionar archivos' }}
                </span>
              </div>
            </div>
          </div>

          <div v-if="selectedPhotos.length > 0" class="flex items-center gap-2 border-t pt-4">
            <Button variant="destructive" size="sm" @click="deleteSelected">
              <X class="mr-2 h-4 w-4" />
              Eliminar seleccionados ({{ selectedPhotos.length }})
            </Button>
            <Button variant="outline" size="sm" @click="selectAll">
              {{ selectedPhotos.length === currentPhotos.length ? 'Deseleccionar todo' : 'Seleccionar todo' }}
            </Button>
          </div>
        </CardContent>
      </Card>

      <!-- Filtros por categoría -->
      <div class="flex flex-wrap items-center gap-2">
        <Badge
          v-for="cat in categories"
          :key="cat.value"
          :variant="selectedCategory === cat.value ? 'default' : 'outline'"
          :class="cn(
            'cursor-pointer transition hover:bg-accent',
            selectedCategory === cat.value && getCategoryBadgeColor(cat.value)
          )"
          @click="selectedCategory = cat.value"
        >
          {{ cat.label }}
          <span class="ml-1 text-xs opacity-70">
            ({{ photosByCategory[cat.value]?.length || 0 }})
          </span>
        </Badge>
      </div>

      <!-- Grid de fotos -->
      <div v-if="currentPhotos.length > 0" class="grid grid-cols-2 gap-4 md:grid-cols-3 lg:grid-cols-4">
        <div
          v-for="photo in currentPhotos"
          :key="photo.id"
          class="group relative overflow-hidden rounded-lg border bg-card shadow-sm transition hover:shadow-md"
        >
          <!-- Foto -->
          <div class="aspect-square overflow-hidden">
            <img
              :src="photo.thumbnail_url"
              :alt="photo.caption || 'Foto'"
              class="h-full w-full object-cover transition group-hover:scale-105"
              loading="lazy"
            />
          </div>

          <!-- Badge de categoría -->
          <Badge
            :class="cn(
              'absolute top-2 left-2 text-xs',
              getCategoryBadgeColor(photo.category)
            )"
          >
            {{ categories.find(c => c.value === photo.category)?.label || photo.category }}
          </Badge>

          <!-- Checkbox -->
          <div class="absolute top-2 right-2">
            <input
              v-model="selectedPhotos"
              type="checkbox"
              :value="photo.id"
              class="h-4 w-4 rounded border-primary text-primary focus:ring-primary"
            />
          </div>

          <!-- Overlay con info -->
          <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/80 to-transparent p-2 opacity-0 transition group-hover:opacity-100">
            <p v-if="photo.caption" class="text-xs text-white font-medium">
              {{ photo.caption }}
            </p>
            <p class="text-xs text-white/70">
              {{ formatFileSize(photo.file_size) }}
            </p>
          </div>

          <!-- Drag handle (visual only) -->
          <div class="absolute left-2 bottom-2 opacity-0 transition group-hover:opacity-100">
            <GripVertical class="h-4 w-4 text-white" />
          </div>
        </div>
      </div>

      <!-- Empty state -->
      <Card v-else class="border-0 shadow-lg">
        <CardContent class="flex flex-col items-center justify-center py-12">
          <ImageIcon class="h-16 w-16 text-muted-foreground/50" />
          <p class="mt-4 text-lg font-medium text-muted-foreground">
            No hay fotos en esta categoría
          </p>
          <p class="mt-2 text-sm text-muted-foreground">
            Sube la primera foto usando el botón de arriba
          </p>
        </CardContent>
      </Card>
    </div>
  </AppSidebarLayout>
</template>
