<script setup lang="ts">
import { ref, nextTick, onMounted, computed } from 'vue'
import { useForm } from '@inertiajs/vue3'
import { router } from '@inertiajs/vue3'
import { usePage } from '@inertiajs/vue3'
import { Button } from '@/Components/ui/button'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/Components/ui/card'
import { Badge } from '@/Components/ui/badge'
import { Textarea } from '@/Components/ui/textarea'
import { Avatar, AvatarFallback } from '@/Components/ui/avatar'
import { ArrowLeft, Send, User, MoreVertical, Archive, Trash } from 'lucide-vue-next'

interface Message {
  id: number
  content: string
  sender_id: number
  is_read: boolean
  read_at: string | null
  created_at: string
  sender: {
    id: number
    name: string
    avatar_url: string | null
  }
}

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
  messages: Message[]
}

interface Props {
  conversation: Conversation
}

const props = defineProps<Props>()
const page = usePage()
const auth = computed(() => page.props.auth as { id: number })
const messagesContainer = ref<HTMLElement | null>(null)

const replyForm = useForm({
  message: '',
})

const goBack = () => window.history.back()

const otherUser = () => {
  return props.conversation.buyer.id === auth.value.id ? props.conversation.seller : props.conversation.buyer
}

const isSender = (message: Message) => {
  return message.sender_id === auth.value.id
}

const sendMessage = () => {
  replyForm.post(`/messaging/${props.conversation.id}/reply`, {
    onSuccess: () => {
      replyForm.reset()
      scrollToBottom()
    }
  })
}

const scrollToBottom = () => {
  nextTick(() => {
    if (messagesContainer.value) {
      messagesContainer.value.scrollTop = messagesContainer.value.scrollHeight
    }
  })
}

const formatDate = (date: string) => {
  const now = new Date()
  const messageDate = new Date(date)
  const diff = now.getTime() - messageDate.getTime()
  const minutes = Math.floor(diff / (1000 * 60))

  if (minutes < 1) return 'Ahora'
  if (minutes < 60) return `Hace ${minutes} min`

  const hours = Math.floor(minutes / 60)
  if (hours < 24) return `Hace ${hours} h`

  const days = Math.floor(hours / 24)
  if (days === 1) return 'Ayer'
  if (days < 7) return `Hace ${days} días`

  return messageDate.toLocaleDateString('es-ES', { day: '2-digit', month: 'short' })
}

onMounted(() => {
  scrollToBottom()
})

const getInitials = (name: string) => {
  return name
    .split(' ')
    .map(n => n[0])
    .join('')
    .toUpperCase()
    .slice(0, 2)
}
</script>

<template>
  <div class="max-w-4xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex items-center gap-4">
      <Button variant="ghost" size="sm" @click="goBack()">
        <ArrowLeft class="h-4 w-4 mr-1" />
        Volver
      </Button>
      <div class="flex-1">
        <h1 class="text-2xl font-bold">
          {{ conversation.listing?.title || conversation.subject || 'Sin asunto' }}
        </h1>
        <p class="text-muted-foreground mt-1 flex items-center gap-2">
          <User class="h-4 w-4" />
          {{ otherUser().name }}
        </p>
      </div>
      <div class="flex gap-2">
        <Button variant="ghost" size="sm">
          <Archive class="h-4 w-4" />
        </Button>
        <Button variant="ghost" size="sm">
          <MoreVertical class="h-4 w-4" />
        </Button>
      </div>
    </div>

    <!-- Messages -->
    <Card>
      <CardContent class="p-6">
        <div
          ref="messagesContainer"
          class="space-y-4 max-h-[600px] overflow-y-auto"
        >
          <div
            v-for="message in conversation.messages"
            :key="message.id"
            class="flex gap-3"
            :class="{
              'flex-row-reverse': isSender(message),
              'flex-row': !isSender(message)
            }"
          >
            <!-- Avatar -->
            <Avatar class="w-8 h-8 flex-shrink-0">
              <AvatarFallback>
                {{ getInitials(message.sender.name) }}
              </AvatarFallback>
            </Avatar>

            <!-- Message Bubble -->
            <div
              class="max-w-[70%] rounded-lg p-3"
              :class="{
                'bg-primary text-primary-foreground': isSender(message),
                'bg-muted': !isSender(message)
              }"
            >
              <p class="text-sm">{{ message.content }}</p>
              <p
                class="text-xs mt-1 opacity-70"
                :class="{
                  'text-right': isSender(message),
                  'text-left': !isSender(message)
                }"
              >
                {{ formatDate(message.created_at) }}
                <span v-if="message.is_read" class="ml-2">✓✓</span>
              </p>
            </div>
          </div>
        </div>
      </CardContent>
    </Card>

    <!-- Reply Form -->
    <Card>
      <CardContent class="p-4">
        <form @submit.prevent="sendMessage" class="flex gap-3">
          <Textarea
            v-model="replyForm.message"
            rows="3"
            placeholder="Escribe tu mensaje..."
            class="flex-1 resize-none"
          />
          <Button
            type="submit"
            :disabled="replyForm.processing || !replyForm.message"
            class="self-end"
          >
            <Send class="h-4 w-4" />
          </Button>
        </form>
      </CardContent>
    </Card>
  </div>
</template>
