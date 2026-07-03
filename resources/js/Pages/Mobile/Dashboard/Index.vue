<script setup lang="ts">
import AppMobileLayout from '@/layouts/mobile/AppMobileLayout.vue';
import MobileHeader from '@/Components/mobile/MobileHeader.vue';
import MobileCard from '@/Components/mobile/MobileCard.vue';
import MobileFab from '@/Components/mobile/MobileFab.vue';
import { Head } from '@inertiajs/vue3';
import { Car, Wrench, FileText, Bell, TrendingUp } from 'lucide-vue-next';

interface Vehicle {
  id: number;
  plate: string;
  brand: string;
  model: string;
  current_km: number;
  is_active: boolean;
}

interface MaintenanceEntry {
  id: number;
  type: string;
  title: string;
  cost: number | null;
}

interface Document {
  id: number;
  type: string;
}

interface AlertRule {
  id: number;
  type: string;
}

defineProps<{
  vehicles: Vehicle[];
  recentEntries: MaintenanceEntry[];
  documentsCount: number;
  alertsCount: number;
}>();
</script>

<template>
  <Head title="Dashboard" />

  <AppMobileLayout>
    <MobileHeader title="GarageOS" />

    <div class="p-4 space-y-4">
      <MobileCard class="p-4 bg-linear-to-br from-primary/10 to-accent/10">
        <div class="flex items-center gap-3">
          <div class="p-2 bg-primary/20 rounded-lg">
            <Car class="h-6 w-6 text-primary" />
          </div>
          <div class="flex-1">
            <p class="text-sm text-muted-foreground">Vehículos registrados</p>
            <p class="text-2xl font-bold text-foreground">{{ vehicles.length }}</p>
          </div>
        </div>
      </MobileCard>

      <div class="grid grid-cols-2 gap-3">
        <MobileCard class="p-3 text-center">
          <Wrench class="h-5 w-5 text-muted-foreground mx-auto mb-1" />
          <p class="text-xs text-muted-foreground">Mantenimiento</p>
          <p class="text-lg font-bold text-foreground">{{ recentEntries.length }}</p>
        </MobileCard>

        <MobileCard class="p-3 text-center">
          <FileText class="h-5 w-5 text-muted-foreground mx-auto mb-1" />
          <p class="text-xs text-muted-foreground">Documentos</p>
          <p class="text-lg font-bold text-foreground">{{ documentsCount }}</p>
        </MobileCard>

        <MobileCard class="p-3 text-center">
          <Bell class="h-5 w-5 text-muted-foreground mx-auto mb-1" />
          <p class="text-xs text-muted-foreground">Alertas</p>
          <p class="text-lg font-bold text-foreground">{{ alertsCount }}</p>
        </MobileCard>

        <MobileCard class="p-3 text-center">
          <TrendingUp class="h-5 w-5 text-muted-foreground mx-auto mb-1" />
          <p class="text-xs text-muted-foreground">Coste/Km</p>
          <p class="text-lg font-bold text-foreground">-- €</p>
        </MobileCard>
      </div>

      <MobileCard class="p-4">
        <h3 class="font-semibold text-foreground mb-3">Vehículos recientes</h3>
        <div v-if="vehicles.length === 0" class="text-center py-4">
          <p class="text-sm text-muted-foreground">No tienes vehículos registrados</p>
        </div>
        <div v-else class="space-y-2">
          <Link
            v-for="vehicle in vehicles.slice(0, 3)"
            :key="vehicle.id"
            :href="route('mobile.vehicles.show', vehicle.id)"
            class="flex items-center justify-between p-2 rounded-lg hover:bg-accent/50"
          >
            <div>
              <p class="font-medium text-foreground">{{ vehicle.brand }} {{ vehicle.model }}</p>
              <p class="text-xs text-muted-foreground">{{ vehicle.plate }} · {{ vehicle.current_km.toLocaleString() }} km</p>
            </div>
          </Link>
        </div>
      </MobileCard>
    </div>

    <MobileFab href="/m/vehicles/create" />
  </AppMobileLayout>
</template>
