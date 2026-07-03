<script setup lang="ts">
import { ref, computed } from 'vue'
import { useForm } from '@inertiajs/vue3'
import { router } from '@inertiajs/vue3'
import { Input } from '@/Components/ui/input'
import { Button } from '@/Components/ui/button'
import { Label } from '@/Components/ui/label'
import { Textarea } from '@/Components/ui/textarea'
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/Components/ui/select'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/Components/ui/card'
import { Badge } from '@/Components/ui/badge'
import { Alert, AlertDescription } from '@/Components/ui/alert'
import { Upload, X, Plus, MapPin, DollarSign, Calendar, Fuel, Gauge, Info } from 'lucide-vue-next'

interface Vehicle {
  id: number
  plate: string
  brand: string
  model: string
  year: number
  mileage_km: number
  fuel_type: string
  power_hp: number
  gearbox: string
  photos: string[]
}

interface Props {
  vehicles: Vehicle[]
}

const props = defineProps<Props>()

const form = useForm({
  vehicle_id: null as number | null,
  title: '',
  description: '',
  price: '',
  currency: 'EUR',
  is_negotiable: false,
  location_city: '',
  location_region: '',
  photos: [] as File[],
})

const previewPhotos = ref<string[]>([])

const fuelTypes = ['gasolina', 'diesel', 'hibrido', 'electrico', 'glp', 'otro']
const gearboxes = ['manual', 'automatico']
const regions = ['Madrid', 'Barcelona', 'Valencia', 'Sevilla', 'Bilbao', 'Alicante', 'Málaga', 'Otra']

const selectedVehicle = computed(() => {
  if (!form.vehicle_id) return null
  return props.vehicles.find(v => v.id === form.vehicle_id)
})

const handlePhotoUpload = (event: Event) => {
  const target = event.target as HTMLInputElement
  if (target.files && target.files.length > 0) {
    const files = Array.from(target.files)
    form.photos.push(...files)

    files.forEach(file => {
      const reader = new FileReader()
      reader.onload = (e) => {
        previewPhotos.value.push(e.target?.result as string)
      }
      reader.readAsDataURL(file)
    })
  }
}

const removePhoto = (index: number) => {
  form.photos.splice(index, 1)
  previewPhotos.value.splice(index, 1)
}

const selectVehicle = (vehicleId: number) => {
  form.vehicle_id = vehicleId
  const vehicle = props.vehicles.find(v => v.id === vehicleId)
  if (vehicle) {
    form.title = `${vehicle.brand} ${vehicle.model} ${vehicle.year}`
    form.description = `Vehículo en excelente estado con ${vehicle.mileage_km.toLocaleString('es-ES')} km.`
    previewPhotos.value = [...vehicle.photos]
  }
}

const goBack = () => window.history.back()

const submit = () => {
  const formData = new FormData()

  Object.keys(form.data()).forEach(key => {
    if (key === 'photos') {
      form.photos.forEach((file, index) => {
        formData.append(`photos[${index}]`, file)
      })
    } else if (form[key] !== null) {
      formData.append(key, String(form[key]))
    }
  })

  form.post('/listings', {
    forceFormData: true,
    onSuccess: () => {
      router.visit('/marketplace')
    }
  })
}

const formatPrice = (value: string) => {
  return value.replace(/\D/g, '').replace(/\B(?=(\d{3})+(?!\d))/g, '.')
}
</script>

