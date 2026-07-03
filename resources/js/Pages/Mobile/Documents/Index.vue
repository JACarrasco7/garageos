<script setup lang="ts">
import AppMobileLayout from '@/layouts/mobile/AppMobileLayout.vue';
import MobileHeader from '@/Components/mobile/MobileHeader.vue';
import MobileCard from '@/Components/mobile/MobileCard.vue';
import MobileFab from '@/Components/mobile/MobileFab.vue';
import { Head, Link } from '@inertiajs/vue3';
import { FileText } from 'lucide-vue-next';
import { cn } from '@/lib/utils';
import { router } from '@inertiajs/vue3';

interface Document {
  id: number;
  type: string;
  title?: string;
  document_date?: string;
  expiry_date?: string;
  mime_type?: string;
}

interface Vehicle {
  id: number;
  plate: string;
  brand: string;
  model: string;
}

defineProps<{
  documents: Document[];
  vehicle?: Vehicle;
}>();

const typeLabels: Record<string, string> = {
  factura: 'Factura',
  itv: 'ITV',
  seguro: 'Seguro',
  impuesto: 'Impuesto',
  otro: 'Otro',
};

const typeColors: Record<string, string> = {
  factura: 'bg-blue-100 text-blue-600',
  itv: 'bg-green-100 text-green-600',
  seguro: 'bg-purple-100 text-purple-600',
  impuesto: 'bg-yellow-100 text-yellow-600',
  otro: 'bg-gray-100 text-gray-600',
};
</script>

<template>
  <Head title="Documentos" />

  <AppMobileLayout>
    <MobileHeader :title="vehicle ? `${vehicle.brand} ${vehicle.model}` : 'Documentos'" :show-back="!!vehicle" @back="router.visit(route('mobile.vehicles.index'))" />

    <div class="p-4 space-y-4">
      <div v-if="documents.length === 0" class="text-center py-12">
        <div class="mx-auto flex h-20 w-20 items-center justify-center rounded-2xl bg-muted mb-4">
          <FileText class="h-10 w-10 text-muted-foreground" />
        </div>
        <h3 class="font-medium text-foreground mb-2">No hay documentos</h3>
        <p class="text-sm text-muted-foreground mb-4">Añade tu primer documento</p>
      </div>

      <div v-else class="grid grid-cols-2 gap-3">
        <div v-for="doc in documents" :key="doc.id" class="space-y-2">
          <MobileCard class="p-3 relative">
            <div :class="cn('p-2 rounded-lg', typeColors[doc.type] || 'bg-gray-100')">
              <FileText class="h-6 w-6" />
            </div>
            <p class="text-sm font-medium truncate">{{ doc.title || typeLabels[doc.type] || doc.type }}</p>
            <p v-if="doc.expiry_date" class="text-xs text-muted-foreground">
              Vence: {{ doc.expiry_date }}
            </p>
          </MobileCard>
        </div>
      </div>
    </div>

    <MobileFab :href="vehicle ? `/m/vehicles/${vehicle.id}/documents/upload` : '/m/documents/upload'" />
  </AppMobileLayout>
</template>
