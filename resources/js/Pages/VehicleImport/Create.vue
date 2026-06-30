<script setup lang="ts">
import { useForm, Link } from '@inertiajs/vue3'
import { Head } from '@inertiajs/vue3'
import AppSidebarLayout from '@/layouts/app/AppSidebarLayout.vue'
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/Components/ui/card'
import { Button } from '@/Components/ui/button'
import { Input } from '@/Components/ui/input'
import { Label } from '@/Components/ui/label'
import { ArrowLeft, Plus } from 'lucide-vue-next'

const form = useForm({
  plate_original: '',
  brand: '',
  model: '',
  year: '',
  engine_cc: '',
  power_kw: '',
})

const submit = () => {
  form.post(route('imports.store'))
}
</script>

<template>
  <Head title="Importar Vehículo" />

  <AppSidebarLayout>
    <template #header>
      Importar Vehículo desde Alemania
    </template>

    <div class="max-w-2xl mx-auto space-y-6">
      <Card class="border-0 shadow-lg">
        <CardHeader>
          <CardTitle>Nueva Importación</CardTitle>
          <CardDescription>Introduce los datos del vehículo alemán para generar la matrícula española</CardDescription>
        </CardHeader>
        <CardContent>
          <form @submit.prevent="submit" class="space-y-6">
            <div class="space-y-2">
              <Label for="plate_original">Matrícula Alemán</Label>
              <Input
                id="plate_original"
                v-model="form.plate_original"
                type="text"
                placeholder="B-XX-1234"
                required
              />
              <p class="text-xs text-muted-foreground">Formato: B-XX-1234</p>
            </div>

            <div class="grid grid-cols-2 gap-4">
              <div class="space-y-2">
                <Label for="brand">Marca</Label>
                <Input id="brand" v-model="form.brand" type="text" required />
              </div>
              <div class="space-y-2">
                <Label for="model">Modelo</Label>
                <Input id="model" v-model="form.model" type="text" required />
              </div>
            </div>

            <div class="space-y-2">
              <Label for="year">Año</Label>
              <Input id="year" v-model="form.year" type="number" required />
            </div>

            <div class="grid grid-cols-2 gap-4">
              <div class="space-y-2">
                <Label for="engine_cc">Cilindrada (cc)</Label>
                <Input id="engine_cc" v-model="form.engine_cc" type="number" />
              </div>
              <div class="space-y-2">
                <Label for="power_kw">Potencia (kW)</Label>
                <Input id="power_kw" v-model="form.power_kw" type="number" />
              </div>
            </div>

            <div class="flex justify-end space-x-3 pt-4">
              <Button as-child variant="ghost">
                <Link :href="route('imports.index')">
                  <ArrowLeft class="w-4 h-4 mr-1" />
                  Cancelar
                </Link>
              </Button>
              <Button type="submit" :disabled="form.processing">
                <Plus class="w-4 h-4 mr-1" />
                {{ form.processing ? 'Procesando...' : 'Crear Solicitud' }}
              </Button>
            </div>
          </form>
        </CardContent>
      </Card>
    </div>
  </AppSidebarLayout>
</template>