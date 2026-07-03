<script setup lang="ts">
import AppMobileLayout from '@/layouts/mobile/AppMobileLayout.vue';
import MobileHeader from '@/Components/mobile/MobileHeader.vue';
import MobileCard from '@/Components/mobile/MobileCard.vue';
import { Head, router } from '@inertiajs/vue3';
import { Button } from '@/Components/ui/button';
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/Components/ui/tabs';
import { useCamera } from '@/Composables/useCamera';
import { ref, computed } from 'vue';
import { Camera, Upload, X, Trash2, Loader2, Check } from 'lucide-vue-next';

interface Photo {
  id: number;
  url: string;
  category: string;
  caption: string;
}

const props = defineProps<{
  vehicle: {
    id: number;
    photos: Photo[];
  };
}>();

const { takePicture } = useCamera();

const photos = ref<Photo[]>(props.vehicle.photos || []);
const pendingPhotos = ref<Array<{ url: string; file: File; category: string }>>([]);
const selectedCategory = ref('principal');
const selectedPhoto = ref<Photo | null>(null);
const isUploading = ref(false);
const uploadProgress = ref(0);

const categories = [
  { value: 'all', label: 'Todas' },
  { value: 'principal', label: 'Principal' },
  { value: 'frontal', label: 'Frente' },
  { value: 'lateral', label: 'Lateral' },
  { value: 'trasero', label: 'Atrás' },
  { value: 'interior', label: 'Interior' },
  { value: 'motor', label: 'Motor' },
  { value: 'averia', label: 'Avería' },
  { value: 'daño', label: 'Daño' },
  { value: 'documento', label: 'Documento' },
  { value: 'antes_reparacion', label: 'Antes' },
  { value: 'despues_reparacion', label: 'Después' },
];

const filteredPhotos = computed(() => {
  if (selectedCategory.value === 'all') return photos.value;
  return photos.value.filter(p => p.category === selectedCategory.value);
});

const handleCamera = async () => {
  const image = await takePicture();
  if (image) {
    const response = await fetch(image);
    const blob = await response.blob();
    const file = new File([blob], `photo-${Date.now()}.jpg`, { type: 'image/jpeg' });
    pendingPhotos.value.push({
      url: image,
      file,
      category: selectedCategory.value === 'all' ? 'principal' : selectedCategory.value,
    });
  }
};

const handleGallery = async () => {
  const input = document.createElement('input');
  input.type = 'file';
  input.accept = 'image/*';
  input.multiple = true;
  input.onchange = (e) => {
    const files = (e.target as HTMLInputElement).files;
    if (files) {
      Array.from(files).forEach((file) => {
        pendingPhotos.value.push({
          url: URL.createObjectURL(file),
          file,
          category: selectedCategory.value === 'all' ? 'principal' : selectedCategory.value,
        });
      });
    }
  };
  input.click();
};

const uploadPhotos = async () => {
  if (pendingPhotos.value.length === 0) return;

  isUploading.value = true;
  uploadProgress.value = 0;

  const formData = new FormData();
  const category = selectedCategory.value === 'all' ? 'principal' : selectedCategory.value;

  pendingPhotos.value.forEach((photo) => {
    formData.append('files[]', photo.file);
  });
  formData.append('category', category);

  const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

  try {
    const response = await fetch(route('mobile.vehicles.photos.store', props.vehicle.id), {
      method: 'POST',
      headers: {
        Accept: 'application/json',
        'X-CSRF-TOKEN': csrfToken || '',
      },
      body: formData,
    });

    if (!response.ok) throw new Error('Upload failed');

    pendingPhotos.value = [];
    uploadProgress.value = 100;
    isUploading.value = false;
    await router.reload({ only: ['vehicle'] });
  } catch (error) {
    console.error('Upload error:', error);
    alert('Error al subir las fotos');
    isUploading.value = false;
  }
};

const removePendingPhoto = (index: number) => {
  pendingPhotos.value.splice(index, 1);
};

const deletePhoto = async (photoId: number) => {
  if (!confirm('¿Eliminar esta foto?')) return;
  await router.delete(route('mobile.vehicles.photos.destroy', { vehicle: props.vehicle.id, photo: photoId }), {
    onSuccess: () => {
      photos.value = photos.value.filter(p => p.id !== photoId);
    },
  });
};

const getBorderColor = (category: string) => {
  const colors: Record<string, string> = {
    principal: 'border-green-500',
    averia: 'border-red-500',
    daño: 'border-orange-500',
    documento: 'border-blue-500',
    antes_reparacion: 'border-yellow-500',
    despues_reparacion: 'border-emerald-500',
  };
  return colors[category] || 'border-transparent';
};
</script>

