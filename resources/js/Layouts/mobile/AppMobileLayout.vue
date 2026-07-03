<script setup lang="ts">
import { computed, onMounted, ref, watch } from 'vue';
import { usePage } from '@inertiajs/vue3';
import { Toaster } from 'vue-sonner';
import { Home, Car, Wrench, Upload, Bell, User } from 'lucide-vue-next';
import { cn } from '@/lib/utils';

const page = usePage();
const route = window.route;

const currentRoute = computed(() => page.url);

const navItems = [
  { title: 'Inicio', route: 'mobile.dashboard', icon: Home, path: '/m/dashboard' },
  { title: 'Vehículos', route: 'mobile.vehicles.index', icon: Car, path: '/m/vehicles' },
  { title: 'Manten.', route: 'mobile.maintenance.index', icon: Wrench, path: '/m/maintenance' },
  { title: 'Docs', route: 'mobile.documents.index', icon: Upload, path: '/m/documents' },
  { title: 'Alertas', route: 'mobile.alerts.index', icon: Bell, path: '/m/alerts' },
  { title: 'Perfil', route: 'mobile.profile', icon: User, path: '/m/profile' },
];

const isActive = (routeName: string) => {
  return currentRoute.value.startsWith('/' + routeName.split('.')[1]);
};
</script>

<template>
  <div class="flex h-dvh w-full flex-col bg-background">
    <!-- Status bar (iOS) -->
    <div class="h-safe-area-inset-top bg-background/95 backdrop-blur supports-[backdrop-filter]:bg-background/60" />

    <!-- Main content -->
    <main class="flex-1 overflow-y-auto pb-safe-area-inset-bottom">
      <slot />
    </main>

    <!-- Tab bar (bottom) -->
    <nav class="fixed bottom-0 left-0 right-0 z-50 grid grid-cols-6 border-t bg-card/95 backdrop-blur supports-[backdrop-filter]:bg-card/60 safe-area-inset-bottom">
      <template v-for="item in navItems" :key="item.route">
        <Link
          :href="route(item.route)"
          :class="cn(
            'flex flex-col items-center justify-center gap-1 py-2 px-1 transition-colors',
            'text-xs font-medium',
            isActive(item.route)
              ? 'text-primary'
              : 'text-muted-foreground hover:text-foreground'
          )"
        >
          <component
            :is="item.icon"
            :class="cn(
              'h-5 w-5',
              isActive(item.route) ? 'text-primary' : 'text-muted-foreground'
            )"
          />
          <span class="truncate">{{ item.title }}</span>
        </Link>
      </template>
    </nav>
  </div>

  <Toaster close-by="mobile-toast" />
</template>

<style scoped>
nav {
  height: calc(56px + env(safe-area-inset-bottom));
}
</style>
