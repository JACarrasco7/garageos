<script setup lang="ts">
import { Link } from '@inertiajs/vue3'
import AppSidebarLayout from '@/layouts/app/AppSidebarLayout.vue'
import { Head } from '@inertiajs/vue3'
import { Card, CardContent, CardHeader, CardTitle } from '@/Components/ui/card'
import { Badge } from '@/Components/ui/badge'
import { Wrench } from 'lucide-vue-next'

interface Workshop {
  id: number
  name: string
  address: string | null
  city: string | null
  phone: string | null
  is_verified: boolean
}

defineProps<{
  workshops: Workshop[]
}>()
</script>

<template>
  <Head title="Mis Talleres" />

  <AppSidebarLayout>
    <template #header>
      Mis Talleres
    </template>

    <div class="max-w-4xl mx-auto space-y-6">
      <Card class="border-0 shadow-lg">
        <CardHeader>
          <CardTitle class="flex items-center gap-2">
            <Wrench class="h-5 w-5" />
            Talleres colaboradores
          </CardTitle>
        </CardHeader>
        <CardContent>
          <div v-if="!workshops?.length" class="text-center py-8 text-muted-foreground">
            <p>No tienes talleres registrados</p>
          </div>

          <div v-else class="space-y-4">
            <div
              v-for="workshop in workshops"
              :key="workshop.id"
              class="border border-border rounded-lg p-4"
            >
              <div class="flex justify-between items-start">
                <div>
                  <h4 class="font-medium">{{ workshop.name }}</h4>
                  <p class="text-sm text-muted-foreground">{{ workshop.address }}</p>
                  <p class="text-sm text-muted-foreground">{{ workshop.city }}</p>
                </div>
                <Badge :variant="workshop.is_verified ? 'default' : 'secondary'">
                  {{ workshop.is_verified ? 'Verificado' : 'Pendiente' }}
                </Badge>
              </div>
            </div>
          </div>
        </CardContent>
      </Card>
    </div>
  </AppSidebarLayout>
</template>
