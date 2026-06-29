<script setup lang="ts">
import { ref } from 'vue'
import { Link } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head } from '@inertiajs/vue3'
import { Card, CardContent, CardHeader, CardTitle } from '@/Components/ui/card'
import { Badge } from '@/Components/ui/badge'
import { Input } from '@/Components/ui/input'
import { Button } from '@/Components/ui/button'
import { BarChart3, FileText, Bell, Plus, Car } from 'lucide-vue-next'

interface Vehicle {
  id: number
  plate: string
  brand: string
  model: string
  year: number
  current_km: number
  is_active: boolean
}

interface Alert {
  id: number
  type: string
  title: string
  body: string
  read_at: string | null
}

defineProps<{
  vehicles?: Vehicle[]
  alerts?: Alert[]
  stats?: {
    total_vehicles: number
    total_documents: number
    pending_alerts: number
  }
}>()

const search = ref('')
</script>

<template>
  <Head title="Dashboard" />

  <AuthenticatedLayout>
    <template #header>
      <h2 class="text-xl font-semibold leading-tight text-foreground">
        Dashboard
      </h2>
    </template>

    <div class="flex justify-between mb-4">
      <Input
        v-model="search"
        placeholder="Buscar vehículos..."
        class="max-w-xs"
      />
      <Button as-child variant="link" size="sm">
        <Link :href="route('dashboard.stats')">
          Ver estadísticas →
        </Link>
      </Button>
    </div>

    <div class="py-12">
      <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
        <!-- Stats -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
          <Card>
            <CardContent class="pt-6">
              <div class="flex items-center">
                <div class="p-3 bg-blue-100 dark:bg-blue-900/50 rounded-lg">
                  <BarChart3 class="h-6 w-6 text-blue-600" />
                </div>
                <div class="ml-4">
                  <p class="text-sm text-muted-foreground">Vehículos</p>
                  <p class="text-2xl font-bold text-foreground">{{ stats?.total_vehicles ?? 0 }}</p>
                </div>
              </div>
              <Button as-child variant="link" size="sm" class="mt-4 p-0">
                <Link :href="route('vehicles.index')">Ver todos →</Link>
              </Button>
            </CardContent>
          </Card>

          <Card>
            <CardContent class="pt-6">
              <div class="flex items-center">
                <div class="p-3 bg-green-100 dark:bg-green-900/50 rounded-lg">
                  <FileText class="h-6 w-6 text-green-600" />
                </div>
                <div class="ml-4">
                  <p class="text-sm text-muted-foreground">Documentos</p>
                  <p class="text-2xl font-bold text-foreground">{{ stats?.total_documents ?? 0 }}</p>
                </div>
              </div>
            </CardContent>
          </Card>

          <Card>
            <CardContent class="pt-6">
              <div class="flex items-center">
                <div class="p-3 bg-orange-100 dark:bg-orange-900/50 rounded-lg">
                  <Bell class="h-6 w-6 text-orange-600" />
                </div>
                <div class="ml-4">
                  <p class="text-sm text-muted-foreground">Alertas pendientes</p>
                  <p class="text-2xl font-bold" :class="stats?.pending_alerts ? 'text-amber-600 dark:text-amber-400' : 'text-foreground'">
                    {{ stats?.pending_alerts ?? 0 }}
                  </p>
                </div>
              </div>
            </CardContent>
          </Card>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
          <!-- Vehículos -->
          <Card>
            <CardHeader class="flex flex-row items-center justify-between">
              <CardTitle>Mis Vehículos</CardTitle>
              <Button as-child variant="outline" size="sm">
                <Link :href="route('vehicles.create')">
                  <Plus class="mr-2 h-4 w-4" />
                  Añadir
                </Link>
              </Button>
            </CardHeader>
            <CardContent>
              <div v-if="!vehicles?.length" class="text-center py-8 text-muted-foreground">
                <Car class="mx-auto h-12 w-12 mb-2 opacity-50" />
                <p>No tienes vehículos registrados</p>
                <Button as-child class="mt-4">
                  <Link :href="route('vehicles.create')">Añadir primer vehículo</Link>
                </Button>
              </div>

              <div v-else class="space-y-3">
                <Link
                  v-for="vehicle in vehicles"
                  :key="vehicle.id"
                  :href="route('vehicles.show', vehicle.id)"
                  class="block p-3 border border-border rounded-lg hover:bg-accent transition"
                >
                  <div class="flex justify-between items-start">
                    <div>
                      <span class="font-medium">{{ vehicle.brand }} {{ vehicle.model }}</span>
                      <span class="text-sm text-muted-foreground ml-2">{{ vehicle.plate }}</span>
                    </div>
                    <Badge :variant="vehicle.is_active ? 'default' : 'secondary'">
                      {{ vehicle.is_active ? 'Activo' : 'Inactivo' }}
                    </Badge>
                  </div>
                  <div class="text-sm text-muted-foreground mt-1">
                    {{ vehicle.current_km.toLocaleString() }} km
                  </div>
                </Link>
              </div>
            </CardContent>
          </Card>

          <!-- Alertas -->
          <Card>
            <CardHeader>
              <CardTitle>Alertas Recientes</CardTitle>
            </CardHeader>
            <CardContent>
              <div v-if="!alerts?.length" class="text-center py-8 text-muted-foreground">
                <p>✅ No hay alertas pendientes</p>
              </div>

              <div v-else class="space-y-3">
                <div
                  v-for="alert in alerts"
                  :key="alert.id"
                  class="p-3 border border-orange-200 dark:border-orange-800 rounded-lg bg-orange-50 dark:bg-orange-900/20"
                >
                  <div class="flex justify-between items-start">
                    <span class="font-medium text-sm">{{ alert.title }}</span>
                    <Badge variant="outline" class="border-orange-300 text-orange-700 dark:border-orange-700 dark:text-orange-400">
                      {{ alert.type }}
                    </Badge>
                  </div>
                  <p class="text-sm text-muted-foreground mt-1">{{ alert.body }}</p>
                </div>
              </div>
            </CardContent>
          </Card>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
