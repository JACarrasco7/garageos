<script setup lang="ts">
import { ref, computed } from 'vue'
import { Dialog, DialogContent, DialogHeader, DialogTitle, DialogDescription } from '@/Components/ui/dialog'
import { Button } from '@/Components/ui/button'
import { Card, CardContent, CardHeader, CardTitle } from '@/Components/ui/card'
import { CheckCircle2, AlertCircle, ShieldCheck, FileText, User, CreditCard, Shield } from 'lucide-vue-next'

interface Props {
  open: boolean
  onOpenChange: (open: boolean) => void
}

const props = defineProps<Props>()
const emit = defineEmits<{
  (e: 'openChange', open: boolean): void
}>()

const requirements = [
  { icon: FileText, label: 'DNI del comprador', description: 'Documento de identidad del comprador' },
  { icon: FileText, label: 'DNI del vendedor', description: 'Documento de identidad del vendedor en Alemania' },
  { icon: ShieldCheck, label: 'Verificación VIN', description: 'Verificación del número de identificación del vehículo' },
  { icon: CreditCard, label: 'Contrato legal', description: 'Contrato de compraventa firmado' },
  { icon: Shield, label: 'ITV aprobada', description: 'Inspección técnica de vehículos aprobada' },
]

const isRequirementCompleted = (index: number): boolean => {
  return index < 3
}
</script>

<template>
  <Dialog v-model:open="props.open" @update:open="emit('openChange', $event)">
    <DialogContent class="max-w-2xl">
      <DialogHeader>
        <DialogTitle>¿Qué necesito para verificar mi importación?</DialogTitle>
        <DialogDescription>
          Estos son los documentos y pasos necesarios para completar la importación legalmente.
        </DialogDescription>
      </DialogHeader>

      <div class="space-y-4">
        <Card v-for="(req, index) in requirements" :key="index" class="border-l-4" :class="isRequirementCompleted(index) ? 'border-green-500' : 'border-muted'">
          <CardHeader class="pb-2">
            <CardTitle class="flex items-center gap-2 text-base">
              <component :is="req.icon" class="h-5 w-5" :class="isRequirementCompleted(index) ? 'text-green-600' : 'text-muted-foreground'" />
              {{ req.label }}
              <CheckCircle2 v-if="isRequirementCompleted(index)" class="h-4 w-4 text-green-600 ml-auto" />
            </CardTitle>
          </CardHeader>
          <CardContent>
            <p class="text-sm text-muted-foreground">{{ req.description }}</p>
          </CardContent>
        </Card>
      </div>

      <div class="flex justify-end gap-3 mt-6">
        <Button variant="outline" @click="emit('openChange', false)">
          Cerrar
        </Button>
        <Button as-child>
          <a href="/imports">Ver mis importaciones</a>
        </Button>
      </div>
    </DialogContent>
  </Dialog>
</template>
