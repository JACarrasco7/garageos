<script setup lang="ts">
import { onMounted, provide, ref } from 'vue';
import { useFcm } from '@/Composables/useFcm';
import { Toaster } from 'vue-sonner';

import AppSidebar from '@/Components/AppSidebar.vue';
import OnboardingTour from '@/Components/OnboardingTour.vue';
import WebSidebar from '@/Components/WebSidebar.vue';
import WebHeader from '@/Components/WebHeader.vue';
import WebMainContent from '@/Components/WebMainContent.vue';

const { registerFcm, addListeners } = useFcm();

const sidebarOpen = ref(true);
const mobileOpen = ref(false);

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
    <div class="flex h-dvh w-full overflow-hidden bg-background relative transition-colors duration-500 p-2 gap-2">
        <!-- Background subtle pattern -->
        <div class="absolute inset-0 opacity-30 pointer-events-none">
            <div class="absolute inset-0 bg-[radial-gradient(circle_at_30%_20%,rgba(var(--primary),0.03)_0%,transparent_50%)]" />
            <div class="absolute inset-0 bg-[radial-gradient(circle_at_70%_80%,rgba(var(--accent),0.02)_0%,transparent_40%)]" />
        </div>

        <!-- Sidebar (desktop) -->
        <aside
            :class="[
                'hidden md:flex h-full z-30 shrink-0 rounded-2xl border backdrop-blur-2xl transition-all duration-500 ease-in-out',
                sidebarOpen ? 'w-64' : 'w-16',
                'bg-card/50 border-white/15 shadow-xl',
            ]"
        >
            <WebSidebar>
                <AppSidebar :collapsed="!sidebarOpen" />
            </WebSidebar>
        </aside>

        <!-- Sidebar (mobile drawer) -->
        <transition
            enter-active-class="transition-transform duration-300 ease-out"
            leave-active-class="transition-transform duration-300 ease-in"
            enter-from-class="-translate-x-full"
            leave-to-class="-translate-x-full"
        >
            <aside
                v-if="mobileOpen"
                class="fixed inset-y-2 left-2 z-50 flex h-auto w-72 flex-col rounded-2xl border backdrop-blur-3xl md:hidden bg-card/50 border-white/15 shadow-xl"
            >
                <AppSidebar :collapsed="false" @navigate="mobileOpen = false" />
            </aside>
        </transition>

        <!-- Main Content Column -->
        <div class="flex flex-1 flex-col gap-2 overflow-hidden">
            <WebHeader>
                <template #default>
                    <slot name="header" />
                </template>
                <template #actions>
                    <slot name="header-actions" />
                </template>
            </WebHeader>

            <WebMainContent>
                <slot />
            </WebMainContent>
        </div>

        <Toaster position="top-right" rich-colors />
        <OnboardingTour />
    </div>
</template>
