<script setup lang="ts">
import { Link, router } from '@inertiajs/vue3';
import AppMobileLayout from '@/layouts/mobile/AppMobileLayout.vue';
import MobileHeader from '@/Components/mobile/MobileHeader.vue';
import MobileCard from '@/Components/mobile/MobileCard.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { Button } from '@/Components/ui/button';
import { Input } from '@/Components/ui/input';
import { Label } from '@/Components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/Components/ui/select';
import { computed } from 'vue';

interface Garage {
  id: number;
  name: string;
}

interface VehicleSpec {
  engine_cc?: number;
  power_hp?: number;
  torque_nm?: number;
  transmission?: string;
  drive?: string;
  doors?: number;
  seats?: number;
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
  specs?: VehicleSpec;
}

const props = defineProps<{
  vehicle?: Vehicle;
  garages: Garage[];
}>();

const form = useForm({
  garage_id: props.vehicle?.garage_id || '',
  plate: props.vehicle?.plate || '',
  vin: props.vehicle?.vin || '',
  brand: props.vehicle?.brand || '',
  model: props.vehicle?.model || '',
  year: props.vehicle?.year || new Date().getFullYear(),
  fuel_type: props.vehicle?.fuel_type || 'gasolina',
  color: props.vehicle?.color || '',
  current_km: props.vehicle?.current_km || 0,
  engine_cc: props.vehicle?.specs?.engine_cc || '',
  power_hp: props.vehicle?.specs?.power_hp || '',
  torque_nm: props.vehicle?.specs?.torque_nm || '',
  transmission: props.vehicle?.specs?.transmission || '',
  drive: props.vehicle?.specs?.drive || '',
  doors: props.vehicle?.specs?.doors || '',
  seats: props.vehicle?.specs?.seats || '',
});

const isEditing = computed(() => !!props.vehicle?.id);

const submit = () => {
  if (isEditing.value) {
    router.put(route('vehicles.update', props.vehicle!.id), form.data(), {
      preserveScroll: true,
    });
  } else {
    router.post(route('vehicles.store'), form.data(), {
      preserveScroll: true,
    });
  }
};
</script>

<template>
  <Head :title="isEditing ? 'Editar Vehículo' : 'Nuevo Vehículo'" />

  <AppMobileLayout>
    <MobileHeader
      :title="isEditing ? 'Editar Vehículo' : 'Nuevo Vehículo'"
      :show-back="true"
      @back="router.visit(route('mobile.vehicles.index'))"
    />

    <div class="p-4">
      <form @submit.prevent="submit">
        <div class="space-y-4">
          <div>
            <Label for="garage_id">Garaje</Label>
            <Select v-model="form.garage_id" required>
              <SelectTrigger class="w-full mt-1">
                <SelectValue placeholder="Selecciona un garaje" />
              </SelectTrigger>
              <SelectContent>
                <SelectItem v-for="garage in garages" :key="garage.id" :value="garage.id">
                  {{ garage.name }}
                </SelectItem>
              </SelectContent>
            </Select>
          </div>

          <div class="grid grid-cols-2 gap-4">
            <div>
              <Label for="plate">Matrícula</Label>
              <Input id="plate" v-model="form.plate" required class="mt-1" />
            </div>
            <div>
              <Label for="vin">VIN</Label>
              <Input id="vin" v-model="form.vin" class="mt-1" />
            </div>
          </div>

          <div class="grid grid-cols-2 gap-4">
            <div>
              <Label for="brand">Marca</Label>
              <Input id="brand" v-model="form.brand" required class="mt-1" />
            </div>
            <div>
              <Label for="model">Modelo</Label>
              <Input id="model" v-model="form.model" required class="mt-1" />
            </div>
          </div>

          <div class="grid grid-cols-2 gap-4">
            <div>
              <Label for="year">Año</Label>
              <Input id="year" type="number" v-model="form.year" required class="mt-1" />
            </div>
            <div>
              <Label for="fuel_type">Combustible</Label>
              <Select v-model="form.fuel_type" required>
                <SelectTrigger class="w-full mt-1">
                  <SelectValue />
                </SelectTrigger>
                <SelectContent>
                  <SelectItem value="gasolina">Gasolina</SelectItem>
                  <SelectItem value="diesel">Diesel</SelectItem>
                  <SelectItem value="hibrido">Híbrido</SelectItem>
                  <SelectItem value="electrico">Eléctrico</SelectItem>
                  <SelectItem value="glp">GLP</SelectItem>
                </SelectContent>
              </Select>
            </div>
          </div>

          <div class="grid grid-cols-2 gap-4">
            <div>
              <Label for="current_km">Km actuales</Label>
              <Input id="current_km" type="number" v-model="form.current_km" required class="mt-1" />
            </div>
            <div>
              <Label for="color">Color</Label>
              <Input id="color" v-model="form.color" class="mt-1" />
            </div>
          </div>

          <MobileCard class="p-4">
            <h3 class="font-semibold text-foreground mb-3">Datos técnicos (opcional)</h3>
            <div class="grid grid-cols-2 gap-4">
              <div>
                <Label for="engine_cc">Cilindrada (cc)</Label>
                <Input id="engine_cc" type="number" v-model="form.engine_cc" class="mt-1" />
              </div>
              <div>
                <Label for="power_hp">Potencia (CV)</Label>
                <Input id="power_hp" type="number" v-model="form.power_hp" class="mt-1" />
              </div>
            </div>
          </MobileCard>
        </div>

        <div class="mt-6">
          <Button type="submit" class="w-full" :disabled="form.processing">
            {{ isEditing ? 'Guardar cambios' : 'Crear vehículo' }}
          </Button>
        </div>
      </form>
    </div>
  </AppMobileLayout>
</template>
