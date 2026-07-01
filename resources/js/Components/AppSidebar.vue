<script setup lang="ts">
import { computed } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import {
    LayoutDashboard,
    Car,
    Wrench,
    Upload,
    Download,
    Shield,
    Settings,
} from 'lucide-vue-next';

import { cn } from '@/lib/utils';
import { Avatar, AvatarFallback } from '@/Components/ui/avatar';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuLabel,
    DropdownMenuSeparator,
    DropdownMenuTrigger,
} from '@/Components/ui/dropdown-menu';

defineProps<{
    collapsed?: boolean;
}>();

const emit = defineEmits<{
    navigate: [];
}>();

const page = usePage();

type NavItem = {
    title: string;
    route: string;
    icon: typeof Car;
    badge?: string | null;
};

const navGroups = computed<{ label: string; items: NavItem[] }[]>(() => [
    {
        label: 'Principal',
        items: [
            { title: 'Dashboard', route: 'dashboard', icon: LayoutDashboard },
            { title: 'Vehículos', route: 'vehicles.index', icon: Car },
            { title: 'Talleres', route: 'workshops.index', icon: Wrench },
            { title: 'Planes', route: 'subscription.index', icon: Shield, badge: 'new' },
        ],
    },
    {
        label: 'Operación',
        items: [
            { title: 'Importar', route: 'vehicles.import.create', icon: Upload },
            { title: 'Exportar', route: 'vehicles.export', icon: Download },
            { title: 'Configuración', route: 'profile.edit', icon: Settings },
        ],
    },
]);

const isActive = (routeName: string) => {
    try {
        const path = page.url;
        const section = routeName.split('.')[0];
        return path === '/' + section || path.startsWith('/' + section + '/');
    } catch {
        return false;
    }
};

const user = computed(() => (page.props.auth as { user?: { name?: string; email?: string } } | undefined)?.user);
</script>

