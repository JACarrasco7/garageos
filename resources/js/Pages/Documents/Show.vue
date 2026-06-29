<script setup lang="ts">
import { Link } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head } from '@inertiajs/vue3'
import Card from '@/Components/Card.vue'
import Badge from '@/Components/Badge.vue'

interface Document {
  id: number
  type: string
  title: string
  file_path: string
  file_size: number
  document_date: string | null
  expiry_date: string | null
  amount: number | null
}

defineProps<{
  document: Document
}>()
</script>

<template>
  <Head :title="document.title" />

  <AuthenticatedLayout>
    <template #header>
      <h2 class="text-xl font-semibold leading-tight">{{ document.title }}</h2>
    </template>

    <div class="py-12">
      <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
        <Card>
          <div class="space-y-4">
            <div class="flex justify-between items-start">
              <div>
                <h3 class="text-lg font-medium">{{ document.title }}</h3>
                <p class="text-sm text-gray-500">{{ document.type }}</p>
              </div>
              <Badge variant="info">
                {{ (document.file_size / 1024).toFixed(1) }} KB
              </Badge>
            </div>

            <div class="border-t pt-4">
              <p class="text-sm text-gray-500">Fecha: {{ document.document_date }}</p>
              <p class="text-sm text-gray-500">Vence: {{ document.expiry_date || 'N/A' }}</p>
              <p class="text-sm text-gray-500">Importe: {{ document.amount ? document.amount + ' €' : 'N/A' }}</p>
            </div>

            <div class="flex justify-end space-x-3 pt-4">
              <a
                :href="`/storage/${document.file_path}`"
                target="_blank"
                class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700"
              >
                Ver documento
              </a>
              <Link
                :href="route('documents.index')"
                class="px-4 py-2 text-gray-600 hover:underline"
              >
                Volver
              </Link>
            </div>
          </div>
        </Card>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
