<script setup lang="ts">
import { useForm } from '@inertiajs/vue3'
import { Link } from '@inertiajs/vue3'
import WebLayout from '@/layouts/WebLayout.vue'
import { Head } from '@inertiajs/vue3'
import { Card, CardContent, CardHeader, CardTitle } from '@/Components/ui/card'
import { Badge } from '@/Components/ui/badge'
import { Button } from '@/Components/ui/button'
import { ArrowLeft, CheckCircle, XCircle, Clock, RefreshCw } from 'lucide-vue-next'

interface VehicleImport {
  id: number
  plate_original: string
  plate_new: string | null
  brand: string
  model: string
  year: number
  status: string
  rejection_reason: string | null
}

defineProps<{ vehicleImport: VehicleImport }>()

const processForm = useForm({})

const statusConfig: Record<string, { label: string; variant: 'default' | 'secondary' | 'destructive' | 'outline' | 'success'; icon: any }> = {
  pending: { label: 'Pendiente', variant: 'outline', icon: Clock },
  processing: { label: 'Procesando', variant: 'secondary', icon: RefreshCw },
  approved: { label: 'Aprobado', variant: 'default', icon: CheckCircle },
  rejected: { label: 'Rechazado', variant: 'destructive', icon: XCircle },
}
</script>

<template>
  <Head title="Detalle Importación" />

  <WebLayout>
    <template #header>
      Detalle Importación
    </template>

    <div class="max-w-2xl mx-auto space-y-6">
      <Card class="glass-surface border-0">
        <CardHeader>
          <CardTitle>Importación de Vehículo</CardTitle>
        </CardHeader>
        <CardContent class="space-y-4">
          <div class="space-y-1">
            <p class="text-sm text-muted-foreground">Matrícula Original (Alemania)</p>
            <p class="font-semibold">{{ vehicleImport.plate_original }}</p>
          </div>

          <div class="space-y-1">
            <p class="text-sm text-muted-foreground">Matrícula Asignada (España)</p>
            <p class="font-semibold" :class="vehicleImport.plate_new ? 'text-green-600' : 'text-muted-foreground'">
              {{ vehicleImport.plate_new || 'Pendiente' }}
            </p>
          </div>

          <div class="space-y-1">
            <p class="text-sm text-muted-foreground">Vehículo</p>
            <p class="font-semibold">{{ vehicleImport.brand }} {{ vehicleImport.model }} ({{ vehicleImport.year }})</p>
          </div>

          <div class="space-y-1">
            <p class="text-sm text-muted-foreground">Estado</p>
            <Badge :variant="statusConfig[vehicleImport.status]?.variant ?? 'outline'" class="capitalize">
              {{ statusConfig[vehicleImport.status]?.label ?? vehicleImport.status }}
            </Badge>
          </div>

          <div v-if="vehicleImport.rejection_reason" class="bg-destructive/10 p-4 rounded-lg">
            <p class="text-sm text-destructive font-medium">Motivo rechazo: {{ vehicleImport.rejection_reason }}</p>
          </div>

          <div v-if="vehicleImport.status === 'approved'" class="pt-4 border-t">
            <Button
              @click="processForm.post(route('imports.process', vehicleImport.id))"
              :disabled="processForm.processing"
            >
              <RefreshCw v-if="processForm.processing" class="mr-2 h-4 w-4 animate-spin" />
              {{ processForm.processing ? 'Procesando...' : 'Procesar Importación' }}
            </Button>
          </div>
        </CardContent>
      </Card>
    </div>
  </WebLayout>
</template>
