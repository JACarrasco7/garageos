<script setup lang="ts">
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import { Link } from '@inertiajs/vue3';
import { Car } from 'lucide-vue-next';
</script>

<template>
    <div class="flex min-h-screen flex-col items-center justify-center bg-gradient-to-br from-background via-background to-muted/30 px-4">
        <div class="w-full max-w-md">
            <!-- Logo & Branding -->
            <div class="text-center mb-8">
                <Link href="/" class="inline-flex items-center gap-3 mb-4">
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-primary text-primary-foreground shadow-lg">
                        <Car class="h-6 w-6" />
                    </div>
                    <span class="text-2xl font-bold tracking-tight text-foreground">GarageOS</span>
                </Link>
                <p class="text-sm text-muted-foreground">
                    Gestiona tu inteligente
                </p>
            </div>

            <!-- Card -->
            <div class="bg-card rounded-2xl shadow-xl border border-border/50 p-8 backdrop-blur-sm">
                <slot />
            </div>

            <!-- Footer -->
            <p class="mt-8 text-center text-xs text-muted-foreground">
                &copy; 2026 GarageOS. Todos los derechos reservados.
            </p>
        </div>
    </div>
</template>
