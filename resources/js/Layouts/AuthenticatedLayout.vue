<script setup lang="ts">
import { onMounted } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import { useFcm } from '@/Composables/useFcm';
import Toaster from 'vue-sonner';

import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarGroup,
    SidebarGroupContent,
    SidebarGroupLabel,
    SidebarHeader,
    SidebarInset,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
    SidebarProvider,
    SidebarRail,
    SidebarSeparator,
    SidebarTrigger,
} from '@/Components/ui/sidebar';

import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuLabel,
    DropdownMenuSeparator,
    DropdownMenuTrigger,
} from '@/Components/ui/dropdown-menu';

import { Avatar, AvatarFallback } from '@/Components/ui/avatar';
import { Breadcrumb, BreadcrumbItem, BreadcrumbLink, BreadcrumbList, BreadcrumbPage, BreadcrumbSeparator } from '@/Components/ui/breadcrumb';
import { Separator } from '@/Components/ui/separator';

import { ChevronUp, LayoutDashboard, Car, Wrench, FileText, ShoppingBag, CreditCard, Settings, User, LogOut, BarChart3 } from 'lucide-vue-next';

const { registerFcm, addListeners } = useFcm();
const page = usePage();

onMounted(() => {
    if ('PushNotifications' in window) {
        registerFcm();
        addListeners();
    }
});

const navMain = [
    { title: 'Dashboard', route: 'dashboard', icon: LayoutDashboard },
    { title: 'Vehículos', route: 'vehicles.index', icon: Car },
    { title: 'Mantenimiento', route: 'maintenance.index', icon: Wrench },
    { title: 'Documentos', route: 'documents.index', icon: FileText },
    { title: 'Marketplace', route: 'marketplace.index', icon: ShoppingBag },
    { title: 'Importar', route: 'vehicles.import', icon: BarChart3 },
];

const navSecondary = [
    { title: 'Suscripción', route: 'subscription.index', icon: CreditCard },
    { title: 'Configuración', route: 'profile.edit', icon: Settings },
];

const isActive = (routeName: string) => {
    try {
        const path = page.url;
        const section = routeName.split('.')[0];
        return path.startsWith('/' + section);
    } catch {
        return false;
    }
};
</script>

