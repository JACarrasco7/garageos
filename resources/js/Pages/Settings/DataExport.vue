<script setup lang="ts">
import { ref } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import WebLayout from '@/layouts/WebLayout.vue';
import { Card, CardContent, CardHeader, CardTitle } from '@/Components/ui/card';
import { Button } from '@/Components/ui/button';
import { Download, Trash2, AlertTriangle } from 'lucide-vue-next';

const exporting = ref(false);
const downloadUrl = ref<string | null>(null);

const exportData = async () => {
  exporting.value = true;
  try {
    const response = await fetch(route('data-export.export'));
    const data = await response.json();
    downloadUrl.value = data.url;
  } catch (error) {
    console.error('Export error:', error);
  } finally {
    exporting.value = false;
  }
};

const deleteAccount = () => {
  if (confirm('¿Estás seguro? Esta acción eliminará tu cuenta y todos tus datos.')) {
    router.delete(route('account.delete'));
  }
};
</script>

<template>
  <Head title="Exportar datos" />

  <WebLayout>
    <template #header>
      Exportar datos
    </template>

    <div class="max-w-2xl space-y-6">
      <Card class="glass-surface">
        <CardHeader>
          <CardTitle class="flex items-center gap-2">
            <Download class="h-5 w-5" />
            Exportar mis datos
          </CardTitle>
        </CardHeader>
        <CardContent class="space-y-4">
          <p class="text-sm text-muted-foreground">
            Descarga una copia de todos tus datos en formato Excel.
          </p>
          <Button @click="exportData" :disabled="exporting" class="glass-button">
            {{ exporting ? 'Generando...' : 'Exportar datos' }}
          </Button>
          <a
            v-if="downloadUrl"
            :href="downloadUrl"
            class="block text-sm text-primary underline"
            target="_blank"
          >
            Descargar archivo
          </a>
        </CardContent>
      </Card>

      <Card class="glass-surface border-destructive">
        <CardHeader>
          <CardTitle class="flex items-center gap-2 text-destructive">
            <AlertTriangle class="h-5 w-5" />
            Eliminar cuenta
          </CardTitle>
        </CardHeader>
        <CardContent class="space-y-4">
          <p class="text-sm text-muted-foreground">
            Esta acción eliminará permanentemente tu cuenta y todos tus datos.
          </p>
          <Button variant="destructive" @click="deleteAccount">
            <Trash2 class="mr-2 h-4 w-4" />
            Eliminar cuenta
          </Button>
        </CardContent>
      </Card>
    </div>
  </WebLayout>
</template>
