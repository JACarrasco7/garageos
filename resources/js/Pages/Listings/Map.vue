<script setup lang="ts">
import { ref } from 'vue'
import { Head, router } from '@inertiajs/vue3'
import AppSidebarLayout from '@/layouts/app/AppSidebarLayout.vue'
import { Card, CardContent } from '@/Components/ui/card'
import ListingMap from '@/Components/ListingMap.vue'
import { Input } from '@/Components/ui/input'
import { Button } from '@/Components/ui/button'
import { Search, MapPin } from 'lucide-vue-next'

interface Listing {
  id: number
  title: string
  brand: string
  model: string
  price_eur: number
  lat: number
  lng: number
  year: number
  mileage_km: number
}

const searchQuery = ref('')
const listings = ref<Listing[]>([])

const search = async () => {
  if (!searchQuery.value.trim()) return

  const response = await fetch(`/listings/search?q=${encodeURIComponent(searchQuery.value)}`)
  const data = await response.json()
  listings.value = data.data
}

const searchNearby = async (lat: number, lng: number) => {
  const response = await fetch(`/listings/nearby?lat=${lat}&lng=${lng}&radius=50`)
  const data = await response.json()
  listings.value = data.data
}
</script>

<template>
  <Head title="Mapa de Listings" />

  <AppSidebarLayout>
    <template #header>
      Mapa de Listings
    </template>

    <div class="max-w-6xl mx-auto space-y-6 p-6">
      <Card class="border-none shadow-none bg-transparent">
        <CardContent class="p-0">
          <div class="flex gap-2 mb-4">
            <div class="relative flex-1">
              <Search class="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-muted-foreground" />
              <Input
                v-model="searchQuery"
                type="text"
                placeholder="Buscar listings..."
                class="pl-10"
                @keyup.enter="search"
              />
            </div>
            <Button @click="search">
              Buscar
            </Button>
          </div>
          <ListingMap :listings="listings" />
        </CardContent>
      </Card>
    </div>
  </AppSidebarLayout>
</template>