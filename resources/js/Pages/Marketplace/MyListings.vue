<script setup lang="ts">
import { ref } from 'vue'
import { router, Link } from '@inertiajs/vue3'
import WebLayout from '@/layouts/WebLayout.vue'
import PageCard from '@/Components/PageCard.vue'
import { Button } from '@/Components/ui/button'
import { CardContent, CardHeader, CardTitle } from '@/Components/ui/card'
import { Badge } from '@/Components/ui/badge'
import { Input } from '@/Components/ui/input'
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/Components/ui/select'
import { Plus, Edit, Trash2, Eye, BarChart3, Calendar } from 'lucide-vue-next'

interface Listing {
  id: number
  title: string
  price: number
  currency: string
  status: string
  location_city: string
  location_region: string
  views: number
  created_at: string
  expires_at: string
  photos: string[]
  vehicle: {
    id: number
    brand: string
    model: string
    year: number
  }
}

interface Props {
  listings: {
    data: Listing[]
    current_page: number
    last_page: number
    per_page: number
    total: number
  }
}

const props = defineProps<Props>()
const filterStatus = ref('all')

const filteredListings = () => {
  if (filterStatus.value === 'all') {
    return props.listings.data
  }
  return props.listings.data.filter(l => l.status === filterStatus.value)
}

const getStatusColor = (status: string) => {
  switch (status) {
    case 'active': return 'bg-green-500'
    case 'sold': return 'bg-blue-500'
    case 'reserved': return 'bg-yellow-500'
    case 'draft': return 'bg-gray-500'
    case 'expired': return 'bg-red-500'
    default: return 'bg-gray-500'
  }
}

const getStatusText = (status: string) => {
  switch (status) {
    case 'active': return 'Activo'
    case 'sold': return 'Vendido'
    case 'reserved': return 'Reservado'
    case 'draft': return 'Borrador'
    case 'expired': return 'Expirado'
    default: return status
  }
}

const formatDate = (date: string) => {
  return new Date(date).toLocaleDateString('es-ES', {
    year: 'numeric',
    month: 'short',
    day: 'numeric'
  })
}

const deleteListing = (id: number) => {
  if (confirm('¿Estás seguro de que deseas eliminar este anuncio?')) {
    router.delete(`/marketplace/listings/${id}`)
  }
}

const goBack = () => window.history.back()
</script>

