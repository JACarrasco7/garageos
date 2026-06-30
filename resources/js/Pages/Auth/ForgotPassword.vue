<script setup lang="ts">
import AuthCardLayout from '@/layouts/auth/AuthCardLayout.vue';
import { Button } from '@/Components/ui/button';
import { Input } from '@/Components/ui/input';
import { Label } from '@/Components/ui/label';
import { Head, useForm } from '@inertiajs/vue3';

defineProps<{
    status?: string;
}>();

const form = useForm({
    email: '',
});

const submit = () => {
    form.post(route('password.email'));
};
</script>

<template>
    <AuthCardLayout>
        <Head title="Recuperar contraseña" />

        <div class="space-y-6">
            <div class="text-center">
                <h2 class="text-2xl font-bold text-foreground">¿Olvidaste tu contraseña?</h2>
                <p class="text-sm text-muted-foreground mt-1">Te enviamos un enlace para restablecerla</p>
            </div>

            <div
                v-if="status"
                class="rounded-lg bg-green-50 dark:bg-green-900/20 p-3 text-sm font-medium text-green-600 dark:text-green-400"
            >
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

                <Button
                    type="submit"
                    class="w-full"
                    :disabled="form.processing"
                >
                    Enviar enlace
                </Button>
            </form>
        </div>
    </AuthCardLayout>
</template>
