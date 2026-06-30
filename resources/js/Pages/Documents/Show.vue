<script setup lang="ts">
import { Link } from '@inertiajs/vue3'
import AppSidebarLayout from '@/layouts/app/AppSidebarLayout.vue'
import { Head } from '@inertiajs/vue3'
import { Card, CardContent, CardHeader, CardTitle } from '@/Components/ui/card'
import { Badge } from '@/Components/ui/badge'
import { Button } from '@/Components/ui/button'
import { ArrowLeft, FileText } from 'lucide-vue-next'

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

  <AppSidebarLayout>
    <template #header>
      {{ document.title }}
    </template>

    <div class="max-w-2xl mx-auto space-y-6">
      <Card class="border-0 shadow-lg">
        <CardHeader>
          <div class="flex justify-between items-start">
            <div>
              <CardTitle>{{ document.title }}</CardTitle>
              <p class="text-sm text-muted-foreground mt-1">{{ document.type }}</p>
            </div>
            <Badge variant="secondary">
              {{ (document.file_size / 1024).toFixed(1) }} KB
            </Badge>
          </div>
        </CardHeader>
        <CardContent class="space-y-4">
          <div class="border-t border-border pt-4">
            <p class="text-sm text-muted-foreground">Fecha: {{ document.document_date }}</p>
            <p class="text-sm text-muted-foreground">Vence: {{ document.expiry_date || 'N/A' }}</p>
            <p class="text-sm text-muted-foreground">Importe: {{ document.amount ? document.amount + ' €' : 'N/A' }}</p>
          </div>

          <div class="flex justify-end space-x-3 pt-4">
            <Button as-child>
              <a :href="`/storage/${document.file_path}`" target="_blank">
                <FileText class="w-4 h-4 mr-1" />
                Ver documento
              </a>
            </Button>
            <Button as-child variant="ghost">
              <Link :href="route('documents.index')">
                <ArrowLeft class="w-4 h-4 mr-1" />
                Volver
              </Link>
            </Button>
          </div>
        </CardContent>
      </Card>
    </div>
  </AppSidebarLayout>
</template>
