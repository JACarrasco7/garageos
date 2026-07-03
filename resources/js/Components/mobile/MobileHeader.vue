<script setup lang="ts">
import { ChevronLeft } from 'lucide-vue-next';

defineProps<{
  title: string;
  showBack?: boolean;
  backRoute?: string;
}>();

const emit = defineEmits<{
  (e: 'back'): void;
}>();
</script>

<template>
  <header class="sticky top-0 z-30 flex items-center justify-between border-b bg-card/95 backdrop-blur supports-[backdrop-filter]:bg-card/60 safe-area-inset-top">
    <div class="flex items-center">
      <button
        v-if="showBack"
        @click="emit('back')"
        class="flex items-center justify-center w-10 h-10 rounded-full hover:bg-accent transition-colors"
      >
        <slot name="back-icon">
          <ChevronLeft class="h-5 w-5" />
        </slot>
      </button>
    </div>
    <h1 class="text-lg font-semibold text-foreground">{{ title }}</h1>
    <div class="w-10" />
  </header>
</template>
