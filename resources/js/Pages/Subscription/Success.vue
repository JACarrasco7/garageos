<script setup lang="ts">
import { Head } from '@inertiajs/vue3'
import WebLayout from '@/layouts/WebLayout.vue'
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/Components/ui/card'
import { Button } from '@/Components/ui/button'
import { Link } from '@inertiajs/vue3'
import { CheckCircle } from 'lucide-vue-next'
</script>

<template>
  <Head title="Suscripción activada" />
  <WebLayout>
    <template #header>
      ¡Suscripción activada!
    </template>
    <div class="max-w-md mx-auto space-y-6">
      <Card class="text-center">
        <CardHeader>
          <div class="flex justify-center mb-4">
            <CheckCircle class="h-16 w-16 text-green-500" />
          </div>
          <CardTitle>¡Gracias por suscribirte!</CardTitle>
          <CardDescription>Tu suscripción está activa y ya puedes disfrutar de todas las funcionalidades.</CardDescription>
        </CardHeader>
        <CardContent>
          <Button as-child class="w-full">
            <Link :href="route('dashboard')">Ir al Dashboard</Link>
          </Button>
        </CardContent>
      </Card>
    </div>
  </WebLayout>
</template>
