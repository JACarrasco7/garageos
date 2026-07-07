<script setup lang="ts">
import { Link } from '@inertiajs/vue3'
import AppSidebarLayout from '@/layouts/app/AppSidebarLayout.vue'
import { Head } from '@inertiajs/vue3'
import { Card, CardContent, CardHeader, CardTitle } from '@/Components/ui/card'
import { Badge } from '@/Components/ui/badge'
import { Button } from '@/Components/ui/button'
import { ArrowLeft } from 'lucide-vue-next'

interface Vehicle {
  id: number
  plate: string
  brand: string
  model: string
}

interface MaintenanceEntry {
  id: number
  type: string
  title: string
  service_date: string
  km_at_service: number
  cost: number | null
  is_verified: boolean
}

defineProps<{
  vehicle: Vehicle
  entry: MaintenanceEntry
}>()
</script>

<template>
  <Head :title="entry.title" />

  <AppSidebarLayout>
    <template #header>
      {{ entry.title }}
    </template>

    <div class="max-w-2xl mx-auto space-y-6">
      <Card class="glass-surface border-0">
        <CardHeader>
          <div class="flex justify-between items-start">
            <div>
              <CardTitle>{{ entry.title }}</CardTitle>
              <p class="text-sm text-muted-foreground capitalize">{{ entry.type }}</p>
            </div>
            <Badge :variant="entry.is_verified ? 'default' : 'secondary'">
              {{ entry.is_verified ? 'Verificado' : 'Pendiente' }}
            </Badge>
          </div>
        </CardHeader>
        <CardContent class="space-y-4">
          <div class="border-t border-border pt-4">
            <p class="text-sm text-muted-foreground">Fecha: {{ entry.service_date }}</p>
            <p class="text-sm text-muted-foreground">Km: {{ entry.km_at_service.toLocaleString() }} km</p>
            <p class="text-sm text-muted-foreground">Coste: {{ entry.cost ? entry.cost + ' €' : 'N/A' }}</p>
          </div>

          <div class="flex justify-end space-x-3 pt-4">
            <Button as-child variant="ghost">
              <Link :href="route('maintenance.index', { vehicle: vehicle.id })">
                <ArrowLeft class="mr-2 h-4 w-4" />
                Volver
              </Link>
            </Button>
          </div>
        </CardContent>
      </Card>
    </div>
  </AppSidebarLayout>
</template>
