<script setup lang="ts">
import { Button } from '@/Components/ui/button';

defineProps<{
    title?: string;
    description?: string;
    actionLabel?: string;
    icon?: string;
}>();

defineEmits<{
    action: [];
}>();
</script>

<template>
    <div class="flex flex-col items-center justify-center py-12 px-4 text-center">
        <div class="mx-auto flex h-20 w-20 items-center justify-center rounded-2xl bg-muted mb-4">
            <component :is="icon || 'Car'" class="h-8 w-8 text-muted-foreground" />
        </div>
        <h3 class="font-semibold text-foreground mb-1">{{ title || 'Sin resultados' }}</h3>
        <p class="text-sm text-muted-foreground mb-4 max-w-sm">
            {{ description || 'No se encontraron elementos para mostrar.' }}
        </p>
        <Button v-if="$slots.action" as-child>
            <slot name="action" />
        </Button>
    </div>
</template>