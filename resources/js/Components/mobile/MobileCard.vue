<script setup lang="ts">
import { cn } from '@/lib/utils';

const props = defineProps<{
  class?: string;
}>();
</script>

<template>
  <div
    :class="cn(
      'rounded-2xl border bg-card shadow-sm hover:shadow-md transition-shadow duration-200',
      'active:scale-[0.98] transition-transform',
      'touch-manipulation',
      'min-h-[44px]',
      props.class
    )"
  >
    <slot />
  </div>
</template>
