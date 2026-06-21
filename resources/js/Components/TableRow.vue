<script setup lang="ts">
interface Props {
  hoverable?: boolean
  striped?: boolean
}

withDefaults(defineProps<Props>(), {
  hoverable: true,
  striped: false
})
</script>

<template>
  <tr
    :class="{
      'hover:bg-gray-50 dark:hover:bg-gray-700/50': hoverable,
      'bg-gray-50/50 dark:bg-gray-700/25': striped
    }"
  >
    <slot />
  </tr>
</template>