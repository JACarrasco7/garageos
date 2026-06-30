<script setup lang="ts">
import type { HTMLAttributes } from "vue"
import { cn } from "@/lib/utils"

const props = defineProps<{
  class?: HTMLAttributes["class"]
  total?: number
  perPage?: number
  currentPage?: number
}>()

const emit = defineEmits<{
  (e: "update:page", page: number): void
}>()

const pages = computed(() => {
  if (!props.total || !props.perPage) return []
  const totalPages = Math.ceil(props.total / props.perPage)
  return Array.from({ length: totalPages }, (_, i) => i + 1)
})

const goToPage = (page: number) => {
  emit("update:page", page)
}
</script>

<template>
  <nav
    v-if="pages.length > 1"
    :class="cn('flex items-center justify-center space-x-2', props.class)"
  >
    <slot />
  </nav>
</template>