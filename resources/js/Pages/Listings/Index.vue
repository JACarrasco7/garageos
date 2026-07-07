<script setup lang="ts">
import { Link } from '@inertiajs/vue3'
import AppSidebarLayout from '@/layouts/app/AppSidebarLayout.vue'
import { Card, CardContent, CardHeader, CardTitle } from '@/Components/ui/card'
import { Button } from '@/Components/ui/button'
import { Plus, Search } from 'lucide-vue-next'

interface Listing {
  id: number
  title: string
  brand: string
  model: string
  year: number
  price_eur: number
  mileage_km: number
}

defineProps<{
  listings: Listing[]
}>()
</script>

<template>
  <Head title="Mercado" />

  <AppSidebarLayout>
    <template #header>
      Mercado de Vehículos
    </template>

    <div class="space-y-6">
      <div class="flex items-center justify-between">
        <CardTitle class="text-lg font-semibold">Vehículos en venta</CardTitle>
        <Button as-child>
          <Link :href="route('listings.create')">
            <Plus class="mr-2 h-4 w-4" />
            Nuevo anuncio
          </Link>
        </Button>
      </div>

      <div v-if="listings.length === 0" class="text-center py-12">
        <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-muted mb-4">
          <Search class="h-8 w-8 text-muted-foreground" />
        </div>
        <h3 class="font-medium text-foreground mb-1">No hay anuncios</h3>
        <p class="text-sm text-muted-foreground">Crea tu primer anuncio</p>
      </div>

      <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        <Card v-for="listing in listings" :key="listing.id">
          <CardHeader>
            <CardTitle class="text-base">{{ listing.brand }} {{ listing.model }}</CardTitle>
          </CardHeader>
          <CardContent>
            <p class="text-sm text-muted-foreground">
              {{ listing.year }} • {{ listing.mileage_km.toLocaleString() }} km
            </p>
            <p class="text-lg font-bold text-foreground mt-2">
              {{ listing.price_eur.toLocaleString() }} €
            </p>
          </CardContent>
        </Card>
      </div>
    </div>
  </AppSidebarLayout>
</template>
