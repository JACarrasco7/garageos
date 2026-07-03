<script setup lang="ts">
import { ref } from 'vue'
import { useForm } from '@inertiajs/vue3'
import { router } from '@inertiajs/vue3'
import { Button } from '@/Components/ui/button'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/Components/ui/card'
import { Badge } from '@/Components/ui/badge'
import { Alert, AlertDescription } from '@/Components/ui/alert'
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/Components/ui/select'
import { CreditCard, CheckCircle, XCircle, ArrowRight, AlertTriangle } from 'lucide-vue-next'

interface StripeAccount {
  id: number
  stripe_account_id: string
  charges_enabled: boolean
  payouts_enabled: boolean
  country: string
  business_type: string
  onboarding_completed: boolean
}

interface Props {
  account: StripeAccount | null
  stripeClientId: string
}

const props = defineProps<Props>()

const onboardingForm = useForm({
  business_type: 'individual',
  country: 'ES',
})

const startOnboarding = () => {
  onboardingForm.post('/stripe/connect', {
    onSuccess: () => {
      router.visit('/stripe/connect')
    }
  })
}

const goToDashboard = () => {
  router.visit('/stripe/connect/dashboard')
}

const refreshOnboarding = () => {
  window.location.href = '/stripe/connect/refresh'
}
</script>

<template>
  <div class="max-w-4xl mx-auto space-y-6">
    <!-- Header -->
    <div>
      <h1 class="text-3xl font-bold flex items-center gap-2">
        <CreditCard class="h-8 w-8" />
        Conectar Cuenta Stripe
      </h1>
      <p class="text-muted-foreground mt-1">
        Configura tu cuenta para recibir pagos de clientes
      </p>
    </div>

    <!-- Account Status -->
    <Card v-if="account">
      <CardHeader>
        <CardTitle class="flex items-center gap-2">
          <CheckCircle v-if="account.onboarding_completed" class="h-5 w-5 text-green-500" />
          <XCircle v-else class="h-5 w-5 text-yellow-500" />
          Estado de la Cuenta
        </CardTitle>
      </CardHeader>
      <CardContent class="space-y-4">
        <div class="grid grid-cols-2 gap-4">
          <div class="flex items-center gap-2">
            <CheckCircle :class="account.charges_enabled ? 'text-green-500' : 'text-gray-400'" class="h-5 w-5" />
            <span>Cobros habilitados</span>
          </div>
          <div class="flex items-center gap-2">
            <CheckCircle :class="account.payouts_enabled ? 'text-green-500' : 'text-gray-400'" class="h-5 w-5" />
            <span>Pagos habilitados</span>
          </div>
        </div>

        <div class="text-sm text-muted-foreground">
          <p>ID de cuenta: {{ account.stripe_account_id }}</p>
          <p>País: {{ account.country }}</p>
          <p>Tipo: {{ account.business_type }}</p>
        </div>

        <div class="flex gap-2">
          <Button
            v-if="!account.onboarding_completed"
            variant="outline"
            @click="refreshOnboarding"
          >
            Completar Onboarding
          </Button>
          <Button
            v-if="account.onboarding_completed"
            variant="outline"
            @click="goToDashboard"
          >
            Ir al Dashboard Stripe
            <ArrowRight class="h-4 w-4 ml-2" />
          </Button>
          <Button
            v-if="account.onboarding_completed"
            @click="router.visit('/billing')"
          >
            Ver Ingresos
          </Button>
        </div>
      </CardContent>
    </Card>

    <!-- Onboarding Form -->
    <Card v-if="!account">
      <CardHeader>
        <CardTitle>Crear Cuenta</CardTitle>
        <CardDescription>
          Conecta tu cuenta con Stripe Connect para recibir pagos
        </CardDescription>
      </CardHeader>
      <CardContent class="space-y-4">
        <Alert v-if="stripeClientId === 'your_stripe_client_id'" type="warning">
          <AlertTriangle class="h-4 w-4" />
          <AlertDescription>
            Configura STRIPE_CLIENT_ID en el archivo .env
          </AlertDescription>
        </Alert>

        <div class="space-y-4">
          <div class="space-y-2">
            <label class="text-sm font-medium">Tipo de negocio</label>
            <Select v-model="onboardingForm.business_type">
              <SelectTrigger>
                <SelectValue placeholder="Selecciona el tipo" />
              </SelectTrigger>
              <SelectContent>
                <SelectItem value="individual">Individual</SelectItem>
                <SelectItem value="company">Empresa</SelectItem>
              </SelectContent>
            </Select>
          </div>

          <div class="space-y-2">
            <label class="text-sm font-medium">País</label>
            <Select v-model="onboardingForm.country">
              <SelectTrigger>
                <SelectValue placeholder="Selecciona el país" />
              </SelectTrigger>
              <SelectContent>
                <SelectItem value="ES">España</SelectItem>
                <SelectItem value="DE">Alemania</SelectItem>
                <SelectItem value="FR">Francia</SelectItem>
                <SelectItem value="IT">Italia</SelectItem>
                <SelectItem value="PT">Portugal</SelectItem>
              </SelectContent>
            </Select>
          </div>

          <Button
            @click="startOnboarding"
            :disabled="onboardingForm.processing || stripeClientId === 'your_stripe_client_id'"
            class="w-full"
          >
            {{ onboardingForm.processing ? 'Procesando...' : 'Conectar con Stripe' }}
            <ArrowRight class="h-4 w-4 ml-2" />
          </Button>
        </div>
      </CardContent>
    </Card>

    <!-- Info Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
      <Card>
        <CardHeader>
          <CardTitle>Comisión</CardTitle>
        </CardHeader>
        <CardContent>
          <p class="text-3xl font-bold">8%</p>
          <p class="text-sm text-muted-foreground">
            Cobra el 8% por cada transacción
          </p>
        </CardContent>
      </Card>

      <Card>
        <CardHeader>
          <CardTitle>Pagos</CardTitle>
        </CardHeader>
        <CardContent>
          <p class="text-3xl font-bold">Instantáneos</p>
          <p class="text-sm text-muted-foreground">
            Los clientes pagan directamente a tu cuenta
          </p>
        </CardContent>
      </Card>
    </div>
  </div>
</template>
