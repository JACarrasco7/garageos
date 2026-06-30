<script setup lang="ts">
import { Link, useForm } from '@inertiajs/vue3'
import AppSidebarLayout from '@/layouts/app/AppSidebarLayout.vue'
import { Head } from '@inertiajs/vue3'
import { Card, CardContent, CardHeader, CardTitle, CardFooter } from '@/Components/ui/card'
import { Button } from '@/Components/ui/button'
import { ArrowLeft, FileText } from 'lucide-vue-next'

interface Vehicle {
  id: number
  plate: string
  brand: string
  model: string
  year: number
  current_km: number
}

const props = defineProps<{
  vehicle: Vehicle
}>()

const form = useForm({})

const submit = () => {
  form.post(route('marketplace.store', { vehicle: props.vehicle.id }))
}
</script>

<template>
  <Head title="Generar Informe de Venta" />

  <AppSidebarLayout>
    <template #header>
      Generar Informe de Venta
    </template>

    <div class="max-w-2xl space-y-6">
      <Card>
        <CardHeader>
          <CardTitle class="flex items-center gap-2">
            <FileText class="h-5 w-5" />
            Informe para venta
          </CardTitle>
        </CardHeader>
        <CardContent class="space-y-4">
          <div class="border-b border-border pb-4">
            <h4 class="font-medium text-lg">{{ vehicle.brand }} {{ vehicle.model }}</h4>
            <p class="text-muted-foreground">{{ vehicle.plate }} • {{ vehicle.year }}</p>
            <p class="text-sm text-muted-foreground">{{ vehicle.current_km.toLocaleString() }} km</p>
          </div>

          <div>
            <h5 class="font-medium mb-2">El informe incluirá:</h5>
            <ul class="text-sm text-muted-foreground space-y-1">
              <li>• Puntuación de salud del vehículo</li>
              <li>• Historial de mantenimientos</li>
              <li>• Documentación disponible</li>
              <li>• PDF descargable con certificado</li>
            </ul>
          </div>
        </CardContent>
        <CardFooter class="justify-end space-x-3">
          <Button as-child variant="ghost">
            <Link :href="route('vehicles.show', vehicle.id)">
              Cancelar
            </Link>
          </Button>
          <Button @click="submit" :disabled="form.processing">
            {{ form.processing ? 'Generando...' : 'Generar Informe' }}
          </Button>
        </CardFooter>
      </Card>
    </div>
  </AppSidebarLayout>
</template>
