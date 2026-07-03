<script setup lang="ts">
import { computed } from 'vue'
import { router } from '@inertiajs/vue3'
import { Button } from '@/Components/ui/button'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/Components/ui/card'
import { Badge } from '@/Components/ui/badge'
import { DollarSign, TrendingUp, CreditCard, ArrowRight } from 'lucide-vue-next'

interface Props {
  hasAccount: boolean
  account?: any
  stats?: {
    totalEarnings: number
    pendingFees: number
    completedFees: number
    transactionCount: number
  }
  recentTransactions?: any[]
}

const props = defineProps<Props>()

const formatCurrency = (amount: number) => {
  return new Intl.NumberFormat('es-ES', {
    style: 'currency',
    currency: 'EUR',
  }).format(amount)
}
</script>

<template>
  <div class="max-w-7xl mx-auto space-y-6">
    <!-- Header -->
    <div>
      <h1 class="text-3xl font-bold flex items-center gap-2">
        <DollarSign class="h-8 w-8" />
        Dashboard de Pagos
      </h1>
      <p class="text-muted-foreground mt-1">
        Gestiona tus ingresos y transacciones
      </p>
    </div>

    <!-- No Account State -->
    <Card v-if="!hasAccount" class="border-dashed">
      <CardContent class="py-12 text-center">
        <CreditCard class="h-12 w-12 text-muted-foreground mx-auto mb-4" />
        <h3 class="text-lg font-semibold mb-2">No tienes cuenta conectada</h3>
        <p class="text-muted-foreground mb-4">
          Conecta tu cuenta con Stripe para empezar a recibir pagos
        </p>
        <Button @click="router.visit('/stripe/connect')">
          Conectar Cuenta
          <ArrowRight class="h-4 w-4 ml-2" />
        </Button>
      </CardContent>
    </Card>

    <!-- Dashboard Content -->
    <template v-else>
      <!-- Stats -->
      <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <Card>
          <CardHeader class="pb-3">
            <CardTitle class="text-sm font-medium">Ingresos Totales</CardTitle>
          </CardHeader>
          <CardContent>
            <div class="text-2xl font-bold flex items-center gap-2">
              <DollarSign class="h-5 w-5 text-green-600" />
              {{ formatCurrency(stats?.totalEarnings || 0) }}
            </div>
            <p class="text-xs text-muted-foreground">Acumulados</p>
          </CardContent>
        </Card>

        <Card>
          <CardHeader class="pb-3">
            <CardTitle class="text-sm font-medium">Fees Pendientes</CardTitle>
          </CardHeader>
          <CardContent>
            <div class="text-2xl font-bold text-yellow-600">
              {{ formatCurrency(stats?.pendingFees || 0) }}
            </div>
            <p class="text-xs text-muted-foreground">Por procesar</p>
          </CardContent>
        </Card>

        <Card>
          <CardHeader class="pb-3">
            <CardTitle class="text-sm font-medium">Fees Completados</CardTitle>
          </CardHeader>
          <CardContent>
            <div class="text-2xl font-bold text-green-600">
              {{ formatCurrency(stats?.completedFees || 0) }}
            </div>
            <p class="text-xs text-muted-foreground">Pagados</p>
          </CardContent>
        </Card>

        <Card>
          <CardHeader class="pb-3">
            <CardTitle class="text-sm font-medium">Transacciones</CardTitle>
          </CardHeader>
          <CardContent>
            <div class="text-2xl font-bold flex items-center gap-2">
              <CreditCard class="h-5 w-5" />
              {{ stats?.transactionCount || 0 }}
            </div>
            <p class="text-xs text-muted-foreground">Total</p>
          </CardContent>
        </Card>
      </div>

      <!-- Recent Transactions -->
      <Card>
        <CardHeader>
          <div class="flex items-center justify-between">
            <CardTitle>Transacciones Recientes</CardTitle>
            <Button variant="outline" @click="router.visit('/billing/invoices')">
              Ver Todas
            </Button>
          </div>
        </CardHeader>
        <CardContent>
          <div v-if="recentTransactions && recentTransactions.length > 0" class="space-y-3">
            <div
              v-for="transaction in recentTransactions"
              :key="transaction.id"
              class="flex items-center justify-between p-3 border rounded-lg"
            >
              <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center">
                  <DollarSign class="h-5 w-5 text-green-600" />
                </div>
                <div>
                  <p class="font-medium">{{ transaction.description }}</p>
                  <p class="text-sm text-muted-foreground">
                    {{ new Date(transaction.created_at).toLocaleDateString('es-ES') }}
                  </p>
                </div>
              </div>
              <div class="text-right">
                <p class="font-bold text-green-600">
                  {{ formatCurrency(transaction.amount) }}
                </p>
                <Badge v-if="transaction.processed_at" class="bg-green-500">
                  Procesado
                </Badge>
                <Badge v-else class="bg-yellow-500">
                  Pendiente
                </Badge>
              </div>
            </div>
          </div>
          <div v-else class="text-center py-8 text-muted-foreground">
            <TrendingUp class="h-8 w-8 mx-auto mb-2 opacity-50" />
            <p>No hay transacciones aún</p>
          </div>
        </CardContent>
      </Card>
    </template>
  </div>
</template>
