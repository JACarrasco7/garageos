<script setup lang="ts">
import { Link, useForm } from '@inertiajs/vue3';
import { Button } from '@/Components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/Components/ui/card';
import { Badge } from '@/Components/ui/badge';
import { Textarea } from '@/Components/ui/textarea';
import { Star, MapPin, Wrench, Shield, Calendar } from 'lucide-vue-next';

const props = defineProps<{
    provider: {
        id: number;
        business_name: string;
        type: string;
        bio: string;
        rating: number;
        review_count: number;
        logo: string | null;
        is_verified: boolean;
        user: { name: string };
        services: Array<{ id: number; name: string; description: string; price: number; duration_minutes: number }>;
        availability: Array<{ day_of_week: number; start_time: string; end_time: string; is_available: boolean }>;
        reviews: Array<{ id: number; rating: number; comment: string; user: { name: string }; created_at: string }>;
    };
}>();

const form = useForm({
    rating: 0,
    comment: '',
    service_type: '',
});

const submitReview = () => {
    form.post(`/providers/${props.provider.id}/reviews`);
};

const days = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'];
</script>

<template>
    <div class="max-w-4xl mx-auto py-8">
        <Link href="/providers" class="inline-flex items-center text-sm text-muted-foreground hover:text-foreground mb-4">
            ← Volver a proveedores
        </Link>

        <Card class="mb-6">
            <CardHeader>
                <div class="flex items-center gap-4">
                    <img
                        v-if="provider.logo"
                        :src="provider.logo"
                        :alt="provider.business_name"
                        class="w-20 h-20 rounded-full object-cover"
                    />
                    <div>
                        <CardTitle class="text-2xl">{{ provider.business_name }}</CardTitle>
                        <p class="text-muted-foreground">{{ provider.user?.name }}</p>
                    </div>
                </div>
            </CardHeader>
            <CardContent>
                <div class="flex items-center gap-4 mb-4">
                    <Badge variant="secondary">{{ provider.type }}</Badge>
                    <div v-if="provider.is_verified" class="flex items-center gap-1 text-green-600">
                        <Shield class="w-4 h-4" />
                        <span class="text-sm">Verificado</span>
                    </div>
                    <div v-if="provider.rating > 0" class="flex items-center gap-1">
                        <Star class="w-4 h-4 fill-yellow-400 text-yellow-400" />
                        <span class="text-sm">{{ provider.rating }} ({{ provider.review_count }} reseñas)</span>
                    </div>
                </div>

                <p class="text-muted-foreground">{{ provider.bio }}</p>
            </CardContent>
        </Card>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <Card>
                <CardHeader>
                    <CardTitle class="text-lg">Servicios</CardTitle>
                </CardHeader>
                <CardContent>
                    <div class="space-y-3">
                        <div v-for="service in provider.services" :key="service.id" class="border-b pb-3">
                            <div class="flex justify-between items-start">
                                <div>
                                    <h4 class="font-medium">{{ service.name }}</h4>
                                    <p class="text-sm text-muted-foreground">{{ service.description }}</p>
                                </div>
                                <div class="text-right">
                                    <p class="font-bold">{{ service.price }} €</p>
                                    <p class="text-sm text-muted-foreground">{{ service.duration_minutes }} min</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </CardContent>
            </Card>

            <Card>
                <CardHeader>
                    <CardTitle class="text-lg">Horario</CardTitle>
                </CardHeader>
                <CardContent>
                    <div class="space-y-2">
                        <div v-for="day in 5" :key="day" class="flex justify-between">
                            <span>{{ days[day - 1] }}</span>
                            <span class="text-muted-foreground">
                                {{ provider.availability.find(a => a.day_of_week === day && a.is_available)
                                    ? `${provider.availability.find(a => a.day_of_week === day && a.is_available)?.start_time.slice(0,5)} - ${provider.availability.find(a => a.day_of_week === day && a.is_available)?.end_time.slice(0,5)}`
                                    : 'Cerrado' }}
                            </span>
                        </div>
                    </div>
                </CardContent>
            </Card>
        </div>

        <Card>
            <CardHeader>
                <CardTitle class="text-lg">Reseñas</CardTitle>
            </CardHeader>
            <CardContent>
                <div class="space-y-4 mb-4">
                    <div v-for="review in provider.reviews" :key="review.id" class="border-b pb-3">
                        <div class="flex items-center gap-2 mb-1">
                            <Star class="w-4 h-4 fill-yellow-400 text-yellow-400" />
                            <span class="font-medium">{{ review.rating }}/5</span>
                            <span class="text-muted-foreground">por {{ review.user?.name }}</span>
                        </div>
                        <p class="text-muted-foreground">{{ review.comment }}</p>
                        <p class="text-xs text-muted-foreground mt-1">
                            {{ new Date(review.created_at).toLocaleDateString() }}
                        </p>
                    </div>
                </div>

                <form @submit.prevent="submitReview">
                    <Card>
                        <CardHeader>
                            <CardTitle class="text-lg">Escribir reseña</CardTitle>
                        </CardHeader>
                        <CardContent class="space-y-3">
                            <div>
                                <label class="text-sm font-medium">Puntuación</label>
                                <select v-model="form.rating" class="w-full mt-1">
                                    <option value="0">Selecciona...</option>
                                    <option value="1">1 - Muy malo</option>
                                    <option value="2">2 - Malo</option>
                                    <option value="3">3 - Regular</option>
                                    <option value="4">4 - Bueno</option>
                                    <option value="5">5 - Excelente</option>
                                </select>
                            </div>
                            <div>
                                <label class="text-sm font-medium">Comentario</label>
                                <Textarea v-model="form.comment" rows="3" class="mt-1" />
                            </div>
                            <Button type="submit" :disabled="form.processing">
                                Enviar reseña
                            </Button>
                        </CardContent>
                    </Card>
                </form>
            </CardContent>
        </Card>
    </div>
</template>
