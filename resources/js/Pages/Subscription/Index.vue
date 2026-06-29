<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, Link } from '@inertiajs/vue3'
import { Card, CardContent, CardHeader, CardTitle, CardFooter } from '@/Components/ui/card'
import { Button } from '@/Components/ui/button'

defineProps<{
  plans: Array<{
    id: string
    name: string
    price: number
    features: string[]
  }>
}>()
</script>

<template>
  <Head title="Suscripciones" />

  <AuthenticatedLayout>
    <template #header>
      <h2 class="text-xl font-semibold leading-tight">Suscripciones</h2>
    </template>

    <div class="py-12">
      <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <Card v-for="plan in plans" :key="plan.id">
            <CardHeader class="text-center">
              <CardTitle>{{ plan.name }}</CardTitle>
            </CardHeader>
            <CardContent class="text-center">
              <p class="text-3xl font-bold">{{ plan.price }} €/mes</p>
              <ul class="mt-4 space-y-2 text-sm text-muted-foreground">
                <li v-for="feature in plan.features" :key="feature">
                  ✓ {{ feature }}
                </li>
              </ul>
            </CardContent>
            <CardFooter class="justify-center">
              <Button as-child>
                <Link :href="route('subscription.checkout', plan.id)">
                  Suscribirse
                </Link>
              </Button>
            </CardFooter>
          </Card>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
