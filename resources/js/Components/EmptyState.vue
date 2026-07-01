<script setup lang="ts">
import { cn } from '@/lib/utils';
import { Link } from '@inertiajs/vue3';
import { Car } from 'lucide-vue-next';

interface Props {
    title?: string;
    description?: string;
    actionLabel?: string;
    actionHref?: string;
    icon?: any;
    compact?: boolean;
}

withDefaults(defineProps<Props>(), {
    icon: Car,
    compact: false,
});
</script>

<template>
    <div
        :class="cn(
            'relative overflow-hidden rounded-2xl border text-center backdrop-blur-xl',
            compact ? 'p-8' : 'p-12',
            'bg-card/80 border-border shadow-sm',
        )"
    >
        <div class="pointer-events-none absolute inset-0 opacity-30 bg-animated-gradient-subtle" />

        <div class="relative flex flex-col items-center gap-4">
            <div
                :class="cn(
                    'flex items-center justify-center rounded-2xl',
                    compact ? 'h-14 w-14' : 'h-20 w-20',
                    'bg-gradient-to-br from-primary/20 to-accent/10 text-primary ring-1 ring-border',
                )"
            >
                <component :is="icon" :class="compact ? 'h-7 w-7' : 'h-10 w-10'" />
            </div>

            <div class="space-y-1.5 max-w-sm">
                <h3
                    :class="cn(
                        'font-bold tracking-tight',
                        compact ? 'text-base' : 'text-lg',
                        'text-foreground',
                    )"
                >
                    {{ title || 'Sin resultados' }}
                </h3>
                <p
                    v-if="description"
                    :class="cn(
                        'text-sm',
                        'text-muted-foreground',
                    )"
                >
                    {{ description || 'No se encontraron elementos para mostrar.' }}
                </p>
            </div>

            <Link
                v-if="actionLabel && actionHref"
                :href="actionHref"
                :class="cn(
                    'inline-flex items-center gap-2 rounded-lg px-4 py-2 text-sm font-semibold shadow-lg transition-all duration-300 hover:scale-105 active:scale-95',
                    'bg-gradient-to-r from-primary to-primary/85 text-primary-foreground shadow-primary/30',
                )"
            >
                {{ actionLabel }}
            </Link>
            <slot v-else-if="$slots.action" name="action" />
        </div>
    </div>
</template>
