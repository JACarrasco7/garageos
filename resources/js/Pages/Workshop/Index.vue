<script setup lang="ts">
import AppSidebarLayout from '@/layouts/app/AppSidebarLayout.vue'
import { Head, Link } from '@inertiajs/vue3'
import { Card, CardContent, CardHeader, CardTitle } from '@/Components/ui/card'
import { Input } from '@/Components/ui/input'
import { Button } from '@/Components/ui/button'
import { Star, MapPin, Phone } from 'lucide-vue-next'

interface Workshop {
  id: number
  name: string
  description: string
  rating: number
  city: string
  address: string
  phone: string
  logo: string
  reviews_count: number
}

defineProps<{
  workshops: {
    data: Workshop[]
    links: any
  }
}>()
</script>

<template>
  <Head title="Marketplace de Talleres" />

  <AppSidebarLayout>
    <template #header>
      Marketplace de Talleres
    </template>

    <div class="space-y-6">
      <div class="flex gap-4">
        <Input type="text" placeholder="Buscar talleres..." class="flex-1" />
        <Button>Buscar</Button>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <Card v-for="workshop in workshops.data" :key="workshop.id" class="border-0 shadow-lg">
          <CardHeader>
            <CardTitle class="text-lg">{{ workshop.name }}</CardTitle>
          </CardHeader>
          <CardContent class="space-y-3">
            <img v-if="workshop.logo" :src="workshop.logo" :alt="workshop.name" class="w-full h-32 object-cover rounded" />
            <div class="flex items-center gap-2">
              <Star class="h-4 w-4 fill-yellow-400 text-yellow-400" />
              <span>{{ workshop.rating || 0 }}</span>
            </div>
            <p class="text-sm text-muted-foreground">{{ workshop.description }}</p>
            <div class="space-y-1 text-sm">
              <div class="flex items-center gap-2">
                <MapPin class="h-4 w-4" />
                <span>{{ workshop.city }}</span>
              </div>
              <div class="flex items-center gap-2">
                <Phone class="h-4 w-4" />
                <span>{{ workshop.phone }}</span>
              </div>
            </div>
            <Link :href="route('workshops.public.show', workshop.id)" class="block mt-2">
              <Button class="w-full" size="sm">Ver perfil</Button>
            </Link>
          </CardContent>
        </Card>
      </div>
    </div>
  </AppSidebarLayout>
</template>