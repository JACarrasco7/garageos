<script setup lang="ts">
import { Link } from '@inertiajs/vue3'
import { Head } from '@inertiajs/vue3'
import AppSidebarLayout from '@/layouts/app/AppSidebarLayout.vue'
import { Button } from '@/Components/ui/button'
import { Card, CardContent, CardHeader, CardTitle } from '@/Components/ui/card'
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/Components/ui/table'
import { Badge } from '@/Components/ui/badge'

interface ImportRequest {
  id: number
  brand: string
  model: string
  year?: number
  budget_min?: number
  budget_max?: number
  user: {
    workshop?: { city?: string }
  }
}

defineProps<{
  requests: {
    data: ImportRequest[]
  }
}>()
</script>

<template>
  <Head title="Solicitudes Abiertas" />

  <AppSidebarLayout>
    <template #header>
      Solicitudes Abiertas para Ofertar
    </template>

    <div class="container mx-auto py-6">
      <h1 class="text-2xl font-bold mb-6">Solicitudes Abiertas para Ofertar</h1>

      <Card>
        <CardHeader>
          <CardTitle>Solicitudes Disponibles</CardTitle>
        </CardHeader>
        <CardContent>
          <Table>
            <TableHeader>
              <TableRow>
                <TableHead>Vehículo</TableHead>
                <TableHead>Presupuesto</TableHead>
                <TableHead>Ubicación</TableHead>
                <TableHead class="text-right">Acciones</TableHead>
              </TableRow>
            </TableHeader>
            <TableBody>
              <TableRow v-for="request in requests.data" :key="request.id">
                <TableCell>
                  <div class="font-medium">{{ request.brand }} {{ request.model }}</div>
                  <div class="text-sm text-muted-foreground" v-if="request.year">{{ request.year }}</div>
                </TableCell>
                <TableCell>
                  <span v-if="request.budget_min && request.budget_max">
                    {{ request.budget_min }}€ - {{ request.budget_max }}€
                  </span>
                  <span v-else class="text-muted-foreground">Sin rango</span>
                </TableCell>
                <TableCell>
                  <span class="text-sm">{{ request.user.workshop?.city || 'N/A' }}</span>
                </TableCell>
                <TableCell class="text-right">
                  <Button variant="ghost" size="sm" as-child>
                    <Link :href="route('vehicle-import.requests.show', request.id)">Ver Detalles</Link>
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
