<script setup lang="ts">
interface Props {
  title?: string
  subtitle?: string
  padding?: boolean
  hover?: boolean
}

defineProps<Props>()
</script>

<template>
  <div
    class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg"
    :class="{ 'p-6': padding, 'hover:shadow-md transition-shadow': hover }"
  >
    <div v-if="title" class="border-b border-gray-200 dark:border-gray-700 pb-3 mb-4">
      <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">{{ title }}</h3>
      <p v-if="subtitle" class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ subtitle }}</p>
    </div>
    <slot />
  </div>
</template>