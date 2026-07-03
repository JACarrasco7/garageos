<script setup lang="ts">
import AppMobileLayout from '@/layouts/mobile/AppMobileLayout.vue';
import MobileHeader from '@/Components/mobile/MobileHeader.vue';
import MobileCard from '@/Components/mobile/MobileCard.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { User, LogOut, Shield, Building } from 'lucide-vue-next';

interface User {
  id: number;
  name: string;
  email: string;
  phone?: string;
}

interface Garage {
  id: number;
  name: string;
}

defineProps<{
  user: User;
  garages: Garage[];
}>();

const logout = () => {
  router.post('/logout');
};
</script>

<template>
  <Head title="Perfil" />

  <AppMobileLayout>
    <MobileHeader title="Perfil" />

    <div class="p-4 space-y-4">
      <MobileCard class="p-4 text-center">
        <div class="mx-auto flex h-20 w-20 items-center justify-center rounded-2xl bg-muted mb-3">
          <User class="h-10 w-10 text-muted-foreground" />
        </div>
        <h2 class="text-xl font-bold text-foreground">{{ user.name }}</h2>
        <p class="text-sm text-muted-foreground">{{ user.email }}</p>
        <p class="text-xs text-muted-foreground mt-1">
          {{ garages.length }} garaje{{ garages.length !== 1 ? 's' : '' }}
        </p>
      </MobileCard>

      <MobileCard class="p-0">
        <Link
          href="/profile"
          class="flex items-center gap-3 p-4 hover:bg-accent/50 transition-colors"
        >
          <User class="h-5 w-5 text-muted-foreground" />
          <span class="flex-1 text-left">Editar perfil</span>
        </Link>
        <Link
          href="/garages"
          class="flex items-center gap-3 p-4 hover:bg-accent/50 transition-colors border-t border-border"
        >
          <Building class="h-5 w-5 text-muted-foreground" />
          <span class="flex-1 text-left">Garajes</span>
        </Link>
        <Link
          href="/subscription"
          class="flex items-center gap-3 p-4 hover:bg-accent/50 transition-colors border-t border-border"
        >
          <Shield class="h-5 w-5 text-muted-foreground" />
          <span class="flex-1 text-left">Suscripción</span>
        </Link>
      </MobileCard>

      <MobileCard class="p-0">
        <button
          class="flex items-center gap-3 p-4 hover:bg-accent/50 transition-colors w-full text-left"
          @click="logout"
        >
          <LogOut class="h-5 w-5 text-muted-foreground" />
          <span class="flex-1">Cerrar sesión</span>
        </button>
      </MobileCard>
    </div>
  </AppMobileLayout>
</template>