<template>
    <div class="flex h-full w-full flex-col">
        <!-- Header / Logo -->
        <div
            :class="[
                'flex shrink-0 items-center border-b border-border bg-gradient-to-r from-primary/5 to-accent/5 h-16 backdrop-blur-xl',
                collapsed ? 'justify-center px-2' : 'gap-3 px-4',
            ]"
        >
            <Link
                :href="route('dashboard')"
                :class="cn(
                    'flex items-center gap-3 rounded-lg text-foreground hover:bg-muted/50',
                    collapsed ? 'h-10 w-10 justify-center' : 'h-10 flex-1 px-2',
                )"
            >
                <div
                    class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br from-primary to-primary/80 text-primary-foreground shadow"
                >
                    <Car class="h-4 w-4" />
                </div>
                <div v-if="!collapsed" class="grid min-w-0 flex-1 text-left text-sm leading-tight">
                    <span class="truncate font-bold">GarageOS</span>
                    <span class="truncate text-xs text-muted-foreground">
                        Gestión inteligente
                    </span>
                </div>
            </Link>
        </div>

        <!-- Nav -->
        <nav class="flex-1 overflow-y-auto px-2 py-4">
            <div
                v-for="(group, gi) in navGroups"
                :key="group.label"
                :class="gi > 0 ? 'mt-6' : ''"
            >
                <div
                    v-if="!collapsed"
                    class="px-3 pb-2 text-xs font-semibold uppercase tracking-wider text-muted-foreground"
                >
                    {{ group.label }}
                </div>
                <ul class="flex flex-col gap-1">
                    <li v-for="item in group.items" :key="item.route">
                        <Link
                            :href="route(item.route)"
                            @click="emit('navigate')"
                            :class="cn(
                                'group relative flex items-center rounded-lg text-sm font-medium transition-all duration-200',
                                collapsed ? 'h-10 w-10 justify-center mx-auto' : 'h-10 gap-3 px-3',
                                isActive(item.route)
                                    ? 'bg-primary/10 text-primary shadow-inner'
                                    : 'text-foreground/70 hover:bg-muted hover:text-foreground hover:translate-x-1',
                            )"
                            :title="collapsed ? item.title : undefined"
                        >
                            <component
                                :is="item.icon"
                                :class="cn('h-4 w-4 shrink-0 transition-transform duration-200 group-hover:scale-110', isActive(item.route) && 'text-primary')"
                            />
                            <span v-if="!collapsed" class="truncate flex-1">
                                {{ item.title }}
                            </span>
                            <span
                                v-if="item.badge && !collapsed"
                                class="flex h-5 min-w-5 items-center justify-center rounded-full bg-accent text-accent-foreground px-1.5 text-[10px] font-semibold"
                            >
                                {{ item.badge }}
                            </span>
                            <span
                                v-if="item.badge && collapsed"
                                class="absolute top-1 right-1 h-2 w-2 rounded-full bg-accent"
                            />
                            <span
                                v-if="isActive(item.route)"
                                class="absolute left-0 top-1/2 -translate-y-1/2 h-6 w-0.5 bg-primary rounded-full"
                            ></span>
                        </Link>
                    </li>
                </ul>
            </div>
        </nav>

        <!-- Footer / User -->
        <div class="shrink-0 border-t border-border bg-gradient-to-r from-muted/30 to-transparent p-2">
            <DropdownMenu>
                <DropdownMenuTrigger as-child>
                    <button
                        :class="cn(
                            'flex w-full items-center rounded-lg text-left transition-all duration-200 hover:bg-muted/50',
                            collapsed ? 'h-10 w-10 justify-center mx-auto' : 'gap-3 px-2 py-1.5',
                        )"
                    >
                        <div class="relative">
                            <Avatar :class="cn('shrink-0', collapsed ? 'h-8 w-8' : 'h-9 w-9')">
                                <AvatarFallback
                                    :class="cn(
                                        'rounded-lg bg-gradient-to-br from-primary to-primary/80 text-primary-foreground font-semibold',
                                        collapsed ? 'text-xs' : 'text-sm',
                                    )"
                                >
                                    {{ (user?.name || 'U').charAt(0).toUpperCase() }}
                                </AvatarFallback>
                            </Avatar>
                            <span class="absolute bottom-0 right-0 h-3 w-3 rounded-full border-2 border-background bg-green-500"></span>
                        </div>
                        <div v-if="!collapsed" class="grid min-w-0 flex-1 text-left text-sm leading-tight">
                            <span class="truncate font-semibold">
                                {{ user?.name || 'Usuario' }}
                            </span>
                            <span class="truncate text-xs text-muted-foreground">
                                {{ user?.email || '' }}
                            </span>
                        </div>
                    </button>
                </DropdownMenuTrigger>
                <DropdownMenuContent
                    class="min-w-56 rounded-xl"
                    side="top"
                    align="end"
                    :side-offset="6"
                >
                    <DropdownMenuLabel class="p-0 font-normal">
                        <div class="flex items-center gap-3 px-2 py-2.5 text-left text-sm">
                            <Avatar class="h-10 w-10 rounded-lg">
                                <AvatarFallback
                                    class="rounded-lg bg-gradient-to-br from-primary to-primary/80 text-primary-foreground font-semibold"
                                >
                                    {{ (user?.name || 'U').charAt(0).toUpperCase() }}
                                </AvatarFallback>
                            </Avatar>
                            <div class="grid min-w-0 flex-1 text-left text-sm leading-tight">
                                <span class="truncate font-semibold">
                                    {{ user?.name || 'Usuario' }}
                                </span>
                                <span class="truncate text-xs text-muted-foreground">
                                    {{ user?.email || '' }}
                                </span>
                            </div>
                        </div>
                    </DropdownMenuLabel>
                    <DropdownMenuSeparator />
                    <DropdownMenuItem as-child>
                        <Link
                            :href="route('profile.edit')"
                            class="flex cursor-pointer items-center gap-2"
                        >
                            <User class="h-4 w-4" />
                            <span>Perfil</span>
                        </Link>
                    </DropdownMenuItem>
                    <DropdownMenuSeparator />
                    <DropdownMenuItem as-child>
                        <Link
                            :href="route('logout')"
                            method="post"
                            as="button"
                            class="flex w-full cursor-pointer items-center gap-2 text-destructive focus:text-destructive"
                        >
                            <LogOut class="h-4 w-4" />
                            <span>Cerrar sesión</span>
                        </Link>
                    </DropdownMenuItem>
                </DropdownMenuContent>
            </DropdownMenu>
        </div>
    </div>
</template>
