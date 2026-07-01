<script setup lang="ts">
import { onMounted, provide, ref } from 'vue';
import { Link } from '@inertiajs/vue3';
import { useFcm } from '@/Composables/useFcm';
import { Toaster } from 'vue-sonner';
import { PanelLeftClose, PanelLeftOpen, Menu } from 'lucide-vue-next';

import AppSidebar from '@/Components/AppSidebar.vue';

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
    <div class="flex h-dvh w-full overflow-hidden bg-muted/30">
        <!-- Sidebar (desktop) -->
        <aside
            :class="[
                'hidden md:flex shrink-0 h-full border-r border-border bg-card transition-[width] duration-300 ease-in-out',
                sidebarOpen ? 'w-64' : 'w-16',
            ]"
        >
            <AppSidebar :collapsed="!sidebarOpen" @navigate="toggleSidebar" />
        </aside>

        <!-- Sidebar (mobile drawer) -->
        <transition
            enter-active-class="transition-opacity duration-200"
            leave-active-class="transition-opacity duration-200"
            enter-from-class="opacity-0"
            leave-to-class="opacity-0"
        >
            <div
                v-if="mobileOpen"
                class="fixed inset-0 z-40 bg-black/50 md:hidden"
                @click="closeMobile"
            />
        </transition>
        <transition
            enter-active-class="transition-transform duration-300 ease-out"
            leave-active-class="transition-transform duration-300 ease-in"
            enter-from-class="-translate-x-full"
            leave-to-class="-translate-x-full"
        >
            <aside
                v-if="mobileOpen"
                class="fixed inset-y-0 left-0 z-50 flex h-full w-72 flex-col border-r border-border bg-card md:hidden shadow-xl"
            >
                <AppSidebar :collapsed="false" @navigate="closeMobile" />
            </aside>
        </transition>

        <!-- Main content -->
        <div class="flex min-w-0 flex-1 flex-col">
            <!-- Header -->
            <header
                class="flex h-16 shrink-0 items-center gap-3 border-b border-border bg-card/80 backdrop-blur-xl px-4 md:px-6 shadow-sm"
            >
                <Button
                    variant="ghost"
                    size="icon"
                    class="md:hidden"
                    aria-label="Abrir menú"
                    @click="showMobile"
                >
                    <Menu class="h-5 w-5" />
                </Button>

                <Button
                    variant="ghost"
                    size="icon"
                    class="hidden md:inline-flex"
                    :aria-label="sidebarOpen ? 'Cerrar sidebar' : 'Abrir sidebar'"
                    @click="toggleSidebar"
                >
                    <PanelLeftClose v-if="sidebarOpen" class="h-5 w-5" />
                    <PanelLeftOpen v-else class="h-5 w-5" />
                </Button>

                <Separator orientation="vertical" class="h-6" />

                <Breadcrumb class="min-w-0 flex-1">
                    <BreadcrumbList>
                        <BreadcrumbItem>
                            <BreadcrumbLink as-child>
                                <Link :href="route('dashboard')" class="text-sm font-medium">
                                    Dashboard
                                </Link>
                            </BreadcrumbLink>
                        </BreadcrumbItem>
                        <template v-if="$slots.header">
                            <BreadcrumbSeparator />
                            <BreadcrumbItem>
                                <BreadcrumbPage class="text-sm text-muted-foreground">
                                    <slot name="header" />
                                </BreadcrumbPage>
                            </BreadcrumbItem>
                        </template>
                    </BreadcrumbList>
                </Breadcrumb>

                <slot name="header-actions" />
            </header>

            <!-- Content -->
            <main class="flex-1 overflow-auto">
                <div class="mx-auto w-full max-w-7xl p-4 md:p-6 lg:p-8">
                    <slot />
                </div>
            </main>

            <!-- Footer -->
            <footer class="shrink-0 border-t border-border bg-card px-4 py-3 text-center text-sm text-muted-foreground">
                <p>
                    © {{ new Date().getFullYear() }} GarageOS. Gestión inteligente de vehículos.
                </p>
            </footer>
        </div>

        <Toaster position="top-right" rich-colors />
    </div>
</template>
