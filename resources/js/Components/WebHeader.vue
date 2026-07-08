<script setup lang="ts">
import { usePage } from '@inertiajs/vue3';
import { Search, Bell } from 'lucide-vue-next';
import { Avatar, AvatarFallback, AvatarImage } from '@/Components/ui/avatar';
import { Input } from '@/Components/ui/input';
import { Button } from '@/Components/ui/button';

defineSlots<{
    default: [];
    actions: [];
}>();

const page = usePage();
const user = (page.props.auth as any)?.user;
</script>

<template>
    <header class="flex items-center justify-between p-3 mb-3 rounded-2xl glass-panel border-white/20 shadow-sm gap-4">
        <div class="flex items-center gap-6 flex-1">
            <h1 class="text-lg font-semibold tracking-tight text-foreground whitespace-nowrap">
                <slot />
            </h1>

            <!-- Global Search -->
            <div class="hidden md:flex items-center relative max-w-md w-full">
                <Search class="absolute left-3 h-4 w-4 text-muted-foreground" />
                <Input
                    placeholder="Buscar vehículo, documento..."
                    class="pl-9 bg-white/10 border-white/10 focus:bg-white/20 transition-all h-9 text-sm"
                />
            </div>
        </div>

        <div class="flex items-center gap-3">
            <Button variant="ghost" size="icon" class="rounded-full glass-button h-9 w-9">
                <Bell class="h-4 w-4" />
            </Button>
        </div>
    </header>
</template>
