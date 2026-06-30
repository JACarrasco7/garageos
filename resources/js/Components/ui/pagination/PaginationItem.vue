<script setup lang="ts">
import type { HTMLAttributes } from "vue"
import { cn } from "@/lib/utils"

const props = defineProps<{
  class?: HTMLAttributes["class"]
  isActive?: boolean
}>()
</script>

<template>
  <li :class="cn('list-none', props.class)">
    <slot />
  </li>
</template>