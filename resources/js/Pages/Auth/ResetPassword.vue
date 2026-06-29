<script setup lang="ts">
import GuestLayout from '@/Layouts/GuestLayout.vue';
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
    <GuestLayout>
        <Head title="Reset Password" />

        <form @submit.prevent="submit">
            <div>
                <Label for="email">Email</Label>

                <Input
                    id="email"
                    type="email"
                    class="mt-1 block w-full"
                    v-model="form.email"
                    required
                    autofocus
                    autocomplete="username"
                />

                <p v-if="form.errors.email" class="text-sm text-destructive mt-2">{{ form.errors.email }}</p>
            </div>

            <div class="mt-4">
                <Label for="password">Password</Label>

                <Input
                    id="password"
                    type="password"
                    class="mt-1 block w-full"
                    v-model="form.password"
                    required
                    autocomplete="new-password"
                />

                <p v-if="form.errors.password" class="text-sm text-destructive mt-2">{{ form.errors.password }}</p>
            </div>

            <div class="mt-4">
                <Label for="password_confirmation">Confirm Password</Label>

                <Input
                    id="password_confirmation"
                    type="password"
                    class="mt-1 block w-full"
                    v-model="form.password_confirmation"
                    required
                    autocomplete="new-password"
                />

                <p v-if="form.errors.password_confirmation" class="text-sm text-destructive mt-2">{{ form.errors.password_confirmation }}</p>
            </div>

            <div class="mt-4 flex items-center justify-end">
                <Button
                    :disabled="form.processing"
                    :class="{ 'opacity-25': form.processing }"
                >
                    Reset Password
                </Button>
            </div>
        </form>
    </GuestLayout>
</template>
