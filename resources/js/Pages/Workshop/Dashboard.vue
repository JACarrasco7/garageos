<script setup lang="ts">
import AppSidebarLayout from '@/layouts/app/AppSidebarLayout.vue'
import { Head, Link } from '@inertiajs/vue3'
import { Card, CardContent, CardHeader, CardTitle } from '@/Components/ui/card'
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/Components/ui/table'
import { Button } from '@/Components/ui/button'
import { Wrench, Car } from 'lucide-vue-next'

interface Vehicle {
  id: number
  plate: string
  brand: string
  model: string
  year: number
  current_km: number
  owner: {
    name: string
  }
}

defineProps<{
  vehicles: Vehicle[]
}>()
</script>

<template>
  <Head title="Panel Taller" />

  <AppSidebarLayout>
    <div class="flex flex-col gap-6 p-6">
      <div>
        <h1 class="text-2xl font-bold">Panel de Taller</h1>
        <p class="text-muted-foreground">Vehículos asignados para mantenimiento</p>
      </div>

      <Card>
        <CardHeader>
          <CardTitle class="flex items-center gap-2">
            <Car class="h-5 w-5" />
            Vehículos asignados
          </CardTitle>
        </CardHeader>
        <CardContent>
          <Table>
            <TableHeader>
              <TableRow>
                <TableHead>Matrícula</TableHead>
                <TableHead>Vehículo</TableHead>
                <TableHead>Año</TableHead>
                <TableHead>Km</TableHead>
                <TableHead>Propietario</TableHead>
                <TableHead class="text-right">Acciones</TableHead>
              </TableRow>
            </TableHeader>
            <TableBody>
              <TableRow v-for="vehicle in vehicles" :key="vehicle.id">
                <TableCell class="font-medium">{{ vehicle.plate }}</TableCell>
                <TableCell>{{ vehicle.brand }} {{ vehicle.model }}</TableCell>
                <TableCell>{{ vehicle.year }}</TableCell>
                <TableCell>{{ vehicle.current_km.toLocaleString() }} km</TableCell>
                <TableCell>{{ vehicle.owner.name }}</TableCell>
                <TableCell class="text-right">
                  <Button as-child variant="ghost" size="sm">
                    <Link :href="route('maintenance.index', { vehicle: vehicle.id })">
                      <Wrench class="mr-2 h-4 w-4" />
                      Mantenimiento
                    </Link>
                  </Button>
                </TableCell>
              </TableRow>
            </TableBody>
          </Table>
        </CardContent>
      </Card>
    </div>
  </AppSidebarLayout>
</template>
