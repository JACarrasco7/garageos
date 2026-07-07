<script setup lang="ts">
import { Link } from '@inertiajs/vue3'
import WebLayout from '@/layouts/WebLayout.vue'
import { Card, CardContent, CardHeader, CardTitle } from '@/Components/ui/card'
import { Button } from '@/Components/ui/button'
import { FileText, Calendar, Plus, ExternalLink } from 'lucide-vue-next'

interface Vehicle {
  id: number
  plate: string
  brand: string
  model: string
}

interface Document {
  id: number
  type: string
  title: string
  file_path: string
  expiry_date: string | null
  amount: number | null
}

defineProps<{
  vehicle?: Vehicle
  documents: Record<string, Document[]>
}>()

const documentLabels: Record<string, string> = {
  factura: 'Facturas',
  itv: 'ITV',
  seguro: 'Seguros',
  impuesto: 'Impuestos',
  otro: 'Otros',
}
</script>

<template>
  <WebLayout>
    <template #header>
      <span v-if="vehicle">
        Documentos - {{ vehicle.brand }} {{ vehicle.model }}
      </span>
      <span v-else>
        Documentos
      </span>
    </template>

    <Card class="glass-surface border-0">
      <CardHeader class="flex flex-row items-center justify-between pb-3">
        <CardTitle class="text-lg font-semibold">Documentos del vehículo</CardTitle>
        <Button variant="ghost" size="sm" class="rounded-lg glass-tab" @click="$emit('open-upload')">
          <Plus class="mr-2 h-4 w-4" />
          Subir documento
        </Button>
      </CardHeader>
      <CardContent>
        <div v-if="Object.keys(documents).length === 0" class="text-center py-12">
          <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-muted mb-4">
            <FileText class="h-8 w-8 text-muted-foreground" />
          </div>
          <h3 class="font-medium text-foreground mb-1">No hay documentos</h3>
          <p class="text-sm text-muted-foreground">Sube tu primer documento</p>
        </div>

        <div v-else class="space-y-6">
          <div v-for="(group, type) in documents" :key="type">
            <h4 class="text-sm font-semibold uppercase tracking-wider text-muted-foreground mb-3">
              {{ documentLabels[type] || type }}
            </h4>
            <div class="space-y-2">
              <div
                v-for="doc in group"
                :key="doc.id"
                class="flex justify-between items-center p-3 rounded-lg glass-item hover:bg-accent/5 transition-colors"
              >
                <div>
                  <p class="font-medium text-foreground">{{ doc.title }}</p>
                  <p v-if="doc.expiry_date" class="text-sm text-muted-foreground flex items-center gap-1 mt-1">
                    <Calendar class="h-3 w-3" />
                    Vence: {{ doc.expiry_date }}
                  </p>
                </div>
                <a
                  :href="`/storage/${doc.file_path}`"
                  target="_blank"
                  class="flex items-center gap-1 text-sm text-primary hover:underline"
                >
                  Ver <ExternalLink class="h-3 w-3" />
                </a>
              </div>
            </div>
          </div>
        </div>
      </CardContent>
    </Card>
  </WebLayout>
</template>
