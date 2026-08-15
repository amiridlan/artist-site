<template>
  <div class="pt-24 pb-16 min-h-screen bg-cream-50 px-4">
    <div class="max-w-3xl mx-auto">
      <div class="text-center mb-10">
        <h1 class="text-3xl font-heading font-bold text-charcoal-800 mb-2">{{ $t('fanclub.subscribe.heading') }}</h1>
        <p class="text-charcoal-500">{{ $t('fanclub.subscribe.subtitle') }}</p>
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
              <span class="inline-block px-3 py-0.5 rounded-full bg-gray-100 text-charcoal-600 text-xs font-semibold uppercase tracking-wider mb-2">{{ $t('fanclub.tiers.basic') }}</span>
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
            <span class="px-2 py-0.5 rounded-full bg-amber-400 text-white text-xs font-bold">{{ $t('fanclub.tiers.bestValue') }}</span>
          </div>
          <div class="flex items-center justify-between mb-4">
            <div>
              <span class="inline-block px-3 py-0.5 rounded-full bg-amber-100 text-amber-700 text-xs font-semibold uppercase tracking-wider mb-2">{{ $t('fanclub.tiers.gold') }}</span>
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
      <div v-if="error" role="alert" class="bg-red-50 border border-red-200 text-red-700 rounded-lg px-4 py-3 text-sm mb-6">
        {{ error }}
      </div>

      <!-- Pay button -->
      <div class="text-center">
        <button
          @click="pay"
          :disabled="!selectedTier || loading"
          class="px-10 py-3.5 bg-jade-gradient text-white font-semibold rounded-full hover:shadow-jade-glow transition-all duration-300 disabled:opacity-50 disabled:cursor-not-allowed"
        >
          {{ loading ? $t('fanclub.preparingPayment') : $t('fanclub.subscribe.payNow', { amount: selectedTier === 'gold' ? '60' : '30' }) }}
        </button>
        <p class="text-xs text-charcoal-400 mt-3">
          {{ $t('fanclub.securePaymentMethods') }}
        </p>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue'
import { useI18n } from 'vue-i18n'
import { useFanStore } from '@/stores/fan'

const fan          = useFanStore()
const selectedTier = ref<'basic' | 'gold' | null>(null)
const loading      = ref(false)
const error        = ref<string | null>(null)
const { t }        = useI18n()

const basicBenefits = computed(() => [
  t('fanclub.benefitsList.newsletter'),
  t('fanclub.benefitsList.wallpapers'),
  t('fanclub.benefitsList.announcements'),
  t('fanclub.benefitsList.idCard'),
])
const goldBenefits = computed(() => [
  ...basicBenefits.value,
  t('fanclub.benefitsList.priorityTicketing'),
  t('fanclub.benefitsList.merchDiscount'),
  t('fanclub.benefitsList.discordRadio'),
  t('fanclub.benefitsList.birthdayShoutout'),
])

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
    error.value = err?.body?.message || t('fanclub.subscribe.paymentInitError')
    loading.value = false
  }
}
</script>
