<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { Car, Shield, Calendar, Wrench, FileText, Users, BarChart3 } from 'lucide-vue-next';
import { Button } from '@/Components/ui/button';
import PageCard from '@/Components/PageCard.vue';
import { CardContent, CardHeader, CardTitle } from '@/Components/ui/card';

defineProps<{
    canLogin?: boolean;
    canRegister?: boolean;
}>();
</script>

<template>
    <Head title="GarageOS - Gestión inteligente de tu vehículo" />
    <div class="min-h-screen bg-background">
        <div class="container mx-auto px-4 py-12 max-w-7xl">
            <!-- Header -->
            <header class="flex items-center justify-between mb-12">
                <div class="flex items-center gap-3">
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-linear-to-br from-primary to-primary/80 text-primary-foreground shadow-md">
                        <Car class="h-7 w-7" />
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-foreground">GarageOS</h1>
                        <p class="text-sm text-muted-foreground">Gestión inteligente</p>
                    </div>
                </div>
                <nav v-if="canLogin" class="flex items-center gap-2">
                    <template v-if="$page.props.auth.user">
                        <Button as-child variant="ghost">
                            <Link :href="route('dashboard')">Dashboard</Link>
                        </Button>
                    </template>
                    <template v-else>
                        <Button as-child variant="ghost">
                            <Link :href="route('login')">Iniciar sesión</Link>
                        </Button>
                        <Button as-child v-if="canRegister">
                            <Link :href="route('register')">Registrarse</Link>
                        </Button>
                    </template>
                </nav>
            </header>

            <!-- Hero -->
            <div class="text-center mb-16 relative">
                <div class="absolute inset-0 -z-10 opacity-20">
                    <div class="absolute top-1/4 left-1/2 -translate-x-1/2 -translate-y-1/2 w-96 h-96 rounded-full bg-primary blur-3xl"></div>
                </div>
                <h2 class="text-4xl md:text-6xl font-bold text-foreground mb-6">
                    Gestión inteligente de tu
                    <span class="text-primary block">vehículo</span>
                </h2>
                <p class="text-lg text-muted-foreground max-w-2xl mx-auto mb-8">
                    Controla mantenimientos, documentos, y todo lo relacionado con tu coche desde un solo lugar.
                </p>
                <div class="flex items-center justify-center gap-4">
                    <Button as-child size="lg" class="bg-primary hover:shadow-md transition-shadow">
                        <Link v-if="canRegister" :href="route('register')">Comenzar gratis</Link>
                    </Button>
                    <Button as-child variant="outline" size="lg" class="border-border/50 hover:bg-accent/5">
                        <Link v-if="$page.props.auth.user" :href="route('vehicles.index')">Ver mis vehículos</Link>
                    </Button>
                </div>
            </div>

            <!-- Features -->
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                <PageCard>
                    <template #title>
                      <CardHeader class="pb-2">
                        <div class="h-12 w-12 rounded-lg bg-primary/10 flex items-center justify-center mb-2">
                            <Car class="h-6 w-6 text-primary" />
                        </div>
                        <CardTitle class="text-base">Gestión de vehículos</CardTitle>
                      </CardHeader>
                    </template>

                    <CardContent>
                      <p class="text-sm text-muted-foreground">
                        Registra y gestiona todos tus vehículos con información detallada.
                      </p>
                    </CardContent>
                </PageCard>

                <PageCard>
                    <template #title>
                      <CardHeader class="pb-2">
                        <div class="h-12 w-12 rounded-lg bg-primary/10 flex items-center justify-center mb-2">
                            <Wrench class="h-6 w-6 text-primary" />
                        </div>
                        <CardTitle class="text-base">Mantenimiento</CardTitle>
                      </CardHeader>
                    </template>

                    <CardContent>
                      <p class="text-sm text-muted-foreground">
                        Programa y registra todos los mantenimientos de tu vehículo.
                      </p>
                    </CardContent>
                </PageCard>

                <PageCard>
                    <template #title>
                      <CardHeader class="pb-2">
                        <div class="h-12 w-12 rounded-lg bg-primary/10 flex items-center justify-center mb-2">
                            <FileText class="h-6 w-6 text-primary" />
                        </div>
                        <CardTitle class="text-base">Documentos</CardTitle>
                      </CardHeader>
                    </template>

                    <CardContent>
                      <p class="text-sm text-muted-foreground">
                        Almacena y organiza todos los documentos de tu vehículo.
                      </p>
                    </CardContent>
                </PageCard>

                <PageCard>
                    <template #title>
                      <CardHeader class="pb-2">
                        <div class="h-12 w-12 rounded-lg bg-accent/10 flex items-center justify-center mb-2">
                            <Shield class="h-6 w-6 text-accent" />
                        </div>
                        <CardTitle class="text-base">Suscripciones</CardTitle>
                      </CardHeader>
                    </template>

                    <CardContent>
                      <p class="text-sm text-muted-foreground">
                        Planes adaptados a las necesidades de tu taller.
                      </p>
                    </CardContent>
                </PageCard>

                <PageCard>
                    <template #title>
                      <CardHeader class="pb-2">
                        <div class="h-12 w-12 rounded-lg bg-primary/10 flex items-center justify-center mb-2">
                            <Users class="h-6 w-6 text-primary" />
                        </div>
                        <CardTitle class="text-base">Clientes</CardTitle>
                      </CardHeader>
                    </template>

                    <CardContent>
                      <p class="text-sm text-muted-foreground">
                        Gestiona clientes y sus vehículos desde el panel de taller.
                      </p>
                    </CardContent>
                </PageCard>

                <PageCard>
                    <template #title>
                      <CardHeader class="pb-2">
                        <div class="h-12 w-12 rounded-lg bg-primary/10 flex items-center justify-center mb-2">
                            <BarChart3 class="h-6 w-6 text-primary" />
                        </div>
                        <CardTitle class="text-base">Reportes</CardTitle>
                      </CardHeader>
                    </template>

                    <CardContent>
                      <p class="text-sm text-muted-foreground">
                        Genera informes detallados de mantenimiento e ingresos.
                      </p>
                    </CardContent>
                </PageCard>
            </div>
        </div>
    </div>
</template>
