<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { Car, Shield, Calendar, Wrench, FileText, Users, BarChart3 } from 'lucide-vue-next';
import { Button } from '@/Components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/Components/ui/card';

defineProps<{
    canLogin?: boolean;
    canRegister?: boolean;
}>();
</script>

<template>
    <Head title="GarageOS - Gestión inteligente de tu vehículo" />
    <div class="min-h-screen bg-gradient-to-br from-background to-muted">
        <div class="container mx-auto px-4 py-12 max-w-7xl">
            <!-- Header -->
            <header class="flex items-center justify-between mb-12">
                <div class="flex items-center gap-3">
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-br from-primary to-primary/80 text-primary-foreground shadow-lg">
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
                <div class="absolute inset-0 -z-10">
                    <div class="absolute top-1/4 left-1/2 -translate-x-1/2 -translate-y-1/2 w-96 h-96 rounded-full bg-gradient-to-br from-primary/20 to-accent/20 blur-3xl"></div>
                    <div class="absolute top-1/3 right-1/4 w-64 h-64 rounded-full bg-gradient-to-br from-secondary/20 to-transparent blur-2xl"></div>
                </div>
                <h2 class="text-4xl md:text-6xl font-bold text-foreground mb-6">
                    Gestión inteligente de tu
                    <span class="text-primary block">vehículo</span>
                </h2>
                <p class="text-lg text-muted-foreground max-w-2xl mx-auto mb-8">
                    Controla mantenimientos, documentos, y todo lo relacionado con tu coche desde un solo lugar.
                </p>
                <div class="flex items-center justify-center gap-4">
                    <Button as-child size="lg" class="bg-gradient-to-r from-primary to-primary/90 hover:shadow-lg hover:shadow-primary/30 transition-all duration-200">
                        <Link v-if="canRegister" :href="route('register')">Comenzar gratis</Link>
                    </Button>
                    <Button as-child variant="outline" size="lg" class="border-primary/20 hover:bg-primary/5">
                        <Link v-if="$page.props.auth.user" :href="route('vehicles.index')">Ver mis vehículos</Link>
                    </Button>
                </div>
            </div>

            <!-- Features -->
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                <Card class="group hover:-translate-y-1 hover:shadow-lg transition-all duration-300 border-border/50">
                    <CardHeader>
                        <div class="h-12 w-12 rounded-lg bg-gradient-to-br from-primary/10 to-accent/10 flex items-center justify-center mb-2">
                            <Car class="h-6 w-6 text-primary" />
                        </div>
                        <CardTitle class="text-base">Gestión de vehículos</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <CardDescription class="text-sm text-muted-foreground">
                            Registra y gestiona todos tus vehículos con información detallada.
                        </CardDescription>
                    </CardContent>
                </Card>

                <Card class="group hover:-translate-y-1 hover:shadow-lg transition-all duration-300 border-border/50">
                    <CardHeader>
                        <div class="h-12 w-12 rounded-lg bg-gradient-to-br from-primary/10 to-accent/10 flex items-center justify-center mb-2">
                            <Wrench class="h-6 w-6 text-primary" />
                        </div>
                        <CardTitle class="text-base">Mantenimiento</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <CardDescription class="text-sm text-muted-foreground">
                            Programa y registra todos los mantenimientos de tu vehículo.
                        </CardDescription>
                    </CardContent>
                </Card>

                <Card class="group hover:-translate-y-1 hover:shadow-lg transition-all duration-300 border-border/50">
                    <CardHeader>
                        <div class="h-12 w-12 rounded-lg bg-gradient-to-br from-primary/10 to-accent/10 flex items-center justify-center mb-2">
                            <FileText class="h-6 w-6 text-primary" />
                        </div>
                        <CardTitle class="text-base">Documentos</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <CardDescription class="text-sm text-muted-foreground">
                            Almacena y organiza todos los documentos de tu vehículo.
                        </CardDescription>
                    </CardContent>
                </Card>

                <Card class="group hover:-translate-y-1 hover:shadow-lg transition-all duration-300 border-border/50">
                    <CardHeader>
                        <div class="h-12 w-12 rounded-lg bg-gradient-to-br from-accent/10 to-destructive/10 flex items-center justify-center mb-2">
                            <Shield class="h-6 w-6 text-accent" />
                        </div>
                        <CardTitle class="text-base">Suscripciones</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <CardDescription class="text-sm text-muted-foreground">
                            Planes adaptados a las necesidades de tu taller.
                        </CardDescription>
                    </CardContent>
                </Card>

                <Card class="group hover:-translate-y-1 hover:shadow-lg transition-all duration-300 border-border/50">
                    <CardHeader>
                        <div class="h-12 w-12 rounded-lg bg-gradient-to-br from-primary/10 to-accent/10 flex items-center justify-center mb-2">
                            <Users class="h-6 w-6 text-primary" />
                        </div>
                        <CardTitle class="text-base">Clientes</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <CardDescription class="text-sm text-muted-foreground">
                            Gestiona clientes y sus vehículos desde el panel de taller.
                        </CardDescription>
                    </CardContent>
                </Card>

                <Card class="group hover:-translate-y-1 hover:shadow-lg transition-all duration-300 border-border/50">
                    <CardHeader>
                        <div class="h-12 w-12 rounded-lg bg-gradient-to-br from-primary/10 to-accent/10 flex items-center justify-center mb-2">
                            <BarChart3 class="h-6 w-6 text-primary" />
                        </div>
                        <CardTitle class="text-base">Reportes</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <CardDescription class="text-sm text-muted-foreground">
                            Genera informes detallados de mantenimiento e ingresos.
                        </CardDescription>
                    </CardContent>
                </Card>
            </div>
        </div>
    </div>
</template>
