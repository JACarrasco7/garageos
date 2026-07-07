<script setup lang="ts">
import { onMounted, provide, ref } from 'vue';
import { Link } from '@inertiajs/vue3';
import { useFcm } from '@/Composables/useFcm';
import { Toaster } from 'vue-sonner';
import { PanelLeftClose, PanelLeftOpen, Menu } from 'lucide-vue-next';

import AppSidebar from '@/Components/AppSidebar.vue';
import AppHeader from '@/Components/layout/AppHeader.vue';
import ThemeToggle from '@/Components/ThemeToggle.vue';
import OnboardingTour from '@/Components/OnboardingTour.vue';

import {
    Breadcrumb,
    BreadcrumbItem,
    BreadcrumbLink,
    BreadcrumbList,
    BreadcrumbPage,
    BreadcrumbSeparator,
} from '@/Components/ui/breadcrumb';
import { Button } from '@/Components/ui/button';
import { Separator } from '@/Components/ui/separator';

const { registerFcm, addListeners } = useFcm();

const sidebarOpen = ref(true);
const mobileOpen = ref(false);

const toggleSidebar = () => {
    sidebarOpen.value = !sidebarOpen.value;
};
const closeMobile = () => {
    mobileOpen.value = false;
};
const showMobile = () => {
    mobileOpen.value = true;
};

provide('sidebarOpen', sidebarOpen);
provide('mobileSidebarOpen', mobileOpen);

onMounted(() => {
    if ('PushNotifications' in window) {
        registerFcm();
        addListeners();
    }
});
</script>

<template>
    <div class="flex h-dvh w-full overflow-hidden bg-background relative transition-colors duration-500">
        <!-- Background subtle pattern -->
        <div class="absolute inset-0 opacity-30 pointer-events-none">
            <div class="absolute inset-0 bg-[radial-gradient(circle_at_30%_20%,rgba(var(--primary),0.03)_0%,transparent_50%)]" />
            <div class="absolute inset-0 bg-[radial-gradient(circle_at_70%_80%,rgba(var(--accent),0.02)_0%,transparent_40%)]" />
        </div>

        <!-- Sidebar (desktop) - flotante -->
        <aside
            :class="[
                'hidden md:flex fixed top-3 left-3 bottom-3 z-30 shrink-0 rounded-2xl border backdrop-blur-2xl transition-all duration-500 ease-in-out',
                sidebarOpen ? 'w-64' : 'w-16',
                'bg-card/50 border-white/15 shadow-xl',
            ]"
        >
            <AppSidebar :collapsed="!sidebarOpen" />
        </aside>

        <!-- Sidebar (mobile overlay) -->
        <transition
            enter-active-class="transition-opacity duration-200"
            leave-active-class="transition-opacity duration-200"
            enter-from-class="opacity-0"
            leave-to-class="opacity-0"
        >
            <div
                v-if="mobileOpen"
                class="fixed inset-0 z-40 md:hidden backdrop-blur-sm bg-black/50"
                @click="closeMobile"
            />
        </transition>

        <!-- Sidebar (mobile drawer) -->
        <transition
            enter-active-class="transition-transform duration-300 ease-out"
            leave-active-class="transition-transform duration-300 ease-in"
            enter-from-class="-translate-x-full"
            leave-to-class="-translate-x-full"
        >
            <aside
                v-if="mobileOpen"
                :class="[
                    'fixed inset-y-0 left-0 top-3 bottom-3 z-50 flex h-auto w-72 flex-col rounded-2xl border backdrop-blur-2xl md:hidden',
                    'bg-card/50 border-white/15 shadow-xl'
                ]"
            >
                <AppSidebar :collapsed="false" @navigate="closeMobile" />
            </aside>
        </transition>

        <!-- Main content -->
        <div class="flex min-w-0 flex-1 flex-col gap-3 p-3 md:pl-64">
            <AppHeader>
                <template #header>
                    <slot name="header" />
                </template>
                <template #header-actions>
                    <slot name="header-actions" />
                </template>
            </AppHeader>

            <main class="flex-1 min-h-0 rounded-2xl border backdrop-blur-xl flex flex-col overflow-hidden bg-card/35 border-white/12 shadow-lg transition-all duration-500">
                <div class="flex-1 overflow-auto p-6 md:p-8 lg:p-10">
                    <slot />
                </div>
            </main>
        </div>

        <Toaster position="top-right" rich-colors />
        <OnboardingTour />
    </div>
</template>