<template>
  <div class="max-w-4xl mx-auto space-y-6">
    <!-- Header -->
    <div>
      <h1 class="text-3xl font-bold">Publicar anuncio</h1>
      <p class="text-muted-foreground mt-1">Vende tu vehículo en el marketplace de GarageOS</p>
    </div>

    <form @submit.prevent="submit" class="space-y-6">
      <!-- Step 1: Select Vehicle -->
      <Card>
        <CardHeader>
          <CardTitle>1. Selecciona tu vehículo</CardTitle>
          <CardDescription>Elige uno de tus vehículos del garaje</CardDescription>
        </CardHeader>
        <CardContent>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <Card
              v-for="vehicle in props.vehicles"
              :key="vehicle.id"
              :class="[
                'cursor-pointer transition-all',
                form.vehicle_id === vehicle.id ? 'ring-2 ring-primary' : 'hover:shadow-md'
              ]"
              @click="selectVehicle(vehicle.id)"
            >
              <CardHeader class="pb-3">
                <div class="flex items-start justify-between">
                  <div>
                    <CardTitle class="text-base">{{ vehicle.brand }} {{ vehicle.model }}</CardTitle>
                    <CardDescription class="text-sm">{{ vehicle.year }} • {{ vehicle.mileage_km.toLocaleString('es-ES') }} km</CardDescription>
                  </div>
                  <Badge v-if="form.vehicle_id === vehicle.id">Seleccionado</Badge>
                </div>
              </CardHeader>
              <CardContent class="pt-0">
                <div class="flex items-center gap-2 text-sm text-muted-foreground">
                  <Fuel class="h-4 w-4" />
                  {{ vehicle.fuel_type }}
                  <Gauge class="h-4 w-4" />
                  {{ vehicle.power_hp }} CV
                  <MapPin class="h-4 w-4" />
                  {{ vehicle.plate }}
                </div>
              </CardContent>
            </Card>
          </div>
        </CardContent>
      </Card>

      <!-- Step 2: Listing Details -->
      <Card>
        <CardHeader>
          <CardTitle>2. Detalles del anuncio</CardTitle>
          <CardDescription>Completa la información del anuncio</CardDescription>
        </CardHeader>
        <CardContent class="space-y-4">
          <div class="space-y-2">
            <Label for="title">Título *</Label>
            <Input
              id="title"
              v-model="form.title"
              placeholder="Ej. BMW 320d 2018 en perfecto estado"
            />
          </div>

          <div class="space-y-2">
            <Label for="description">Descripción *</Label>
            <Textarea
              id="description"
              v-model="form.description"
              rows="5"
              placeholder="Describe el estado del vehículo, características, historial de mantenimiento..."
            />
          </div>

          <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="space-y-2">
              <Label for="price">Precio (€) *</Label>
              <Input
                id="price"
                v-model="form.price"
                type="number"
                placeholder="Ej. 15000"
                @input="form.price = formatPrice(form.price)"
              />
            </div>

            <div class="space-y-2">
              <Label for="currency">Moneda</Label>
              <Select v-model="form.currency">
                <SelectTrigger>
                  <SelectValue />
                </SelectTrigger>
                <SelectContent>
                  <SelectItem value="EUR">EUR €</SelectItem>
                  <SelectItem value="USD">USD $</SelectItem>
                  <SelectItem value="GBP">GBP £</SelectItem>
                </SelectContent>
              </Select>
            </div>

            <div class="flex items-end">
              <div class="flex items-center space-x-2">
                <input
                  id="is_negotiable"
                  v-model="form.is_negotiable"
                  type="checkbox"
                  class="rounded border-gray-300"
                />
                <Label for="is_negotiable" class="cursor-pointer">Precio negociable</Label>
              </div>
            </div>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="space-y-2">
              <Label for="location_city">Ciudad *</Label>
              <Input
                id="location_city"
                v-model="form.location_city"
                placeholder="Ej. Madrid"
              />
            </div>

            <div class="space-y-2">
              <Label for="location_region">Provincia *</Label>
              <Select v-model="form.location_region">
                <SelectTrigger>
                  <SelectValue placeholder="Selecciona provincia" />
                </SelectTrigger>
                <SelectContent>
                  <SelectItem v-for="region in regions" :key="region" :value="region">
                    {{ region }}
                  </SelectItem>
                </SelectContent>
              </Select>
            </div>
          </div>
        </CardContent>
      </Card>

      <!-- Step 3: Photos -->
      <Card>
        <CardHeader>
          <CardTitle>3. Fotos del vehículo</CardTitle>
          <CardDescription>Añade fotos para atraer más compradores</CardDescription>
        </CardHeader>
        <CardContent class="space-y-4">
          <div class="border-2 border-dashed rounded-lg p-8 text-center hover:border-primary cursor-pointer">
            <input
              type="file"
              multiple
              accept="image/*"
              @change="handlePhotoUpload"
              class="hidden"
              ref="fileInput"
            >
            <Upload class="h-12 w-12 mx-auto text-muted-foreground mb-4" />
            <p class="text-sm font-medium">Click o arrastra fotos aquí</p>
            <p class="text-xs text-muted-foreground mt-1">PNG, JPG hasta 5MB por foto</p>
          </div>

          <div v-if="previewPhotos.length > 0" class="grid grid-cols-3 md:grid-cols-4 gap-4">
            <div
              v-for="(photo, index) in previewPhotos"
              :key="index"
              class="relative group"
            >
              <img
                :src="photo"
                alt="Foto del vehículo"
                class="w-full h-32 object-cover rounded-lg"
              >
              <Button
                variant="destructive"
                size="sm"
                class="absolute top-2 right-2 opacity-0 group-hover:opacity-100 transition-opacity"
                @click="removePhoto(index)"
              >
                <X class="h-4 w-4" />
              </Button>
            </div>
          </div>

          <Alert>
            <Info class="h-4 w-4" />
            <AlertDescription>
              Se recomienda añadir al menos 5 fotos: frontal, trasera, laterales, interior y motor.
            </AlertDescription>
          </Alert>
        </CardContent>
      </Card>

      <!-- Submit -->
      <div class="flex justify-end gap-3">
        <Button variant="outline" type="button" @click="goBack()">
          Cancelar
        </Button>
        <Button type="submit" :disabled="form.processing">
          {{ form.processing ? 'Publicando...' : 'Publicar anuncio' }}
        </Button>
      </div>
    </form>
  </div>
</template>
