<script setup lang="ts">
import { Card, CardContent, CardFooter } from '@/Components/ui/card'
import { Button } from '@/Components/ui/button'
import { Badge } from '@/Components/ui/badge'
import { ExternalLink } from 'lucide-vue-next'

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

defineProps<{
  link: AffiliateLink
}>()

const providerVariant = (provider: string): 'default' | 'outline' | 'secondary' => {
  const map: Record<string, 'default' | 'outline' | 'secondary'> = {
    autodoc: 'default',
    amazon: 'outline',
    recambiosviaweb: 'secondary',
  }
  return map[provider] ?? 'secondary'
}
</script>

<template>
  <Card class="overflow-hidden">
    <div v-if="link.image_url" class="h-32 bg-muted flex items-center justify-center overflow-hidden">
      <img :src="link.image_url" :alt="link.product_name" class="w-full h-full object-cover" />
    </div>
    <CardContent class="p-4">
      <div class="flex items-start justify-between gap-2 mb-2">
        <h4 class="font-medium text-sm leading-tight line-clamp-2">{{ link.product_name }}</h4>
        <Badge :variant="providerVariant(link.provider)" class="shrink-0 text-xs">
          {{ link.provider }}
        </Badge>
      </div>
      <p v-if="link.price" class="text-lg font-bold text-foreground">
        {{ Number(link.price).toFixed(2) }} {{ link.currency }}
      </p>
      <p v-else class="text-sm text-muted-foreground">Precio no disponible</p>
    </CardContent>
    <CardFooter class="p-4 pt-0">
      <Button as-child variant="default" size="sm" class="w-full">
        <a :href="link.affiliate_url" target="_blank" rel="noopener noreferrer">
          <ExternalLink class="mr-2 h-4 w-4" />
          Comprar
        </a>
      </Button>
    </CardFooter>
  </Card>
</template>
