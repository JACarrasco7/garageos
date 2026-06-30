<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { Car, Shield, Calendar, FileText, Wrench } from 'lucide-vue-next';
</script>

<template>
    <div class="flex min-h-screen flex-col items-center justify-center bg-linear-to-br from-background via-background to-muted/30 px-4">
        <div class="w-full max-w-md">
            <!-- Logo & Branding -->
            <div class="text-center mb-8">
                <Link href="/" class="inline-flex items-center gap-3 mb-4">
                    <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-linear-to-br from-primary to-primary/80 text-primary-foreground shadow-xl">
                        <Car class="h-7 w-7" />
                    </div>
                    <div class="text-left">
                        <span class="text-2xl font-bold tracking-tight text-foreground">GarageOS</span>
                        <p class="text-xs text-muted-foreground">Gestión inteligente de vehículos</p>
                    </div>
                </Link>
            </div>

            <!-- Card -->
            <div class="bg-card rounded-2xl shadow-2xl border border-border/50 p-8 backdrop-blur-sm">
                <slot />
            </div>

            <!-- Features -->
            <div class="mt-8 grid grid-cols-2 gap-3 text-center text-xs text-muted-foreground">
                <div class="flex items-center justify-center gap-1.5">
                    <Shield class="h-3 w-3 text-primary" />
                    <span>Seguro</span>
                </div>
                <div class="flex items-center justify-center gap-1.5">
                    <Calendar class="h-3 w-3 text-primary" />
                    <span>Programado</span>
                </div>
                <div class="flex items-center justify-center gap-1.5">
                    <FileText class="h-3 w-3 text-primary" />
                    <span>Documentos</span>
                </div>
                <div class="flex items-center justify-center gap-1.5">
                    <Wrench class="h-3 w-3 text-primary" />
                    <span>Mantenimiento</span>
                </div>
            </div>

            <!-- Footer -->
            <p class="mt-8 text-center text-xs text-muted-foreground">
                &copy; 2026 GarageOS. Todos los derechos reservados.
            </p>
        </div>
    </div>
</template>
