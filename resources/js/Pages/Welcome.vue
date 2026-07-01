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
            <div class="text-center mb-16">
                <h2 class="text-4xl md:text-6xl font-bold text-foreground mb-6">
                    Gestión inteligente de tu
                    <span class="text-primary block">vehículo</span>
                </h2>
                <p class="text-lg text-muted-foreground max-w-2xl mx-auto mb-8">
                    Controla mantenimientos, documentos, y todo lo relacionado con tu coche desde un solo lugar.
                </p>
                <div class="flex items-center justify-center gap-4">
                    <Button as-child size="lg">
                        <Link v-if="canRegister" :href="route('register')">Comenzar gratis</Link>
                    </Button>
                    <Button as-child variant="outline" size="lg">
                        <Link v-if="$page.props.auth.user" :href="route('vehicles.index')">Ver mis vehículos</Link>
                    </Button>
                </div>
            </div>

            <!-- Features -->
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                <Card>
                    <CardHeader>
                        <Car class="h-8 w-8 text-primary mb-2" />
                        <CardTitle>Gestión de vehículos</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <CardDescription>
                            Registra y gestiona todos tus vehículos con información detallada.
                        </CardDescription>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader>
                        <Wrench class="h-8 w-8 text-primary mb-2" />
                        <CardTitle>Mantenimiento</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <CardDescription>
                            Programa y registra todos los mantenimientos de tu vehículo.
                        </CardDescription>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader>
                        <FileText class="h-8 w-8 text-primary mb-2" />
                        <CardTitle>Documentos</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <CardDescription>
                            Almacena y organiza todos los documentos de tu vehículo.
                        </CardDescription>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader>
                        <Shield class="h-8 w-8 text-primary mb-2" />
                        <CardTitle>Suscripciones</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <CardDescription>
                            Planes adaptados a las necesidades de tu taller.
                        </CardDescription>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader>
                        <Users class="h-8 w-8 text-primary mb-2" />
                        <CardTitle>Clientes</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <CardDescription>
                            Gestiona clientes y sus vehículos desde el panel de taller.
                        </CardDescription>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader>
                        <BarChart3 class="h-8 w-8 text-primary mb-2" />
                        <CardTitle>Reportes</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <CardDescription>
                            Genera informes detallados de mantenimiento e ingresos.
                        </CardDescription>
                    </CardContent>
                </Card>
            </div>
        </div>
    </div>
</template>