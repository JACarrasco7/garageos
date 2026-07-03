<script setup lang="ts">
import AppMobileLayout from '@/layouts/mobile/AppMobileLayout.vue';
import MobileHeader from '@/Components/mobile/MobileHeader.vue';
import MobileCard from '@/Components/mobile/MobileCard.vue';
import { Head } from '@inertiajs/vue3';
import { Bell, Shield, Calendar, Wrench, AlertCircle } from 'lucide-vue-next';

interface AlertRule {
  id: number;
  type: string;
  trigger_date?: string;
  trigger_km?: number;
  vehicle: {
    id: number;
    brand: string;
    model: string;
    plate: string;
  };
}

defineProps<{
  alerts: AlertRule[];
}>();

const typeLabels: Record<string, string> = {
  itv: 'ITV',
  seguro: 'Seguro',
  aceite: 'Aceite',
  neumaticos: 'Neumáticos',
  revision: 'Revisión',
  impuesto: 'Impuesto',
  bateria: 'Batería',
  custom: 'Personalizada',
};

const typeIcons: Record<string, any> = {
  itv: Shield,
  seguro: Shield,
  aceite: Wrench,
  neumaticos: AlertCircle,
  revision: Wrench,
  impuesto: Calendar,
  bateria: AlertCircle,
  custom: Bell,
};
</script>

<template>
  <Head title="Alertas" />

  <AppMobileLayout>
    <MobileHeader title="Alertas" />

    <div class="p-4 space-y-4">
      <div v-if="alerts.length === 0" class="text-center py-12">
        <div class="mx-auto flex h-20 w-20 items-center justify-center rounded-2xl bg-muted mb-4">
          <Bell class="h-10 w-10 text-muted-foreground" />
        </div>
        <h3 class="font-medium text-foreground mb-2">No hay alertas</h3>
        <p class="text-sm text-muted-foreground">Todo en orden con tu vehículo</p>
      </div>

      <div v-else class="space-y-3">
        <MobileCard v-for="alert in alerts" :key="alert.id" class="p-3">
          <div class="flex items-start gap-3">
            <div class="p-2 bg-amber-100 dark:bg-amber-950/20 rounded-lg">
              <component :is="typeIcons[alert.type] || Bell" class="h-5 w-5 text-amber-600" />
            </div>
            <div class="flex-1">
              <div class="flex items-center justify-between">
                <h4 class="font-medium text-foreground">
                  {{ typeLabels[alert.type] || alert.type }}
                </h4>
              </div>
              <p class="text-sm text-muted-foreground">
                {{ alert.vehicle.brand }} {{ alert.vehicle.model }} - {{ alert.vehicle.plate }}
              </p>
              <p class="text-xs text-muted-foreground mt-1">
                {{ alert.trigger_date ? 'Vence: ' + alert.trigger_date : 'Km: ' + alert.trigger_km }}
              </p>
            </div>
          </div>
        </MobileCard>
      </div>
    </div>
  </AppMobileLayout>
</template>
