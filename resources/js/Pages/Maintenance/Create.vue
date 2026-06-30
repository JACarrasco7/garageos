<script setup lang="ts">
import { useForm, Link } from '@inertiajs/vue3'
import { Head, router } from '@inertiajs/vue3'
import AppSidebarLayout from '@/layouts/app/AppSidebarLayout.vue'
import { Card, CardContent, CardHeader, CardTitle } from '@/Components/ui/card'
import { Button } from '@/Components/ui/button'
import { Input } from '@/Components/ui/input'
import { Label } from '@/Components/ui/label'
import { Textarea } from '@/Components/ui/textarea'
import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from '@/Components/ui/select'
import { ArrowLeft, Save } from 'lucide-vue-next'

interface Vehicle {
  id: number
  plate: string
  brand: string
  model: string
  current_km: number
}

const props = defineProps<{
  vehicle: Vehicle
}>()

const form = useForm({
  type: 'otro',
  title: '',
  description: '',
  km_at_service: props.vehicle.current_km,
  service_date: new Date().toISOString().split('T')[0],
  cost: '',
  notes: '',
})

const submit = () => {
  form.post(route('maintenance.store', props.vehicle.id), {
    preserveScroll: true,
    onSuccess: () => {
      router.visit(route('maintenance.index', props.vehicle.id))
    },
  })
}

const typeOptions = [
  { value: 'aceite', label: 'Aceite' },
  { value: 'filtros', label: 'Filtros' },
  { value: 'neumaticos', label: 'Neumáticos' },
  { value: 'frenos', label: 'Frenos' },
  { value: 'distribucion', label: 'Distribución' },
  { value: 'embrague', label: 'Embrague' },
  { value: 'bateria', label: 'Batería' },
  { value: 'itv', label: 'ITV' },
  { value: 'revision_general', label: 'Revisión General' },
  { value: 'otro', label: 'Otro' },
]
</script>

<template>
  <Head title="Añadir Mantenimiento" />

  <AppSidebarLayout>
    <template #header>
      Añadir Mantenimiento - {{ vehicle.brand }} {{ vehicle.model }}
    </template>

    <div class="max-w-2xl mx-auto space-y-6">
      <Card class="border-0 shadow-lg">
        <CardHeader>
          <CardTitle>Datos del servicio</CardTitle>
        </CardHeader>
        <CardContent>
          <form @submit.prevent="submit" class="space-y-6">
            <!-- Tipo -->
            <div class="space-y-2">
              <Label for="type">Tipo</Label>
              <Select v-model="form.type">
                <SelectTrigger id="type">
                  <SelectValue placeholder="Selecciona tipo" />
                </SelectTrigger>
                <SelectContent>
                  <SelectItem v-for="opt in typeOptions" :key="opt.value" :value="opt.value">
                    {{ opt.label }}
                  </SelectItem>
                </SelectContent>
              </Select>
            </div>

            <!-- Título -->
            <div class="space-y-2">
              <Label for="title">Título</Label>
              <Input
                id="title"
                v-model="form.title"
                type="text"
                placeholder="Ej: Cambio de aceite y filtros"
                required
              />
            </div>

            <!-- Descripción -->
            <div class="space-y-2">
              <Label for="description">Descripción</Label>
              <Textarea
                id="description"
                v-model="form.description"
                rows="3"
                placeholder="Detalles del servicio realizado..."
              />
            </div>

            <!-- Fecha y Km -->
            <div class="grid grid-cols-2 gap-4">
              <div class="space-y-2">
                <Label for="service_date">Fecha</Label>
                <Input
                  id="service_date"
                  v-model="form.service_date"
                  type="date"
                  required
                />
              </div>
              <div class="space-y-2">
                <Label for="km_at_service">Km en el servicio</Label>
                <Input
                  id="km_at_service"
                  v-model="form.km_at_service"
                  type="number"
                  min="0"
                  required
                />
              </div>
            </div>

            <!-- Coste -->
            <div class="space-y-2">
              <Label for="cost">Coste (€)</Label>
              <Input
                id="cost"
                v-model="form.cost"
                type="number"
                step="0.01"
                min="0"
                placeholder="0.00"
              />
            </div>

            <!-- Notas -->
            <div class="space-y-2">
              <Label for="notes">Notas técnicas</Label>
              <Textarea
                id="notes"
                v-model="form.notes"
                rows="2"
                placeholder="Observaciones, piezas usadas, etc."
              />
            </div>

            <!-- Botones -->
            <div class="flex justify-end space-x-3 pt-4">
              <Button as-child variant="ghost">
                <Link :href="route('maintenance.index', vehicle.id)">
                  <ArrowLeft class="w-4 h-4 mr-1" />
                  Cancelar
                </Link>
              </Button>
              <Button type="submit" :disabled="form.processing">
                <Save class="w-4 h-4 mr-1" />
                {{ form.processing ? 'Guardando...' : 'Guardar entrada' }}
              </Button>
            </div>
          </form>
        </CardContent>
      </Card>
    </div>
  </AppSidebarLayout>
</template>
