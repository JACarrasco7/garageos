<script setup lang="ts">
import WebLayout from '@/layouts/WebLayout.vue';
import DeleteUserForm from './Partials/DeleteUserForm.vue';
import UpdatePasswordForm from './Partials/UpdatePasswordForm.vue';
import UpdateProfileInformationForm from './Partials/UpdateProfileInformationForm.vue';
import { Head } from '@inertiajs/vue3';
import { Card, CardContent, CardHeader, CardTitle } from '@/Components/ui/card';
import { User, Lock, Trash2 } from 'lucide-vue-next';

defineProps<{
    mustVerifyEmail?: boolean;
    status?: string;
}>();
</script>

<template>
    <Head title="Perfil" />

    <WebLayout>
        <template #header>
            Perfil
        </template>

        <div class="space-y-6">
            <div>
                <h2 class="text-xl font-semibold text-foreground mb-1">Configuración de la cuenta</h2>
                <p class="text-sm text-muted-foreground">Administra tu perfil y preferencias</p>
            </div>

            <Card class="glass-surface border-0">
                <CardHeader class="pb-3">
                    <CardTitle class="flex items-center gap-2 text-lg font-semibold">
                        <User class="h-5 w-5" />
                        Información personal
                    </CardTitle>
                </CardHeader>
                <CardContent>
                    <UpdateProfileInformationForm
                        :must-verify-email="mustVerifyEmail"
                        :status="status"
                    />
                </CardContent>
            </Card>

            <Card class="glass-surface border-0">
                <CardHeader class="pb-3">
                    <CardTitle class="flex items-center gap-2 text-lg font-semibold">
                        <Lock class="h-5 w-5" />
                        Cambiar contraseña
                    </CardTitle>
                </CardHeader>
                <CardContent>
                    <UpdatePasswordForm />
                </CardContent>
            </Card>

            <Card class="glass-surface border-0 border-destructive/50">
                <CardHeader class="pb-3">
                    <CardTitle class="flex items-center gap-2 text-lg font-semibold text-destructive">
                        <Trash2 class="h-5 w-5" />
                        Eliminar cuenta
                    </CardTitle>
                </CardHeader>
                <CardContent>
                    <DeleteUserForm />
                </CardContent>
            </Card>
        </div>
    </WebLayout>
</template>
