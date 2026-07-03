<script setup lang="ts">
import { Link, router } from '@inertiajs/vue3';
import AppMobileLayout from '@/layouts/mobile/AppMobileLayout.vue';
import MobileHeader from '@/Components/mobile/MobileHeader.vue';
import MobileCard from '@/Components/mobile/MobileCard.vue';
import { Head } from '@inertiajs/vue3';
import { Car, Calendar, Fuel, Edit, Trash2, ShieldCheck, Wrench } from 'lucide-vue-next';
import { Button } from '@/Components/ui/button';

interface VehicleSpec {
  engine_cc?: number;
  power_hp?: number;
  torque_nm?: number;
  transmission?: string;
  drive?: string;
  doors?: number;
  seats?: number;
}

interface ServicePack {
  id: number;
  maintenance_type: string;
  name: string;
  items: Array<{ name: string; price: number }>;
}

interface Document {
  id: number;
  type: string;
  title?: string;
  document_date?: string;
  expiry_date?: string;
}

interface MaintenanceEntry {
  id: number;
  type: string;
  title: string;
  service_date: string;
  cost?: number;
}

interface AlertRule {
  id: number;
  type: string;
  trigger_date?: string;
  trigger_km?: number;
}

interface Garage {
  id: number;
  name: string;
}

interface Photo {
  id: number;
  url: string;
}

interface Vehicle {
  id: number;
  garage_id: number;
  plate: string;
  vin?: string;
  brand: string;
  model: string;
  year: number;
  fuel_type: string;
  color?: string;
  current_km: number;
  is_active: boolean;
  specs?: VehicleSpec;
  garage: Garage;
  photos?: Photo[];
  documents?: Document[];
  maintenanceEntries?: MaintenanceEntry[];
  alertRules?: AlertRule[];
  recommendedServicePack?: ServicePack;
}

