<script setup lang="ts">
import { Link, useForm } from '@inertiajs/vue3';
import { Button } from '@/Components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/Components/ui/card';
import { Input } from '@/Components/ui/input';
import { Label } from '@/Components/ui/label';

const props = defineProps<{
    stripeKey: string;
    stripeSecret: string;
}>();

const form = useForm({
    stripe_key: props.stripeKey || '',
    stripe_secret: props.stripeSecret || '',
});

const submit = () => {
    form.put(route('admin.stripe-keys.update'));
};
</script>

<template>
    <div class="max-w-2xl mx-auto py-8">
        <Card>
            <CardHeader>
                <CardTitle>Claves de Stripe</CardTitle>
            </CardHeader>
            <CardContent>
                <form @submit.prevent="submit" class="space-y-4">
                    <div>
                        <Label for="stripe_key">Stripe Publishable Key</Label>
                        <Input id="stripe_key" v-model="form.stripe_key" type="text" />
                    </div>

                    <div>
                        <Label for="stripe_secret">Stripe Secret Key</Label>
                        <Input id="stripe_secret" v-model="form.stripe_secret" type="password" />
                    </div>

                    <Button type="submit" :disabled="form.processing">
                        Guardar claves
                    </Button>
                </form>
            </CardContent>
        </Card>
    </div>
</template>
