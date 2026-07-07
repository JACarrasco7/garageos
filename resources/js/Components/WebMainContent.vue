<script setup lang="ts">
defineSlots<{
    default: [];
}>();
</script>

<template>
    <main class="flex-1 min-h-0 overflow-auto p-6 md:p-8 lg:p-10">
        <slot />
    </main>
</template>
