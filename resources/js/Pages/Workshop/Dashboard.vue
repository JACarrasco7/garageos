<script setup lang="ts">
import AppSidebarLayout from '@/layouts/app/AppSidebarLayout.vue'
import { Head, Link } from '@inertiajs/vue3'
import { Card, CardContent, CardHeader, CardTitle } from '@/Components/ui/card'
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/Components/ui/table'
import { Button } from '@/Components/ui/button'
import { Wrench, Car, Clock, CheckCircle2 } from 'lucide-vue-next'

interface Vehicle {
  id: number
  plate: string
  brand: string
  model: string
  year: number
  current_km: number
  status?: 'active' | 'maintenance' | 'pending'
  owner: {
    name: string
  }
}

const props = defineProps<{
  vehicles: Vehicle[]
}>()

const stats = [
  {
    title: 'Total Vehículos',
    value: props.vehicles.length,
    icon: Car,
    color: 'from-primary/10 to-accent/10',
    iconColor: 'text-primary',
  },
  {
    title: 'Activos',
    value: props.vehicles.filter(v => v.status !== 'maintenance').length,
    icon: CheckCircle2,
    color: 'from-green-500/10 to-emerald-500/10',
    iconColor: 'text-green-600 dark:text-green-400',
  },
  {
    title: 'En mantenimiento',
    value: props.vehicles.filter(v => v.status === 'maintenance').length,
    icon: Wrench,
    color: 'from-accent/10 to-orange-500/10',
    iconColor: 'text-accent',
  },
  {
    title: 'Pendientes',
    value: props.vehicles.filter(v => v.status === 'pending').length,
    icon: Clock,
    color: 'from-destructive/10 to-red-500/10',
    iconColor: 'text-destructive',
  },
]
</script>

<template>
  <Head title="Panel Taller" />

  <AppSidebarLayout>
    <div class="flex flex-col gap-6 p-6">
      <div class="flex items-center justify-between">
        <div>
          <h1 class="text-3xl font-bold tracking-tight text-foreground">
            Panel de Taller
          </h1>
          <p class="text-sm text-muted-foreground mt-1">
            Vehículos asignados para mantenimiento
          </p>
        </div>
        <Button as-child class="bg-gradient-to-r from-primary to-primary/80 hover:shadow-lg hover:shadow-primary/25 hover:scale-105 transition-all duration-200">
          <Link :href="route('vehicles.create')">
            <Car class="mr-2 h-4 w-4" />
            Nuevo vehículo
          </Link>
        </Button>
      </div>

      <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
        <Card
          v-for="stat in stats"
          :key="stat.title"
          class="group hover:-translate-y-1 hover:shadow-lg hover:shadow-primary/5 transition-all duration-300 border-border/50 bg-card/50 backdrop-blur-sm overflow-hidden relative"
        >
          <div :class="['absolute inset-0 bg-gradient-to-br opacity-0 group-hover:opacity-100 transition-opacity duration-500', stat.color]" />
          <CardContent class="p-6 relative">
            <div class="flex items-center justify-between">
              <div :class="['h-12 w-12 rounded-xl bg-gradient-to-br flex items-center justify-center shadow-sm', stat.color]">
                <component :is="stat.icon" :class="['h-6 w-6', stat.iconColor]" />
              </div>
            </div>
            <div class="mt-4">
              <p class="text-sm font-medium text-muted-foreground">{{ stat.title }}</p>
              <p class="text-3xl font-bold text-foreground mt-1">{{ stat.value }}</p>
            </div>
          </CardContent>
        </Card>
      </div>

      <Card class="border-border/50 bg-card/50 backdrop-blur-sm">
        <CardHeader class="border-b border-border/50">
          <CardTitle class="flex items-center gap-2">
            <div class="h-8 w-8 rounded-lg bg-gradient-to-br from-primary/10 to-accent/10 flex items-center justify-center">
              <Car class="h-4 w-4 text-primary" />
            </div>
            Vehículos asignados
          </CardTitle>
        </CardHeader>
        <CardContent class="p-0">
          <Table>
            <TableHeader>
              <TableRow class="hover:bg-transparent">
                <TableHead class="font-semibold">Matrícula</TableHead>
                <TableHead class="font-semibold">Vehículo</TableHead>
                <TableHead class="font-semibold">Año</TableHead>
                <TableHead class="font-semibold">Km</TableHead>
                <TableHead class="font-semibold">Propietario</TableHead>
                <TableHead class="text-right font-semibold">Acciones</TableHead>
              </TableRow>
            </TableHeader>
            <TableBody>
              <TableRow v-for="vehicle in vehicles" :key="vehicle.id" class="group hover:bg-accent/5 transition-colors">
                <TableCell class="font-medium">
                  <code class="px-2 py-1 rounded bg-muted text-sm">{{ vehicle.plate }}</code>
                </TableCell>
                <TableCell class="font-medium text-foreground">
                  {{ vehicle.brand }} {{ vehicle.model }}
                </TableCell>
                <TableCell>{{ vehicle.year }}</TableCell>
                <TableCell>{{ vehicle.current_km.toLocaleString() }} km</TableCell>
                <TableCell>{{ vehicle.owner.name }}</TableCell>
                <TableCell class="text-right">
                  <Button as-child variant="ghost" size="sm" class="hover:bg-primary/10 hover:text-primary transition-colors">
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
