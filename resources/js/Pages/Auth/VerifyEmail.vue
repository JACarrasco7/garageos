<script setup lang="ts">
import { computed } from 'vue';
import AuthCardLayout from '@/layouts/auth/AuthCardLayout.vue';
import { Button } from '@/Components/ui/button';
import { Head, Link, useForm } from '@inertiajs/vue3';

const props = defineProps<{
    status?: string;
}>();

const form = useForm({});

const submit = () => {
    form.post(route('verification.send'));
};

const verificationLinkSent = computed(
    () => props.status === 'verification-link-sent',
);
</script>

<template>
    <AuthCardLayout>
        <Head title="Verificar email" />

        <div class="space-y-6">
            <div class="text-center">
                <h2 class="text-2xl font-bold text-foreground">Verifica tu email</h2>
                <p class="text-sm text-muted-foreground mt-1">Antes de continuar, confirma tu email</p>
            </div>

            <div class="text-sm text-muted-foreground">
                Gracias por registrarte. Recibe un email con el enlace de verificación
                o solicita otro si no lo recibiste.
            </div>

            <div
                class="rounded-lg bg-green-50 dark:bg-green-900/20 p-3 text-sm font-medium text-green-600 dark:text-green-400"
                v-if="verificationLinkSent"
            >
                Se ha enviado un nuevo enlace de verificación a tu email.
            </div>

            <form @submit.prevent="submit" class="space-y-4">
                <Button
                    type="submit"
                    class="w-full"
                    :disabled="form.processing"
                >
                    Reenviar email de verificación
                </Button>

                <Link
                    :href="route('logout')"
                    method="post"
                    as="button"
                    class="block w-full text-center text-sm text-muted-foreground hover:underline"
                >
                    Cerrar sesión
                </Link>
            </form>
        </div>
    </AuthCardLayout>
</template>
