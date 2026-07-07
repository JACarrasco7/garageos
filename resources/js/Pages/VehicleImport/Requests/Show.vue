<script setup lang="ts">
import { Link } from '@inertiajs/vue3'
import { Head } from '@inertiajs/vue3'
import AppSidebarLayout from '@/layouts/app/AppSidebarLayout.vue'
import { Button } from '@/Components/ui/button'
import { Card, CardContent, CardHeader, CardTitle } from '@/Components/ui/card'
import { Badge } from '@/Components/ui/badge'
import { Star } from 'lucide-vue-next'

defineProps<{
  request: Object
}>()
</script>

<template>
  <Head title="Solicitud de Importación" />

  <AppSidebarLayout>
    <template #header>
      {{ request.brand }} {{ request.model }}
    </template>

    <div class="container mx-auto py-6">
      <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">{{ request.brand }} {{ request.model }}</h1>
        <Badge :variant="request.status === 'open' ? 'default' : 'secondary'">
          {{ request.status === 'open' ? 'Abierta' : 'Cerrada' }}
        </Badge>
      </div>

      <div class="grid gap-6">
        <Card>
          <CardHeader>
            <CardTitle>Detalles de la Solicitud</CardTitle>
          </CardHeader>
          <CardContent>
            <div class="grid grid-cols-2 gap-4 text-sm">
              <div><span class="font-medium">Año:</span> {{ request.year || 'N/A' }}</div>
              <div><span class="font-medium">Combustible:</span> {{ request.fuel_type || 'N/A' }}</div>
              <div><span class="font-medium">Kilometraje:</span> {{ request.mileage || 'N/A' }}</div>
              <div><span class="font-medium">Presupuesto:</span> {{ request.budget_min }}€ - {{ request.budget_max }}€</div>
            </div>
            <p v-if="request.description" class="mt-4">{{ request.description }}</p>
          </CardContent>
        </Card>

        <Card>
          <CardHeader>
            <CardTitle>Ofertas Recibidas ({{ request.offers.length }})</CardTitle>
          </CardHeader>
          <CardContent>
            <div v-if="request.offers.length === 0" class="text-center py-8 text-muted-foreground">
              Aún no hay ofertas para esta solicitud.
            </div>

            <div v-else class="space-y-4">
              <div v-for="offer in request.offers" :key="offer.id" class="border rounded-lg p-4">
                <div class="flex justify-between items-start mb-2">
                  <div>
                    <div class="font-medium">{{ offer.provider.name }}</div>
                    <div class="text-2xl font-bold text-foreground">{{ offer.price }}€</div>
                  </div>
                  <Badge :variant="offer.status === 'accepted' ? 'default' : 'outline'">
                    {{ offer.status === 'accepted' ? 'Aceptada' : 'Pendiente' }}
                  </Badge>
                </div>

                <div class="text-sm text-muted-foreground mb-2">
                  <span v-if="offer.delivery_time_days">{{ offer.delivery_time_days }} días de entrega</span>
                  <span v-if="offer.warranty_months"> • {{ offer.warranty_months }} meses de garantía</span>
                </div>

                <div class="flex items-center gap-1 mb-2">
                  <Star v-for="i in 5" :key="i" class="h-4 w-4" :class="i <= offer.rating_avg ? 'fill-yellow-400 text-yellow-400' : 'text-muted'" />
                  <span class="text-sm text-muted-foreground ml-2">({{ offer.rating_count }} valoraciones)</span>
                </div>

                <Button v-if="request.status === 'open' && offer.status === 'pending'"
                  size="sm"
                  :href="route('vehicle-import.offers.accept', offer.id)"
                  method="post"
                  as="button">
                  Aceptar Oferta
                </Button>
              </div>
            </div>
          </CardContent>
        </Card>
      </div>
    </div>
  </AppSidebarLayout>
</template>
