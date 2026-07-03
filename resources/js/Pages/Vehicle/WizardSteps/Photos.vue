<script setup lang="ts">
import { ref, computed } from 'vue';
import { useForm } from '@inertiajs/vue3';
import Label from '@/Components/ui/label/Label.vue';
import Button from '@/Components/ui/button/Button.vue';
import { Upload, X, Image as ImageIcon } from 'lucide-vue-next';

const props = defineProps<{
    modelValue: ReturnType<typeof useForm>;
}>();

const emit = defineEmits<{
    (e: 'update:modelValue', value: typeof props.modelValue): void;
}>();

const form = computed(() => props.modelValue);
const preview = ref<string | null>(null);
const fileInput = ref<HTMLInputElement>();

const handleFileChange = (event: Event) => {
    const target = event.target as HTMLInputElement;
    const file = target.files?.[0];

    if (file) {
        if (file.size > 10 * 1024 * 1024) {
            alert('La foto es demasiado grande. Máximo 10MB.');
            return;
        }

        form.value.photo = file;

        const reader = new FileReader();
        reader.onload = (e) => {
            preview.value = e.target?.result as string;
        };
        reader.readAsDataURL(file);
    }
};

const removePhoto = () => {
    form.value.photo = null;
    preview.value = null;
    if (fileInput.value) {
        fileInput.value.value = '';
    }
};

const openFileDialog = () => {
    fileInput.value?.click();
};
</script>

<template>
  <div class="space-y-6">
    <div class="space-y-4">
      <h3 class="text-lg font-semibold text-foreground">Foto del vehículo</h3>
      <p class="text-sm text-muted-foreground">
        Añade una foto principal de tu vehículo. Esto es opcional pero ayuda a identificarlo visualmente.
      </p>
    </div>

    <div class="space-y-4">
      <Label for="photo">Foto principal</Label>

      <div
        v-if="!preview"
        class="flex h-64 cursor-pointer flex-col items-center justify-center rounded-xl border-2 border-dashed border-border bg-muted/30 transition-colors hover:border-primary/50 hover:bg-muted/50"
        @click="openFileDialog"
      >
        <ImageIcon class="h-12 w-12 text-muted-foreground/50" />
        <p class="mt-3 text-sm text-muted-foreground">
          Haz clic para subir una foto
        </p>
        <p class="mt-1 text-xs text-muted-foreground/60">
          JPG, PNG hasta 10MB
        </p>
      </div>

      <div v-else class="relative">
        <img
          :src="preview"
          alt="Vista previa"
          class="h-64 w-full rounded-xl object-cover"
        />
        <Button
          variant="destructive"
          size="icon"
          class="absolute right-2 top-2"
          @click="removePhoto"
        >
          <X class="h-4 w-4" />
        </Button>
      </div>

      <input
        ref="fileInput"
        type="file"
        accept="image/jpeg,image/png,image/webp"
        class="hidden"
        @change="handleFileChange"
      />

      <Button
        v-if="preview"
        variant="outline"
        size="sm"
        class="mt-2"
        @click="openFileDialog"
      >
        <Upload class="mr-2 h-4 w-4" />
        Cambiar foto
      </Button>
    </div>
  </div>
</template>
