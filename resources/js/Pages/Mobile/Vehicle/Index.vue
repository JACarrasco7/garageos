<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import AppMobileLayout from '@/layouts/mobile/AppMobileLayout.vue';
import MobileHeader from '@/Components/mobile/MobileHeader.vue';
import MobileCard from '@/Components/mobile/MobileCard.vue';
import MobileFab from '@/Components/mobile/MobileFab.vue';
import { Head } from '@inertiajs/vue3';
import { Car } from 'lucide-vue-next';
import { toast } from 'vue-sonner';

interface Vehicle {
  id: number;
  plate: string;
  brand: string;
  model: string;
  year: number;
  fuel_type: string;
  current_km: number;
  is_active: boolean;
}

defineProps<{
  vehicles: Vehicle[];
}>();
</script>

<template>
  <Head title="Mis Vehículos" />

  <AppMobileLayout>
    <MobileHeader title="Mis Vehículos" />

    <div class="p-4 space-y-4">
      <div v-if="vehicles.length === 0" class="text-center py-12">
        <div class="mx-auto flex h-20 w-20 items-center justify-center rounded-2xl bg-muted mb-4">
          <Car class="h-10 w-10 text-muted-foreground" />
        </div>
        <h3 class="font-medium text-foreground mb-2">No tienes vehículos registrados</h3>
        <p class="text-sm text-muted-foreground mb-6">Comienza añadiendo tu primer vehículo</p>
        <Link
          href="/m/vehicles/create"
          class="inline-flex items-center justify-center rounded-lg bg-primary px-4 py-2 text-sm font-medium text-primary-foreground"
        >
          Añadir primer vehículo
        </Link>
      </div>

      <div v-else class="space-y-3">
        <Link
          v-for="vehicle in vehicles"
          :key="vehicle.id"
          :href="route('mobile.vehicles.show', vehicle.id)"
          class="block"
        >
          <MobileCard class="p-4">
            <div class="flex items-center justify-between">
              <div class="flex-1">
                <h4 class="font-semibold text-lg text-foreground">
                  {{ vehicle.brand }} {{ vehicle.model }}
                </h4>
                <p class="text-sm text-muted-foreground">{{ vehicle.plate }}</p>
                <div class="flex items-center gap-4 mt-2 text-xs text-muted-foreground">
                  <div class="flex items-center gap-1">
                    <Calendar class="h-3 w-3" />
                    {{ vehicle.year }}
                  </div>
                  <div class="flex items-center gap-1">
                    <Fuel class="h-3 w-3" />
                    {{ vehicle.fuel_type }}
                  </div>
                </div>
                <p class="text-sm text-muted-foreground mt-1">{{ vehicle.current_km.toLocaleString() }} km</p>
              </div>
              <ChevronRight class="h-5 w-5 text-muted-foreground" />
            </div>
          </MobileCard>
        </Link>
      </div>
    </div>

    <MobileFab href="/m/vehicles/create" />
  </AppMobileLayout>
</template>
