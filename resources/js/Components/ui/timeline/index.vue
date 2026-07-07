<script setup lang="ts">
interface TimelineItem {
  id: number | string
  status: string
  location?: string
  note?: string
  occurred_at: string
  photo_path?: string
}

interface Props {
  items: TimelineItem[]
}

defineProps<Props>()

const statusLabels: Record<string, string> = {
  pickup_scheduled: 'Recogida programada',
  picked_up: 'Vehículo recogido',
  in_transit: 'En tránsito',
  customs: 'En aduana',
  delivered: 'Entregado',
}
</script>

<template>
  <div class="space-y-4">
    <div v-for="(item, index) in items" :key="item.id" class="flex gap-4">
      <div class="flex flex-col items-center">
        <div class="w-3 h-3 rounded-full bg-primary" />
        <div v-if="index < items.length - 1" class="w-px h-12 bg-border mt-2" />
      </div>
      <div class="flex-1 pb-8">
        <p class="font-medium">{{ statusLabels[item.status] || item.status }}</p>
        <p v-if="item.location" class="text-sm text-muted-foreground">{{ item.location }}</p>
        <p v-if="item.note" class="text-sm mt-1">{{ item.note }}</p>
        <p class="text-xs text-muted-foreground mt-1">
          {{ new Date(item.occurred_at).toLocaleString() }}
        </p>
      </div>
    </div>
  </div>
</template>
