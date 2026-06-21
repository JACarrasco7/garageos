<script setup lang="ts">
import { ref, computed } from 'vue'
import { Link } from '@inertiajs/vue3'

interface Notification {
  id: number
  type: string
  title: string
  body: string
  read_at: string | null
  created_at: string
}

const props = defineProps<{
  notifications?: Notification[]
}>()

const isOpen = ref(false)
const unreadCount = computed(() => props.notifications?.filter(n => !n.read_at).length ?? 0)

const toggle = () => {
  isOpen.value = !isOpen.value
}

const close = () => {
  isOpen.value = false
}
</script>

<template>
  <div class="relative">
    <button
      @click="toggle"
      class="relative p-2 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200"
    >
      <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
      </svg>
      <span
        v-if="unreadCount > 0"
        class="absolute -top-1 -right-1 flex h-4 w-4 items-center justify-center rounded-full bg-red-500 text-xs text-white"
      >
        {{ unreadCount > 9 ? '9+' : unreadCount }}
      </span>
    </button>

    <div
      v-if="isOpen"
      class="absolute right-0 mt-2 w-80 rounded-md bg-white shadow-lg ring-1 ring-black ring-opacity-5 dark:bg-gray-800 z-50"
      @click.away="close"
    >
      <div class="p-3 border-b dark:border-gray-700">
        <h3 class="font-medium text-sm">Notificaciones</h3>
      </div>
      <div class="max-h-64 overflow-y-auto">
        <div v-if="!notifications?.length" class="p-4 text-center text-sm text-gray-500">
          No hay notificaciones
        </div>
        <div
          v-for="notification in notifications"
          :key="notification.id"
          class="p-3 border-b dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700/50"
          :class="{ 'bg-blue-50 dark:bg-blue-900/10': !notification.read_at }"
        >
          <p class="text-sm font-medium">{{ notification.title }}</p>
          <p class="text-xs text-gray-500 mt-1">{{ notification.body }}</p>
          <p class="text-xs text-gray-400 mt-1">{{ notification.created_at }}</p>
        </div>
      </div>
    </div>
  </div>
</template>
