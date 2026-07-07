<script setup lang="ts">
import AppSidebarLayout from '@/layouts/app/AppSidebarLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { Card, CardContent, CardHeader, CardTitle } from '@/Components/ui/card';
import { Input } from '@/Components/ui/input';
import { Textarea } from '@/Components/ui/textarea';
import { Button } from '@/Components/ui/button';
import { Label } from '@/Components/ui/label';
import { Switch } from '@/Components/ui/switch';
import { Contact } from '@/types';
import { computed } from 'vue';

const props = defineProps<{
    contact?: Contact;
    status?: string;
}>();

const form = useForm({
    phone: props.contact?.phone ?? '',
    address: props.contact?.address ?? '',
    postal_code: props.contact?.postal_code ?? '',
    city: props.contact?.city ?? '',
    province: props.contact?.province ?? '',
    country: props.contact?.country ?? '',
    is_public: props.contact?.is_public ?? false,
});

const submit = () => {
    form.upsert('contact.update', form);
};
</script>

<template>
    <Head title="Contacto" />

    <AppSidebarLayout>
        <template #header>
            Contacto
        </template>

        <div class="space-y-6">
            <div>
                <h2 class="text-xl font-semibold text-foreground mb-1">Información de contacto</h2>
                <p class="text-sm text-muted-foreground">Gestiona tus datos de contacto y visibilidad</p>
            </div>

            <Card class="border-0 shadow-lg">
                <CardHeader class="pb-3">
                    <CardTitle class="text-lg font-semibold">
                        Datos de contacto
                    </CardTitle>
                </CardHeader>
                <CardContent>
                    <form @submit.prevent="submit" class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="space-y-2">
                                <Label for="phone">Teléfono</Label>
                                <Input
                                    id="phone"
                                    v-model="form.phone"
                                    placeholder="+34 600 000 000"
                              />
                            </div>
                            <div class="space-y-2">
                                <Label for="postal_code">Código postal</Label>
                                <Input
                                    id="postal_code"
                                    v-model="form.postal_code"
                                    placeholder="28001"
                              />
                            </div>
                        </div>

                        <div class="space-y-2">
                            <Label for="address">Dirección</Label>
                            <Input
                                id="address"
                                v-model="form.address"
                                placeholder="Calle, número, piso"
                            />
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div class="space-y-2">
                                <Label for="city">Ciudad</Label>
                                <Input
                                    id="city"
                                    v-model="form.city"
                                    placeholder="Madrid"
                              />
                            </div>
                            <div class="space-y-2">
                                <Label for="province">Provincia</Label>
                                <Input
                                    id="province"
                                    v-model="form.province"
                                    placeholder="Madrid"
                              />
                            </div>
                            <div class="space-y-2">
                                <Label for="country">País</Label>
                                <Input
                                    id="country"
                                    v-model="form.country"
                                    placeholder="España"
                              />
                            </div>
                        </div>

                        <div class="flex items-center space-x-2">
                            <Switch id="is_public" v-model="form.is_public" />
                            <Label for="is_public" class="text-sm">
                                Hacer pública esta información
                            </Label>
                        </div>

                        <Button type="submit" :disabled="form.processing">
                            Guardar cambios
                        </Button>
                    </form>
                </CardContent>
            </Card>
        </div>
    </AppSidebarLayout>
</template>
