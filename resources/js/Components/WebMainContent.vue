<script setup lang="ts">
defineSlots<{
    default: [];
}>();
</script>

<template>
    <main class="flex-1 min-h-0 rounded-2xl border backdrop-blur-xl flex flex-col overflow-hidden bg-card/35 border-white/12 shadow-lg">
        <div class="flex-1 overflow-auto p-6 md:p-8 lg:p-10">
            <slot />
        </div>
    </main>
</template>
