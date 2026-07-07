<script setup lang="ts">
defineSlots<{
    default: [];
    actions: [];
}>();
</script>

<template>
    <header class="flex items-center justify-between p-4 rounded-2xl border backdrop-blur-xl bg-card/35 border-white/12 shadow-lg mb-3">
        <div class="flex items-center gap-4">
            <slot />
        </div>
        <div class="flex items-center gap-2">
            <slot name="actions" />
        </div>
    </header>
</template>