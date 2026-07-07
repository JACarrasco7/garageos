<script setup lang="ts">
import { useForm } from '@inertiajs/vue3'
import { Head } from '@inertiajs/vue3'
import WebLayout from '@/layouts/WebLayout.vue'
import { Button } from '@/Components/ui/button'
import { Card, CardContent, CardHeader, CardTitle } from '@/Components/ui/card'
import { Input } from '@/Components/ui/input'
import { Label } from '@/Components/ui/label'
import { Textarea } from '@/Components/ui/textarea'

const form = useForm({
  brand: '',
  model: '',
  year: null,
  fuel_type: '',
  mileage: null,
  budget_min: null,
  budget_max: null,
  url_link: '',
  description: '',
})

const submit = () => {
  form.post(route('vehicle-import.requests.store'))
}
</script>

<template>
  <Head title="Nueva Solicitud de Importación" />

  <WebLayout>
    <template #header>
      Nueva Solicitud de Importación
    </template>

    <div class="container mx-auto py-6">
      <h1 class="text-2xl font-bold mb-6">Nueva Solicitud de Importación</h1>

      <Card class="max-w-2xl">
        <CardHeader>
          <CardTitle>Detalles del Vehículo</CardTitle>
        </CardHeader>
        <CardContent>
          <form @submit.prevent="submit" class="space-y-4">
            <div class="grid grid-cols-2 gap-4">
              <div>
                <Label for="brand">Marca</Label>
                <Input id="brand" v-model="form.brand" required />
              </div>
              <div>
                <Label for="model">Modelo</Label>
                <Input id="model" v-model="form.model" required />
              </div>
            </div>

            <div class="grid grid-cols-3 gap-4">
              <div>
                <Label for="year">Año</Label>
                <Input id="year" type="number" v-model="form.year" />
              </div>
              <div>
                <Label for="fuel_type">Combustible</Label>
                <Input id="fuel_type" v-model="form.fuel_type" />
              </div>
              <div>
                <Label for="mileage">Kilometraje</Label>
                <Input id="mileage" type="number" v-model="form.mileage" />
              </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
              <div>
                <Label for="budget_min">Presupuesto Mínimo (€)</Label>
                <Input id="budget_min" type="number" step="0.01" v-model="form.budget_min" />
              </div>
              <div>
                <Label for="budget_max">Presupuesto Máximo (€)</Label>
                <Input id="budget_max" type="number" step="0.01" v-model="form.budget_max" />
              </div>
            </div>

            <div>
              <Label for="url_link">Enlace al vehículo</Label>
              <Input id="url_link" type="url" v-model="form.url_link" placeholder="https://" />
            </div>

            <div>
              <Label for="description">Descripción</Label>
              <Textarea id="description" v-model="form.description" />
            </div>

            <Button type="submit" :disabled="form.processing">Crear Solicitud</Button>
          </form>
        </CardContent>
      </Card>
    </div>
  </WebLayout>
</template>
