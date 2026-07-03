<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';
import { Button } from '@/Components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/Components/ui/card';
import { Input } from '@/Components/ui/input';
import { Textarea } from '@/Components/ui/textarea';
import { Label } from '@/Components/ui/label';

const form = useForm({
    business_name: '',
    type: '',
    bio: '',
});

const submit = () => {
    form.post(route('providers.store'));
};
</script>

<template>
    <div class="max-w-2xl mx-auto py-8">
        <Card>
            <CardHeader>
                <CardTitle>Registrarse como Proveedor</CardTitle>
            </CardHeader>
            <CardContent>
                <form @submit.prevent="submit" class="space-y-4">
                    <div>
                        <Label for="business_name">Nombre del Negocio</Label>
                        <Input id="business_name" v-model="form.business_name" required />
                    </div>

                    <div>
                        <Label for="type">Tipo de Servicio</Label>
                        <select id="type" v-model="form.type" class="w-full mt-1">
                            <option value="">Selecciona...</option>
                            <option value="taller">Taller de Mechanica</option>
                            <option value="electricidad">Electricidad Automotriz</option>
                            <option value="pintura">Pintura y Bodywork</option>
                            <option value="neumatica">Neumática</option>
                            <option value="transmision">Transmisión</option>
                            <option value="frenos">Frenos</option>
                        </select>
                    </div>

                    <div>
                        <Label for="bio">Descripción del Negocio</Label>
                        <Textarea id="bio" v-model="form.bio" rows="4" />
                    </div>

                    <Button type="submit" :disabled="form.processing">
                        {{ form.processing ? 'Guardando...' : 'Registrarse' }}
                    </Button>
                </form>
            </CardContent>
        </Card>
    </div>
</template>
