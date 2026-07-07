<script setup lang="ts">
import WebSidebar from '@/Components/WebSidebar.vue';
import WebHeader from '@/Components/WebHeader.vue';
import WebMainContent from '@/Components/WebMainContent.vue';

interface WebLayoutProps {
  sidebarOpen?: boolean;
}

defineProps<WebLayoutProps>();
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
            class="hidden md:flex fixed top-3 left-3 bottom-3 z-30 w-64 shrink-0 rounded-2xl border backdrop-blur-2xl shadow-xl bg-card/50 border-white/15"
        >
            <WebSidebar />
        </aside>

        <!-- Main content -->
        <div class="flex min-w-0 flex-1 flex-col gap-3 p-3 md:pl-72">
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
    </div>
</template>