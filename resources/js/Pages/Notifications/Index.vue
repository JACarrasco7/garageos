<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { router } from '@inertiajs/vue3'
import { Button } from '@/Components/ui/button'
import { Card, CardContent, CardHeader, CardTitle } from '@/Components/ui/card'
import { Badge } from '@/Components/ui/badge'
import { Bell, CheckCircle, Clock, MessageCircle, DollarSign, Search, Calendar } from 'lucide-vue-next'

interface Notification {
  id: number
  type: string
  title: string
  content: string
  read_at: string | null
  created_at: string
  data: any
}

const notifications = ref<Notification[]>([])
const unreadCount = ref(0)

const markAsRead = (id: number) => {
  router.post(`/notifications/${id}/read`, {}, {
    preserveScroll: true,
  })
}

const markAllAsRead = () => {
  router.post('/notifications/read-all', {}, {
    preserveScroll: true,
  })
}

const deleteNotification = (id: number) => {
  router.delete(`/notifications/${id}`, {
    preserveScroll: true,
  })
}

const getIcon = (type: string) => {
  switch (type) {
    case 'message': return MessageCircle
    case 'offer': return DollarSign
    case 'search_alert': return Search
    case 'import_reminder': return Calendar
    default: return Bell
  }
}

const getBadgeColor = (type: string) => {
  switch (type) {
    case 'message': return 'bg-blue-500'
    case 'offer': return 'bg-green-500'
    case 'search_alert': return 'bg-purple-500'
    case 'import_reminder': return 'bg-yellow-500'
    default: return 'bg-gray-500'
  }
}

const getTypeLabel = (type: string) => {
  switch (type) {
    case 'message': return 'Mensaje'
    case 'offer': return 'Oferta'
    case 'search_alert': return 'Alerta de Búsqueda'
    case 'import_reminder': return 'Recordatorio'
    default: return 'Notificación'
  }
}

const formatTime = (date: string) => {
  const now = new Date()
  const then = new Date(date)
  const diff = now.getTime() - then.getTime()
  const minutes = Math.floor(diff / (1000 * 60))

  if (minutes < 1) return 'Ahora'
  if (minutes < 60) return `Hace ${minutes} min`

  const hours = Math.floor(minutes / 60)
  if (hours < 24) return `Hace ${hours} h`

  const days = Math.floor(hours / 24)
  if (days === 1) return 'Ayer'
  if (days < 7) return `Hace ${days} días`

  return then.toLocaleDateString('es-ES', { day: '2-digit', month: 'short' })
}

onMounted(() => {
  router.get('/notifications', {}, {
    preserveState: true,
    onSuccess: (page) => {
      notifications.value = page.props.notifications as Notification[]
      unreadCount.value = page.props.unread_count as number
    }
  })
})
</script>

<template>
  <div class="max-w-4xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
      <div>
        <h1 class="text-3xl font-bold flex items-center gap-2">
          <Bell class="h-8 w-8" />
          Notificaciones
        </h1>
        <p class="text-muted-foreground mt-1">
          {{ unreadCount }} sin leer
        </p>
      </div>
      <div class="flex gap-2">
        <Button
          v-if="unreadCount > 0"
          variant="outline"
          @click="markAllAsRead"
        >
          Marcar como leídas
        </Button>
        <Button
          variant="ghost"
          @click="router.visit('/notifications/settings')"
        >
          Configurar
        </Button>
      </div>
    </div>

    <!-- Notifications List -->
    <div class="space-y-4">
      <div
        v-for="notification in notifications"
        :key="notification.id"
        class="border rounded-lg p-4 transition-colors"
        :class="{ 'bg-muted/50': !notification.read_at }"
      >
        <div class="flex items-start gap-4">
          <div class="mt-1">
            <component :is="getIcon(notification.type)" class="h-5 w-5 text-primary" />
          </div>

          <div class="flex-1">
            <div class="flex items-start justify-between mb-2">
              <div>
                <h3 class="font-semibold">{{ notification.title }}</h3>
                <p class="text-sm text-muted-foreground">{{ notification.content }}</p>
              </div>
              <div class="flex items-center gap-2">
                <Badge :class="getBadgeColor(notification.type)">
                  {{ getTypeLabel(notification.type) }}
                </Badge>
                <span class="text-sm text-muted-foreground">
                  {{ formatTime(notification.created_at) }}
                </span>
              </div>
            </div>

            <div v-if="!notification.read_at" class="flex items-center gap-2 text-sm">
              <Clock class="h-4 w-4 text-muted-foreground" />
              <span class="text-muted-foreground">Sin leer</span>
              <Button
                variant="ghost"
                size="sm"
                @click="markAsRead(notification.id)"
              >
                <CheckCircle class="h-4 w-4 mr-1" />
                Marcar como leída
              </Button>
            </div>
          </div>

          <Button
            variant="ghost"
            size="sm"
            @click="deleteNotification(notification.id)"
          >
            Eliminar
          </Button>
        </div>
      </div>

      <!-- Empty State -->
      <div v-if="notifications.length === 0" class="text-center py-12 text-muted-foreground">
        <Bell class="h-12 w-12 mx-auto mb-4 opacity-50" />
        <p>No hay notificaciones</p>
      </div>
    </div>
  </div>
</template>
