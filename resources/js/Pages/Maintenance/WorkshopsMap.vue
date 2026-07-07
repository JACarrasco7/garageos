<script setup lang="ts">
import { Link } from '@inertiajs/vue3'
import WebLayout from '@/layouts/WebLayout.vue'
import { Head } from '@inertiajs/vue3'
import { Card, CardContent, CardHeader, CardTitle } from '@/Components/ui/card'
import WorkshopMap from '@/Components/WorkshopMap.vue'

interface Workshop {
  id: number
  name: string
  address: string | null
  city: string | null
  lat: number
  lng: number
  rating: number
  distance_km?: number
}

defineProps<{
  workshops: Workshop[]
}>()
</script>

<template>
  <Head title="Mapa de Talleres" />

  <WebLayout>
    <template #header>
      Mapa de Talleres
    </template>

    <div class="max-w-6xl mx-auto space-y-6 p-6">
      <Card class="border-none shadow-none bg-transparent">
        <CardContent class="p-0">
          <WorkshopMap :workshops="workshops" />
        </CardContent>
      </Card>
    </div>
  </WebLayout>
</template>