defineProps<{
  vehicle: Vehicle;
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
  <Head :title="vehicle.brand" />

  <AppMobileLayout>
    <MobileHeader :title="vehicle.brand" :show-back="true" />

    <div class="p-4 space-y-4">
      <MobileCard class="p-4">
        <div class="flex items-start justify-between">
          <div class="flex-1">
            <h2 class="text-xl font-bold text-foreground">
              {{ vehicle.brand }} {{ vehicle.model }}
            </h2>
            <p class="text-sm text-muted-foreground">{{ vehicle.plate }}</p>
            <p class="text-xs text-muted-foreground mt-1">{{ vehicle.garage.name }}</p>
          </div>
          <div class="flex items-center gap-2">
            <Link
              :href="route('vehicles.edit', vehicle.id)"
              class="p-2 rounded-lg hover:bg-accent"
            >
              <Edit class="h-4 w-4" />
            </Link>
            <Button
              variant="ghost"
              size="sm"
              @click="router.delete(route('vehicles.destroy', vehicle.id))"
              class="p-2"
            >
              <Trash2 class="h-4 w-4 text-destructive" />
            </Button>
          </div>
        </div>

        <div class="grid grid-cols-2 gap-4 mt-4">
          <div>
            <p class="text-xs text-muted-foreground">Año</p>
            <p class="font-medium">{{ vehicle.year }}</p>
          </div>
          <div>
            <p class="text-xs text-muted-foreground">Combustible</p>
            <p class="font-medium">{{ vehicle.fuel_type }}</p>
          </div>
          <div>
            <p class="text-xs text-muted-foreground">Km actuales</p>
            <p class="font-medium">{{ vehicle.current_km.toLocaleString() }} km</p>
          </div>
          <div>
            <p class="text-xs text-muted-foreground">Estado</p>
            <p :class="vehicle.is_active ? 'text-green-600' : 'text-muted-foreground'">
              {{ vehicle.is_active ? 'Activo' : 'Inactivo' }}
            </p>
          </div>
        </div>
      </MobileCard>

      <MobileCard v-if="vehicle.specs" class="p-4">
        <h3 class="font-semibold text-foreground mb-3">Ficha técnica</h3>
        <div class="grid grid-cols-2 gap-3 text-sm">
          <div v-if="vehicle.specs.engine_cc">
            <p class="text-xs text-muted-foreground">Cilindrada</p>
            <p class="font-medium">{{ vehicle.specs.engine_cc }} cc</p>
          </div>
          <div v-if="vehicle.specs.power_hp">
            <p class="text-xs text-muted-foreground">Potencia</p>
            <p class="font-medium">{{ vehicle.specs.power_hp }} CV</p>
          </div>
          <div v-if="vehicle.specs.torque_nm">
            <p class="text-xs text-muted-foreground">Par motor</p>
            <p class="font-medium">{{ vehicle.specs.torque_nm }} Nm</p>
          </div>
          <div v-if="vehicle.specs.transmission">
            <p class="text-xs text-muted-foreground">Transmisión</p>
            <p class="font-medium">{{ vehicle.specs.transmission }}</p>
          </div>
        </div>
      </MobileCard>

      <MobileCard v-if="vehicle.recommendedServicePack" class="p-4">
        <div class="flex items-center gap-2 mb-3">
          <Wrench class="h-5 w-5 text-primary" />
          <h3 class="font-semibold text-foreground">Servicio recomendado</h3>
        </div>
        <div class="space-y-2">
          <p class="font-medium">{{ vehicle.recommendedServicePack.name }}</p>
          <div class="space-y-1">
            <div
              v-for="(item, index) in vehicle.recommendedServicePack.items"
              :key="index"
              class="flex justify-between text-sm"
            >
              <span class="text-muted-foreground">{{ item.name }}</span>
              <span class="font-medium">{{ item.price }}€</span>
            </div>
          </div>
          <Button
            size="sm"
            class="w-full mt-3"
            @click="router.visit(route('maintenance.create', vehicle.id))"
          >
            Programar servicio
          </Button>
        </div>
      </MobileCard>

      <MobileCard class="p-4">
        <h3 class="font-semibold text-foreground mb-3">Alertas activas</h3>
        <div v-if="vehicle.alertRules && vehicle.alertRules.length > 0" class="space-y-2">
          <div
            v-for="alert in vehicle.alertRules"
            :key="alert.id"
            class="flex items-center gap-2 p-2 rounded-lg bg-amber-50 dark:bg-amber-950/20"
          >
            <ShieldCheck class="h-4 w-4 text-amber-600" />
            <div class="flex-1">
              <p class="text-sm font-medium">
                {{ typeLabels[alert.type] || alert.type }}
              </p>
              <p class="text-xs text-muted-foreground">
                {{ alert.trigger_date ? 'Caduca: ' + alert.trigger_date : 'Km: ' + alert.trigger_km }}
              </p>
            </div>
          </div>
        </div>
        <p v-else class="text-sm text-muted-foreground">No hay alertas activas</p>
      </MobileCard>

      <Link :href="route('mobile.vehicles.photos', vehicle.id)" class="block">
        <MobileCard class="p-4">
          <div class="flex items-center justify-between">
            <div>
              <h3 class="font-semibold text-foreground">Fotos del vehículo</h3>
              <p class="text-sm text-muted-foreground">
                {{ vehicle.photos?.length || 0 }} foto(s)
              </p>
            </div>
            <div class="flex -space-x-2">
              <img
                v-for="(photo, index) in (vehicle.photos || []).slice(0, 3)"
                :key="photo.id"
                :src="photo.url"
                class="w-10 h-10 rounded-full border-2 border-background object-cover"
                :style="{ zIndex: 10 - index }"
              />
              <div
                v-if="(vehicle.photos?.length || 0) > 3"
                class="w-10 h-10 rounded-full border-2 border-background bg-muted flex items-center justify-center text-xs font-medium"
                :style="{ zIndex: 7 }"
              >
                +{{ (vehicle.photos?.length || 0) - 3 }}
              </div>
            </div>
          </div>
        </MobileCard>
      </Link>
    </div>
  </AppMobileLayout>
</template>
