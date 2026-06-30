<script setup lang="ts">
import { Link, useForm } from '@inertiajs/vue3'
import AppSidebarLayout from '@/layouts/app/AppSidebarLayout.vue'
import { Head } from '@inertiajs/vue3'
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/Components/ui/card'
import { Button } from '@/Components/ui/button'
import { Input } from '@/Components/ui/input'
import { Label } from '@/Components/ui/label'
import { ArrowLeft, Upload } from 'lucide-vue-next'

const form = useForm({
  file: null as File | null,
})

const submit = () => {
  form.post(route('vehicles.import.store'))
}

const handleFile = (event: Event) => {
  const target = event.target as HTMLInputElement
  form.file = target.files?.[0] ?? null
}
</script>

<template>
  <Head title="Importar Vehículos" />

  <AppSidebarLayout>
    <template #header>
      Importar Vehículos
    </template>

    <div class="max-w-2xl mx-auto space-y-6">
      <Card class="border-0 shadow-lg">
        <CardHeader>
          <CardTitle>Importar desde CSV</CardTitle>
          <CardDescription>Sube un archivo CSV con los datos de los vehículos</CardDescription>
        </CardHeader>
        <CardContent>
          <form @submit.prevent="submit" class="space-y-6">
            <div>
              <Label for="csv_file">Archivo CSV</Label>
              <Input
                id="csv_file"
                type="file"
                accept=".csv,.txt"
                @change="handleFile"
                class="mt-1"
                required
              />
              <p class="text-xs text-muted-foreground mt-1">
                Columnas: plate, brand, model, year, fuel_type, current_km
              </p>
            </div>

            <div class="flex justify-end space-x-3 pt-4">
              <Button as-child variant="ghost">
                <Link :href="route('vehicles.index')">
                  <ArrowLeft class="w-4 h-4 mr-1" />
                  Cancelar
                </Link>
              </Button>
              <Button type="submit" :disabled="form.processing">
                <Upload class="w-4 h-4 mr-1" />
                {{ form.processing ? 'Importando...' : 'Importar' }}
              </Button>
            </div>
          </form>
        </CardContent>
      </Card>
    </div>
  </AppSidebarLayout>
</template>
