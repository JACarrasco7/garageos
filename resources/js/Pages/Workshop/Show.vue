<script setup lang="ts">
import AppSidebarLayout from '@/layouts/app/AppSidebarLayout.vue'
import { Head, Link } from '@inertiajs/vue3'
import { Card, CardContent, CardHeader, CardTitle } from '@/Components/ui/card'
import { Button } from '@/Components/ui/button'
import { Textarea } from '@/Components/ui/textarea'
import { Star, MapPin, Phone, Mail } from 'lucide-vue-next'

interface Review {
  id: number
  rating: number
  comment: string
  user: { name: string }
  created_at: string
}

interface Workshop {
  id: number
  name: string
  description: string
  rating: number
  city: string
  address: string
  phone: string
  email: string
  logo: string
  reviews: Review[]
}

defineProps<{
  workshop: Workshop
  reviews: Review[]
}>()
</script>

<template>
  <Head :title="workshop.name" />

  <AppSidebarLayout>
    <template #header>
      {{ workshop.name }}
    </template>

    <div class="space-y-6">
      <Card class="glass-surface border-0">
        <CardHeader>
          <CardTitle class="text-2xl">{{ workshop.name }}</CardTitle>
        </CardHeader>
        <CardContent class="space-y-4">
          <img v-if="workshop.logo" :src="workshop.logo" :alt="workshop.name" class="w-32 h-32 rounded-lg object-cover" />
          <div class="flex items-center gap-2">
            <Star class="h-5 w-5 fill-yellow-400 text-yellow-400" />
            <span class="text-lg font-semibold">{{ workshop.rating || 0 }}</span>
          </div>
          <p class="text-muted-foreground">{{ workshop.description }}</p>
          <div class="space-y-2 text-sm">
            <div class="flex items-center gap-2">
              <MapPin class="h-4 w-4" />
              <span>{{ workshop.address }}, {{ workshop.city }}</span>
            </div>
            <div class="flex items-center gap-2">
              <Phone class="h-4 w-4" />
              <span>{{ workshop.phone }}</span>
            </div>
            <div class="flex items-center gap-2">
              <Mail class="h-4 w-4" />
              <span>{{ workshop.email }}</span>
            </div>
          </div>
        </CardContent>
      </Card>

      <Card class="glass-surface border-0">
        <CardHeader>
          <CardTitle>Reseñas</CardTitle>
        </CardHeader>
        <CardContent class="space-y-4">
          <div v-if="reviews.length" class="space-y-4">
            <div v-for="review in reviews" :key="review.id" class="border-b pb-4">
              <div class="flex items-center gap-2 mb-1">
                <Star v-for="i in 5" :key="i" class="h-4 w-4" :class="i <= review.rating ? 'fill-yellow-400 text-yellow-400' : ''" />
              </div>
              <p class="text-sm">{{ review.comment }}</p>
              <p class="text-xs text-muted-foreground">Por {{ review.user.name }} • {{ review.created_at }}</p>
            </div>
          </div>
          <p v-else class="text-muted-foreground">Aún no hay reseñas.</p>
        </CardContent>
      </Card>
    </div>
  </AppSidebarLayout>
</template>
