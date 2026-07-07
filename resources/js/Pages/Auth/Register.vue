<script setup lang="ts">
import AuthCardLayout from '@/layouts/auth/AuthCardLayout.vue';
import { Button } from '@/Components/ui/button';
import { Input } from '@/Components/ui/input';
import { Label } from '@/Components/ui/label';
import { Checkbox } from '@/Components/ui/checkbox';
import { Head, Link, useForm } from '@inertiajs/vue3';

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    gdpr_consent: false,
});

const submit = () => {
    form.transform((data) => ({
        ...data,
        gdpr_consent: data.gdpr_consent ? '1' : '0',
    })).post(route('register'), {
        onError: () => {
            form.reset('password', 'password_confirmation');
        },
        onFinish: () => {
            form.reset('password', 'password_confirmation');
        },
    });
};
</script>

<template>
    <AuthCardLayout>
        <Head title="Crear cuenta" />

        <div class="space-y-6">
            <div class="text-center">
                <h2 class="text-2xl font-bold text-foreground">Crear cuenta</h2>
                <p class="text-sm text-muted-foreground mt-1">Regístrate gratis</p>
            </div>

            <form @submit.prevent="submit" class="space-y-4">
                <div class="space-y-2">
                    <Label for="name">Nombre</Label>
                    <Input
                        id="name"
                        type="text"
                        v-model="form.name"
                        required
                        autofocus
                        autocomplete="name"
                        placeholder="Tu nombre"
                    />
                    <p v-if="form.errors.name" class="text-sm text-destructive">{{ form.errors.name }}</p>
                </div>

                <div class="space-y-2">
                    <Label for="email">Email</Label>
                    <Input
                        id="email"
                        type="email"
                        v-model="form.email"
                        required
                        autocomplete="username"
                        placeholder="tu@email.com"
                    />
                    <p v-if="form.errors.email" class="text-sm text-destructive">{{ form.errors.email }}</p>
                </div>

                <div class="space-y-2">
                    <Label for="password">Contraseña</Label>
                    <Input
                        id="password"
                        type="password"
                        v-model="form.password"
                        required
                        autocomplete="new-password"
                        placeholder="••••••••"
                    />
                    <p v-if="form.errors.password" class="text-sm text-destructive">{{ form.errors.password }}</p>
                </div>

                <div class="space-y-2">
                    <Label for="password_confirmation">Confirmar contraseña</Label>
                    <Input
                        id="password_confirmation"
                        type="password"
                        v-model="form.password_confirmation"
                        required
                        autocomplete="new-password"
                        placeholder="••••••••"
                    />
                    <p v-if="form.errors.password_confirmation" class="text-sm text-destructive">{{ form.errors.password_confirmation }}</p>
                </div>

                <div class="flex items-start space-x-2">
                    <Checkbox
                        id="gdpr_consent"
                        :model-value="form.gdpr_consent"
                        @update:model-value="form.gdpr_consent = $event"
                    />
                    <div class="grid gap-1.5 leading-none">
                        <Label for="gdpr_consent" class="text-sm font-normal">
                            Acepto la política de privacidad y el tratamiento de mis datos
                        </Label>
                        <p class="text-xs text-muted-foreground">
                            Consulta nuestra <a href="/privacy" class="text-primary hover:underline">política de privacidad</a>
                        </p>
                    </div>
                </div>
                <p v-if="form.errors.gdpr_consent" class="text-sm text-destructive">{{ form.errors.gdpr_consent }}</p>

                <Button
                    type="submit"
                    class="w-full"
                    :disabled="form.processing"
                >
                    <span v-if="form.processing">Creando cuenta...</span>
                    <span v-else>Crear cuenta</span>
                </Button>
            </form>

            <p class="text-center text-sm text-muted-foreground">
                ¿Ya tienes cuenta?
                <Link :href="route('login')" class="text-primary hover:underline ml-1">
                    Inicia sesión
                </Link>
            </p>
        </div>
    </AuthCardLayout>
</template>
