<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, Link } from '@inertiajs/vue3'
import { Card, CardContent, CardHeader, CardTitle } from '@/Components/ui/card'
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/Components/ui/table'
import { Button } from '@/Components/ui/button'
import { Wrench } from 'lucide-vue-next'

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

  <AuthenticatedLayout>
    <template #header>
      <h2 class="text-xl font-semibold leading-tight">Panel de Taller</h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <Card>
          <CardHeader>
            <CardTitle>Vehículos asignados</CardTitle>
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
    </div>
  </AuthenticatedLayout>
</template>
