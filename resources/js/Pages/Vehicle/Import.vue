<script setup lang="ts">
import { Link, useForm } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
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

  <AuthenticatedLayout>
    <template #header>
      <h2 class="text-xl font-semibold leading-tight">Importar Vehículos</h2>
    </template>

    <div class="py-12">
      <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
        <Card>
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

              <div class="flex justify-end space-x-3">
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
    </div>
  </AuthenticatedLayout>
</template>
