<script setup lang="ts">
import { ref, onMounted, computed } from 'vue'
import { useForm } from '@inertiajs/vue3'
import { router } from '@inertiajs/vue3'
import { Button } from '@/Components/ui/button'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/Components/ui/card'
import { Badge } from '@/Components/ui/badge'
import { Bell, BellOff, CheckCircle, XCircle, Info } from 'lucide-vue-next'

interface NotificationSetting {
  id: string
  name: string
  description: string
  enabled: boolean
}

const form = useForm({
  fcm_token: '',
  notifications: [] as NotificationSetting[],
})

const notificationSupported = computed(() => 'Notification' in window)
const notificationPermission = computed(() => window.Notification?.permission || 'default')

const loadNotifications = () => {
  form.notifications = [
    {
      id: 'messages',
      name: 'Mensajes',
      description: 'Notificaciones de nuevos mensajes',
      enabled: true,
    },
    {
      id: 'offers',
      name: 'Ofertas',
      description: 'Notificaciones de ofertas en tus anuncios',
      enabled: true,
    },
    {
      id: 'search_alerts',
      name: 'Alertas de Búsqueda',
      description: 'Notificaciones cuando aparezcan nuevos anuncios',
      enabled: true,
    },
    {
      id: 'import_reminders',
      name: 'Recordatorios de Importación',
      description: 'Recordatorios de fechas límite (ITV, impuestos, placas)',
      enabled: true,
    },
  ]
}

const requestPermission = () => {
  if ('Notification' in window) {
    Notification.requestPermission().then((permission) => {
      if (permission === 'granted') {
        initFcm()
      }
    })
  }
}

const initFcm = () => {
  if ('serviceWorker' in navigator && 'PushManager' in window) {
    navigator.serviceWorker.register('/sw.js').then((registration) => {
      console.log('Service Worker registrado')
    }).catch((error) => {
      console.error('Error registrando Service Worker:', error)
    })
  }
}

const saveSettings = () => {
  form.post('/notifications/settings', {
    onSuccess: () => {
      console.log('Configuración guardada')
    }
  })
}

const toggleNotification = (id: string) => {
  const notification = form.notifications.find(n => n.id === id)
  if (notification) {
    notification.enabled = !notification.enabled
  }
}

onMounted(() => {
  loadNotifications()
})
</script>

<template>
  <div class="max-w-4xl mx-auto space-y-6">
    <!-- Header -->
    <div>
      <h1 class="text-3xl font-bold flex items-center gap-2">
        <Bell class="h-8 w-8" />
        Notificaciones Push
      </h1>
      <p class="text-muted-foreground mt-1">
        Configura las notificaciones en tu dispositivo móvil
      </p>
    </div>

    <!-- Permission Status -->
    <Card>
      <CardHeader>
        <CardTitle>Estado de Permisos</CardTitle>
      </CardHeader>
      <CardContent>
        <div v-if="notificationSupported" class="flex items-center justify-between">
          <div class="flex items-center gap-3">
            <CheckCircle v-if="notificationPermission === 'granted'" class="h-5 w-5 text-green-500" />
            <XCircle v-else class="h-5 w-5 text-red-500" />
            <div>
              <p class="font-medium">
                {{ notificationPermission === 'granted' ? 'Permisos activados' : 'Permisos desactivados' }}
              </p>
              <p class="text-sm text-muted-foreground">
                {{ notificationPermission === 'granted' ? 'Recibirás notificaciones push' : 'Activa las notificaciones para recibir alertas' }}
              </p>
            </div>
          </div>
          <Button
            v-if="notificationPermission !== 'granted'"
            @click="requestPermission"
          >
            Activar Notificaciones
          </Button>
        </div>
        <div v-else class="flex items-center gap-3 text-muted-foreground">
          <Info class="h-5 w-5" />
          <p>Tu navegador no soporta notificaciones push</p>
        </div>
      </CardContent>
    </Card>

    <!-- Notification Types -->
    <Card>
      <CardHeader>
        <CardTitle>Tipo de Notificaciones</CardTitle>
        <CardDescription>
          Selecciona qué notificaciones quieres recibir
        </CardDescription>
      </CardHeader>
      <CardContent class="space-y-4">
        <div
          v-for="notification in form.notifications"
          :key="notification.id"
          class="flex items-center justify-between p-4 border rounded-lg"
        >
          <div class="flex items-start gap-3">
            <div class="mt-1">
              <Bell v-if="notification.enabled" class="h-5 w-5 text-primary" />
              <BellOff v-else class="h-5 w-5 text-muted-foreground" />
            </div>
            <div>
              <p class="font-medium">{{ notification.name }}</p>
              <p class="text-sm text-muted-foreground">
                {{ notification.description }}
              </p>
            </div>
          </div>
          <Badge :class="notification.enabled ? 'bg-green-500' : 'bg-gray-500'">
            {{ notification.enabled ? 'Activado' : 'Desactivado' }}
          </Badge>
        </div>

        <div class="flex justify-end gap-2">
          <Button
            variant="outline"
            @click="loadNotifications"
          >
            Restablecer
          </Button>
          <Button
            @click="saveSettings"
            :disabled="form.processing"
          >
            Guardar Configuración
          </Button>
        </div>
      </CardContent>
    </Card>

    <!-- Test Notification -->
    <Card>
      <CardHeader>
        <CardTitle>Probar Notificación</CardTitle>
        <CardDescription>
          Envía una notificación de prueba
        </CardDescription>
      </CardHeader>
      <CardContent>
        <Button
          @click="form.post('/notifications/test')"
          :disabled="notificationPermission !== 'granted'"
          variant="outline"
        >
          Enviar Notificación de Prueba
        </Button>
      </CardContent>
    </Card>
  </div>
</template>
