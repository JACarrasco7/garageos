<script setup lang="ts">
defineSlots<{
    default: [];
    actions: [];
}>();
</script>

<template>
    <header class="flex items-center justify-between p-4 mb-3">
        <div class="flex items-center gap-4">
            <h1 class="text-lg font-semibold tracking-tight text-foreground"><slot /></h1>
        </div>
        <div class="flex items-center gap-2">
            <slot name="actions" />
        </div>
    </header>
</template>
