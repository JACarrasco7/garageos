<script setup lang="ts">
import { Link } from '@inertiajs/vue3'
import { BellIcon, UserIcon, CogIcon, ArrowLeftOnRectangleIcon } from '@heroicons/vue/24/outline'
import NotificationBell from '@/Components/NotificationBell.vue'

interface Props {
  user: {
    name: string
    email: string
  }
}

defineProps<Props>()
</script>

<template>
  <header class="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
    <div class="flex items-center justify-between px-6 py-4">
      <div class="flex items-center gap-4">
        <slot name="breadcrumbs" />
      </div>

      <div class="flex items-center gap-4">
        <NotificationBell />

        <div class="relative">
          <button
            class="flex items-center gap-2 text-gray-700 dark:text-gray-300 hover:text-gray-900 dark:hover:text-gray-100"
            id="user-menu"
          >
            <UserIcon class="w-5 h-5" />
            <span class="text-sm font-medium">{{ user.name }}</span>
          </button>

          <div class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 rounded-md shadow-lg py-1 hidden">
            <Link
              :href="route('profile.edit')"
              class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700"
            >
              Configuración
            </Link>
            <Link
              :href="route('logout')"
              method="post"
              class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700"
            >
              Cerrar sesión
            </Link>
          </div>
        </div>
      </div>
    </div>
  </header>
</template>