<template>
  <WebLayout>
    <template #header>
      Mis Anuncios
    </template>

    <div class="space-y-6">
      <!-- Header -->
      <div class="flex items-center justify-between">
        <div>
          <h1 class="text-3xl font-bold flex items-center gap-2">
            <BarChart3 class="h-8 w-8" />
            Mis Anuncios
          </h1>
          <p class="text-muted-foreground mt-1">
            Gestiona tus anuncios publicados en el marketplace
          </p>
        </div>
        <Button @click="router.visit('/marketplace/create')">
          <Plus class="h-4 w-4 mr-2" />
          Nuevo Anuncio
        </Button>
      </div>

      <!-- Stats -->
      <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <PageCard>
          <template #title>
            <CardHeader class="pb-3">
              <CardTitle class="text-sm font-medium">Total</CardTitle>
            </CardHeader>
          </template>
          <CardContent>
            <div class="text-2xl font-bold">{{ listings.total }}</div>
            <p class="text-xs text-muted-foreground">Anuncios publicados</p>
          </CardContent>
        </PageCard>

        <PageCard>
          <template #title>
            <CardHeader class="pb-3">
              <CardTitle class="text-sm font-medium">Activos</CardTitle>
            </CardHeader>
          </template>
          <CardContent>
            <div class="text-2xl font-bold text-green-600">
              {{ listings.data.filter(l => l.status === 'active').length }}
            </div>
            <p class="text-xs text-muted-foreground">Anuncios activos</p>
          </CardContent>
        </PageCard>

        <PageCard>
          <template #title>
            <CardHeader class="pb-3">
              <CardTitle class="text-sm font-medium">Vendidos</CardTitle>
            </CardHeader>
          </template>
          <CardContent>
            <div class="text-2xl font-bold text-blue-600">
              {{ listings.data.filter(l => l.status === 'sold').length }}
            </div>
            <p class="text-xs text-muted-foreground">Vehículos vendidos</p>
          </CardContent>
        </PageCard>

        <PageCard>
          <template #title>
            <CardHeader class="pb-3">
              <CardTitle class="text-sm font-medium">Vistas Totales</CardTitle>
            </CardHeader>
          </template>
          <CardContent>
            <div class="text-2xl font-bold">
              {{ listings.data.reduce((sum, l) => sum + l.views, 0) }}
            </div>
            <p class="text-xs text-muted-foreground">Visitas acumuladas</p>
          </CardContent>
        </PageCard>
      </div>

      <!-- Filters -->
      <PageCard>
        <CardContent class="pt-6">
          <div class="flex gap-4 items-center">
            <label class="text-sm font-medium">Estado:</label>
            <Select v-model="filterStatus">
              <SelectTrigger class="w-[180px]">
                <SelectValue placeholder="Filtrar por estado" />
              </SelectTrigger>
              <SelectContent>
                <SelectItem value="all">Todos</SelectItem>
                <SelectItem value="active">Activos</SelectItem>
                <SelectItem value="sold">Vendidos</SelectItem>
                <SelectItem value="reserved">Reservados</SelectItem>
                <SelectItem value="draft">Borradores</SelectItem>
                <SelectItem value="expired">Expirados</SelectItem>
              </SelectContent>
            </Select>
          </div>
        </CardContent>
      </PageCard>

      <!-- Listings Grid -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <PageCard
          v-for="listing in filteredListings()"
          :key="listing.id"
          class="overflow-hidden transition-shadow"
        >
          <CardContent class="p-0">
            <!-- Photo -->
            <div class="aspect-video bg-muted relative">
              <img
                v-if="listing.photos.length > 0"
                :src="listing.photos[0]"
                :alt="listing.title"
                class="w-full h-full object-cover"
              />
              <div v-else class="w-full h-full flex items-center justify-center">
                <span class="text-muted-foreground">Sin foto</span>
              </div>
              <Badge
                :class="getStatusColor(listing.status)"
                class="absolute top-2 right-2"
          >
            {{ getStatusText(listing.status) }}
          </Badge>
        </div>

        <!-- Content -->
        <CardHeader>
          <CardTitle class="line-clamp-1">{{ listing.title }}</CardTitle>
          <CardDescription>
            {{ listing.vehicle.brand }} {{ listing.vehicle.model }} {{ listing.vehicle.year }}
          </CardDescription>
        </CardHeader>

        <CardContent class="space-y-4">
          <!-- Price -->
          <div>
            <p class="text-2xl font-bold">
              {{ listing.price.toLocaleString('es-ES') }} {{ listing.currency }}
            </p>
          </div>

          <!-- Stats -->
          <div class="flex items-center gap-4 text-sm text-muted-foreground">
            <div class="flex items-center gap-1">
              <Eye class="h-4 w-4" />
              {{ listing.views }} vistas
            </div>
            <div class="flex items-center gap-1">
              <Calendar class="h-4 w-4" />
              {{ formatDate(listing.created_at) }}
            </div>
          </div>

          <!-- Actions -->
          <div class="flex gap-2">
            <Button
              variant="outline"
              size="sm"
              class="flex-1"
              @click="router.visit(`/marketplace/listings/${listing.id}`)"
            >
              <Eye class="h-4 w-4 mr-2" />
              Ver
            </Button>
            <Button
              variant="outline"
              size="sm"
              @click="router.visit(`/marketplace/listings/${listing.id}/edit`)"
            >
              <Edit class="h-4 w-4" />
            </Button>
            <Button
              v-if="listing.status !== 'sold'"
              variant="destructive"
              size="sm"
              @click="deleteListing(listing.id)"
            >
              <Trash2 class="h-4 w-4" />
            </Button>
          </div>
        </CardContent>
      </PageCard>
    </div>

    <!-- Empty State -->
    <div v-if="filteredListings().length === 0" class="text-center py-12">
      <BarChart3 class="h-12 w-12 text-muted-foreground mx-auto mb-4" />
      <p class="text-muted-foreground">
        No hay anuncios con el estado seleccionado
      </p>
      <Button
        v-if="filterStatus === 'all'"
        class="mt-4"
        @click="router.visit('/marketplace/create')"
      >
        <Plus class="h-4 w-4 mr-2" />
        Crear Primer Anuncio
      </Button>
    </div>
  </div>
</WebLayout>
</template>
