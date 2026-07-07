<script setup lang="ts">
import { ShieldCheck, CheckCircle2 } from 'lucide-vue-next'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/Components/ui/card'
import { Badge } from '@/Components/ui/badge'

interface VehicleImport {
  id: number
  brand: string
  model: string
  year: number
  vin: string | null
  plate_original: string | null
}

interface Verification {
  id: number
  vin_verified: boolean
  ownership_verified: boolean
  technical_data_verified: boolean
  itv_verified: boolean
  legal_status_verified: boolean
  overall_status: string
  verified_at: string | null
}

defineProps<{
  import: VehicleImport
  verification: Verification
}>()

const statusColors = {
  CERTIFIED: 'bg-green-600 text-white',
  VERIFIED: 'bg-blue-600 text-white',
  PARTIAL: 'bg-yellow-500 text-black',
  UNVERIFIED: 'bg-red-600 text-white',
}
</script>

<template>
  <div class="min-h-screen bg-muted/30 flex items-center justify-center p-4">
    <Card class="max-w-2xl w-full">
      <CardHeader class="text-center">
        <div class="flex justify-center mb-4">
          <ShieldCheck class="h-16 w-16 text-primary" />
        </div>
        <CardTitle class="text-3xl">Certificado de Verificación GarageOS</CardTitle>
        <CardDescription>
          Este certificado verifica la legalidad y estado técnico de un vehículo importado.
        </CardDescription>
      </CardHeader>
      <CardContent class="space-y-6">
        <div class="grid grid-cols-2 gap-4">
          <div>
            <span class="text-sm text-muted-foreground">Marca</span>
            <p class="font-semibold">{{ import.brand }}</p>
          </div>
          <div>
            <span class="text-sm text-muted-foreground">Modelo</span>
            <p class="font-semibold">{{ import.model }}</p>
          </div>
          <div>
            <span class="text-sm text-muted-foreground">Año</span>
            <p class="font-semibold">{{ import.year }}</p>
          </div>
          <div>
            <span class="text-sm text-muted-foreground">VIN</span>
            <p class="font-mono text-sm">{{ import.vin }}</p>
          </div>
        </div>

        <div class="border-t pt-4">
          <h3 class="font-semibold mb-3">Verificaciones realizadas</h3>
          <div class="grid grid-cols-2 gap-3">
            <div class="flex items-center gap-2">
              <CheckCircle2 v-if="verification.vin_verified" class="h-5 w-5 text-green-600" />
              <span class="text-sm">VIN verificado</span>
            </div>
            <div class="flex items-center gap-2">
              <CheckCircle2 v-if="verification.ownership_verified" class="h-5 w-5 text-green-600" />
              <span class="text-sm">Titularidad verificada</span>
            </div>
            <div class="flex items-center gap-2">
              <CheckCircle2 v-if="verification.technical_data_verified" class="h-5 w-5 text-green-600" />
              <span class="text-sm">Datos técnicos verificados</span>
            </div>
            <div class="flex items-center gap-2">
              <CheckCircle2 v-if="verification.itv_verified" class="h-5 w-5 text-green-600" />
              <span class="text-sm">ITV verificada</span>
            </div>
            <div class="flex items-center gap-2">
              <CheckCircle2 v-if="verification.legal_status_verified" class="h-5 w-5 text-green-600" />
              <span class="text-sm">Estatus legal verificado</span>
            </div>
          </div>
        </div>

        <div class="border-t pt-4 text-center">
          <Badge :class="[statusColors[verification.overall_status]]" class="text-lg px-6 py-2">
            {{ verification.overall_status }}
          </Badge>
          <p class="text-sm text-muted-foreground mt-2">
            Certificado emitido el {{ verification.verified_at ? new Date(verification.verified_at).toLocaleDateString() : 'N/A' }}
          </p>
        </div>
      </CardContent>
    </Card>
  </div>
</template>
