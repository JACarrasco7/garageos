<script setup lang="ts">
import { Plus } from 'lucide-vue-next';
import { Link } from '@inertiajs/vue3';
import { cn } from '@/lib/utils';

const props = defineProps<{
  class?: string;
  href?: string;
}>();
</script>

<template>
  <Link
    v-if="href"
    :href="href"
    :class="cn(
      'fixed bottom-[calc(56px+env(safe-area-inset-bottom))] right-4',
      'flex items-center justify-center w-14 h-14 rounded-full',
      'bg-primary text-primary-foreground shadow-lg',
      'hover:bg-primary/90 active:scale-95',
      'transition-all duration-200 touch-manipulation',
      'z-40',
      props.class
    )"
  >
    <Plus class="h-6 w-6" />
  </Link>
  <button
    v-else
    :class="cn(
      'fixed bottom-[calc(56px+env(safe-area-inset-bottom))] right-4',
      'flex items-center justify-center w-14 h-14 rounded-full',
      'bg-primary text-primary-foreground shadow-lg',
      'hover:bg-primary/90 active:scale-95',
      'transition-all duration-200 touch-manipulation',
      'z-40',
      props.class
    )"
  >
    <Plus class="h-6 w-6" />
  </button>
</template>
