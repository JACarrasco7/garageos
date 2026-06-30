<script setup lang="ts">
import { Link } from '@inertiajs/vue3'
import AppSidebarLayout from '@/layouts/app/AppSidebarLayout.vue'
import { Card, CardContent, CardHeader, CardTitle } from '@/Components/ui/card'
import { Button } from '@/Components/ui/button'
import { Badge } from '@/Components/ui/badge'
import { Plus, FileText, Car } from 'lucide-vue-next'

defineProps<{ imports: any }>()

function statusVariant(status: string): string {
  const variants: Record<string, string> = {
    pending: 'secondary',
    processing: 'default',
    approved: 'default',
    rejected: 'destructive',
  }
  return variants[status] || 'secondary'
}
</script>

<template>
  <AppSidebarLayout>
    <template #header>
      Importaciones
    </template>

    <Card class="border-0 shadow-lg">
      <CardHeader class="flex flex-row items-center justify-between pb-3">
        <CardTitle class="text-lg font-semibold">Importaciones de Vehículos</CardTitle>
        <Button as-child variant="outline" size="sm" class="rounded-lg">
          <Link :href="route('imports.create')">
            <Plus class="mr-2 h-4 w-4" />
            Nueva Importación
          </Link>
        </Button>
      </CardHeader>
      <CardContent>
        <div v-if="!imports?.data?.length" class="text-center py-12">
          <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-muted mb-4">
            <Car class="h-8 w-8 text-muted-foreground" />
          </div>
          <h3 class="font-medium text-foreground mb-1">No hay importaciones</h3>
          <p class="text-sm text-muted-foreground">Crea tu primera importación</p>
        </div>

        <div v-else class="overflow-x-auto">
          <table class="min-w-full divide-y divide-border">
            <thead>
              <tr>
                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-muted-foreground">Matrícula Original</th>
                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-muted-foreground">Matrícula Nueva</th>
                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-muted-foreground">Vehículo</th>
                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-muted-foreground">Estado</th>
                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-muted-foreground">Acciones</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-border">
              <tr v-for="vehicleImport in imports.data" :key="vehicleImport.id" class="hover:bg-accent">
                <td class="px-4 py-3 text-sm">{{ vehicleImport.plate_original }}</td>
                <td class="px-4 py-3 text-sm">{{ vehicleImport.plate_new || '-' }}</td>
                <td class="px-4 py-3 text-sm">{{ vehicleImport.brand }} {{ vehicleImport.model }}</td>
                <td class="px-4 py-3">
                  <Badge :variant="statusVariant(vehicleImport.status)" class="capitalize">
                    {{ vehicleImport.status }}
                  </Badge>
                </td>
                <td class="px-4 py-3">
                  <Link :href="route('imports.show', vehicleImport.id)" class="text-sm text-primary hover:underline">
                    Ver
                  </Link>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </CardContent>
    </Card>
  </AppSidebarLayout>
</template>