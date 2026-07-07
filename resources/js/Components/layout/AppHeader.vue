<script setup lang="ts">
import { inject, computed } from 'vue';
import { Link } from '@inertiajs/vue3';
import { Button } from '@/Components/ui/button';
import { Separator } from '@/Components/ui/separator';
import { 
    Breadcrumb, 
    BreadcrumbItem, 
    BreadcrumbLink, 
    BreadcrumbList, 
    BreadcrumbPage, 
    BreadcrumbSeparator 
} from '@/Components/ui/breadcrumb';
import { PanelLeftClose, PanelLeftOpen, Menu } from 'lucide-vue-next';
import ThemeToggle from '@/Components/ThemeToggle.vue';

const sidebarOpen = inject<import('vue').Ref<boolean>>('sidebarOpen');
const mobileOpen = inject<import('vue').Ref<boolean>>('mobileSidebarOpen');

const isSidebarOpen = computed(() => sidebarOpen?.value ?? false);
const isMobileOpen = computed(() => mobileOpen?.value ?? false);

const toggleSidebar = () => {
    if (sidebarOpen) sidebarOpen.value = !sidebarOpen.value;
};

const showMobile = () => {
    if (mobileOpen) mobileOpen.value = true;
};
</script>

<template>
    <header
        :class="[
            'shrink-0 flex h-16 items-center gap-3 rounded-xl border backdrop-blur-xl px-4 md:px-6 transition-all duration-300',
            'bg-card/45 border-white/12 shadow-md supports-backdrop-filter:bg-card/45'
        ]"
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
            :aria-label="isSidebarOpen ? 'Cerrar sidebar' : 'Abrir sidebar'"
            @click="toggleSidebar"
        >
            <PanelLeftClose v-if="isSidebarOpen" class="h-5 w-5" />
            <PanelLeftOpen v-else class="h-5 w-5" />
        </Button>

        <Separator
            orientation="vertical"
            class="h-6 bg-border"
        />

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

        <ThemeToggle />
        <slot name="header-actions" />
    </header>
</template>
