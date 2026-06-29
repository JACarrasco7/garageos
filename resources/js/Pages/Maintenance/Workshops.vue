<script setup lang="ts">
import { Link } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head } from '@inertiajs/vue3'
import Card from '@/Components/Card.vue'
import Badge from '@/Components/Badge.vue'

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

  <AuthenticatedLayout>
    <template #header>
      <h2 class="text-xl font-semibold leading-tight">Mis Talleres</h2>
    </template>

    <div class="py-12">
      <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <Card title="Talleres colaboradores">
          <div v-if="!workshops?.length" class="text-center py-8 text-gray-500">
            <p>No tienes talleres registrados</p>
          </div>

          <div v-else class="space-y-4">
            <div
              v-for="workshop in workshops"
              :key="workshop.id"
              class="border dark:border-gray-700 rounded-lg p-4"
            >
              <div class="flex justify-between items-start">
                <div>
                  <h4 class="font-medium">{{ workshop.name }}</h4>
                  <p class="text-sm text-gray-500">{{ workshop.address }}</p>
                  <p class="text-sm text-gray-500">{{ workshop.city }}</p>
                </div>
                <Badge :variant="workshop.is_verified ? 'success' : 'gray'">
                  {{ workshop.is_verified ? 'Verificado' : 'Pendiente' }}
                </Badge>
              </div>
            </div>
          </div>
        </Card>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
