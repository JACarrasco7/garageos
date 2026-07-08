<script setup lang="ts">
defineSlots<{
    default: [];
}>();
</script>

<template>
    <main class="glass-panel rounded-2xl p-6 md:p-8 border-white/20 shadow-sm h-full overflow-y-auto">
            <slot />
    </main>
</template>
