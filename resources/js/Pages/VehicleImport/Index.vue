<script setup lang="ts">
import { Link } from '@inertiajs/vue3'
import WebLayout from '@/layouts/WebLayout.vue'
import { Head } from '@inertiajs/vue3'
import PageCard from '@/Components/PageCard.vue'
import { CardContent, CardHeader, CardTitle } from '@/Components/ui/card'
import { Button } from '@/Components/ui/button'
import { Badge } from '@/Components/ui/badge'
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/Components/ui/table'
import { Plus, Car } from 'lucide-vue-next'

interface VehicleImport {
  id: number
  plate_original: string
  plate_new: string | null
  brand: string
  model: string
  status: string
}

defineProps<{ imports: { data: VehicleImport[] } }>()

const statusConfig: Record<string, { label: string; variant: 'default' | 'secondary' | 'destructive' | 'outline' }> = {
  pending: { label: 'Pendiente', variant: 'secondary' },
  processing: { label: 'Procesando', variant: 'default' },
  approved: { label: 'Aprobado', variant: 'default' },
  rejected: { label: 'Rechazado', variant: 'destructive' },
}
</script>

<template>
  <Head title="Importaciones" />

  <WebLayout>
    <template #header>
      Importaciones
    </template>

    <PageCard>
      <template #title>
        <CardHeader class="flex flex-row items-center justify-between pb-3">
          <CardTitle class="text-lg font-semibold">Importaciones de Vehículos</CardTitle>
          <Button as-child variant="outline" size="sm" class="rounded-lg">
            <Link :href="route('imports.create')">
              <Plus class="mr-2 h-4 w-4" />
              Nueva Importación
            </Link>
          </Button>
        </CardHeader>
      </template>

      <CardContent>
        <div v-if="!imports?.data?.length" class="text-center py-12">
          <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-muted mb-4">
            <Car class="h-8 w-8 text-muted-foreground" />
          </div>
          <h3 class="font-medium text-foreground mb-1">No hay importaciones</h3>
          <p class="text-sm text-muted-foreground">Crea tu primera importación</p>
        </div>

        <Table v-else>
          <TableHeader>
            <TableRow>
              <TableHead>Matrícula Original</TableHead>
              <TableHead>Matrícula Nueva</TableHead>
              <TableHead>Vehículo</TableHead>
              <TableHead>Estado</TableHead>
              <TableHead class="text-right">Acciones</TableHead>
            </TableRow>
          </TableHeader>
          <TableBody>
            <TableRow v-for="vehicleImport in imports.data" :key="vehicleImport.id">
              <TableCell class="font-medium">{{ vehicleImport.plate_original }}</TableCell>
              <TableCell>{{ vehicleImport.plate_new || '-' }}</TableCell>
              <TableCell>{{ vehicleImport.brand }} {{ vehicleImport.model }}</TableCell>
              <TableCell>
                <Badge :variant="statusConfig[vehicleImport.status]?.variant ?? 'secondary'" class="capitalize">
                  {{ statusConfig[vehicleImport.status]?.label ?? vehicleImport.status }}
                </Badge>
              </TableCell>
              <TableCell class="text-right">
                <Button as-child variant="ghost" size="sm">
                  <Link :href="route('imports.show', vehicleImport.id)">Ver</Link>
                </Button>
              </TableCell>
            </TableRow>
          </TableBody>
        </Table>
      </CardContent>
    </PageCard>
  </WebLayout>
</template>
