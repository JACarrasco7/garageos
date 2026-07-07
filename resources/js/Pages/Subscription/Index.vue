<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3'
import WebLayout from '@/layouts/WebLayout.vue'
import { Card, CardContent, CardHeader, CardTitle, CardFooter } from '@/Components/ui/card'
import { Button } from '@/Components/ui/button'
import { Badge } from '@/Components/ui/badge'
import { Shield, Car, Check, CreditCard } from 'lucide-vue-next'

defineProps<{
  plans: Array<{
    id: string
    name: string
    price: number
    features: string[]
    vehicle_limit: number | null
  }>
  subscribed?: boolean
  subscription?: any
}>()

function cancel() {
  router.post(route('subscription.cancel'))
}

function resume() {
  router.post(route('subscription.resume'))
}
</script>

<template>
  <Head title="Planes" />

  <WebLayout>
    <template #header>
      Planes
    </template>

    <div class="max-w-4xl space-y-6">
      <!-- Plans Grid -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <Card
          v-for="plan in plans"
          :key="plan.id"
          class="glass-surface border-0 transition-shadow"
        >
          <CardHeader class="text-center pb-3">
            <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-xl bg-primary/10 mb-3">
              <Shield class="h-6 w-6 text-primary" />
            </div>
            <CardTitle class="text-xl">{{ plan.name }}</CardTitle>
          </CardHeader>
          <CardContent class="text-center">
            <div class="mb-4">
              <span class="text-3xl font-bold text-foreground">{{ (plan.price / 100).toFixed(2) }} €</span>
              <span class="text-sm text-muted-foreground">/mes</span>
            </div>
            <ul class="space-y-2 text-sm">
              <li
                v-for="feature in plan.features"
                :key="feature"
                class="flex items-center justify-center gap-2 text-muted-foreground"
              >
                <Check class="h-4 w-4 text-green-500" />
                {{ feature }}
              </li>
            </ul>
            <p v-if="plan.vehicle_limit" class="text-xs text-muted-foreground mt-3">
              Hasta {{ plan.vehicle_limit }} vehículos
            </p>
          </CardContent>
          <CardFooter class="justify-center pt-3">
            <Button as-child class="w-full">
              <Link :href="route('subscription.checkout', plan.id)">
                <CreditCard class="mr-2 h-4 w-4" />
                Suscribirse
              </Link>
            </Button>
          </CardFooter>
        </Card>
      </div>

      <!-- Current subscription status -->
      <Card v-if="subscribed" class="glass-surface border-0">
        <CardHeader class="pb-3">
          <CardTitle class="flex items-center gap-2 text-lg font-semibold">
            <Shield class="h-5 w-5" />
            Tu suscripción actual
          </CardTitle>
        </CardHeader>
        <CardContent>
          <div class="flex items-center justify-between">
            <div>
              <p class="font-medium text-foreground">Plan {{ subscription?.stripe_price ? 'activo' : '' }}</p>
              <p class="text-sm text-muted-foreground">
                {{ subscription?.on_grace_period ? 'Cancela al final del período' : 'Activa' }}
              </p>
            </div>
            <form v-if="subscription && !subscription.on_grace_period" @submit.prevent="cancel">
              <Button variant="destructive" size="sm">Cancelar</Button>
            </form>
            <form v-else-if="subscription?.on_grace_period" @submit.prevent="resume">
              <Button variant="default" size="sm">Reactivar</Button>
            </form>
          </div>
        </CardContent>
      </Card>
    </div>
  </WebLayout>
</template>
