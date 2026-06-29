<script setup lang="ts">
import { Card, CardContent, CardHeader, CardTitle } from '@/Components/ui/card'
import { Badge } from '@/Components/ui/badge'

interface Vehicle {
  id: number
  plate: string
  brand: string
  model: string
  year: number
  fuel_type: string
  current_km: number
  specs?: {
    engine_cc: number | null
    power_hp: number | null
  } | null
  documents?: Array<{
    type: string
    expiry_date: string | null
  }>
}

defineProps<{
  vehicle: Vehicle
}>()
</script>

<template>
  <div class="min-h-screen bg-background py-12">
    <div class="max-w-2xl mx-auto px-4">
      <Card>
        <CardHeader>
          <CardTitle class="text-2xl">
            {{ vehicle.brand }} {{ vehicle.model }}
          </CardTitle>
        </CardHeader>
        <CardContent class="space-y-6">
          <div class="grid grid-cols-2 gap-4">
            <div>
              <span class="text-muted-foreground">Matrícula:</span>
              <span class="font-medium">{{ vehicle.plate }}</span>
            </div>
            <div>
              <span class="text-muted-foreground">Año:</span>
              <span class="font-medium">{{ vehicle.year }}</span>
            </div>
            <div>
              <span class="text-muted-foreground">Km:</span>
              <span class="font-medium">{{ vehicle.current_km.toLocaleString() }}</span>
            </div>
            <div>
              <span class="text-muted-foreground">Combustible:</span>
              <Badge variant="secondary" class="ml-1 capitalize">{{ vehicle.fuel_type }}</Badge>
            </div>
          </div>

          <div v-if="vehicle.specs">
            <h2 class="text-lg font-semibold mb-2">Especificaciones</h2>
            <p v-if="vehicle.specs.engine_cc">{{ vehicle.specs.engine_cc }} cc</p>
            <p v-if="vehicle.specs.power_hp">{{ vehicle.specs.power_hp }} CV</p>
          </div>

          <div v-if="vehicle.documents?.length">
            <h2 class="text-lg font-semibold mb-2">ITV</h2>
            <div
              v-for="doc in vehicle.documents"
              :key="doc.type"
              class="text-sm"
            >
              <span class="text-muted-foreground">Vence:</span>
              {{ doc.expiry_date }}
            </div>
          </div>

          <p class="text-sm text-muted-foreground mt-8">
            Escanea el QR para ver la información del vehículo
          </p>
        </CardContent>
      </Card>
    </div>
  </div>
</template>
