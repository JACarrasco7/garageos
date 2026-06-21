<script setup lang="ts">
interface Props {
  striped?: boolean
  hoverable?: boolean
  compact?: boolean
}

withDefaults(defineProps<Props>(), {
  striped: false,
  hoverable: true,
  compact: false
})
</script>

<template>
  <div class="overflow-x-auto">
    <table
      class="min-w-full divide-y divide-gray-200 dark:divide-gray-700"
      :class="{ 'text-sm': compact }"
    >
      <thead class="bg-gray-50 dark:bg-gray-800/50">
        <tr>
          <slot name="header" />
        </tr>
      </thead>
      <tbody
        class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700"
        :class="{ 'divide-y': !striped }"
      >
        <slot name="body" />
      </tbody>
    </table>
  </div>
</template>