<template>
  <Head title="Fotos del Vehículo" />

  <AppMobileLayout>
    <MobileHeader
      title="Fotos del Vehículo"
      :show-back="true"
      @back="router.visit(route('mobile.vehicles.show', vehicle.id))"
    />

    <div class="pb-24">
      <Tabs v-model="selectedCategory" class="w-full">
        <div class="overflow-x-auto bg-background border-b px-2">
          <TabsList class="inline-flex w-max h-10 bg-transparent p-0">
            <TabsTrigger
              v-for="cat in categories"
              :key="cat.value"
              :value="cat.value"
              class="data-[state=active]:bg-primary data-[state=active]:text-primary-foreground"
            >
              {{ cat.label }}
            </TabsTrigger>
          </TabsList>
        </div>

        <TabsContent value="all" class="mt-4 px-4">
          <div class="grid grid-cols-2 gap-3">
            <div
              v-for="photo in filteredPhotos"
              :key="photo.id"
              @click="selectedPhoto = photo"
              class="relative aspect-square rounded-lg overflow-hidden border-2 cursor-pointer"
              :class="getBorderColor(photo.category)"
            >
              <img :src="photo.url" :alt="photo.caption" class="w-full h-full object-cover" />
              <div class="absolute inset-0 bg-linear-to-t from-black/50 to-transparent opacity-0 hover:opacity-100 transition-opacity">
                <div class="absolute bottom-2 left-2 right-2">
                  <p class="text-white text-xs truncate">{{ photo.caption }}</p>
                  <p class="text-white/70 text-xs capitalize">{{ photo.category.replace('_', ' ') }}</p>
                </div>
              </div>
              <Button
                size="icon"
                variant="destructive"
                @click.stop="deletePhoto(photo.id)"
                class="absolute top-2 right-2 h-6 w-6"
              >
                <Trash2 class="h-3 w-3" />
              </Button>
            </div>
          </div>
        </TabsContent>

        <TabsContent :value="selectedCategory" class="mt-4 px-4">
          <div v-if="filteredPhotos.length === 0" class="text-center py-8 text-muted-foreground">
            <Camera class="h-12 w-12 mx-auto mb-2 opacity-50" />
            <p>Sin fotos en esta categoría</p>
          </div>
          <div v-else class="grid grid-cols-2 gap-3">
            <div
              v-for="photo in filteredPhotos"
              :key="photo.id"
              @click="selectedPhoto = photo"
              class="relative aspect-square rounded-lg overflow-hidden border-2 cursor-pointer"
              :class="getBorderColor(photo.category)"
            >
              <img :src="photo.url" :alt="photo.caption" class="w-full h-full object-cover" />
              <Button
                size="icon"
                variant="destructive"
                @click.stop="deletePhoto(photo.id)"
                class="absolute top-2 right-2 h-6 w-6"
              >
                <Trash2 class="h-3 w-3" />
              </Button>
            </div>
          </div>
        </TabsContent>
      </Tabs>

      <div v-if="pendingPhotos.length > 0" class="mt-4 px-4">
        <MobileCard class="p-4">
          <h3 class="font-semibold mb-3 flex items-center gap-2">
            <Check class="h-4 w-4 text-primary" />
            {{ pendingPhotos.length }} foto(s) pendiente(s)
          </h3>
          <div class="grid grid-cols-4 gap-2 mb-3">
            <div v-for="(photo, index) in pendingPhotos" :key="index" class="relative aspect-square rounded-lg overflow-hidden">
              <img :src="photo.url" class="w-full h-full object-cover" />
              <Button
                size="icon"
                variant="destructive"
                @click="removePendingPhoto(index)"
                class="absolute top-1 right-1 h-5 w-5"
              >
                <X class="h-3 w-3" />
              </Button>
            </div>
          </div>
          <div v-if="isUploading" class="mb-3">
            <div class="w-full bg-muted rounded-full h-2">
              <div class="bg-primary h-2 rounded-full transition-all" :style="{ width: uploadProgress + '%' }" />
            </div>
            <p class="text-xs text-muted-foreground mt-1 text-center">Subiendo... {{ uploadProgress }}%</p>
          </div>
          <Button @click="uploadPhotos" :disabled="isUploading" class="w-full">
            <Loader2 v-if="isUploading" class="mr-2 h-4 w-4 animate-spin" />
            Subir fotos
          </Button>
        </MobileCard>
      </div>
    </div>

    <div class="fixed bottom-0 left-0 right-0 bg-background border-t p-4 safe-area-bottom">
      <div class="flex gap-2">
        <Button @click="handleCamera" class="flex-1 h-14 text-lg">
          <Camera class="mr-2 h-5 w-5" />
          Foto
        </Button>
        <Button variant="outline" @click="handleGallery" class="flex-1 h-14 text-lg">
          <Upload class="mr-2 h-5 w-5" />
          Galería
        </Button>
      </div>
    </div>

    <div v-if="selectedPhoto" class="fixed inset-0 z-50 bg-black/90 flex items-center justify-center p-4" @click="selectedPhoto = null">
      <img :src="selectedPhoto.url" :alt="selectedPhoto.caption" class="max-w-full max-h-full object-contain" />
      <div class="absolute bottom-0 left-0 right-0 bg-linear-to-t from-black to-transparent p-4">
        <p class="text-white text-lg font-medium">{{ selectedPhoto.caption }}</p>
        <p class="text-white/70 text-sm capitalize">{{ selectedPhoto.category.replace('_', ' ') }}</p>
      </div>
    </div>
  </AppMobileLayout>
</template>
