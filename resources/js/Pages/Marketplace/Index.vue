<script setup lang="ts">
import { ref, computed } from 'vue'
import { router, Link } from '@inertiajs/vue3'
import WebLayout from '@/layouts/WebLayout.vue'
import { Input } from '@/Components/ui/input'
import { Button } from '@/Components/ui/button'
import { Badge } from '@/Components/ui/badge'
import PageCard from '@/Components/PageCard.vue'
import { CardContent, CardHeader, CardTitle } from '@/Components/ui/card'
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/Components/ui/select'
import { Search, MapPin, Calendar, Fuel, Gauge, DollarSign, Heart, ExternalLink } from 'lucide-vue-next'

interface Listing {
  id: number
  title: string
  description: string
  price: number
  currency: string
  is_negotiable: boolean
  location_city: string
  location_region: string
  status: string
  views: number
  created_at: string
  brand: string
  model: string
  year: number
  mileage_km: number
  fuel_type: string
  power_hp: number
  gearbox: string
  photos: string[]
  user: {
    name: string
    avatar_url: string | null
  }
}

interface Props {
  listings: Listing[]
  filters?: {
    brand?: string
    model?: string
    minPrice?: number
    maxPrice?: number
    yearFrom?: number
    yearTo?: number
    fuelType?: string
    location?: string
  }
}

const props = defineProps<Props>()

const searchQuery = ref('')
const selectedBrand = ref('')
const selectedFuelType = ref('')
const priceRange = ref({ min: '', max: '' })
const yearRange = ref({ from: '', to: '' })

const filteredListings = computed(() => {
  let listings = props.listings.filter(l => l.status === 'active')

  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase()
    listings = listings.filter(l =>
      l.title.toLowerCase().includes(query) ||
      l.brand.toLowerCase().includes(query) ||
      l.model.toLowerCase().includes(query) ||
      l.description.toLowerCase().includes(query)
    )
  }

  if (selectedBrand.value) {
    listings = listings.filter(l => l.brand === selectedBrand.value)
  }

  if (selectedFuelType.value) {
    listings = listings.filter(l => l.fuel_type === selectedFuelType.value)
  }

  if (priceRange.value.min) {
    listings = listings.filter(l => l.price >= Number(priceRange.value.min))
  }

  if (priceRange.value.max) {
    listings = listings.filter(l => l.price <= Number(priceRange.value.max))
  }

  if (yearRange.value.from) {
    listings = listings.filter(l => l.year >= Number(yearRange.value.from))
  }

  if (yearRange.value.to) {
    listings = listings.filter(l => l.year <= Number(yearRange.value.to))
  }

  return listings
})

const brands = computed(() => {
  return [...new Set(props.listings.map(l => l.brand))].sort()
})

const fuelTypes = ['gasolina', 'diesel', 'hibrido', 'electrico', 'glp', 'otro']

const toggleFavorite = (listingId: number) => {
  router.post(`/listings/${listingId}/favorite`, {}, {
    preserveScroll: true,
  })
}

const goToCreate = () => {
  router.visit('/listings/create')
}

const goToDetail = (listingId: number) => {
  router.visit(`/listings/${listingId}`)
}
</script>

