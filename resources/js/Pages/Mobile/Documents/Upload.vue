<script setup lang="ts">
import AppMobileLayout from '@/layouts/mobile/AppMobileLayout.vue';
import MobileHeader from '@/Components/mobile/MobileHeader.vue';
import MobileCard from '@/Components/mobile/MobileCard.vue';
import { Head, router, useForm } from '@inertiajs/vue3';
import { Button } from '@/Components/ui/button';
import { Input } from '@/Components/ui/input';
import { Label } from '@/Components/ui/label';
import { ref } from 'vue';
import { useCamera } from '@/Composables/useCamera';
import { FileUp, Camera, Upload, X, Loader2 } from 'lucide-vue-next';

const { takePicture } = useCamera();

const form = useForm({
  type: 'otro',
  title: '',
  file: null as File | null,
  document_date: '',
  expiry_date: '',
  amount: '',
});

const props = defineProps<{
  vehicle: {
    id: number;
    plate: string;
    brand: string;
    model: string;
  };
}>();

const cameraImage = ref<string | null>(null);
const isUploading = ref(false);

const handleCamera = async () => {
  const image = await takePicture();
  if (image) {
    cameraImage.value = image;
    const response = await fetch(image);
    const blob = await response.blob();
    form.file = new File([blob], 'camera-photo.jpg', { type: 'image/jpeg' });
  }
};

const handleGallery = async () => {
  const input = document.createElement('input');
  input.type = 'file';
  input.accept = 'image/*,application/pdf';
  input.onchange = (e) => {
    const file = (e.target as HTMLInputElement).files?.[0];
    if (file) {
      form.file = file;
      cameraImage.value = file.type.startsWith('image/') ? URL.createObjectURL(file) : null;
    }
  };
  input.click();
};

const handleUpload = () => {
  isUploading.value = true;
  form.post(route('mobile.documents.store', props.vehicle.id), {
    onFinish: () => {
      isUploading.value = false;
    },
    onSuccess: () => {
      router.visit(route('mobile.vehicles.show', props.vehicle.id));
    },
  });
};

const clearImage = () => {
  form.file = null;
  cameraImage.value = null;
};
</script>

<template>
  <Head title="Subir Documento" />

  <AppMobileLayout>
    <MobileHeader title="Subir Documento" :show-back="true" @back="router.visit(route('mobile.vehicles.show', props.vehicle.id))" />

    <div class="p-4 space-y-4">
      <MobileCard v-if="!cameraImage" class="p-4 text-center">
        <div class="mx-auto flex h-24 w-24 items-center justify-center rounded-2xl bg-muted mb-4">
          <FileUp class="h-10 w-10 text-muted-foreground" />
        </div>
        <h3 class="font-medium text-foreground mb-2">Selecciona documento</h3>
        <p class="text-sm text-muted-foreground mb-4">Cámara o galería</p>

        <div class="space-y-2">
          <Button @click="handleCamera" class="w-full">
            <Camera class="mr-2 h-4 w-4" />
            Escanear
          </Button>
          <Button variant="outline" @click="handleGallery" class="w-full">
            <Upload class="mr-2 h-4 w-4" />
            Galería
          </Button>
        </div>
      </MobileCard>

      <MobileCard v-else class="p-4">
        <div class="relative mb-4">
          <img :src="cameraImage" alt="Preview" class="w-full h-48 object-cover rounded-lg" />
          <Button @click="clearImage" size="icon" variant="destructive" class="absolute top-2 right-2">
            <X class="h-4 w-4" />
          </Button>
        </div>

        <div class="space-y-3">
          <div>
            <Label for="type">Tipo</Label>
            <select v-model="form.type" class="w-full mt-1 px-3 py-2 border rounded-md bg-background">
              <option value="factura">Factura</option>
              <option value="itv">ITV</option>
              <option value="seguro">Seguro</option>
              <option value="impuesto">Impuesto</option>
              <option value="otro">Otro</option>
            </select>
          </div>

          <div>
            <Label for="title">Título</Label>
            <Input v-model="form.title" placeholder="Opcional" class="mt-1" />
          </div>

          <Button @click="handleUpload" :disabled="isUploading" class="w-full">
            <Loader2 v-if="isUploading" class="mr-2 h-4 w-4 animate-spin" />
            {{ isUploading ? 'Subiendo...' : 'Subir documento' }}
          </Button>
        </div>
      </MobileCard>

      <MobileCard class="p-4">
        <h3 class="font-semibold text-foreground mb-3">Tipos soportados</h3>
        <div class="grid grid-cols-2 gap-2 text-sm">
          <div class="flex items-center gap-2">
            <div class="w-2 h-2 rounded-full bg-primary" />
            <span>Facturas</span>
          </div>
          <div class="flex items-center gap-2">
            <div class="w-2 h-2 rounded-full bg-primary" />
            <span>ITV</span>
          </div>
          <div class="flex items-center gap-2">
            <div class="w-2 h-2 rounded-full bg-primary" />
            <span>Seguros</span>
          </div>
          <div class="flex items-center gap-2">
            <div class="w-2 h-2 rounded-full bg-primary" />
            <span>Permisos</span>
          </div>
        </div>
      </MobileCard>
    </div>
  </AppMobileLayout>
</template>
