<script setup lang="ts">
import AppMobileLayout from '@/layouts/mobile/AppMobileLayout.vue';
import MobileHeader from '@/Components/mobile/MobileHeader.vue';
import MobileCard from '@/Components/mobile/MobileCard.vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import { Button } from '@/Components/ui/button';
import { Input } from '@/Components/ui/input';
import { Label } from '@/Components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/Components/ui/select';
import { Textarea } from '@/Components/ui/textarea';

interface Vehicle {
  id: number;
  plate: string;
  brand: string;
  model: string;
}

const props = defineProps<{
  vehicle: Vehicle;
}>();

const form = useForm({
  type: '',
  title: '',
  description: '',
  km_at_service: 0,
  service_date: new Date().toISOString().split('T')[0],
  cost: 0,
  notes: '',
});

const submit = () => {
  form.post(`/m/vehicles/${props.vehicle.id}/maintenance`, {
    preserveScroll: true,
  });
};
</script>

<template>
  <Head title="Nueva Entrada" />

  <AppMobileLayout>
    <MobileHeader title="Nueva Entrada" :show-back="true" @back="router.visit(`/m/vehicles/${props.vehicle.id}`)" />

    <div class="p-4">
      <form @submit.prevent="submit">
        <div class="space-y-4">
          <div>
            <Label for="type">Tipo de mantenimiento</Label>
            <Select v-model="form.type" required>
              <SelectTrigger class="w-full mt-1">
                <SelectValue placeholder="Selecciona tipo" />
              </SelectTrigger>
              <SelectContent>
                <SelectItem value="aceite">Aceite</SelectItem>
                <SelectItem value="filtros">Filtros</SelectItem>
                <SelectItem value="neumaticos">Neumáticos</SelectItem>
                <SelectItem value="frenos">Frenos</SelectItem>
                <SelectItem value="itv">ITV</SelectItem>
                <SelectItem value="revision_general">Revisión General</SelectItem>
                <SelectItem value="otro">Otro</SelectItem>
              </SelectContent>
            </Select>
          </div>

          <div>
            <Label for="title">Título</Label>
            <Input id="title" v-model="form.title" required class="mt-1" />
          </div>

          <div>
            <Label for="description">Descripción</Label>
            <Textarea id="description" v-model="form.description" class="mt-1" />
          </div>

          <div class="grid grid-cols-2 gap-4">
            <div>
              <Label for="km_at_service">Km</Label>
              <Input id="km_at_service" type="number" v-model="form.km_at_service" required class="mt-1" />
            </div>
            <div>
              <Label for="service_date">Fecha</Label>
              <Input id="service_date" type="date" v-model="form.service_date" required class="mt-1" />
            </div>
          </div>

          <div>
            <Label for="cost">Coste (€)</Label>
            <Input id="cost" type="number" step="0.01" v-model="form.cost" class="mt-1" />
          </div>

          <div>
            <Label for="notes">Notas</Label>
            <Textarea id="notes" v-model="form.notes" class="mt-1" />
          </div>
        </div>

        <div class="mt-6">
          <Button type="submit" class="w-full" :disabled="form.processing">
            Guardar entrada
          </Button>
        </div>
      </form>
    </div>
  </AppMobileLayout>
</template>
