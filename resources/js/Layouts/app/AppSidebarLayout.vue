<script setup lang="ts">
import { onMounted } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import { useFcm } from '@/Composables/useFcm';
import Toaster from 'vue-sonner';

import AppSidebar from '@/Components/AppSidebar.vue';
import {
    SidebarInset,
    SidebarTrigger,
} from '@/Components/ui/sidebar';

import {
    Breadcrumb,
    BreadcrumbItem,
    BreadcrumbLink,
    BreadcrumbList,
    BreadcrumbPage,
    BreadcrumbSeparator,
} from '@/Components/ui/breadcrumb';
import { Separator } from '@/Components/ui/separator';

const { registerFcm, addListeners } = useFcm();

onMounted(() => {
    if ('PushNotifications' in window) {
        registerFcm();
        addListeners();
    }
});
</script>

<template>
    <AppSidebar />

    <SidebarInset>
        <header class="flex h-16 items-center gap-2 border-b border-border bg-card/80 supports-backdrop-filter:bg-card/60 backdrop-blur px-4 shadow-sm">
            <SidebarTrigger class="-ml-1 h-8 w-8 rounded-lg hover:bg-muted" />
            <Separator orientation="vertical" class="mr-2 h-6" />
            <Breadcrumb>
                <BreadcrumbList>
                    <BreadcrumbItem>
                        <BreadcrumbLink as-child>
                            <Link :href="route('dashboard')" class="text-sm font-medium text-foreground">Dashboard</Link>
                        </BreadcrumbLink>
                    </BreadcrumbItem>
                    <BreadcrumbSeparator v-if="$slots.header" />
                    <BreadcrumbItem v-if="$slots.header">
                        <BreadcrumbPage class="text-sm text-muted-foreground">
                            <slot name="header" />
                        </BreadcrumbPage>
                    </BreadcrumbItem>
                </BreadcrumbList>
            </Breadcrumb>
        </header>

        <main class="flex flex-1 flex-col gap-4 p-4 md:p-6 pt-0">
            <slot />
        </main>
    </SidebarInset>
</template>