<script setup lang="ts">
const year = new Date().getFullYear()
</script>

<template>
  <footer class="bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700 py-6 mt-auto">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <p class="text-center text-sm text-gray-500 dark:text-gray-400">
        © {{ year }} GarageOS. Todos los derechos reservados.
      </p>
    </div>
  </footer>
</template>