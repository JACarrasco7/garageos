<script setup lang="ts">
import AuthCardLayout from '@/layouts/auth/AuthCardLayout.vue';
import { Button } from '@/Components/ui/button';
import { Input } from '@/Components/ui/input';
import { Label } from '@/Components/ui/label';
import { Head, useForm } from '@inertiajs/vue3';

const props = defineProps<{
    email: string;
    token: string;
}>();

const form = useForm({
    token: props.token,
    email: props.email,
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('password.store'), {
        onFinish: () => {
            form.reset('password', 'password_confirmation');
        },
    });
};
</script>

<template>
    <AuthCardLayout>
        <Head title="Nueva contraseña" />

        <div class="space-y-6">
            <div class="text-center">
                <h2 class="text-2xl font-bold text-foreground">Nueva contraseña</h2>
                <p class="text-sm text-muted-foreground mt-1">Elige una contraseña segura</p>
            </div>

            <form @submit.prevent="submit" class="space-y-4">
                <div class="space-y-2">
                    <Label for="email">Email</Label>
                    <Input
                        id="email"
                        type="email"
                        v-model="form.email"
                        required
                        autofocus
                        autocomplete="username"
                        placeholder="tu@email.com"
                    />
                    <p v-if="form.errors.email" class="text-sm text-destructive">{{ form.errors.email }}</p>
                </div>

                <div class="space-y-2">
                    <Label for="password">Nueva contraseña</Label>
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

                <Button
                    type="submit"
                    class="w-full"
                    :disabled="form.processing"
                >
                    Guardar contraseña
                </Button>
            </form>
        </div>
    </AuthCardLayout>
</template>
