<script setup lang="ts">
import { useForm, Link } from '@inertiajs/vue3'
import AppSidebarLayout from '@/layouts/app/AppSidebarLayout.vue'
import { Card, CardContent, CardHeader, CardTitle } from '@/Components/ui/card'
import { Button } from '@/Components/ui/button'
import { Input } from '@/Components/ui/input'
import { Label } from '@/Components/ui/label'
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/Components/ui/select'
import { ArrowLeft, Car } from 'lucide-vue-next'

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
  <AppSidebarLayout>
    <template #header>
      Añadir Vehículo
    </template>

    <Card class="border-0 shadow-lg max-w-2xl">
      <CardHeader class="pb-3">
        <CardTitle class="flex items-center gap-2 text-lg font-semibold">
          <Car class="h-5 w-5" />
          Nuevo vehículo
        </CardTitle>
      </CardHeader>
      <CardContent>
        <form @submit.prevent="submit" class="space-y-4">
          <div class="space-y-2">
            <Label for="plate">Matrícula</Label>
            <Input
              id="plate"
              v-model="form.plate"
              type="text"
              required
              maxlength="10"
              placeholder="1234-ABC"
            />
            <p v-if="form.errors.plate" class="text-sm text-destructive">{{ form.errors.plate }}</p>
          </div>

          <div class="space-y-2">
            <Label for="brand">Marca</Label>
            <Input
              id="brand"
              v-model="form.brand"
              type="text"
              required
              maxlength="50"
              placeholder="Toyota"
            />
          </div>

          <div class="space-y-2">
            <Label for="model">Modelo</Label>
            <Input
              id="model"
              v-model="form.model"
              type="text"
              required
              maxlength="80"
              placeholder="Corolla"
            />
          </div>

          <div class="space-y-2">
            <Label for="year">Año</Label>
            <Input
              id="year"
              v-model="form.year"
              type="number"
              required
              :min="1900"
              :max="new Date().getFullYear() + 1"
            />
          </div>

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
          </div>

          <div class="space-y-2">
            <Label for="current_km">Km actuales</Label>
            <Input
              id="current_km"
              v-model="form.current_km"
              type="number"
              required
              min="0"
            />
          </div>

          <div class="flex justify-end gap-3 pt-4">
            <Button as-child variant="outline" size="sm" class="rounded-lg">
              <Link :href="route('vehicles.index')">
                <ArrowLeft class="mr-2 h-4 w-4" />
                Cancelar
              </Link>
            </Button>
            <Button type="submit" size="sm" class="rounded-lg" :disabled="form.processing">
              Guardar vehículo
            </Button>
          </div>
        </form>
      </CardContent>
    </Card>
  </AppSidebarLayout>
</template>
