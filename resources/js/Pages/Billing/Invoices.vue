<script setup lang="ts">
import { ref, computed } from 'vue'
import { router } from '@inertiajs/vue3'
import { Button } from '@/Components/ui/button'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/Components/ui/card'
import { Badge } from '@/Components/ui/badge'
import { Input } from '@/Components/ui/input'
import { DollarSign, Search, Download } from 'lucide-vue-next'

interface PlatformFee {
  id: number
  amount: number
  currency: string
  description: string
  processed_at: string | null
  created_at: string
  payment_intent: {
    description: string
    amount: number
  }
}

interface Props {
  fees: {
    data: PlatformFee[]
    current_page: number
    last_page: number
    per_page: number
    total: number
  }
}

const props = defineProps<Props>()

const searchQuery = ref('')

const goBack = () => window.history.back()

const filteredFees = computed(() => {
  if (!searchQuery.value) return props.fees.data
  return props.fees.data.filter(fee =>
    fee.description.toLowerCase().includes(searchQuery.value.toLowerCase())
  )
})

const formatCurrency = (amount: number) => {
  return new Intl.NumberFormat('es-ES', {
    style: 'currency',
    currency: 'EUR',
  }).format(amount)
}

const formatDate = (date: string | null) => {
  if (!date) return 'Pendiente'
  return new Date(date).toLocaleDateString('es-ES', {
    day: '2-digit',
    month: 'short',
    year: 'numeric'
  })
}
</script>

<template>
  <div class="max-w-7xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
      <div>
        <h1 class="text-3xl font-bold flex items-center gap-2">
          <DollarSign class="h-8 w-8" />
          Facturas y Comisiones
        </h1>
        <p class="text-muted-foreground mt-1">
          Historial completo de transacciones
        </p>
      </div>
      <Button variant="outline" @click="goBack()">
        Volver
      </Button>
    </div>

    <!-- Search -->
    <Card>
      <CardContent class="pt-6">
        <div class="relative">
          <Search class="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-muted-foreground" />
          <Input
            v-model="searchQuery"
            placeholder="Buscar en facturas..."
            class="pl-10"
          />
        </div>
      </CardContent>
    </Card>

    <!-- Total Summary -->
    <Card>
      <CardContent class="pt-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
          <div>
            <p class="text-sm text-muted-foreground">Total Facturado</p>
            <p class="text-2xl font-bold">
              {{ formatCurrency(fees.data.reduce((sum, fee) => sum + fee.amount, 0)) }}
            </p>
          </div>
          <div>
            <p class="text-sm text-muted-foreground">Procesado</p>
            <p class="text-2xl font-bold text-green-600">
              {{ formatCurrency(fees.data.filter(f => f.processed_at).reduce((sum, fee) => sum + fee.amount, 0)) }}
            </p>
          </div>
          <div>
            <p class="text-sm text-muted-foreground">Pendiente</p>
            <p class="text-2xl font-bold text-yellow-600">
              {{ formatCurrency(fees.data.filter(f => !f.processed_at).reduce((sum, fee) => sum + fee.amount, 0)) }}
            </p>
          </div>
        </div>
      </CardContent>
    </Card>

    <!-- Invoices List -->
    <div class="space-y-4">
      <div
        v-for="fee in filteredFees"
        :key="fee.id"
        class="border rounded-lg p-4 hover:shadow-md transition-shadow"
      >
        <div class="flex items-center justify-between">
          <div class="flex-1">
            <div class="flex items-center gap-3 mb-2">
              <h3 class="font-semibold">{{ fee.description }}</h3>
              <Badge :class="fee.processed_at ? 'bg-green-500' : 'bg-yellow-500'">
                {{ fee.processed_at ? 'Procesado' : 'Pendiente' }}
              </Badge>
            </div>
            <p class="text-sm text-muted-foreground">
              {{ fee.payment_intent?.description || 'Transacción' }}
            </p>
            <p class="text-sm text-muted-foreground mt-1">
              Fecha: {{ formatDate(fee.created_at) }}
            </p>
          </div>
          <div class="text-right ml-4">
            <p class="text-2xl font-bold">{{ formatCurrency(fee.amount) }}</p>
            <Button variant="ghost" size="sm" class="mt-2">
              <Download class="h-4 w-4 mr-2" />
              Descargar
            </Button>
          </div>
        </div>
      </div>

      <!-- Empty State -->
      <div v-if="filteredFees.length === 0" class="text-center py-12 text-muted-foreground">
        <DollarSign class="h-12 w-12 mx-auto mb-4 opacity-50" />
        <p>No hay facturas</p>
      </div>
    </div>

    <!-- Pagination -->
    <div v-if="fees.last_page > 1" class="flex justify-center gap-2">
      <Button
        variant="outline"
        :disabled="fees.current_page === 1"
        @click="router.visit(`/billing/invoices?page=${fees.current_page - 1}`)"
      >
        Anterior
      </Button>
      <span class="py-2 px-4 bg-muted rounded">
        {{ fees.current_page }} / {{ fees.last_page }}
      </span>
      <Button
        variant="outline"
        :disabled="fees.current_page === fees.last_page"
        @click="router.visit(`/billing/invoices?page=${fees.current_page + 1}`)"
      >
        Siguiente
      </Button>
    </div>
  </div>
</template>
