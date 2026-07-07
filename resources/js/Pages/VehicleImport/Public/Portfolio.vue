<script setup lang="ts">
import { Head } from '@inertiajs/vue3'
import AppSidebarLayout from '@/layouts/app/AppSidebarLayout.vue'
import { Star } from 'lucide-vue-next'
import { Card, CardContent, CardHeader, CardTitle } from '@/Components/ui/card'
import { Badge } from '@/Components/ui/badge'

interface Provider {
  id: number
  name: string
  workshop?: {
    city?: string
    country?: string
  }
}

interface Offer {
  id: number
  price: number
  rating_avg: number
  rating_count: number
  delivery_time_days?: number
  warranty_months?: number
  request: {
    brand: string
    model: string
  }
}

defineProps<{
  provider: Provider
  offers: {
    data: Offer[]
  }
}>()
</script>

<template>
  <Head title="Portafolio de Proveedor" />

  <AppSidebarLayout>
    <template #header>
      Portafolio de {{ provider.name }}
    </template>

    <div class="container mx-auto py-6">
      <div class="flex items-center gap-4 mb-6">
        <div>
          <h1 class="text-2xl font-bold">{{ provider.name }}</h1>
          <p class="text-muted-foreground">{{ provider.workshop?.city }}, {{ provider.workshop?.country }}</p>
        </div>
      </div>

      <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
        <Card v-for="offer in offers.data" :key="offer.id">
          <CardHeader>
            <CardTitle class="text-lg">
              {{ offer.request.brand }} {{ offer.request.model }}
            </CardTitle>
          </CardHeader>
          <CardContent>
            <div class="space-y-2">
              <div class="text-2xl font-bold text-foreground">{{ offer.price }}€</div>

              <div class="flex items-center gap-1">
                <Star v-for="i in 5" :key="i" class="h-4 w-4" :class="i <= offer.rating_avg ? 'fill-yellow-400 text-yellow-400' : 'text-muted'" />
                <span class="text-sm text-muted-foreground ml-2">({{ offer.rating_count }} valoraciones)</span>
              </div>

              <div class="text-sm text-muted-foreground">
                <span v-if="offer.delivery_time_days">{{ offer.delivery_time_days }} días de entrega</span>
                <span v-if="offer.warranty_months"> • {{ offer.warranty_months }} meses de garantía</span>
              </div>

              <Badge variant="outline">Completado</Badge>
            </div>
          </CardContent>
        </Card>
      </div>
    </div>
  </AppSidebarLayout>
</template>
