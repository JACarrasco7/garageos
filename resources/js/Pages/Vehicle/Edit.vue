<script setup lang="ts">
import { Link, useForm } from '@inertiajs/vue3'
import WebLayout from '@/layouts/WebLayout.vue'
import { Head } from '@inertiajs/vue3'
import PageCard from '@/Components/PageCard.vue'
import { CardContent, CardHeader, CardTitle } from '@/Components/ui/card'
import { Button } from '@/Components/ui/button'
import { Input } from '@/Components/ui/input'
import { Label } from '@/Components/ui/label'
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/Components/ui/select'
import { ArrowLeft, Car } from 'lucide-vue-next'

interface Garage {
  id: number
  name: string
}

interface Vehicle {
  id: number
  plate: string
  vin: string | null
  brand: string
  model: string
  year: number
  fuel_type: string
  color: string | null
  current_km: number
  garage_id: number
  specs?: {
    engine_cc: number | null
    power_hp: number | null
    transmission: string | null
  } | null
}

const props = defineProps<{
  vehicle: Vehicle
  garages: Garage[]
}>()

const form = useForm({
  garage_id: props.vehicle.garage_id,
  plate: props.vehicle.plate,
  vin: props.vehicle.vin || '',
  brand: props.vehicle.brand,
  model: props.vehicle.model,
  year: String(props.vehicle.year),
  fuel_type: props.vehicle.fuel_type,
  color: props.vehicle.color || '',
  current_km: String(props.vehicle.current_km),
  engine_cc: String(props.vehicle.specs?.engine_cc || ''),
  power_hp: String(props.vehicle.specs?.power_hp || ''),
  transmission: props.vehicle.specs?.transmission || '',
})

const submit = () => {
  form.put(route('vehicles.update', props.vehicle.id))
}
</script>

<template>
  <Head title="Editar Vehículo" />

  <WebLayout>
    <template #header>
      Editar Vehículo
    </template>

    <PageCard class="max-w-2xl">
      <template #title>
        <CardHeader class="pb-3">
          <CardTitle class="flex items-center gap-2 text-lg font-semibold">
            <Car class="h-5 w-5" />
            Editar vehículo
          </CardTitle>
        </CardHeader>
      </template>

      <CardContent>
        <form @submit.prevent="submit" class="space-y-4">
          <div class="grid grid-cols-2 gap-4">
            <div class="space-y-2">
              <Label for="brand">Marca</Label>
              <Input
                id="brand"
                v-model="form.brand"
                type="text"
                required
              />
              <p v-if="form.errors.brand" class="text-sm text-destructive">{{ form.errors.brand }}</p>
            </div>

            <div class="space-y-2">
              <Label for="model">Modelo</Label>
              <Input
                id="model"
                v-model="form.model"
                type="text"
                required
              />
              <p v-if="form.errors.model" class="text-sm text-destructive">{{ form.errors.model }}</p>
            </div>
          </div>

          <div class="grid grid-cols-2 gap-4">
            <div class="space-y-2">
              <Label for="plate">Matrícula</Label>
              <Input
                id="plate"
                v-model="form.plate"
                type="text"
                required
              />
              <p v-if="form.errors.plate" class="text-sm text-destructive">{{ form.errors.plate }}</p>
            </div>

            <div class="space-y-2">
              <Label for="year">Año</Label>
              <Input
                id="year"
                v-model="form.year"
                type="number"
                required
              />
              <p v-if="form.errors.year" class="text-sm text-destructive">{{ form.errors.year }}</p>
            </div>
          </div>

          <div class="grid grid-cols-2 gap-4">
            <div class="space-y-2">
              <Label for="fuel_type">Combustible</Label>
              <Select v-model="form.fuel_type">
                <SelectTrigger>
                  <SelectValue placeholder="Seleccionar" />
                </SelectTrigger>
                <SelectContent>
                  <SelectItem value="gasolina">Gasolina</SelectItem>
                  <SelectItem value="diesel">Diesel</SelectItem>
                  <SelectItem value="hibrido">Híbrido</SelectItem>
                  <SelectItem value="electrico">Eléctrico</SelectItem>
                  <SelectItem value="glp">GLP</SelectItem>
                </SelectContent>
              </Select>
              <p v-if="form.errors.fuel_type" class="text-sm text-destructive">{{ form.errors.fuel_type }}</p>
            </div>

            <div class="space-y-2">
              <Label for="current_km">Km actuales</Label>
              <Input
                id="current_km"
                v-model="form.current_km"
                type="number"
                required
              />
              <p v-if="form.errors.current_km" class="text-sm text-destructive">{{ form.errors.current_km }}</p>
            </div>
          </div>

          <div class="space-y-3">
            <h3 class="text-sm font-semibold uppercase tracking-wider text-muted-foreground">Especificaciones</h3>
            <div class="grid grid-cols-3 gap-4">
              <div class="space-y-2">
                <Label for="engine_cc">Cilindrada (cc)</Label>
                <Input
                  id="engine_cc"
                  v-model="form.engine_cc"
                  type="number"
                />
              </div>

              <div class="space-y-2">
                <Label for="power_hp">Potencia (CV)</Label>
                <Input
                  id="power_hp"
                  v-model="form.power_hp"
                  type="number"
                />
              </div>

              <div class="space-y-2">
                <Label for="transmission">Transmisión</Label>
                <Select v-model="form.transmission">
                  <SelectTrigger>
                    <SelectValue placeholder="Seleccionar" />
                  </SelectTrigger>
                  <SelectContent>
                    <SelectItem value="">Seleccionar</SelectItem>
                    <SelectItem value="manual">Manual</SelectItem>
                    <SelectItem value="automatic">Automático</SelectItem>
                  </SelectContent>
                </Select>
              </div>
            </div>
          </div>

          <div class="flex justify-end gap-3 pt-4">
            <Button as-child variant="outline" size="sm" class="rounded-lg">
              <Link :href="route('vehicles.show', props.vehicle.id)">
                <ArrowLeft class="mr-2 h-4 w-4" />
                Cancelar
              </Link>
            </Button>
            <Button type="submit" size="sm" class="rounded-lg" :disabled="form.processing">
              {{ form.processing ? 'Guardando...' : 'Guardar cambios' }}
            </Button>
          </div>
        </form>
      </CardContent>
    </PageCard>
  </WebLayout>
</template>

