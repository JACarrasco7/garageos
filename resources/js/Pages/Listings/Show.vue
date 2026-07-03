<script setup lang="ts">
import { ref } from 'vue'
import { useForm } from '@inertiajs/vue3'
import { router } from '@inertiajs/vue3'
import { Button } from '@/Components/ui/button'
import { Badge } from '@/Components/ui/badge'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/Components/ui/card'
import { Alert, AlertDescription } from '@/Components/ui/alert'
import { Dialog, DialogContent, DialogHeader, DialogTitle } from '@/Components/ui/dialog'
import { Textarea } from '@/Components/ui/textarea'
import { Heart, Share, MapPin, Calendar, Fuel, Gauge, MessageCircle, DollarSign, User, ChevronLeft, ChevronRight, X } from 'lucide-vue-next'

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
  expires_at: string
  brand: string
  model: string
  year: number
  mileage_km: number
  fuel_type: string
  power_hp: number
  gearbox: string
  photos: string[]
  user: {
    id: number
    name: string
    avatar_url: string | null
  }
  is_favorite: boolean
}

interface Props {
  listing: Listing
}

const props = defineProps<Props>()

const currentPhotoIndex = ref(0)
const showContactModal = ref(false)

const messageForm = useForm({
  message: '',
})

const goBack = () => window.history.back()

const toggleFavorite = () => {
  router.post(`/marketplace/listings/${props.listing.id}/favorite`, {}, {
    preserveScroll: true,
    onSuccess: () => {
      props.listing.is_favorite = !props.listing.is_favorite
    }
  })
}

const nextPhoto = () => {
  if (currentPhotoIndex.value < props.listing.photos.length - 1) {
    currentPhotoIndex.value++
  }
}

const prevPhoto = () => {
  if (currentPhotoIndex.value > 0) {
    currentPhotoIndex.value--
  }
}

const sendMessage = () => {
  messageForm.post(`/listings/${props.listing.id}/message`, {
    onSuccess: () => {
      showContactModal.value = false
      messageForm.reset()
    }
  })
}

const shareListing = () => {
  if (navigator.share) {
    navigator.share({
      title: props.listing.title,
      text: `${props.listing.title} - ${props.listing.price.toLocaleString('es-ES')} ${props.listing.currency}`,
      url: window.location.href
    })
  } else {
    navigator.clipboard.writeText(window.location.href)
  }
}

const formatDate = (date: string) => {
  return new Date(date).toLocaleDateString('es-ES', {
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  })
}

const timeAgo = (date: string) => {
  const now = new Date()
  const then = new Date(date)
  const diff = now.getTime() - then.getTime()
  const days = Math.floor(diff / (1000 * 60 * 60 * 24))

  if (days === 0) return 'Hoy'
  if (days === 1) return 'Ayer'
  if (days < 7) return `Hace ${days} días`
  if (days < 30) return `Hace ${Math.floor(days / 7)} semanas`
  return formatDate(date)
}
</script>

