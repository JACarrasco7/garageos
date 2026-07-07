<script setup lang="ts">
import { Link } from '@inertiajs/vue3'
import AppSidebarLayout from '@/layouts/app/AppSidebarLayout.vue'
import { Head } from '@inertiajs/vue3'
import { Button } from '@/Components/ui/button'
import { Card, CardContent, CardHeader, CardTitle } from '@/Components/ui/card'
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/Components/ui/table'
import { Badge } from '@/Components/ui/badge'

defineProps<{
  requests: Object
}>()
</script>

<template>
  <Head title="Mis Solicitudes de Importación" />

  <AppSidebarLayout>
    <template #header>
      Mis Solicitudes de Importación
    </template>

    <div class="container mx-auto py-6">
      <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Mis Solicitudes de Importación</h1>
        <Button as-child>
          <Link :href="route('vehicle-import.requests.create')">Nueva Solicitud</Link>
        </Button>
      </div>

      <Card>
        <CardHeader>
          <CardTitle>Solicitudes</CardTitle>
        </CardHeader>
        <CardContent>
          <Table>
            <TableHeader>
              <TableRow>
                <TableHead>Vehículo</TableHead>
                <TableHead>Presupuesto</TableHead>
                <TableHead>Estado</TableHead>
                <TableHead>Ofertas</TableHead>
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
                  <Badge :variant="request.status === 'open' ? 'default' : 'secondary'">
                    {{ request.status === 'open' ? 'Abierta' : 'Cerrada' }}
                  </Badge>
                </TableCell>
                <TableCell>{{ request.offers_count || 0 }}</TableCell>
                <TableCell class="text-right">
                  <Button variant="ghost" size="sm" as-child>
                    <Link :href="route('vehicle-import.requests.show', request.id)">Ver</Link>
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
