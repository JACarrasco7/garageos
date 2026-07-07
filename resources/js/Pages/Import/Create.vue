<script setup lang="ts">
import { useForm, Head } from '@inertiajs/vue3';
import WebLayout from '@/layouts/WebLayout.vue';
import { Card, CardContent, CardHeader, CardTitle } from '@/Components/ui/card';
import { Button } from '@/Components/ui/button';
import { Input } from '@/Components/ui/input';
import { Label } from '@/Components/ui/label';
import { Link } from '@inertiajs/vue3';

const form = useForm({
    plate_original: '',
    brand: '',
    model: '',
    year: new Date().getFullYear(),
    engine_cc: null as number | null,
    power_kw: null as number | null,
    co2_emissions: null as number | null,
    origin_country: 'DE',
});

const submit = () => {
    form.post(route('imports.store'));
};
</script>

<template>
    <Head title="Nueva importación" />

    <WebLayout>
        <template #header>
            Nueva importación
        </template>

        <div class="max-w-2xl">
            <Card>
                <CardHeader>
                    <CardTitle>Datos del vehículo</CardTitle>
                </CardHeader>
                <CardContent>
                    <form @submit.prevent="submit" class="space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div class="space-y-2">
                                <Label for="brand">Marca</Label>
                                <Input
                                    id="brand"
                                    v-model="form.brand"
                                    required
                                    placeholder="BMW"
                                />
                            </div>
                            <div class="space-y-2">
                                <Label for="model">Modelo</Label>
                                <Input
                                    id="model"
                                    v-model="form.model"
                                    required
                                    placeholder="Serie 3"
                                />
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div class="space-y-2">
                                <Label for="year">Año</Label>
                                <Input
                                    id="year"
                                    type="number"
                                    v-model="form.year"
                                    required
                                />
                            </div>
                            <div class="space-y-2">
                                <Label for="plate_original">Matrícula original</Label>
                                <Input
                                    id="plate_original"
                                    v-model="form.plate_original"
                                    placeholder="M-AB 1234"
                                />
                            </div>
                        </div>

                        <div class="grid grid-cols-3 gap-4">
                            <div class="space-y-2">
                                <Label for="engine_cc">CC</Label>
                                <Input
                                    id="engine_cc"
                                    type="number"
                                    v-model="form.engine_cc"
                                    placeholder="2000"
                                />
                            </div>
                            <div class="space-y-2">
                                <Label for="power_kw">KW</Label>
                                <Input
                                    id="power_kw"
                                    type="number"
                                    v-model="form.power_kw"
                                    placeholder="150"
                                />
                            </div>
                            <div class="space-y-2">
                                <Label for="co2_emissions">CO2 g/km</Label>
                                <Input
                                    id="co2_emissions"
                                    type="number"
                                    v-model="form.co2_emissions"
                                    placeholder="120"
                                />
                            </div>
                        </div>

                        <div class="flex gap-2 pt-4">
                            <Link :href="route('imports.index')">
                                <Button variant="outline" type="button">
                                    Cancelar
                                </Button>
                            </Link>
                            <Button type="submit" :disabled="form.processing">
                                <span v-if="form.processing">Creando...</span>
                                <span v-else>Crear importación</span>
                            </Button>
                        </div>
                    </form>
                </CardContent>
            </Card>
        </div>
    </WebLayout>
</template>
