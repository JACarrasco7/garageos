<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { cn } from '@/lib/utils';

const show = ref(false);

onMounted(() => {
  setTimeout(() => {
    show.value = true;
  }, 10);
});
</script>

<template>
  <div
    :class="cn(
      'transition-all duration-300 ease-out',
      show ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-4'
    )"
  >
    <slot />
  </div>
</template>
