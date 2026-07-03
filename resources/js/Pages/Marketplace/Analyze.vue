<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';
import { Button } from '@/Components/ui/button';
import { Input } from '@/Components/ui/input';
import { Label } from '@/Components/ui/label';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/Components/ui/card';
import { Link } from '@inertiajs/vue3';

const props = defineProps<{
    analyzed?: boolean;
    data?: Record<string, any>;
}>();

const form = useForm({
    url: '',
});

const submit = () => {
    form.post(route('marketplace.analyze.store'));
};
</script>

<template>
    <div class="max-w-2xl mx-auto py-8">
        <Card>
            <CardHeader>
                <CardTitle>Analizar Anuncio</CardTitle>
                <CardDescription>
                    Pega la URL de un anuncio de mobile.de, autoscout24.de o ebay-kleinanzeigen.de para importar los datos automáticamente
                </CardDescription>
            </CardHeader>
            <CardContent>
                <form @submit.prevent="submit" class="space-y-4">
                    <div>
                        <Label for="url">URL del Anuncio</Label>
                        <Input
                            id="url"
                            v-model="form.url"
                            type="url"
                            placeholder="https://m.mobile.de/..."
                            required
                        />
                        <p v-if="form.errors.url" class="text-sm text-destructive mt-1">
                            {{ form.errors.url }}
                        </p>
                    </div>

                    <Button type="submit" :disabled="form.processing">
                        {{ form.processing ? 'Analizando...' : 'Analizar' }}
                    </Button>
                </form>

                <div v-if="analyzed && data" class="mt-6 space-y-4">
                    <h3 class="font-semibold">Datos Extraídos</h3>
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div v-if="data.title">
                            <span class="text-muted-foreground">Título:</span>
                            <p>{{ data.title }}</p>
                        </div>
                        <div v-if="data.price">
                            <span class="text-muted-foreground">Precio:</span>
                            <p>{{ data.price }} €</p>
                        </div>
                        <div v-if="data.brand">
                            <span class="text-muted-foreground">Marca:</span>
                            <p>{{ data.brand }}</p>
                        </div>
                        <div v-if="data.model">
                            <span class="text-muted-foreground">Modelo:</span>
                            <p>{{ data.model }}</p>
                        </div>
                        <div v-if="data.year">
                            <span class="text-muted-foreground">Año:</span>
                            <p>{{ data.year }}</p>
                        </div>
                        <div v-if="data.mileage">
                            <span class="text-muted-foreground">Kilometraje:</span>
                            <p>{{ data.mileage }} km</p>
                        </div>
                        <div v-if="data.fuel_type">
                            <span class="text-muted-foreground">Combustible:</span>
                            <p>{{ data.fuel_type }}</p>
                        </div>
                        <div v-if="data.power_hp">
                            <span class="text-muted-foreground">Potencia:</span>
                            <p>{{ data.power_hp }} CV</p>
                        </div>
                    </div>

                    <Link :href="route('marketplace.create')" class="block mt-4">
                        <Button class="w-full">Crear Anuncio con estos Datos</Button>
                    </Link>
                </div>
            </CardContent>
        </Card>
    </div>
</template>