<template>
  <WebLayout>
    <template #header>
      Marketplace
    </template>

    <div class="space-y-6">
      <!-- Header -->
      <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
          <h1 class="text-3xl font-bold">Marketplace</h1>
          <p class="text-muted-foreground mt-1">
            {{ filteredListings.length }} vehículos disponibles
          </p>
        </div>
        <Button @click="goToCreate">
          <ExternalLink class="h-4 w-4 mr-2" />
          Publicar anuncio
        </Button>
      </div>

      <!-- Search and Filters -->
      <PageCard>
        <CardContent class="pt-6">
          <div class="space-y-4">
            <!-- Search Bar -->
            <div class="relative">
              <Search class="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-muted-foreground" />
              <Input
                v-model="searchQuery"
                placeholder="Buscar por marca, modelo, ubicación..."
                class="pl-10"
              />
            </div>

            <!-- Filters -->
            <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
              <div>
                <label class="text-sm font-medium mb-1 block">Marca</label>
                <Select v-model="selectedBrand">
                  <SelectTrigger class="h-9">
                    <SelectValue placeholder="Todas" />
                  </SelectTrigger>
                  <SelectContent>
                    <SelectItem value="">Todas</SelectItem>
                    <SelectItem v-for="brand in brands" :key="brand" :value="brand">
                      {{ brand }}
                    </SelectItem>
                  </SelectContent>
                </Select>
              </div>

              <div>
                <label class="text-sm font-medium mb-1 block">Combustible</label>
                <Select v-model="selectedFuelType">
                  <SelectTrigger class="h-9">
                    <SelectValue placeholder="Todos" />
                  </SelectTrigger>
                  <SelectContent>
                    <SelectItem value="">Todos</SelectItem>
                    <SelectItem v-for="fuel in fuelTypes" :key="fuel" :value="fuel">
                      {{ fuel }}
                    </SelectItem>
                  </SelectContent>
                </Select>
              </div>

              <div>
                <label class="text-sm font-medium mb-1 block">Precio mínimo</label>
                <Input
                  v-model="priceRange.min"
                  type="number"
                  placeholder="€0"
                  class="h-9"
                />
              </div>

              <div>
                <label class="text-sm font-medium mb-1 block">Precio máximo</label>
                <Input
                  v-model="priceRange.max"
                  type="number"
                  placeholder="€50,000"
                  class="h-9"
                />
              </div>

              <div>
                <label class="text-sm font-medium mb-1 block">Año</label>
                <div class="flex gap-2">
                  <Input
                    v-model="yearRange.from"
                    type="number"
                    placeholder="Desde"
                    class="flex-1 h-9"
                  />
                  <Input
                    v-model="yearRange.to"
                    type="number"
                    placeholder="Hasta"
                    class="flex-1 h-9"
                  />
                </div>
              </div>
            </div>
          </div>
        </CardContent>
      </PageCard>

      <!-- Listings Grid -->
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        <PageCard
          v-for="listing in filteredListings"
          :key="listing.id"
          class="overflow-hidden cursor-pointer transition-shadow duration-200"
          @click="goToDetail(listing.id)"
        >
          <CardContent class="p-0">
            <div class="relative h-40 sm:h-48 bg-muted">
              <img
                v-if="listing.photos.length > 0"
                :src="listing.photos[0]"
                :alt="listing.title"
                class="w-full h-full object-cover"
              />
              <div v-else class="w-full h-full flex items-center justify-center">
                <span class="text-muted-foreground text-sm">Sin foto</span>
              </div>
              <Button
                variant="ghost"
                size="sm"
                class="absolute top-2 right-2 bg-white/80 hover:bg-white h-7 w-7 p-0"
                @click.stop="toggleFavorite(listing.id)"
              >
                <Heart class="h-3.5 w-3.5" />
              </Button>
              <Badge class="absolute top-2 left-2 text-xs">
                {{ listing.is_negotiable ? 'Negociable' : 'Precio fijo' }}
              </Badge>
            </div>
            <div class="p-4 space-y-3">
              <div class="flex justify-between items-start">
                <h3 class="font-bold text-lg">{{ listing.brand }} {{ listing.model }}</h3>
                <span class="text-xl font-bold">
                  {{ listing.price.toLocaleString('es-ES') }} {{ listing.currency }}
                </span>
              </div>
              <p class="text-sm text-muted-foreground">
                {{ listing.year }} • {{ listing.mileage_km.toLocaleString('es-ES') }} km
              </p>
              <div class="flex items-center gap-3 text-xs text-muted-foreground">
                <div class="flex items-center gap-1">
                  <Fuel class="h-3.5 w-3.5" />
                  {{ listing.fuel_type }}
                </div>
                <div class="flex items-center gap-1">
                  <Gauge class="h-3.5 w-3.5" />
                  {{ listing.power_hp }} CV
                </div>
                <div class="flex items-center gap-1">
                  <MapPin class="h-3.5 w-3.5" />
                  {{ listing.location_city }}
                </div>
              </div>
              <div class="pt-2 border-t flex items-center gap-2">
                <div class="w-6 h-6 rounded-full bg-muted" />
                <span class="text-xs">{{ listing.user.name }}</span>
              </div>
            </div>
          </CardContent>
        </PageCard>
      </div>

      <!-- No Results -->
      <div v-if="filteredListings.length === 0" class="text-center py-12">
        <p class="text-lg text-muted-foreground">No se encontraron anuncios</p>
        <p class="text-sm text-muted-foreground mt-1">Intenta ajustar los filtros de búsqueda</p>
      </div>
    </div>
  </WebLayout>
</template>
