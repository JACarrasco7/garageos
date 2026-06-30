<script setup lang="ts">
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <div class="flex min-h-screen flex-col items-center justify-center bg-gradient-to-br from-slate-50 to-slate-100 dark:from-slate-900 dark:to-slate-800 px-4">
        <div class="w-full max-w-md">
            <div class="text-center mb-8">
                <Link href="/" class="inline-block">
                    <ApplicationLogo class="justify-center" />
                </Link>
                <p class="mt-2 text-sm text-muted-foreground">
                    Gestiona tu garaje de forma inteligente
                </p>
            </div>

            <div class="bg-card rounded-xl shadow-lg border border-border p-8">
                <slot />
            </div>

            <p class="mt-6 text-center text-xs text-muted-foreground">
                &copy; 2026 GarageOS. Todos los derechos reservados.
            </p>
        </div>
    </div>
</template>