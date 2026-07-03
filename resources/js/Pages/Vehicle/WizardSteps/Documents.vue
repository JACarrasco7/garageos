<script setup lang="ts">
import { ref, computed } from 'vue';
import { useForm } from '@inertiajs/vue3';
import Label from '@/Components/ui/label/Label.vue';
import Button from '@/Components/ui/button/Button.vue';
import { Upload, FileText, X } from 'lucide-vue-next';

const props = defineProps<{
    modelValue: ReturnType<typeof useForm>;
}>();

const emit = defineEmits<{
    (e: 'update:modelValue', value: typeof props.modelValue): void;
}>();

const form = computed(() => props.modelValue);

const documents = ref<Array<{ file: File; type: string; preview?: string }>>([]);

const documentTypes = [
    { value: 'factura', label: 'Factura de compra' },
    { value: 'itv', label: 'Última ITV' },
    { value: 'seguro', label: 'Seguro' },
    { value: 'impuesto', label: 'Impuesto de circulación' },
    { value: 'permiso', label: 'Permiso de circulación' },
    { value: 'otro', label: 'Otro' },
];

const handleFileChange = (event: Event, type: string) => {
    const target = event.target as HTMLInputElement;
    const file = target.files?.[0];

    if (file) {
        if (file.size > 20 * 1024 * 1024) {
            alert('El documento es demasiado grande. Máximo 20MB.');
          return;
        }

        const reader = new FileReader();
        reader.onload = (e) => {
            documents.value.push({
                file,
                type,
                preview: file.type.startsWith('image/') ? e.target?.result as string : undefined,
            });
        };
        reader.readAsDataURL(file);
    }
};

const removeDocument = (index: number) => {
    documents.value.splice(index, 1);
};

const openFileDialog = (type: string) => {
    const input = document.createElement('input');
    input.type = 'file';
    input.accept = 'image/*,.pdf';
  input.onchange = (e) => handleFileChange(e as Event, type);
    input.click();
};
</script>

<template>
  <div class="space-y-6">
    <div class="space-y-4">
      <h3 class="text-lg font-semibold text-foreground">Documentos iniciales</h3>
      <p class="text-sm text-muted-foreground">
        Sube los documentos principales de tu vehículo. Esto es opcional pero recomendable.
      </p>
    </div>

    <div class="space-y-4">
      <div class="flex flex-wrap gap-2">
        <Button
          v-for="docType in documentTypes"
          :key="docType.value"
          variant="outline"
          size="sm"
          @click="openFileDialog(docType.value)"
        >
          <Upload class="mr-2 h-4 w-4" />
          {{ docType.label }}
        </Button>
      </div>

      <div
        v-if="documents.length === 0"
        class="flex h-32 flex-col items-center justify-center rounded-xl border-2 border-dashed border-border bg-muted/30"
      >
        <FileText class="h-8 w-8 text-muted-foreground/50" />
        <p class="mt-2 text-sm text-muted-foreground">
          No hay documentos añadidos
        </p>
      </div>

      <div v-else class="space-y-3">
        <div
          v-for="(doc, index) in documents"
          :key="index"
          class="flex items-center gap-3 rounded-xl border p-4"
        >
          <div
            class="flex h-12 w-12 items-center justify-center rounded-lg bg-primary/10"
          >
            <FileText class="h-6 w-6 text-primary" />
          </div>
          <div class="flex-1 space-y-1">
            <p class="text-sm font-medium text-foreground">
              {{ documentTypes.find(d => d.value === doc.type)?.label }}
            </p>
            <p class="text-xs text-muted-foreground">
              {{ doc.file.name }} · {{ (doc.file.size / 1024 / 1024).toFixed(2) }} MB
            </p>
          </div>
          <Button
            variant="ghost"
            size="icon"
            @click="removeDocument(index)"
          >
            <X class="h-4 w-4 text-destructive" />
          </Button>
        </div>
      </div>
    </div>

    <p class="text-xs text-muted-foreground">
      * Los documentos se guardarán después de crear el vehículo
    </p>
  </div>
</template>
