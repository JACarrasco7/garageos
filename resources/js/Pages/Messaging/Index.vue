<script setup lang="ts">
import { ref, computed } from 'vue'
import { router } from '@inertiajs/vue3'
import { usePage } from '@inertiajs/vue3'
import { Button } from '@/Components/ui/button'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/Components/ui/card'
import { Badge } from '@/Components/ui/badge'
import { Input } from '@/Components/ui/input'
import { Textarea } from '@/Components/ui/textarea'
import { Send, Archive, User as UserIcon, MessageSquare, Clock, Search } from 'lucide-vue-next'

interface Conversation {
  id: number
  subject: string | null
  status: string
  last_message_at: string | null
  buyer: {
    id: number
    name: string
    avatar_url: string | null
  }
  seller: {
    id: number
    name: string
    avatar_url: string | null
  }
  listing: {
    id: number
    title: string
    photos: string[]
  } | null
  lastMessage: {
    content: string
    sender_id: number
    created_at: string
  } | null
  unread_count: number
}

const page = usePage()
const auth = computed(() => page.props.auth as { id: number })

const conversations = ref<Conversation[]>([])
const searchQuery = ref('')
const filterStatus = ref('all')

const filteredConversations = () => {
  return conversations.value.filter(conv => {
    const matchesSearch = !searchQuery.value ||
      conv.subject?.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
      conv.listing?.title.toLowerCase().includes(searchQuery.value.toLowerCase())

    const matchesStatus = filterStatus.value === 'all' || conv.status === filterStatus.value

    return matchesSearch && matchesStatus
  })
}

const getStatusColor = (status: string) => {
  switch (status) {
    case 'active': return 'bg-green-500'
    case 'archived': return 'bg-gray-500'
    case 'blocked': return 'bg-red-500'
    default: return 'bg-gray-500'
  }
}

const timeAgo = (date: string | null) => {
  if (!date) return 'Nunca'
  const now = new Date()
  const then = new Date(date)
  const diff = now.getTime() - then.getTime()
  const days = Math.floor(diff / (1000 * 60 * 60 * 24))

  if (days === 0) return 'Hoy'
  if (days === 1) return 'Ayer'
  if (days < 7) return `Hace ${days} días`
  if (days < 30) return `Hace ${Math.floor(days / 7)} semanas`
  return then.toLocaleDateString('es-ES', { day: '2-digit', month: 'short', year: 'numeric' })
}

const getOtherUser = (conversation: Conversation) => {
  return conversation.buyer.id === auth.value.id ? conversation.seller : conversation.buyer
}
</script>

<template>
  <div class="max-w-7xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
      <div>
        <h1 class="text-3xl font-bold flex items-center gap-2">
          <MessageSquare class="h-8 w-8" />
          Mensajes
        </h1>
        <p class="text-muted-foreground mt-1">
          Gestiona tus conversaciones con compradores y vendedores
        </p>
      </div>
    </div>

    <!-- Search and Filters -->
    <Card>
      <CardContent class="pt-6">
        <div class="flex gap-4">
          <div class="flex-1 relative">
            <Search class="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-muted-foreground" />
            <Input
              v-model="searchQuery"
              placeholder="Buscar conversaciones..."
              class="pl-10"
            />
          </div>
          <select
            v-model="filterStatus"
            class="px-4 py-2 border rounded-md bg-background"
          >
            <option value="all">Todas</option>
            <option value="active">Activas</option>
            <option value="archived">Archivadas</option>
            <option value="blocked">Bloqueadas</option>
          </select>
        </div>
      </CardContent>
    </Card>

    <!-- Conversations List -->
    <div class="space-y-4">
      <div
        v-for="conversation in filteredConversations()"
        :key="conversation.id"
        class="group cursor-pointer"
      >
        <Card
          class="hover:shadow-md transition-shadow"
          @click="router.visit(`/messaging/${conversation.id}`)"
        >
          <CardContent class="p-4">
            <div class="flex items-start gap-4">
              <!-- Avatar -->
              <div class="w-12 h-12 rounded-full bg-muted flex items-center justify-center flex-shrink-0">
                <UserIcon class="h-6 w-6" />
              </div>

              <!-- Content -->
              <div class="flex-1 min-w-0">
                <div class="flex items-center justify-between mb-1">
                  <h3 class="font-semibold truncate">
                    {{ conversation.listing?.title || conversation.subject || 'Sin asunto' }}
                  </h3>
                  <div class="flex items-center gap-2 flex-shrink-0">
                    <Badge
                      v-if="conversation.unread_count > 0"
                      class="bg-primary"
                    >
                      {{ conversation.unread_count }}
                    </Badge>
                    <Badge
                      :class="getStatusColor(conversation.status)"
                    >
                      {{ conversation.status }}
                    </Badge>
                    <span class="text-sm text-muted-foreground">
                      {{ timeAgo(conversation.last_message_at) }}
                    </span>
                  </div>
                </div>

                <p class="text-sm text-muted-foreground truncate">
                  {{ conversation.lastMessage?.content || 'Sin mensajes' }}
                </p>

                <div class="flex items-center gap-2 mt-2 text-sm">
                  <UserIcon class="h-4 w-4 text-muted-foreground" />
                  <span class="text-muted-foreground">
                    {{ getOtherUser(conversation).name }}
                  </span>
                </div>
              </div>

              <!-- Archive Button -->
              <Button
                variant="ghost"
                size="sm"
                @click.stop
              >
                <Archive class="h-4 w-4" />
              </Button>
            </div>
          </CardContent>
        </Card>
      </div>

      <div v-if="filteredConversations().length === 0" class="text-center py-12">
        <MessageSquare class="h-12 w-12 text-muted-foreground mx-auto mb-4" />
        <p class="text-muted-foreground">
          No hay conversaciones que coincidan con tu búsqueda
        </p>
      </div>
    </div>
  </div>
</template>
