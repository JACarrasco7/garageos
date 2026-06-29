<script setup lang="ts">
import GuestLayout from '@/Layouts/GuestLayout.vue';
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
    <GuestLayout>
        <Head title="Log in" />

        <div v-if="status" class="mb-4 text-sm font-medium text-green-600 dark:text-green-400">
            {{ status }}
        </div>

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
                    autocomplete="current-password"
                />

                <p v-if="form.errors.password" class="text-sm text-destructive mt-2">{{ form.errors.password }}</p>
            </div>

            <div class="mt-4 flex items-center">
                <Checkbox id="remember" v-model:checked="form.remember" />
                <Label for="remember" class="ms-2 text-sm text-muted-foreground">Remember me</Label>
            </div>

            <div class="mt-4 flex items-center justify-end">
                <Link
                    v-if="canResetPassword"
                    :href="route('password.request')"
                    class="text-sm text-muted-foreground hover:underline"
                >
                    Forgot your password?
                </Link>

                <Button
                    class="ms-4"
                    :disabled="form.processing"
                    :class="{ 'opacity-25': form.processing }"
                >
                    Log in
                </Button>
            </div>
        </form>
    </GuestLayout>
</template>
