<script setup lang="ts">
import { Link } from '@inertiajs/vue3'
import WebLayout from '@/layouts/WebLayout.vue'
import { Head } from '@inertiajs/vue3'
import PageCard from '@/Components/PageCard.vue'
import { CardContent, CardHeader, CardTitle } from '@/Components/ui/card'
import { Badge } from '@/Components/ui/badge'
import { Wrench, MapPin, Phone, ShieldCheck, Clock } from 'lucide-vue-next'

interface Workshop {
  id: number
  name: string
  address: string | null
  city: string | null
  phone: string | null
  is_verified: boolean
}

defineProps<{
  workshops: Workshop[]
}>()
</script>

<template>
  <Head title="Mis Talleres" />

  <WebLayout>
    <template #header>
      Mis Talleres
    </template>

    <div class="max-w-5xl mx-auto space-y-6 p-6">
      <div class="flex items-center justify-between">
        <div>
          <h1 class="text-3xl font-bold tracking-tight text-foreground">
            Mis Talleres
          </h1>
          <p class="text-sm text-muted-foreground mt-1">
            Talleres colaboradores y puntos de servicio
          </p>
        </div>
      </div>

      <div v-if="!workshops?.length" class="text-center py-16">
        <div class="mx-auto flex h-20 w-20 items-center justify-center rounded-3xl bg-gradient-to-br from-primary/10 to-accent/10 mb-4">
          <Wrench class="h-10 w-10 text-primary" />
        </div>
        <h3 class="font-semibold text-foreground mb-1 text-lg">
          No tienes talleres registrados
        </h3>
        <p class="text-sm text-muted-foreground">
          Añade talleres colaboradores para gestionar mantenimientos
        </p>
      </div>

      <div v-else class="grid gap-4 md:grid-cols-2">
        <PageCard
          v-for="(workshop, index) in workshops"
          :key="workshop.id"
          class="group transition-all duration-300 animate-in-up"
          :style="{ animationDelay: `${index * 50}ms` }"
        >
          <CardContent class="p-6">
            <div class="flex justify-between items-start mb-4">
              <div class="flex items-center gap-3">
                <div class="h-12 w-12 rounded-xl bg-gradient-to-br from-primary/10 to-accent/10 flex items-center justify-center">
                  <Wrench class="h-6 w-6 text-primary" />
                </div>
                <div>
                  <h4 class="font-semibold text-lg text-foreground">
                    {{ workshop.name }}
                  </h4>
                  <Badge
                    :variant="workshop.is_verified ? 'default' : 'secondary'"
                    class="mt-1"
                  >
                    <component
                      :is="workshop.is_verified ? ShieldCheck : Clock"
                      class="h-3 w-3 mr-1"
                    />
                    {{ workshop.is_verified ? 'Verificado' : 'Pendiente' }}
                  </Badge>
                </div>
              </div>
            </div>

            <div class="space-y-2">
              <div v-if="workshop.address" class="flex items-start gap-2 text-sm text-muted-foreground">
                <MapPin class="h-4 w-4 mt-0.5 shrink-0" />
                <span>{{ workshop.address }}</span>
              </div>
              <div v-if="workshop.city" class="flex items-center gap-2 text-sm text-muted-foreground">
                <span class="h-4 w-4" />
                <span>{{ workshop.city }}</span>
              </div>
              <div v-if="workshop.phone" class="flex items-center gap-2 text-sm text-muted-foreground">
                <Phone class="h-4 w-4 shrink-0" />
                <span>{{ workshop.phone }}</span>
              </div>
            </div>
          </CardContent>
        </PageCard>
      </div>
    </div>
  </WebLayout>
</template>
