<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { router } from '@inertiajs/vue3'
import { loadStripe } from '@stripe/stripe-js'
import { Button } from '@/Components/ui/button'
import { Card, CardContent, CardHeader, CardTitle } from '@/Components/ui/card'

interface Listing {
  id: number
  title: string
  price: number
  currency: string
}

interface Props {
  listing: Listing
  client_secret: string
}

const props = defineProps<Props>()

const stripe = ref<any>(null)
const elements = ref<any>(null)
const cardElement = ref<HTMLElement | null>(null)
const loading = ref(false)
const error = ref('')

onMounted(async () => {
  stripe.value = await loadStripe(import.meta.env.VITE_STRIPE_PUBLISHABLE_KEY)
  if (stripe.value && cardElement.value) {
    elements.value = stripe.value.elements()
    const card = elements.value.create('card')
    card.mount(cardElement.value)
  }
})

const handleSubmit = async () => {
  loading.value = true
  error.value = ''

  const { error: stripeError } = await stripe.value.confirmCardPayment(props.client_secret, {
    payment_method: {
      card: elements.value.getElement('card'),
    },
  })

  if (stripeError) {
    error.value = stripeError.message
  } else {
    router.visit(`/marketplace/listings/${props.listing.id}`)
  }

  loading.value = false
}
</script>

<template>
  <div class="max-w-2xl mx-auto py-8">
    <Card>
      <CardHeader>
        <CardTitle>Comprar {{ listing.title }}</CardTitle>
      </CardHeader>
      <CardContent>
        <p class="text-2xl font-bold mb-4">
          {{ listing.price.toLocaleString('es-ES') }} {{ listing.currency }}
        </p>
        <div ref="cardElement" class="border rounded p-3 mb-4"></div>
        <p v-if="error" class="text-destructive text-sm mb-2">{{ error }}</p>
        <Button :disabled="loading" class="w-full" @click="handleSubmit">
          {{ loading ? 'Procesando...' : 'Pagar ahora' }}
        </Button>
      </CardContent>
    </Card>
  </div>
</template>