<template>
  <div class="max-w-7xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex items-center gap-4">
      <Button variant="ghost" size="sm" @click="goBack()">
        <ChevronLeft class="h-4 w-4 mr-1" />
        Volver
      </Button>
      <div>
        <h1 class="text-3xl font-bold">{{ listing.title }}</h1>
        <p class="text-muted-foreground mt-1">
          {{ listing.brand }} {{ listing.model }} • {{ listing.year }}
        </p>
      </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
      <!-- Left Column: Photos and Details -->
      <div class="lg:col-span-2 space-y-6">
        <!-- Photos -->
        <Card>
          <CardContent class="p-0">
            <div class="relative aspect-video bg-muted">
              <img
                v-if="listing.photos.length > 0"
                :src="listing.photos[currentPhotoIndex]"
                :alt="listing.title"
                class="w-full h-full object-cover"
              />
              <div v-else class="w-full h-full flex items-center justify-center">
                <span class="text-muted-foreground">Sin fotos</span>
              </div>

              <!-- Photo Navigation -->
              <div v-if="listing.photos.length > 1" class="absolute bottom-4 left-1/2 -translate-x-1/2 flex gap-2">
                <Button
                  variant="secondary"
                  size="sm"
                  :disabled="currentPhotoIndex === 0"
                  @click="prevPhoto"
                >
                  <ChevronLeft class="h-4 w-4" />
                </Button>
                <Button
                  variant="secondary"
                  size="sm"
                  :disabled="currentPhotoIndex === listing.photos.length - 1"
                  @click="nextPhoto"
                >
                  <ChevronRight class="h-4 w-4" />
                </Button>
              </div>
            </div>

            <!-- Photo Thumbnails -->
            <div v-if="listing.photos.length > 1" class="flex gap-2 p-4 overflow-x-auto">
              <img
                v-for="(photo, index) in listing.photos"
                :key="index"
                :src="photo"
                :alt="`Foto ${index + 1}`"
                class="w-24 h-16 object-cover rounded cursor-pointer"
                :class="{ 'ring-2 ring-primary': currentPhotoIndex === index }"
                @click="currentPhotoIndex = index"
              />
            </div>
          </CardContent>
        </Card>

        <!-- Details -->
        <Card>
          <CardHeader>
            <CardTitle>Detalles del vehículo</CardTitle>
          </CardHeader>
          <CardContent>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
              <div class="flex items-center gap-2">
                <Calendar class="h-5 w-5 text-muted-foreground" />
                <div>
                  <p class="text-sm text-muted-foreground">Año</p>
                  <p class="font-medium">{{ listing.year }}</p>
                </div>
              </div>

              <div class="flex items-center gap-2">
                <Fuel class="h-5 w-5 text-muted-foreground" />
                <div>
                  <p class="text-sm text-muted-foreground">Combustible</p>
                  <p class="font-medium">{{ listing.fuel_type }}</p>
                </div>
              </div>

              <div class="flex items-center gap-2">
                <Gauge class="h-5 w-5 text-muted-foreground" />
                <div>
                  <p class="text-sm text-muted-foreground">Potencia</p>
                  <p class="font-medium">{{ listing.power_hp }} CV</p>
                </div>
              </div>

              <div class="flex items-center gap-2">
                <DollarSign class="h-5 w-5 text-muted-foreground" />
                <div>
                  <p class="text-sm text-muted-foreground">Kilometraje</p>
                  <p class="font-medium">{{ listing.mileage_km.toLocaleString('es-ES') }} km</p>
                </div>
              </div>
            </div>
          </CardContent>
        </Card>

        <!-- Description -->
        <Card>
          <CardHeader>
            <CardTitle>Descripción</CardTitle>
          </CardHeader>
          <CardContent>
            <p class="whitespace-pre-line">{{ listing.description }}</p>
          </CardContent>
        </Card>
      </div>

      <!-- Right Column: Seller Info and Actions -->
      <div class="space-y-6">
        <!-- Price Card -->
        <Card>
          <CardContent class="pt-6">
            <div class="space-y-4">
              <div>
                <p class="text-3xl font-bold">
                  {{ listing.price.toLocaleString('es-ES') }} {{ listing.currency }}
                </p>
                <Badge v-if="listing.is_negotiable" class="mt-2">Negociable</Badge>
              </div>

              <div class="flex gap-2">
                <Button
                  variant="outline"
                  class="flex-1"
                  @click="showContactModal = true"
                >
                  <MessageCircle class="h-4 w-4 mr-2" />
                  Contactar
                </Button>
                <Button
                  variant="outline"
                  @click="toggleFavorite"
                >
                  <Heart class="h-4 w-4" />
                </Button>
                <Button
                  variant="outline"
                  @click="shareListing"
                >
                  <Share class="h-4 w-4" />
                </Button>
              </div>
            </div>
          </CardContent>
        </Card>

        <!-- Seller Info -->
        <Card>
          <CardHeader>
            <CardTitle>Vendedor</CardTitle>
          </CardHeader>
          <CardContent>
            <div class="flex items-center gap-3">
              <div class="w-12 h-12 rounded-full bg-muted flex items-center justify-center">
                <User class="h-6 w-6" />
              </div>
              <div>
                <p class="font-medium">{{ listing.user.name }}</p>
                <p class="text-sm text-muted-foreground">Usuario verificado</p>
              </div>
            </div>

            <div class="mt-4 flex items-center gap-2 text-sm">
              <MapPin class="h-4 w-4 text-muted-foreground" />
              {{ listing.location_city }}, {{ listing.location_region }}
            </div>
          </CardContent>
        </Card>

        <!-- Listing Info -->
        <Card>
          <CardHeader>
            <CardTitle>Información</CardTitle>
          </CardHeader>
          <CardContent class="space-y-2 text-sm">
            <div class="flex justify-between">
              <span class="text-muted-foreground">Publicado</span>
              <span>{{ timeAgo(listing.created_at) }}</span>
            </div>
            <div class="flex justify-between">
              <span class="text-muted-foreground">Expira</span>
              <span>{{ formatDate(listing.expires_at) }}</span>
            </div>
            <div class="flex justify-between">
              <span class="text-muted-foreground">Vistas</span>
              <span>{{ listing.views }}</span>
            </div>
            <div class="flex justify-between">
              <span class="text-muted-foreground">Estado</span>
              <Badge>{{ listing.status }}</Badge>
            </div>
          </CardContent>
        </Card>
      </div>
    </div>

    <!-- Contact Modal -->
    <Dialog v-model:open="showContactModal">
      <DialogContent>
        <DialogHeader>
          <DialogTitle>Enviar mensaje</DialogTitle>
        </DialogHeader>
        <div class="space-y-4">
          <div class="space-y-2">
            <label class="text-sm font-medium">Mensaje</label>
            <Textarea
              v-model="messageForm.message"
              rows="4"
              placeholder="Hola, estoy interesado en este vehículo..."
            />
          </div>
          <div class="flex justify-end gap-2">
            <Button variant="outline" @click="showContactModal = false">
              Cancelar
            </Button>
            <Button
              @click="sendMessage"
              :disabled="messageForm.processing || !messageForm.message"
            >
              {{ messageForm.processing ? 'Enviando...' : 'Enviar' }}
            </Button>
          </div>
        </div>
      </DialogContent>
    </Dialog>
  </div>
</template>