<template>
    <SidebarProvider>
        <Sidebar collapsible="icon" variant="sidebar">
            <SidebarHeader>
                <SidebarMenu>
                    <SidebarMenuItem>
                        <SidebarMenuButton size="lg" as-child>
                            <Link :href="route('dashboard')">
                                <div class="flex aspect-square size-8 items-center justify-center rounded-lg bg-primary text-primary-foreground">
                                    <Car class="size-4" />
                                </div>
                                <div class="grid flex-1 text-left text-sm leading-tight">
                                    <span class="truncate font-semibold">GarageOS</span>
                                    <span class="truncate text-xs text-muted-foreground">Gestión vehículos</span>
                                </div>
                            </Link>
                        </SidebarMenuButton>
                    </SidebarMenuItem>
                </SidebarMenu>
            </SidebarHeader>

            <SidebarContent>
                <SidebarGroup>
                    <SidebarGroupLabel>Principal</SidebarGroupLabel>
                    <SidebarGroupContent>
                        <SidebarMenu>
                            <SidebarMenuItem v-for="item in navMain" :key="item.route">
                                <SidebarMenuButton as-child :tooltip="item.title" :isActive="isActive(item.route)">
                                    <Link :href="route(item.route)">
                                        <component :is="item.icon" />
                                        <span>{{ item.title }}</span>
                                    </Link>
                                </SidebarMenuButton>
                            </SidebarMenuItem>
                        </SidebarMenu>
                    </SidebarGroupContent>
                </SidebarGroup>

                <SidebarSeparator />

                <SidebarGroup>
                    <SidebarGroupLabel>Cuenta</SidebarGroupLabel>
                    <SidebarGroupContent>
                        <SidebarMenu>
                            <SidebarMenuItem v-for="item in navSecondary" :key="item.route">
                                <SidebarMenuButton as-child :tooltip="item.title" :isActive="isActive(item.route)">
                                    <Link :href="route(item.route)">
                                        <component :is="item.icon" />
                                        <span>{{ item.title }}</span>
                                    </Link>
                                </SidebarMenuButton>
                            </SidebarMenuItem>
                        </SidebarMenu>
                    </SidebarGroupContent>
                </SidebarGroup>
            </SidebarContent>

            <SidebarFooter>
                <SidebarMenu>
                    <SidebarMenuItem>
                        <DropdownMenu>
                            <DropdownMenuTrigger as-child>
                                <SidebarMenuButton size="lg">
                                    <Avatar class="h-8 w-8 rounded-lg">
                                        <AvatarFallback class="rounded-lg bg-primary text-primary-foreground">
                                            {{ ($page.props.auth.user?.name || 'U').charAt(0).toUpperCase() }}
                                        </AvatarFallback>
                                    </Avatar>
                                    <div class="grid flex-1 text-left text-sm leading-tight">
                                        <span class="truncate font-semibold">{{ $page.props.auth.user?.name }}</span>
                                        <span class="truncate text-xs text-muted-foreground">{{ $page.props.auth.user?.email }}</span>
                                    </div>
                                    <ChevronUp class="ml-auto size-4" />
                                </SidebarMenuButton>
                            </DropdownMenuTrigger>
                            <DropdownMenuContent class="w-[--radix-dropdown-menu-trigger-width] min-w-56 rounded-lg" side="bottom" align="end" :side-offset="4">
                                <DropdownMenuLabel class="p-0 font-normal">
                                    <div class="flex items-center gap-2 px-1 py-1.5 text-left text-sm">
                                        <Avatar class="h-8 w-8 rounded-lg">
                                            <AvatarFallback class="rounded-lg bg-primary text-primary-foreground">
                                                {{ ($page.props.auth.user?.name || 'U').charAt(0).toUpperCase() }}
                                            </AvatarFallback>
                                        </Avatar>
                                        <div class="grid flex-1 text-left text-sm leading-tight">
                                            <span class="truncate font-semibold">{{ $page.props.auth.user?.name }}</span>
                                            <span class="truncate text-xs text-muted-foreground">{{ $page.props.auth.user?.email }}</span>
                                        </div>
                                    </div>
                                </DropdownMenuLabel>
                                <DropdownMenuSeparator />
                                <DropdownMenuItem as-child>
                                    <Link :href="route('profile.edit')" class="cursor-pointer">
                                        <User class="mr-2 h-4 w-4" />
                                        Perfil
                                    </Link>
                                </DropdownMenuItem>
                                <DropdownMenuSeparator />
                                <DropdownMenuItem as-child>
                                    <Link :href="route('logout')" method="post" as="button" class="cursor-pointer w-full text-destructive">
                                        <LogOut class="mr-2 h-4 w-4" />
                                        Cerrar sesión
                                    </Link>
                                </DropdownMenuItem>
                            </DropdownMenuContent>
                        </DropdownMenu>
                    </SidebarMenuItem>
                </SidebarMenu>
            </SidebarFooter>
            <SidebarRail />
        </Sidebar>

        <SidebarInset>
            <header class="flex h-16 shrink-0 items-center gap-2 border-b border-border px-4">
                <SidebarTrigger class="-ml-1" />
                <Separator orientation="vertical" class="mr-2 h-4" />
                <Breadcrumb>
                    <BreadcrumbList>
                        <BreadcrumbItem>
                            <BreadcrumbLink as-child>
                                <Link :href="route('dashboard')">Dashboard</Link>
                            </BreadcrumbLink>
                        </BreadcrumbItem>
                        <BreadcrumbSeparator v-if="$slots.header" />
                        <BreadcrumbItem v-if="$slots.header">
                            <BreadcrumbPage>
                                <slot name="header" />
                            </BreadcrumbPage>
                        </BreadcrumbItem>
                    </BreadcrumbList>
                </Breadcrumb>
            </header>

            <div class="flex flex-1 flex-col gap-4 p-4 pt-0">
                <slot />
            </div>

            <Toaster position="top-right" :richColors="true" />
        </SidebarInset>
    </SidebarProvider>
</template>
