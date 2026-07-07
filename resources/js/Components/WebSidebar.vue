<script setup lang="ts">
interface WebSidebarProps {
  collapsed?: boolean;
}

defineProps<WebSidebarProps>();
defineEmits<{
  navigate: [];
}>();
</script>

<template>
    <div class="flex h-full w-full flex-col">
        <slot />
    </div>
</template>
