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

import { ChevronUp, LayoutDashboard, Car, Wrench, FileText, ShoppingBag, CreditCard, Settings, User, LogOut, BarChart3, Shield, Calendar, Users, Receipt, TrendingUp } from 'lucide-vue-next';

const { registerFcm, addListeners } = useFcm();
const page = usePage();

onMounted(() => {
    if ('PushNotifications' in window) {
        registerFcm();
        addListeners();
    }
});

const navMain = [
    { title: 'Dashboard', route: 'dashboard', icon: LayoutDashboard, badge: null },
    { title: 'Vehículos', route: 'vehicles.index', icon: Car, badge: null },
    { title: 'Mantenimiento', route: 'maintenance.index', icon: Wrench, badge: null },
    { title: 'Documentos', route: 'documents.index', icon: FileText, badge: null },
    { title: 'Marketplace', route: 'marketplace.index', icon: ShoppingBag, badge: null },
    { title: 'Planes', route: 'subscriptions.index', icon: Shield, badge: 'new' },
    { title: 'Reportes', route: 'reports.index', icon: TrendingUp, badge: null },
];

const navSecondary = [
    { title: 'Rutinas', route: 'routines.index', icon: Calendar },
    { title: 'Clientes', route: 'clients.index', icon: Users },
    { title: 'Facturación', route: 'billing.index', icon: Receipt },
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
        <Sidebar collapsible="icon" variant="sidebar" class="border-r border-sidebar-border bg-card">
            <SidebarHeader class="border-b border-sidebar-border bg-muted/30">
                <SidebarMenu>
                    <SidebarMenuItem>
                        <SidebarMenuButton size="lg" as-child class="hover:bg-transparent">
                            <Link :href="route('dashboard')" class="flex items-center gap-3">
                                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-linear-to-br from-primary to-primary/80 text-primary-foreground shadow-lg">
                                    <Car class="h-5 w-5" />
                                </div>
                                <div class="grid flex-1 text-left text-sm leading-tight">
                                    <span class="truncate font-bold text-foreground">GarageOS</span>
                                    <span class="truncate text-xs text-muted-foreground">Gestión inteligente</span>
                                </div>
                            </Link>
                        </SidebarMenuButton>
                    </SidebarMenuItem>
                </SidebarMenu>
            </SidebarHeader>

            <SidebarContent class="bg-muted/10">
                <SidebarGroup>
                    <SidebarGroupLabel class="text-xs font-semibold uppercase tracking-wider text-muted-foreground">
                        Principal
                    </SidebarGroupLabel>
                    <SidebarGroupContent>
                        <SidebarMenu>
                            <SidebarMenuItem v-for="item in navMain" :key="item.route" class="relative">
                                <SidebarMenuButton as-child :tooltip="item.title" :isActive="isActive(item.route)"
                                    class="group transition-all duration-200 hover:translate-x-1">
                                    <Link :href="route(item.route)" class="relative">
                                        <div class="flex items-center gap-3">
                                            <component :is="item.icon" class="h-4 w-4 shrink-0" />
                                            <span class="truncate">{{ item.title }}</span>
                                        </div>
                                        <span v-if="item.badge" class="absolute -top-1 -right-1 flex h-5 min-w-5 items-center justify-center rounded-full bg-destructive px-1 text-xs font-medium text-destructive-foreground">
                                            {{ item.badge }}
                                        </span>
                                    </Link>
                                </SidebarMenuButton>
                            </SidebarMenuItem>
                        </SidebarMenu>
                    </SidebarGroupContent>
                </SidebarGroup>

                <SidebarSeparator class="mx-4" />

                <SidebarGroup>
                    <SidebarGroupLabel class="text-xs font-semibold uppercase tracking-wider text-muted-foreground">
                        Operación
                    </SidebarGroupLabel>
                    <SidebarGroupContent>
                        <SidebarMenu>
                            <SidebarMenuItem v-for="item in navSecondary" :key="item.route">
                                <SidebarMenuButton as-child :tooltip="item.title" :isActive="isActive(item.route)"
                                    class="group transition-all duration-200 hover:translate-x-1">
                                    <Link :href="route(item.route)">
                                        <div class="flex items-center gap-3">
                                            <component :is="item.icon" class="h-4 w-4 shrink-0" />
                                            <span class="truncate">{{ item.title }}</span>
                                        </div>
                                    </Link>
                                </SidebarMenuButton>
                            </SidebarMenuItem>
                        </SidebarMenu>
                    </SidebarGroupContent>
                </SidebarGroup>
            </SidebarContent>

            <SidebarFooter class="border-t border-sidebar-border bg-muted/30">
                <SidebarMenu>
                    <SidebarMenuItem>
                        <DropdownMenu>
                            <DropdownMenuTrigger as-child>
                                <SidebarMenuButton size="lg" class="w-full justify-start gap-3 hover:bg-sidebar-accent">
                                    <Avatar class="h-9 w-9 rounded-xl">
                                        <AvatarFallback class="rounded-xl bg-linear-to-br from-primary to-primary/80 text-primary-foreground">
                                            {{ ($page.props.auth.user?.name || 'U').charAt(0).toUpperCase() }}
                                        </AvatarFallback>
                                    </Avatar>
                                    <div class="grid flex-1 text-left text-sm leading-tight">
                                        <span class="truncate font-semibold">{{ $page.props.auth.user?.name }}</span>
                                        <span class="truncate text-xs text-muted-foreground">{{ $page.props.auth.user?.email }}</span>
                                    </div>
                                    <ChevronUp class="ml-auto size-4 mt-1" />
                                </SidebarMenuButton>
                            </DropdownMenuTrigger>
                            <DropdownMenuContent class="w-[--radix-dropdown-menu-trigger-width] min-w-64 rounded-xl" side="bottom" align="end" :side-offset="6">
                                <DropdownMenuLabel class="p-0 font-normal">
                                    <div class="flex items-center gap-3 px-2 py-2.5 text-left text-sm">
                                        <Avatar class="h-10 w-10 rounded-xl">
                                            <AvatarFallback class="rounded-xl bg-linear-to-br from-primary to-primary/80 text-primary-foreground">
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
                                    <Link :href="route('profile.edit')" class="cursor-pointer flex items-center gap-2">
                                        <User class="h-4 w-4" />
                                        <span>Perfil</span>
                                    </Link>
                                </DropdownMenuItem>
                                <DropdownMenuSeparator />
                                <DropdownMenuItem as-child>
                                    <Link :href="route('logout')" method="post" as="button" class="cursor-pointer w-full flex items-center gap-2 text-destructive">
                                        <LogOut class="h-4 w-4" />
                                        <span>Cerrar sesión</span>
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
            <header class="flex h-16 items-center gap-2 border-b border-border bg-card px-4 shadow-sm">
                <SidebarTrigger class="-ml-1 h-8 w-8 rounded-lg hover:bg-muted" />
                <Separator orientation="vertical" class="mr-2 h-6" />
                <Breadcrumb>
                    <BreadcrumbList>
                        <BreadcrumbItem>
                            <BreadcrumbLink as-child>
                                <Link :href="route('dashboard')" class="text-sm font-medium">Dashboard</Link>
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

            <Toaster position="top-right" :richColors="true" closeButton />
        </SidebarInset>
    </SidebarProvider>
</template>
