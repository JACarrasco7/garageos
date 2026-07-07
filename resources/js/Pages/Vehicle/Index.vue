<script setup lang="ts">
import { Link } from '@inertiajs/vue3'
import WebLayout from '@/layouts/WebLayout.vue'
import { Head } from '@inertiajs/vue3'
import PageCard from '@/Components/PageCard.vue'
import { CardContent, CardHeader, CardTitle } from '@/Components/ui/card'
import { Badge } from '@/Components/ui/badge'
import { Button } from '@/Components/ui/button'
import { Plus, Car, Calendar, Fuel } from 'lucide-vue-next'

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

  <WebLayout>
    <template #header>
      Mis Vehículos
    </template>

    <PageCard data-tour="vehicles">
      <template #title>
        <CardHeader class="flex flex-row items-center justify-between pb-3">
          <CardTitle class="text-lg font-semibold">Lista de vehículos</CardTitle>
          <Button as-child variant="ghost" size="sm" class="rounded-lg glass-tab">
            <Link :href="route('vehicles.wizard')">
              <Plus class="mr-2 h-4 w-4" />
              Añadir vehículo
            </Link>
          </Button>
        </CardHeader>
      </template>

      <CardContent>
        <div v-if="vehicles.length === 0" class="text-center py-12">
          <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-muted mb-4">
            <Car class="h-8 w-8 text-muted-foreground" />
          </div>
          <h3 class="font-medium text-foreground mb-1">No tienes vehículos registrados</h3>
          <p class="text-sm text-muted-foreground mb-4">Comienza añadiendo tu primer vehículo</p>
          <Button as-child class="glass-button">
            <Link :href="route('vehicles.wizard')">Añadir primer vehículo</Link>
          </Button>
        </div>

        <div v-else class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
          <Link
            v-for="vehicle in vehicles"
            :key="vehicle.id"
            :href="route('vehicles.show', vehicle.id)"
            class="glass-panel border-0 hover:bg-accent/5 transition-all duration-200 group"
          >
            <div class="flex justify-between items-start mb-3">
              <div>
                <h4 class="font-semibold text-lg text-foreground group-hover:text-primary transition-colors">
                  {{ vehicle.brand }} {{ vehicle.model }}
                </h4>
                <p class="text-sm text-muted-foreground">{{ vehicle.plate }}</p>
              </div>
              <Badge :variant="vehicle.is_active ? 'default' : 'secondary'" class="shrink-0">
                {{ vehicle.is_active ? 'Activo' : 'Inactivo' }}
              </Badge>
            </div>
            <div class="flex items-center gap-4 text-sm text-muted-foreground">
              <div class="flex items-center gap-1">
                <Calendar class="h-3 w-3" />
                {{ vehicle.year }}
              </div>
              <div class="flex items-center gap-1">
                <Fuel class="h-3 w-3" />
                {{ vehicle.fuel_type }}
              </div>
            </div>
            <p class="text-sm text-muted-foreground mt-2">{{ vehicle.current_km.toLocaleString() }} km</p>
          </Link>
        </div>
      </CardContent>
    </PageCard>
  </WebLayout>
</template>
