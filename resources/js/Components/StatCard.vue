<script setup lang="ts">
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';
import { cn } from '@/lib/utils';
import { ArrowRight } from 'lucide-vue-next';

interface Trend {
    value: number;
    label?: string;
}

interface Props {
    title: string;
    value: string | number;
    icon: unknown;
    accent?: 'primary' | 'accent' | 'destructive' | 'warning' | 'success';
    description?: string;
    href?: string;
    trend?: Trend;
    pulse?: boolean;
    class?: string;
}

const props = withDefaults(defineProps<Props>(), { accent: 'primary' });

const accentBg = computed(() => {
    const map: Record<NonNullable<Props['accent']>, string> = {
        primary: 'bg-gradient-to-br from-primary to-primary/85',
        accent: 'bg-gradient-to-br from-accent to-accent/85',
        destructive: 'bg-gradient-to-br from-destructive to-destructive/85',
        warning: 'bg-gradient-to-br from-amber-500 to-amber-600',
        success: 'bg-gradient-to-br from-emerald-500 to-emerald-600',
    };
    return map[props.accent];
});

const accentText = computed(() => {
    const map: Record<NonNullable<Props['accent']>, string> = {
        primary: 'text-primary',
        accent: 'text-accent',
        destructive: 'text-destructive',
        warning: 'text-amber-600',
        success: 'text-emerald-600',
    };
    return map[props.accent];
});

const cardClasses = computed(() =>
    cn(
        'group relative overflow-hidden rounded-2xl border backdrop-blur-xl p-6 transition-all duration-300',
        'bg-card/80 border-border shadow-sm hover:bg-card hover:shadow-md',
        'glass-kpi',
        props.href && 'block cursor-pointer',
        props.class,
    ),
);
</script>

<template>
    <Link v-if="href" :href="href" :class="cardClasses">
        <div
            :class="cn(
                'absolute -top-12 -right-12 h-32 w-32 rounded-full blur-3xl opacity-40 transition-opacity duration-500',
                accentBg,
                'group-hover:opacity-60',
            )"
        />

        <div class="relative flex items-start justify-between gap-4">
            <div class="min-w-0 flex-1 space-y-2">
                <p :class="cn('text-xs font-semibold uppercase tracking-wider', 'text-muted-foreground')">
                    {{ title }}
                </p>
                <p
                    :class="cn(
                        'text-3xl font-bold tracking-tight text-foreground',
                        pulse && 'badge-pulse',
                    )"
                >
                    {{ value }}
                </p>
                <div v-if="description || trend" class="flex items-center gap-3 pt-1">
                    <p
                        v-if="trend"
                        :class="cn(
                            'inline-flex items-center gap-1 text-xs font-semibold',
                            trend.value >= 0 ? 'text-emerald-600' : 'text-red-600',
                        )"
                    >
                        <span>{{ trend.value >= 0 ? '↑' : '↓' }}</span>
                        <span>{{ Math.abs(trend.value) }}%</span>
                        <span v-if="trend.label" class="font-normal text-muted-foreground">{{ trend.label }}</span>
                    </p>
                    <p v-if="description" class="text-xs text-muted-foreground">
                        {{ description }}
                    </p>
                </div>
            </div>

            <div
                :class="cn(
                    'flex h-12 w-12 shrink-0 items-center justify-center rounded-xl text-primary-foreground shadow-lg transition-transform duration-300 group-hover:scale-110 group-hover:-rotate-3',
                    accentBg,
                )"
            >
                <component :is="icon" class="h-5 w-5" />
            </div>
        </div>

        <span
            :class="cn(
                'mt-4 inline-flex items-center gap-1.5 text-xs font-semibold transition-all duration-200 opacity-0 -translate-x-1 group-hover:opacity-100 group-hover:translate-x-0',
                accentText,
            )"
        >
            Ver detalle
            <ArrowRight class="h-3.5 w-3.5" />
        </span>
    </Link>

    <div v-else :class="cardClasses">
        <div
            :class="cn(
                'absolute -top-12 -right-12 h-32 w-32 rounded-full blur-3xl opacity-40 transition-opacity duration-500',
                accentBg,
                'group-hover:opacity-60',
            )"
        />

        <div class="relative flex items-start justify-between gap-4">
            <div class="min-w-0 flex-1 space-y-2">
                <p :class="cn('text-xs font-semibold uppercase tracking-wider', 'text-muted-foreground')">
                    {{ title }}
                </p>
                <p
                    :class="cn(
                        'text-3xl font-bold tracking-tight text-foreground',
                        pulse && 'badge-pulse',
                    )"
                >
                    {{ value }}
                </p>
                <div v-if="description || trend" class="flex items-center gap-3 pt-1">
                    <p
                        v-if="trend"
                        :class="cn(
                            'inline-flex items-center gap-1 text-xs font-semibold',
                            trend.value >= 0 ? 'text-emerald-600' : 'text-red-600',
                        )"
                    >
                        <span>{{ trend.value >= 0 ? '↑' : '↓' }}</span>
                        <span>{{ Math.abs(trend.value) }}%</span>
                        <span v-if="trend.label" class="font-normal text-muted-foreground">{{ trend.label }}</span>
                    </p>
                    <p v-if="description" class="text-xs text-muted-foreground">
                        {{ description }}
                    </p>
                </div>
            </div>

            <div
                :class="cn(
                    'flex h-12 w-12 shrink-0 items-center justify-center rounded-xl text-primary-foreground shadow-lg transition-transform duration-300 group-hover:scale-110 group-hover:-rotate-3',
                    accentBg,
                )"
            >
                <component :is="icon" class="h-5 w-5" />
            </div>
        </div>
    </div>
</template>
