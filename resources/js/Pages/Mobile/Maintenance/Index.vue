<script setup lang="ts">
import AppMobileLayout from '@/layouts/mobile/AppMobileLayout.vue';
import MobileHeader from '@/Components/mobile/MobileHeader.vue';
import MobileCard from '@/Components/mobile/MobileCard.vue';
import MobileFab from '@/Components/mobile/MobileFab.vue';
import { Head } from '@inertiajs/vue3';
import { Wrench, Calendar, Car } from 'lucide-vue-next';
import { router } from '@inertiajs/vue3';

interface Vehicle {
  id: number;
  plate: string;
  brand: string;
  model: string;
}

interface MaintenanceEntry {
  id: number;
  type: string;
  title: string;
  service_date: string;
  cost?: number;
  km_at_service: number;
}

defineProps<{
  vehicle: Vehicle;
  entries: MaintenanceEntry[];
}>();

const typeLabels: Record<string, string> = {
  aceite: 'Aceite',
  filtros: 'Filtros',
  neumaticos: 'Neumáticos',
  frenos: 'Frenos',
  itv: 'ITV',
  revision_general: 'Revisión General',
};
</script>

<template>
  <Head :title="`Mantenimiento - ${vehicle.brand}`" />

  <AppMobileLayout>
    <MobileHeader :title="`Mantenimiento - ${vehicle.brand}`" :show-back="true" @back="router.visit(`/m/vehicles/${vehicle.id}`)" />

    <div class="p-4 space-y-4">
      <MobileCard class="p-4">
        <div class="flex items-center gap-3">
          <div class="p-2 bg-blue-100 dark:bg-blue-950/20 rounded-lg">
            <Car class="h-6 w-6 text-blue-600" />
          </div>
          <div class="flex-1">
            <p class="text-sm text-muted-foreground">Vehículo</p>
            <p class="font-semibold text-foreground">{{ vehicle.brand }} {{ vehicle.model }}</p>
            <p class="text-xs text-muted-foreground">{{ vehicle.plate }}</p>
          </div>
        </div>
      </MobileCard>

      <div v-if="entries.length === 0" class="text-center py-8">
        <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-muted mb-3">
          <Wrench class="h-8 w-8 text-muted-foreground" />
        </div>
        <h3 class="font-medium text-foreground mb-1">No hay registros</h3>
        <p class="text-sm text-muted-foreground">Añade el primer mantenimiento</p>
      </div>

      <div v-else class="space-y-3">
        <div v-for="entry in entries" :key="entry.id" class="border-b border-border pb-3 last:border-0">
          <div class="flex items-center justify-between">
            <div class="flex-1">
              <h4 class="font-medium text-foreground">{{ entry.title }}</h4>
              <p class="text-xs text-muted-foreground">
                {{ entry.service_date }} · {{ entry.km_at_service.toLocaleString() }} km
              </p>
            </div>
            <div class="text-right">
              <p class="font-semibold text-foreground" v-if="entry.cost">
                {{ entry.cost }} €
              </p>
              <p class="text-xs text-muted-foreground" v-else>Gratis</p>
            </div>
          </div>
        </div>
      </div>
    </div>

    <MobileFab :href="route('mobile.maintenance.create', vehicle.id)" />
  </AppMobileLayout>
</template>
