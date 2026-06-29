<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, Link } from '@inertiajs/vue3'
import Card from '@/Components/Card.vue'
import Table from '@/Components/Table.vue'
import TableRow from '@/Components/TableRow.vue'

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
        <Card title="Vehículos asignados">
          <Table>
            <TableRow v-for="vehicle in vehicles" :key="vehicle.id">
              <td class="px-4 py-2">{{ vehicle.plate }}</td>
              <td class="px-4 py-2">{{ vehicle.brand }} {{ vehicle.model }}</td>
              <td class="px-4 py-2">{{ vehicle.year }}</td>
              <td class="px-4 py-2">{{ vehicle.current_km.toLocaleString() }} km</td>
              <td class="px-4 py-2">{{ vehicle.owner.name }}</td>
              <td class="px-4 py-2">
                <Link
                  :href="route('maintenance.index', { vehicle: vehicle.id })"
                  class="text-sm text-blue-600 hover:underline"
                >
                  Añadir mantenimiento
                </Link>
              </td>
            </TableRow>
          </Table>
        </Card>
      </div>
    </div>
  </AuthenticatedLayout>
</template>