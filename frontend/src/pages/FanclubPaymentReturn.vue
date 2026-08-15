<template>
  <div class="pt-24 pb-16 min-h-screen bg-cream-50 flex items-center justify-center px-4">
    <div class="w-full max-w-md text-center">

      <!-- Checking -->
      <template v-if="state === 'checking'">
        <div class="w-16 h-16 rounded-full bg-jade-100 flex items-center justify-center mx-auto mb-6 animate-pulse">
          <span class="text-2xl">⏳</span>
        </div>
        <h2 class="text-xl font-heading font-bold text-charcoal-800 mb-2">{{ $t('fanclub.paymentReturn.verifying') }}</h2>
        <p class="text-charcoal-500 text-sm">{{ $t('fanclub.paymentReturn.verifyingDesc') }}</p>
      </template>

      <!-- Success: new registration -->
      <template v-else-if="state === 'success' && paymentType === 'registration'">
        <div class="w-16 h-16 rounded-full bg-jade-500 flex items-center justify-center mx-auto mb-6">
          <span class="text-white text-3xl">✓</span>
        </div>
        <h2 class="text-2xl font-heading font-bold text-charcoal-800 mb-2">{{ $t('fanclub.paymentReturn.successRegTitle') }}</h2>
        <p class="text-charcoal-500 text-sm mb-2">
          {{ $t('fanclub.paymentReturn.successRegDesc1', { tier: tierLabel }) }}
        </p>
        <p class="text-charcoal-400 text-sm mb-8">
          {{ $t('fanclub.paymentReturn.successRegDesc2', { email }) }}
        </p>
        <RouterLink
          to="/fanclub/login"
          class="inline-block px-8 py-3 bg-jade-gradient text-white font-semibold rounded-full hover:shadow-jade-glow transition-all duration-300"
        >
          {{ $t('fanclub.paymentReturn.signInToPortal') }}
        </RouterLink>
      </template>

      <!-- Success: renewal -->
      <template v-else-if="state === 'success' && paymentType === 'renewal'">
        <div class="w-16 h-16 rounded-full bg-jade-500 flex items-center justify-center mx-auto mb-6">
          <span class="text-white text-3xl">✓</span>
        </div>
        <h2 class="text-2xl font-heading font-bold text-charcoal-800 mb-2">{{ $t('fanclub.paymentReturn.successRenewTitle') }}</h2>
        <p class="text-charcoal-500 text-sm mb-8">
          {{ $t('fanclub.paymentReturn.successRenewDesc', { tier: tierLabel }) }}
        </p>
        <RouterLink
          to="/fanclub/portal"
          class="inline-block px-8 py-3 bg-jade-gradient text-white font-semibold rounded-full hover:shadow-jade-glow transition-all duration-300"
        >
          {{ $t('fanclub.status.goToPortal') }}
        </RouterLink>
      </template>

      <!-- Pending -->
      <template v-else-if="state === 'pending'">
        <div class="w-16 h-16 rounded-full bg-amber-100 flex items-center justify-center mx-auto mb-6">
          <span class="text-2xl">⏳</span>
        </div>
        <h2 class="text-xl font-heading font-bold text-charcoal-800 mb-2">{{ $t('fanclub.paymentReturn.pendingTitle') }}</h2>
        <p class="text-charcoal-500 text-sm mb-6">
          {{ $t('fanclub.paymentReturn.pendingDesc') }}
          <span v-if="email" class="block mt-2">{{ $t('fanclub.paymentReturn.pendingEmailNote', { email }) }}</span>
        </p>
        <RouterLink to="/fanclub" class="text-jade-600 font-medium hover:text-jade-700 text-sm">
          {{ $t('fanclub.backToFanclub') }}
        </RouterLink>
      </template>

      <!-- Failed -->
      <template v-else-if="state === 'failed'">
        <div class="w-16 h-16 rounded-full bg-red-100 flex items-center justify-center mx-auto mb-6">
          <span class="text-2xl">✕</span>
        </div>
        <h2 class="text-xl font-heading font-bold text-charcoal-800 mb-2">{{ $t('fanclub.paymentReturn.failedTitle') }}</h2>
        <p class="text-charcoal-500 text-sm mb-6">{{ $t('fanclub.paymentReturn.failedDesc') }}</p>
        <RouterLink
          :to="paymentType === 'renewal' ? '/fanclub/subscribe' : '/fanclub/register'"
          class="inline-block px-8 py-3 bg-jade-gradient text-white font-semibold rounded-full hover:shadow-jade-glow transition-all duration-300"
        >
          {{ $t('common.retry') }}
        </RouterLink>
      </template>

    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import { useI18n } from 'vue-i18n'
import { useFanStore } from '@/stores/fan'

const route = useRoute()
const fan   = useFanStore()
const { t }  = useI18n()

const state       = ref<'checking' | 'success' | 'pending' | 'failed'>('checking')
const paymentType = ref<'registration' | 'renewal'>('registration')
const tier        = ref('')
const email       = ref('')

const tierLabel = computed(() => tier.value === 'gold' ? t('fanclub.tiers.gold') : t('fanclub.tiers.basic'))

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
