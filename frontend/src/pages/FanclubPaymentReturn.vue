<template>
  <div class="pt-24 pb-16 min-h-screen bg-cream-50 flex items-center justify-center px-4">
    <div class="w-full max-w-md text-center">

      <!-- Checking -->
      <template v-if="state === 'checking'">
        <div class="w-16 h-16 rounded-full bg-jade-100 flex items-center justify-center mx-auto mb-6 animate-pulse">
          <span class="text-2xl">⏳</span>
        </div>
        <h2 class="text-xl font-heading font-bold text-charcoal-800 mb-2">Verifying payment…</h2>
        <p class="text-charcoal-500 text-sm">Please wait while we confirm your payment.</p>
      </template>

      <!-- Success: new registration -->
      <template v-else-if="state === 'success' && paymentType === 'registration'">
        <div class="w-16 h-16 rounded-full bg-jade-500 flex items-center justify-center mx-auto mb-6">
          <span class="text-white text-3xl">✓</span>
        </div>
        <h2 class="text-2xl font-heading font-bold text-charcoal-800 mb-2">You're in!</h2>
        <p class="text-charcoal-500 text-sm mb-2">
          Your <strong class="capitalize">{{ tier }}</strong> membership is now active.
        </p>
        <p class="text-charcoal-400 text-sm mb-8">
          Log in with <strong>{{ email }}</strong> and the password you set during registration.
        </p>
        <RouterLink
          to="/fanclub/login"
          class="inline-block px-8 py-3 bg-jade-gradient text-white font-semibold rounded-full hover:shadow-jade-glow transition-all duration-300"
        >
          Sign In to Your Portal
        </RouterLink>
      </template>

      <!-- Success: renewal -->
      <template v-else-if="state === 'success' && paymentType === 'renewal'">
        <div class="w-16 h-16 rounded-full bg-jade-500 flex items-center justify-center mx-auto mb-6">
          <span class="text-white text-3xl">✓</span>
        </div>
        <h2 class="text-2xl font-heading font-bold text-charcoal-800 mb-2">Membership Renewed!</h2>
        <p class="text-charcoal-500 text-sm mb-8">
          Your <strong class="capitalize">{{ tier }}</strong> membership has been renewed for another year.
        </p>
        <RouterLink
          to="/fanclub/portal"
          class="inline-block px-8 py-3 bg-jade-gradient text-white font-semibold rounded-full hover:shadow-jade-glow transition-all duration-300"
        >
          Go to My Portal
        </RouterLink>
      </template>

      <!-- Pending -->
      <template v-else-if="state === 'pending'">
        <div class="w-16 h-16 rounded-full bg-amber-100 flex items-center justify-center mx-auto mb-6">
          <span class="text-2xl">⏳</span>
        </div>
        <h2 class="text-xl font-heading font-bold text-charcoal-800 mb-2">Payment Pending</h2>
        <p class="text-charcoal-500 text-sm mb-6">
          Your payment is being processed. We'll activate your account once confirmed — this usually takes a few minutes.
          <span v-if="email" class="block mt-2">We'll activate <strong>{{ email }}</strong>.</span>
        </p>
        <RouterLink to="/fanclub" class="text-jade-600 font-medium hover:text-jade-700 text-sm">
          Back to Fanclub
        </RouterLink>
      </template>

      <!-- Failed -->
      <template v-else-if="state === 'failed'">
        <div class="w-16 h-16 rounded-full bg-red-100 flex items-center justify-center mx-auto mb-6">
          <span class="text-2xl">✕</span>
        </div>
        <h2 class="text-xl font-heading font-bold text-charcoal-800 mb-2">Payment Failed</h2>
        <p class="text-charcoal-500 text-sm mb-6">Your payment was not successful. Please try again.</p>
        <RouterLink
          :to="paymentType === 'renewal' ? '/fanclub/subscribe' : '/fanclub/register'"
          class="inline-block px-8 py-3 bg-jade-gradient text-white font-semibold rounded-full hover:shadow-jade-glow transition-all duration-300"
        >
          Try Again
        </RouterLink>
      </template>

    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import { useFanStore } from '@/stores/fan'

const route = useRoute()
const fan   = useFanStore()

const state       = ref<'checking' | 'success' | 'pending' | 'failed'>('checking')
const paymentType = ref<'registration' | 'renewal'>('registration')
const tier        = ref('')
const email       = ref('')

const API_BASE = import.meta.env.VITE_API_URL || 'http://localhost:8000/api'

onMounted(async () => {
  const billCode = route.query.billcode as string | undefined
  const status   = route.query.status   as string | undefined  // '1' paid, '3' failed

  if (!billCode) {
    state.value = 'failed'
    return
  }

  if (status === '3') {
    // ToyyibPay explicitly told us it failed — still fetch type info
    await checkStatus(billCode, true)
    state.value = 'failed'
    return
  }

  await checkStatus(billCode, false)
})

async function checkStatus(billCode: string, knownFailed: boolean, retries = 3): Promise<void> {
  try {
    const res  = await fetch(`${API_BASE}/fan/payment/status?bill_code=${billCode}`, {
      headers: { Accept: 'application/json' },
    })
    const data = await res.json()

    paymentType.value = data.type    || 'registration'
    tier.value        = data.tier    || ''
    email.value       = data.email   || ''

    if (knownFailed) {
      state.value = 'failed'
      return
    }

    if (data.status === 'paid') {
      state.value = 'success'
      // If renewal, refresh user state
      if (data.type === 'renewal') await fan.fetchMe()
    } else if (data.status === 'failed') {
      state.value = 'failed'
    } else {
      // still pending — retry with delay
      if (retries > 0) {
        await new Promise(r => setTimeout(r, 3000))
        await checkStatus(billCode, false, retries - 1)
      } else {
        state.value = 'pending'
      }
    }
  } catch {
    state.value = 'pending'
  }
}
</script>
