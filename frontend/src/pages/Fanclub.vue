<template>
  <div class="pt-24 pb-16 min-h-screen bg-cream-50">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
      <!-- Page header -->
      <div ref="headerRef" class="text-center mb-16">
        <h1 class="text-4xl md:text-5xl font-heading font-bold text-charcoal-800 mb-3">{{ $t('fanclub.title') }}</h1>
        <p class="text-charcoal-500 text-lg max-w-2xl mx-auto">
          {{ $t('fanclub.subtitle') }}
        </p>
      </div>

      <!-- CTA Card -->
      <div ref="ctaRef" class="relative bg-white rounded-2xl p-8 md:p-12 shadow-lg overflow-hidden mb-16">
        <!-- Gold/Jade gradient border -->
        <div class="absolute inset-0 rounded-2xl p-[2px]" style="background: linear-gradient(135deg, #C9A84C, #00B4A0, #C9A84C);">
          <div class="w-full h-full bg-white rounded-2xl"></div>
        </div>

        <div class="relative z-10 text-center">
          <!-- LOGGED IN + ACTIVE (including cancelled-but-not-yet-expired) -->
          <template v-if="fan.isActive">
            <div class="inline-flex items-center gap-2 mb-4 px-4 py-1.5 rounded-full text-sm font-medium"
                 :class="fan.user?.tier === 'gold' ? 'bg-amber-100 text-amber-800' : 'bg-jade-50 text-jade-700'">
              <span>{{ fan.user?.tier === 'gold' ? '⭐' : '♡' }}</span>
              {{ fan.user?.tier === 'gold' ? 'Gold' : 'Basic' }} Member
            </div>
            <h2 class="text-2xl md:text-3xl font-heading font-bold text-charcoal-800 mb-2">
              Welcome back, {{ fan.user?.name?.split(' ')[0] }}!
            </h2>
            <p class="text-charcoal-500 max-w-xl mx-auto mb-6">
              <template v-if="fan.user?.status === 'cancelled'">
                Membership cancelled — access remains until <strong>{{ formatDate(fan.user?.expires_at) }}</strong>.
              </template>
              <template v-else>
                Your membership is active until {{ formatDate(fan.user?.expires_at) }}.
              </template>
            </p>
            <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
              <RouterLink
                to="/fanclub/portal"
                class="px-8 py-3 bg-jade-gradient text-white font-semibold rounded-full hover:shadow-jade-glow transition-all duration-300 inline-block"
              >
                Go to My Portal
              </RouterLink>
              <RouterLink
                v-if="fan.user?.status === 'cancelled'"
                to="/fanclub/subscribe"
                class="px-8 py-3 border-2 border-charcoal-200 text-charcoal-700 font-semibold rounded-full hover:border-jade-300 hover:text-jade-700 transition-all duration-300"
              >
                Renew Membership
              </RouterLink>
            </div>
          </template>

          <!-- LOGGED IN + NOT ACTIVE (expired / cancelled) -->
          <template v-else-if="fan.isLoggedIn">
            <h2 class="text-2xl md:text-3xl font-heading font-bold text-charcoal-800 mb-4">
              {{ fan.user?.status === 'expired' ? 'Membership Expired' : 'Membership Inactive' }}
            </h2>
            <p class="text-charcoal-500 max-w-xl mx-auto mb-8">
              Renew your membership to restore access to all exclusive content and benefits.
            </p>
            <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
              <RouterLink
                to="/fanclub/subscribe"
                class="px-8 py-3 bg-jade-gradient text-white font-semibold rounded-full hover:shadow-jade-glow transition-all duration-300"
              >
                Renew Membership
              </RouterLink>
              <button
                @click="fan.logout()"
                class="text-charcoal-400 text-sm hover:text-charcoal-600 transition-colors"
              >
                Sign out
              </button>
            </div>
          </template>

          <!-- GUEST -->
          <template v-else>
            <h2 class="text-2xl md:text-3xl font-heading font-bold text-charcoal-800 mb-4">
              {{ $t('fanclub.ctaHeading') }}
            </h2>
            <p class="text-charcoal-500 max-w-xl mx-auto mb-8">
              {{ $t('fanclub.ctaDesc') }}
            </p>
            <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
              <RouterLink
                to="/fanclub/register"
                class="px-8 py-3 bg-jade-gradient text-white font-semibold rounded-full hover:shadow-jade-glow transition-all duration-300"
              >
                {{ $t('fanclub.joinNow') }}
              </RouterLink>
              <RouterLink
                to="/fanclub/login"
                class="px-8 py-3 border-2 border-charcoal-200 text-charcoal-700 font-semibold rounded-full hover:border-jade-300 hover:text-jade-700 transition-all duration-300"
              >
                Sign In
              </RouterLink>
            </div>
          </template>
        </div>
      </div>

      <!-- Benefits -->
      <div ref="benefitsRef">
        <h2 class="text-2xl font-heading font-bold text-charcoal-800 text-center mb-10">{{ $t('fanclub.benefitsHeading') }}</h2>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          <div v-for="benefit in benefits" :key="benefit.title"
               class="bg-white rounded-xl p-6 shadow-card hover:shadow-card-hover transition-all duration-300">
            <div class="w-12 h-12 rounded-xl bg-jade-50 flex items-center justify-center mb-4">
              <span class="text-2xl">{{ benefit.icon }}</span>
            </div>
            <h3 class="font-heading font-semibold text-charcoal-800 mb-2">{{ benefit.title }}</h3>
            <p class="text-sm text-charcoal-500">{{ benefit.description }}</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { useI18n } from 'vue-i18n'
import { gsap } from 'gsap'
import { useFanStore } from '@/stores/fan'

const { t }  = useI18n()
const fan    = useFanStore()

const headerRef   = ref<HTMLElement | null>(null)
const ctaRef      = ref<HTMLElement | null>(null)
const benefitsRef = ref<HTMLElement | null>(null)

const formatDate = (d: string | null | undefined) =>
  d ? new Date(d).toLocaleDateString('en-GB', { day: 'numeric', month: 'long', year: 'numeric' }) : '—'

const benefits = computed(() => [
  { icon: '🎫', title: t('fanclub.benefit1Title'), description: t('fanclub.benefit1Desc') },
  { icon: '🤝', title: t('fanclub.benefit2Title'), description: t('fanclub.benefit2Desc') },
  { icon: '📸', title: t('fanclub.benefit3Title'), description: t('fanclub.benefit3Desc') },
  { icon: '🎵', title: t('fanclub.benefit4Title'), description: t('fanclub.benefit4Desc') },
  { icon: '🎁', title: t('fanclub.benefit5Title'), description: t('fanclub.benefit5Desc') },
  { icon: '⭐', title: t('fanclub.benefit6Title'), description: t('fanclub.benefit6Desc') },
])

onMounted(async () => {
  // Restore session if token exists
  if (fan.isLoggedIn && !fan.user) {
    await fan.fetchMe()
  }

  if (headerRef.value) {
    gsap.from(headerRef.value.children, {
      y: 20, opacity: 0, duration: 0.6, stagger: 0.1, ease: 'power2.out',
    })
  }
  if (ctaRef.value) {
    gsap.from(ctaRef.value, {
      y: 30, opacity: 0, duration: 0.8, ease: 'power2.out',
      scrollTrigger: { trigger: ctaRef.value, start: 'top 85%' },
    })
  }
})
</script>
