<script setup lang="ts">
import { Link } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head } from '@inertiajs/vue3'
import { Card, CardContent, CardHeader, CardTitle } from '@/Components/ui/card'
import { Badge } from '@/Components/ui/badge'
import { Button } from '@/Components/ui/button'
import { Plus, Car } from 'lucide-vue-next'

interface Vehicle {
  id: number
  plate: string
  brand: string
  model: string
  year: number
  fuel_type: string
  current_km: number
  is_active: boolean
}

defineProps<{
  vehicles: Vehicle[]
}>()
</script>

<template>
  <Head title="Mis Vehículos" />

  <AuthenticatedLayout>
    <template #header>
      <h2 class="text-xl font-semibold leading-tight">Mis Vehículos</h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <Card>
          <CardHeader class="flex flex-row items-center justify-between">
            <CardTitle>Lista de vehículos</CardTitle>
            <Button as-child>
              <Link :href="route('vehicles.create')">
                <Plus class="mr-2 h-4 w-4" />
                Añadir vehículo
              </Link>
            </Button>
          </CardHeader>
          <CardContent>
            <div v-if="vehicles.length === 0" class="text-center py-8 text-muted-foreground">
              <Car class="mx-auto h-12 w-12 mb-2 opacity-50" />
              <p>No tienes vehículos registrados</p>
            </div>

            <div v-else class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
              <Link
                v-for="vehicle in vehicles"
                :key="vehicle.id"
                :href="route('vehicles.show', vehicle.id)"
                class="block border border-border rounded-lg p-4 hover:bg-accent transition"
              >
                <div class="flex justify-between items-start mb-2">
                  <h4 class="font-semibold text-lg">
                    {{ vehicle.brand }} {{ vehicle.model }}
                  </h4>
                  <Badge :variant="vehicle.is_active ? 'default' : 'secondary'">
                    {{ vehicle.is_active ? 'Activo' : 'Inactivo' }}
                  </Badge>
                </div>
                <p class="text-sm text-muted-foreground">
                  {{ vehicle.plate }} • {{ vehicle.year }}
                </p>
                <p class="text-sm text-muted-foreground">
                  {{ vehicle.current_km.toLocaleString() }} km
                </p>
                <span class="mt-3 inline-block text-primary hover:underline text-sm">
                  Ver detalles →
                </span>
              </Link>
            </div>
          </CardContent>
        </Card>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
