<script setup lang="ts">
import { onMounted } from 'vue';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import { Link } from '@inertiajs/vue3';
import { useFcm } from '@/Composables/useFcm';
import { Button } from '@/Components/ui/button';
import {
  DropdownMenu,
  DropdownMenuContent,
  DropdownMenuItem,
  DropdownMenuTrigger,
  DropdownMenuSeparator,
  DropdownMenuLabel,
} from '@/Components/ui/dropdown-menu';
import {
  Sheet,
  SheetContent,
  SheetTrigger,
  SheetHeader,
  SheetTitle,
} from '@/Components/ui/sheet';
import { Menu, ChevronDown, LayoutDashboard, Car, User, LogOut } from 'lucide-vue-next';

const { registerFcm, addListeners } = useFcm();

onMounted(() => {
    if ('PushNotifications' in window) {
        registerFcm();
        addListeners();
    }
});
</script>

<template>
    <div>
        <div class="min-h-screen bg-background">
            <nav class="border-b border-border bg-card">
                <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                    <div class="flex h-16 justify-between">
                        <div class="flex">
                            <div class="flex shrink-0 items-center">
                                <Link :href="route('dashboard')">
                                    <ApplicationLogo class="block h-9 w-auto fill-current text-foreground" />
                                </Link>
                            </div>

                            <div class="hidden space-x-1 sm:-my-px sm:ms-10 sm:flex">
                                <Button
                                    as-child
                                    :variant="route().current('dashboard') ? 'secondary' : 'ghost'"
                                    size="sm"
                                >
                                    <Link :href="route('dashboard')">
                                        <LayoutDashboard class="mr-2 h-4 w-4" />
                                        Dashboard
                                    </Link>
                                </Button>
                                <Button
                                    as-child
                                    :variant="route().current('vehicles.*') ? 'secondary' : 'ghost'"
                                    size="sm"
                                >
                                    <Link :href="route('vehicles.index')">
                                        <Car class="mr-2 h-4 w-4" />
                                        Vehículos
                                    </Link>
                                </Button>
                            </div>
                        </div>

                        <div class="hidden sm:ms-6 sm:flex sm:items-center">
                            <DropdownMenu>
                                <DropdownMenuTrigger as-child>
                                    <Button variant="ghost" size="sm">
                                        {{ $page.props.auth.user.name }}
                                        <ChevronDown class="ml-2 h-4 w-4" />
                                    </Button>
                                </DropdownMenuTrigger>
                                <DropdownMenuContent align="end" class="w-56">
                                    <DropdownMenuLabel class="font-normal">
                                        <div class="flex flex-col space-y-1">
                                            <p class="text-sm font-medium">{{ $page.props.auth.user.name }}</p>
                                            <p class="text-xs text-muted-foreground">{{ $page.props.auth.user.email }}</p>
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
                        </div>

                        <!-- Mobile menu -->
                        <div class="-me-2 flex items-center sm:hidden">
                            <Sheet>
                                <SheetTrigger as-child>
                                    <Button variant="ghost" size="icon">
                                        <Menu class="h-5 w-5" />
                                    </Button>
                                </SheetTrigger>
                                <SheetContent side="right" class="w-72">
                                    <SheetHeader>
                                        <SheetTitle>{{ $page.props.auth.user.name }}</SheetTitle>
                                    </SheetHeader>
                                    <div class="mt-4 space-y-2">
                                        <Button as-child variant="ghost" class="w-full justify-start">
                                            <Link :href="route('dashboard')">
                                                <LayoutDashboard class="mr-2 h-4 w-4" />
                                                Dashboard
                                            </Link>
                                        </Button>
                                        <Button as-child variant="ghost" class="w-full justify-start">
                                            <Link :href="route('vehicles.index')">
                                                <Car class="mr-2 h-4 w-4" />
                                                Vehículos
                                            </Link>
                                        </Button>
                                        <Button as-child variant="ghost" class="w-full justify-start">
                                            <Link :href="route('profile.edit')">
                                                <User class="mr-2 h-4 w-4" />
                                                Perfil
                                            </Link>
                                        </Button>
                                        <Button as-child variant="ghost" class="w-full justify-start text-destructive">
                                            <Link :href="route('logout')" method="post" as="button">
                                                <LogOut class="mr-2 h-4 w-4" />
                                                Cerrar sesión
                                            </Link>
                                        </Button>
                                    </div>
                                </SheetContent>
                            </Sheet>
                        </div>
                    </div>
                </div>
            </nav>

            <header class="bg-card shadow" v-if="$slots.header">
                <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
                    <slot name="header" />
                </div>
            </header>

            <main>
                <slot />
            </main>

            <Toaster position="top-right" :richColors="true" />
        </div>
    </div>
</template>
