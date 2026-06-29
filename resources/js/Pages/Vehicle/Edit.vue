<script setup lang="ts">
import { Link, useForm } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head } from '@inertiajs/vue3'
import { Card, CardContent, CardHeader, CardTitle } from '@/Components/ui/card'
import { Button } from '@/Components/ui/button'
import { Input } from '@/Components/ui/input'
import { Label } from '@/Components/ui/label'
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/Components/ui/select'
import { ArrowLeft } from 'lucide-vue-next'

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

  <AuthenticatedLayout>
    <template #header>
      <h2 class="text-xl font-semibold leading-tight">Editar Vehículo</h2>
    </template>

    <div class="py-12">
      <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
        <Card>
          <CardHeader>
            <CardTitle>Editar vehículo</CardTitle>
          </CardHeader>
          <CardContent>
            <form @submit.prevent="submit" class="space-y-6">
              <div class="grid grid-cols-2 gap-4">
                <div>
                  <Label for="brand">Marca</Label>
                  <Input
                    id="brand"
                    v-model="form.brand"
                    type="text"
                    class="mt-1"
                    required
                  />
                  <p v-if="form.errors.brand" class="text-sm text-destructive mt-1">{{ form.errors.brand }}</p>
                </div>

                <div>
                  <Label for="model">Modelo</Label>
                  <Input
                    id="model"
                    v-model="form.model"
                    type="text"
                    class="mt-1"
                    required
                  />
                  <p v-if="form.errors.model" class="text-sm text-destructive mt-1">{{ form.errors.model }}</p>
                </div>
              </div>

              <div class="grid grid-cols-2 gap-4">
                <div>
                  <Label for="plate">Matrícula</Label>
                  <Input
                    id="plate"
                    v-model="form.plate"
                    type="text"
                    class="mt-1"
                    required
                  />
                  <p v-if="form.errors.plate" class="text-sm text-destructive mt-1">{{ form.errors.plate }}</p>
                </div>

                <div>
                  <Label for="year">Año</Label>
                  <Input
                    id="year"
                    v-model="form.year"
                    type="number"
                    class="mt-1"
                    required
                  />
                  <p v-if="form.errors.year" class="text-sm text-destructive mt-1">{{ form.errors.year }}</p>
                </div>
              </div>

              <div class="grid grid-cols-2 gap-4">
                <div>
                  <Label for="fuel_type">Combustible</Label>
                  <Select v-model="form.fuel_type">
                    <SelectTrigger class="mt-1">
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
                  <p v-if="form.errors.fuel_type" class="text-sm text-destructive mt-1">{{ form.errors.fuel_type }}</p>
                </div>

                <div>
                  <Label for="current_km">Km actuales</Label>
                  <Input
                    id="current_km"
                    v-model="form.current_km"
                    type="number"
                    class="mt-1"
                    required
                  />
                  <p v-if="form.errors.current_km" class="text-sm text-destructive mt-1">{{ form.errors.current_km }}</p>
                </div>
              </div>

              <div>
                <h3 class="text-lg font-medium mb-3">Especificaciones</h3>
                <div class="grid grid-cols-3 gap-4">
                  <div>
                    <Label for="engine_cc">Cilindrada (cc)</Label>
                    <Input
                      id="engine_cc"
                      v-model="form.engine_cc"
                      type="number"
                      class="mt-1"
                    />
                  </div>

                  <div>
                    <Label for="power_hp">Potencia (CV)</Label>
                    <Input
                      id="power_hp"
                      v-model="form.power_hp"
                      type="number"
                      class="mt-1"
                    />
                  </div>

                  <div>
                    <Label for="transmission">Transmisión</Label>
                    <Select v-model="form.transmission">
                      <SelectTrigger class="mt-1">
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

              <div class="flex justify-end space-x-3">
                <Button as-child variant="ghost">
                  <Link :href="route('vehicles.show', props.vehicle.id)">
                    <ArrowLeft class="w-4 h-4 mr-1" />
                    Cancelar
                  </Link>
                </Button>
                <Button type="submit" :disabled="form.processing">
                  {{ form.processing ? 'Guardando...' : 'Guardar cambios' }}
                </Button>
              </div>
            </form>
          </CardContent>
        </Card>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

