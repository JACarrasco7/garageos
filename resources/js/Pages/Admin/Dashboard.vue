<script setup lang="ts">
import { Card, CardContent, CardHeader, CardTitle } from '@/Components/ui/card';
import { Button } from '@/Components/ui/button';
import { Link } from '@inertiajs/vue3';
import { KeySquare, Users, Shield, BarChart3 } from 'lucide-vue-next';

const props = defineProps<{
    stats: {
        users: number;
        roles: number;
        permissions: number;
    };
}>();
</script>

<template>
    <div class="max-w-4xl mx-auto py-8">
        <h1 class="text-2xl font-bold mb-6">Panel de Administración</h1>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
            <Card>
                <CardHeader>
                    <CardTitle class="text-sm font-medium">Usuarios</CardTitle>
                </CardHeader>
                <CardContent>
                    <div class="text-2xl font-bold">{{ stats.users }}</div>
                </CardContent>
            </Card>

            <Card>
                <CardHeader>
                    <CardTitle class="text-sm font-medium">Roles</CardTitle>
                </CardHeader>
                <CardContent>
                    <div class="text-2xl font-bold">{{ stats.roles }}</div>
                </CardContent>
            </Card>

            <Card>
                <CardHeader>
                    <CardTitle class="text-sm font-medium">Permisos</CardTitle>
                </CardHeader>
                <CardContent>
                    <div class="text-2xl font-bold">{{ stats.permissions }}</div>
                </CardContent>
            </Card>
        </div>

        <Card>
            <CardHeader>
                <CardTitle>Administración</CardTitle>
            </CardHeader>
            <CardContent class="space-y-2">
                <Button as-child class="w-full justify-start">
                    <Link href="/admin/stripe-keys">
                        <KeySquare class="w-4 h-4 mr-2" />
                        Gestionar claves Stripe
                    </Link>
                </Button>
            </CardContent>
        </Card>
    </div>
</template>
