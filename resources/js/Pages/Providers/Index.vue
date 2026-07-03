<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { Button } from '@/Components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/Components/ui/card';
import { Input } from '@/Components/ui/input';
import { Badge } from '@/Components/ui/badge';
import { Star, MapPin, Wrench, Shield } from 'lucide-vue-next';

const props = defineProps<{
    providers: {
        data: Array<{
            id: number;
            business_name: string;
            type: string;
            bio: string;
            rating: number;
            review_count: number;
            logo: string | null;
            user: { name: string };
            services: Array<{ name: string; price: number }>;
        }>;
    };
}>();
</script>

<template>
    <div class="max-w-6xl mx-auto py-8">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold">Proveedores Verificados</h1>
            <Link href="/providers/create">
                <Button>Registrarse como Proveedor</Button>
            </Link>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <Card v-for="provider in providers.data" :key="provider.id">
                <CardHeader>
                    <div class="flex items-center gap-3">
                        <img
                            v-if="provider.logo"
                            :src="provider.logo"
                            :alt="provider.business_name"
                            class="w-12 h-12 rounded-full object-cover"
                        />
                        <div>
                            <CardTitle class="text-lg">{{ provider.business_name }}</CardTitle>
                            <p class="text-sm text-muted-foreground">{{ provider.user?.name }}</p>
                        </div>
                    </div>
                </CardHeader>
                <CardContent>
                    <p class="text-sm text-muted-foreground mb-4 line-clamp-2">
                        {{ provider.bio }}
                    </p>

                    <div class="flex items-center gap-2 mb-3">
                        <Badge variant="secondary">{{ provider.type }}</Badge>
                        <div v-if="provider.rating > 0" class="flex items-center gap-1">
                            <Star class="w-4 h-4 fill-yellow-400 text-yellow-400" />
                            <span class="text-sm">{{ provider.rating }}</span>
                        </div>
                    </div>

                    <div class="space-y-2 mb-4">
                        <h4 class="text-sm font-medium">Servicios</h4>
                        <div class="space-y-1">
                            <div
                                v-for="service in provider.services.slice(0, 3)"
                                :key="service.name"
                                class="flex justify-between text-sm"
                            >
                                <span>{{ service.name }}</span>
                                <span class="font-medium">{{ service.price }} €</span>
                            </div>
                        </div>
                    </div>

                    <Link :href="`/providers/${provider.id}`" class="block">
                        <Button class="w-full" variant="outline">Ver Perfil</Button>
                    </Link>
                </CardContent>
            </Card>
        </div>
    </div>
</template>
