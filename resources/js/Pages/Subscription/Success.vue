<script setup lang="ts">
import { Head } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/Components/ui/card'
import { Button } from '@/Components/ui/button'
import { Link } from '@inertiajs/vue3'
import { CheckCircle } from 'lucide-vue-next'
</script>

<template>
  <Head title="Suscripción activada" />
  <AuthenticatedLayout>
    <template #header>
      <h2 class="text-xl font-semibold leading-tight">¡Suscripción activada!</h2>
    </template>
    <div class="py-12">
      <div class="max-w-md mx-auto">
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
    </div>
  </AuthenticatedLayout>
</template>
