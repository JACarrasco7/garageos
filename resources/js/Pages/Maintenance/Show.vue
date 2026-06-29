<script setup lang="ts">
import { Link } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head } from '@inertiajs/vue3'
import Card from '@/Components/Card.vue'
import Badge from '@/Components/Badge.vue'

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

  <AuthenticatedLayout>
    <template #header>
      <h2 class="text-xl font-semibold leading-tight">{{ entry.title }}</h2>
    </template>

    <div class="py-12">
      <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
        <Card>
          <div class="space-y-4">
            <div class="flex justify-between items-start">
              <div>
                <h3 class="text-lg font-medium">{{ entry.title }}</h3>
                <p class="text-sm text-gray-500 capitalize">{{ entry.type }}</p>
              </div>
              <Badge :variant="entry.is_verified ? 'success' : 'gray'">
                {{ entry.is_verified ? 'Verificado' : 'Pendiente' }}
              </Badge>
            </div>

            <div class="border-t pt-4">
              <p class="text-sm text-gray-500">Fecha: {{ entry.service_date }}</p>
              <p class="text-sm text-gray-500">Km: {{ entry.km_at_service.toLocaleString() }} km</p>
              <p class="text-sm text-gray-500">Coste: {{ entry.cost ? entry.cost + ' €' : 'N/A' }}</p>
            </div>

            <div class="flex justify-end space-x-3 pt-4">
              <Link
                :href="route('maintenance.index', { vehicle: vehicle.id })"
                class="px-4 py-2 text-gray-600 hover:underline"
              >
                Volver
              </Link>
            </div>
          </div>
        </Card>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
