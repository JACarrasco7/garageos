<script setup lang="ts">
import { useForm, Link } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
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

const props = defineProps<{
  garages: Garage[]
}>()

const form = useForm({
  garage_id: props.garages[0]?.id || '',
  plate: '',
  vin: '',
  brand: '',
  model: '',
  year: new Date().getFullYear(),
  fuel_type: 'gasolina',
  color: '',
  current_km: 0,
})

const submit = () => {
  form.post(route('vehicles.store'))
}
</script>

<template>
  <AuthenticatedLayout>
    <template #header>
      <h2 class="text-xl font-semibold leading-tight">Añadir Vehículo</h2>
    </template>

    <div class="py-12">
      <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
        <Card>
          <CardHeader>
            <CardTitle>Nuevo vehículo</CardTitle>
          </CardHeader>
          <CardContent>
            <form @submit.prevent="submit" class="space-y-6">
              <div>
                <Label for="plate">Matrícula</Label>
                <Input
                  id="plate"
                  v-model="form.plate"
                  type="text"
                  required
                  maxlength="10"
                  class="mt-1"
                />
                <p v-if="form.errors.plate" class="text-sm text-destructive mt-1">{{ form.errors.plate }}</p>
              </div>

              <div>
                <Label for="brand">Marca</Label>
                <Input
                  id="brand"
                  v-model="form.brand"
                  type="text"
                  required
                  maxlength="50"
                  class="mt-1"
                />
              </div>

              <div>
                <Label for="model">Modelo</Label>
                <Input
                  id="model"
                  v-model="form.model"
                  type="text"
                  required
                  maxlength="80"
                  class="mt-1"
                />
              </div>

              <div>
                <Label for="year">Año</Label>
                <Input
                  id="year"
                  v-model="form.year"
                  type="number"
                  required
                  :min="1900"
                  :max="new Date().getFullYear() + 1"
                  class="mt-1"
                />
              </div>

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
              </div>

              <div>
                <Label for="current_km">Km actuales</Label>
                <Input
                  id="current_km"
                  v-model="form.current_km"
                  type="number"
                  required
                  min="0"
                  class="mt-1"
                />
              </div>

              <div class="flex justify-end space-x-3">
                <Button as-child variant="outline">
                  <Link :href="route('vehicles.index')">
                    <ArrowLeft class="w-4 h-4 mr-1" />
                    Cancelar
                  </Link>
                </Button>
                <Button type="submit" :disabled="form.processing">
                  Guardar vehículo
                </Button>
              </div>
            </form>
          </CardContent>
        </Card>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
