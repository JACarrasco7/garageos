<script setup lang="ts">
import { computed } from 'vue'
import AffiliateProductCard from './AffiliateProductCard.vue'
import { Package } from 'lucide-vue-next'

interface AffiliateLink {
  id: number
  provider: string
  product_name: string
  product_sku: string | null
  affiliate_url: string
  image_url: string | null
  price: number | null
  currency: string
}

const props = defineProps<{
  links: AffiliateLink[]
  title?: string
  emptyMessage?: string
}>()

const hasLinks = computed(() => props.links?.length > 0)
</script>

<template>
  <div>
    <h3 v-if="title" class="text-lg font-semibold mb-4">{{ title }}</h3>

    <div v-if="hasLinks" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
      <AffiliateProductCard v-for="link in links" :key="link.id" :link="link" />
    </div>

    <div v-else class="text-center py-12 text-muted-foreground">
      <Package class="mx-auto h-12 w-12 mb-3 opacity-50" />
      <p>{{ emptyMessage || 'No hay productos recomendados disponibles' }}</p>
    </div>
  </div>
</template>
