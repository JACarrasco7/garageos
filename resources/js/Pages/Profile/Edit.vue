<script setup lang="ts">
import AppSidebarLayout from '@/layouts/app/AppSidebarLayout.vue';
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

    <AppSidebarLayout>
        <template #header>
            Perfil
        </template>

        <div class="max-w-3xl space-y-6">
            <Card class="border-0 shadow-lg">
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

            <Card class="border-0 shadow-lg">
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

            <Card class="border-0 shadow-lg border-destructive/50">
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
    </AppSidebarLayout>
</template>
