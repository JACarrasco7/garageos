<script setup lang="ts">
import { ref } from 'vue';
import { useInfiniteScroll } from '@vueuse/core';
import { cn } from '@/lib/utils';

const props = defineProps<{
  items: any[];
}>();

const emit = defineEmits<{
  (e: 'load-more'): void;
}>();

const loading = ref(false);

const loadMore = async () => {
  if (loading.value) return;
  loading.value = true;
  emit('load-more');
  loading.value = false;
};

useInfiniteScroll(
  () => document.querySelector('.mobile-container') as HTMLElement,
  loadMore,
  { distance: 100 }
);
</script>

<template>
  <div class="mobile-container">
    <slot />
    <div v-if="loading" class="p-4 text-center">
      <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-primary mx-auto"></div>
    </div>
  </div>
</template>
