<script setup lang="ts">
import AuthCardLayout from '@/layouts/auth/AuthCardLayout.vue';
import { Button } from '@/Components/ui/button';
import { Checkbox } from '@/Components/ui/checkbox';
import { Input } from '@/Components/ui/input';
import { Label } from '@/Components/ui/label';
import { Head, Link, useForm } from '@inertiajs/vue3';

defineProps<{
    canResetPassword?: boolean;
    status?: string;
}>();

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => {
            form.reset('password');
        },
    });
};
</script>

<template>
    <AuthCardLayout>
        <Head title="Iniciar sesión" />

        <div class="space-y-6">
            <div class="text-center">
                <h2 class="text-2xl font-bold text-foreground">Bienvenido</h2>
                <p class="text-sm text-muted-foreground mt-1">Ingresa a tu cuenta</p>
            </div>

            <div v-if="status" class="rounded-lg bg-green-50 dark:bg-green-900/20 p-3 text-sm font-medium text-green-600 dark:text-green-400">
                {{ status }}
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
                    <Label for="password">Contraseña</Label>
                    <Input
                        id="password"
                        type="password"
                        v-model="form.password"
                        required
                        autocomplete="current-password"
                        placeholder="••••••••"
                    />
                    <p v-if="form.errors.password" class="text-sm text-destructive">{{ form.errors.password }}</p>
                </div>

                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-2">
                        <Checkbox id="remember" v-model:checked="form.remember" />
                        <Label for="remember" class="text-sm text-muted-foreground">Recordarme</Label>
                    </div>

                    <Link
                        v-if="canResetPassword"
                        :href="route('password.request')"
                        class="text-sm text-primary hover:underline"
                    >
                        ¿Olvidaste tu contraseña?
                    </Link>
                </div>

                <Button
                    type="submit"
                    class="w-full"
                    :disabled="form.processing"
                >
                    Iniciar sesión
                </Button>
            </form>
        </div>
    </AuthCardLayout>
</template>
