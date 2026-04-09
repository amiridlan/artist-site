<template>
  <div class="pt-24 pb-16 min-h-screen bg-cream-50 px-4">
    <div class="max-w-3xl mx-auto">
      <div class="text-center mb-10">
        <h1 class="text-3xl font-heading font-bold text-charcoal-800 mb-2">Choose Your Membership</h1>
        <p class="text-charcoal-500">Select a tier and proceed to payment. Membership is valid for 1 year.</p>
      </div>

      <!-- Tier cards -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        <!-- Basic -->
        <button
          @click="selectedTier = 'basic'"
          type="button"
          class="text-left rounded-2xl border-2 p-6 transition-all duration-200 focus:outline-none"
          :class="selectedTier === 'basic'
            ? 'border-jade-500 bg-jade-50 shadow-jade-glow'
            : 'border-gray-200 bg-white hover:border-jade-300'"
        >
          <div class="flex items-center justify-between mb-4">
            <div>
              <span class="inline-block px-3 py-0.5 rounded-full bg-gray-100 text-charcoal-600 text-xs font-semibold uppercase tracking-wider mb-2">Basic</span>
              <p class="text-3xl font-bold text-charcoal-800">RM 30<span class="text-base font-normal text-charcoal-500">/year</span></p>
            </div>
            <div class="w-6 h-6 rounded-full border-2 flex items-center justify-center flex-shrink-0"
                 :class="selectedTier === 'basic' ? 'border-jade-500 bg-jade-500' : 'border-gray-300'">
              <div v-if="selectedTier === 'basic'" class="w-2.5 h-2.5 rounded-full bg-white" />
            </div>
          </div>
          <ul class="space-y-1.5">
            <li v-for="b in basicBenefits" :key="b" class="flex items-center gap-2 text-sm text-charcoal-600">
              <span class="text-jade-500">✓</span> {{ b }}
            </li>
          </ul>
        </button>

        <!-- Gold -->
        <button
          @click="selectedTier = 'gold'"
          type="button"
          class="text-left rounded-2xl border-2 p-6 transition-all duration-200 focus:outline-none relative overflow-hidden"
          :class="selectedTier === 'gold'
            ? 'border-amber-400 bg-amber-50 shadow-lg'
            : 'border-gray-200 bg-white hover:border-amber-300'"
        >
          <div class="absolute top-3 right-3">
            <span class="px-2 py-0.5 rounded-full bg-amber-400 text-white text-xs font-bold">BEST VALUE</span>
          </div>
          <div class="flex items-center justify-between mb-4">
            <div>
              <span class="inline-block px-3 py-0.5 rounded-full bg-amber-100 text-amber-700 text-xs font-semibold uppercase tracking-wider mb-2">Gold</span>
              <p class="text-3xl font-bold text-charcoal-800">RM 60<span class="text-base font-normal text-charcoal-500">/year</span></p>
            </div>
            <div class="w-6 h-6 rounded-full border-2 flex items-center justify-center flex-shrink-0"
                 :class="selectedTier === 'gold' ? 'border-amber-500 bg-amber-500' : 'border-gray-300'">
              <div v-if="selectedTier === 'gold'" class="w-2.5 h-2.5 rounded-full bg-white" />
            </div>
          </div>
          <ul class="space-y-1.5">
            <li v-for="b in goldBenefits" :key="b" class="flex items-center gap-2 text-sm text-charcoal-600">
              <span class="text-amber-500">✓</span> {{ b }}
            </li>
          </ul>
        </button>
      </div>

      <!-- Error -->
      <div v-if="error" class="bg-red-50 border border-red-200 text-red-700 rounded-lg px-4 py-3 text-sm mb-6">
        {{ error }}
      </div>

      <!-- Pay button -->
      <div class="text-center">
        <button
          @click="pay"
          :disabled="!selectedTier || loading"
          class="px-10 py-3.5 bg-jade-gradient text-white font-semibold rounded-full hover:shadow-jade-glow transition-all duration-300 disabled:opacity-50 disabled:cursor-not-allowed"
        >
          {{ loading ? 'Preparing payment…' : `Pay Now — RM ${selectedTier === 'gold' ? '60' : '30'}` }}
        </button>
        <p class="text-xs text-charcoal-400 mt-3">
          Secure payment via ToyyibPay · DuitNow QR · FPX · Credit/Debit Card
        </p>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import { useFanStore } from '@/stores/fan'

const fan          = useFanStore()
const selectedTier = ref<'basic' | 'gold' | null>(null)
const loading      = ref(false)
const error        = ref<string | null>(null)

const basicBenefits = [
  'Monthly newsletter',
  'Exclusive digital wallpapers',
  'Members-only announcements',
  'Fanclub ID card',
]
const goldBenefits = [
  ...basicBenefits,
  'Priority event ticketing',
  'Exclusive merch discount (10%)',
  'Monthly Discord Radio Talk',
  'Birthday shoutout from members',
]

async function pay() {
  if (!selectedTier.value) return
  error.value   = null
  loading.value = true
  try {
    const data = await fan.api<{ billUrl: string }>('/fan/subscribe', {
      method: 'POST',
      body: JSON.stringify({ tier: selectedTier.value }),
    })
    // Redirect to ToyyibPay payment page
    window.location.href = data.billUrl
  } catch (e: unknown) {
    const err = e as { body?: { message?: string } }
    error.value = err?.body?.message || 'Failed to initiate payment. Please try again.'
    loading.value = false
  }
}
</script>
