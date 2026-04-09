<template>
  <div class="pt-24 pb-16 min-h-screen bg-cream-50 px-4">
    <div class="max-w-4xl mx-auto">

      <!-- Membership card -->
      <div class="relative rounded-2xl overflow-hidden mb-6 shadow-lg">
        <div
          class="absolute inset-0"
          :style="cardGradient"
        />
        <div class="relative z-10 p-8 text-white">
          <div class="flex items-start justify-between mb-6">
            <div>
              <p class="text-white/70 text-sm uppercase tracking-widest font-medium">KLP48 Fanclub</p>
              <h2 class="text-2xl font-heading font-bold mt-1">{{ user?.name }}</h2>
              <p class="text-white/70 text-sm mt-0.5">{{ user?.email }}</p>
            </div>
            <div class="text-right flex flex-col items-end gap-2">
              <span
                class="inline-block px-3 py-1 rounded-full text-sm font-bold uppercase tracking-wider"
                :class="tierBadgeClass"
              >{{ user?.tier }}</span>
              <span
                v-if="user?.status === 'cancelled'"
                class="inline-block px-2.5 py-0.5 rounded-full text-xs font-semibold bg-white/20 text-white"
              >Cancelled</span>
            </div>
          </div>
          <div class="flex items-end justify-between">
            <div>
              <p class="text-white/60 text-xs uppercase tracking-wider">Member since</p>
              <p class="text-white font-medium">{{ formatDate(user?.joined_at) }}</p>
            </div>
            <div class="text-right">
              <p class="text-white/60 text-xs uppercase tracking-wider">
                {{ user?.status === 'cancelled' ? 'Access until' : 'Valid until' }}
              </p>
              <p class="text-white font-medium">{{ formatDate(user?.expires_at) }}</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Cancelled banner -->
      <div v-if="user?.status === 'cancelled'" class="bg-amber-50 border border-amber-200 rounded-xl px-5 py-4 mb-6 flex items-center justify-between gap-4">
        <p class="text-amber-800 text-sm">
          <strong>Membership cancelled.</strong>
          You still have full access until <strong>{{ formatDate(user?.expires_at) }}</strong>, after which your account will no longer be active.
        </p>
        <RouterLink to="/fanclub/subscribe" class="flex-shrink-0 text-sm font-semibold text-amber-700 hover:text-amber-900 underline">
          Renew
        </RouterLink>
      </div>

      <!-- Expiry warning (active members only, within 30 days) -->
      <div
        v-else-if="expiresInDays !== null && expiresInDays <= 30"
        class="bg-amber-50 border border-amber-200 rounded-xl px-5 py-4 mb-6 flex items-center justify-between"
      >
        <p class="text-amber-800 text-sm font-medium">
          ⚠️ Your membership expires in <strong>{{ expiresInDays }} days</strong>.
        </p>
        <RouterLink to="/fanclub/subscribe" class="text-sm font-semibold text-amber-700 hover:text-amber-900 underline">
          Renew now
        </RouterLink>
      </div>

      <!-- Benefits -->
      <div class="bg-white rounded-2xl border border-gray-100 shadow-card p-6 mb-6">
        <h3 class="font-heading font-semibold text-charcoal-800 mb-4">Your Benefits</h3>
        <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
          <div
            v-for="benefit in user?.benefits"
            :key="benefit"
            class="flex items-center gap-2 text-sm text-charcoal-600 bg-jade-50 rounded-lg px-3 py-2"
          >
            <span class="text-jade-500 text-base">✓</span>
            {{ benefit }}
          </div>
        </div>
      </div>

      <!-- Exclusive content grid -->
      <h3 class="font-heading font-semibold text-charcoal-800 text-lg mb-4">Members-Only Content</h3>
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mb-8">
        <div
          v-for="content in exclusiveContent"
          :key="content.title"
          class="bg-white rounded-xl border border-gray-100 shadow-card overflow-hidden hover:shadow-card-hover transition-all duration-300"
        >
          <div class="h-32 flex items-center justify-center text-4xl" :style="`background: ${content.bg}`">
            {{ content.icon }}
          </div>
          <div class="p-4">
            <span class="text-xs font-semibold uppercase tracking-wider text-jade-600">{{ content.tag }}</span>
            <h4 class="font-medium text-charcoal-800 text-sm mt-1">{{ content.title }}</h4>
            <p class="text-xs text-charcoal-500 mt-1">{{ content.desc }}</p>
            <p v-if="content.goldOnly && user?.tier !== 'gold'" class="text-xs text-amber-600 font-medium mt-2">
              ⭐ Gold tier only
            </p>
          </div>
        </div>
      </div>

      <!-- Account actions -->
      <div class="flex items-center justify-between">
        <button @click="handleLogout" class="text-sm text-charcoal-400 hover:text-charcoal-600 transition-colors">
          Sign out
        </button>
        <!-- Cancel button — only shown to active (not yet cancelled) members -->
        <button
          v-if="user?.status === 'active'"
          @click="showCancelModal = true"
          class="text-sm text-red-400 hover:text-red-600 transition-colors"
        >
          Cancel membership
        </button>
      </div>
    </div>

    <!-- ── Cancel confirmation modal ── -->
    <Transition name="fade">
      <div
        v-if="showCancelModal"
        class="fixed inset-0 z-50 flex items-center justify-center px-4"
        @click.self="showCancelModal = false"
      >
        <!-- Backdrop -->
        <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" @click="showCancelModal = false" />

        <!-- Dialog -->
        <div class="relative bg-white rounded-2xl shadow-xl p-8 w-full max-w-md">
          <div class="text-center mb-6">
            <div class="w-14 h-14 rounded-full bg-red-100 flex items-center justify-center mx-auto mb-4">
              <span class="text-2xl">⚠️</span>
            </div>
            <h3 class="text-lg font-heading font-bold text-charcoal-800 mb-2">Cancel Membership?</h3>
            <p class="text-charcoal-500 text-sm leading-relaxed">
              You won't be charged again, but you'll keep full access to the fanclub until
              <strong>{{ formatDate(user?.expires_at) }}</strong>.
              After that date, your membership will no longer be active.
            </p>
          </div>

          <div v-if="cancelError" class="bg-red-50 border border-red-200 text-red-700 rounded-lg px-4 py-3 text-sm mb-4">
            {{ cancelError }}
          </div>

          <div class="flex gap-3">
            <button
              @click="showCancelModal = false"
              class="flex-1 py-2.5 border-2 border-gray-200 text-charcoal-600 font-semibold rounded-full hover:border-gray-300 transition-colors text-sm"
            >
              Keep Membership
            </button>
            <button
              @click="handleCancel"
              :disabled="cancelling"
              class="flex-1 py-2.5 bg-red-500 hover:bg-red-600 text-white font-semibold rounded-full transition-colors text-sm disabled:opacity-60 disabled:cursor-not-allowed"
            >
              {{ cancelling ? 'Cancelling…' : 'Yes, Cancel' }}
            </button>
          </div>
        </div>
      </div>
    </Transition>
  </div>
</template>

<script setup lang="ts">
import { computed, onMounted, ref } from 'vue'
import { useRouter } from 'vue-router'
import { useFanStore } from '@/stores/fan'

const router = useRouter()
const fan    = useFanStore()
const user   = computed(() => fan.user)

onMounted(() => fan.fetchMe())

const formatDate = (d: string | null | undefined) =>
  d ? new Date(d).toLocaleDateString('en-GB', { day: 'numeric', month: 'long', year: 'numeric' }) : '—'

const expiresInDays = computed(() => {
  if (!user.value?.expires_at) return null
  // Compare against end-of-day to match backend logic
  const endOfDay = new Date(user.value.expires_at + 'T23:59:59')
  return Math.ceil((endOfDay.getTime() - Date.now()) / (1000 * 60 * 60 * 24))
})

const cardGradient = computed(() => {
  if (user.value?.status === 'cancelled') {
    return 'background: linear-gradient(135deg, #374151, #6b7280, #374151)'
  }
  return user.value?.tier === 'gold'
    ? 'background: linear-gradient(135deg, #92400e, #d97706, #92400e)'
    : 'background: linear-gradient(135deg, #065f46, #10b981, #065f46)'
})

const tierBadgeClass = computed(() => {
  if (user.value?.status === 'cancelled') return 'bg-gray-300 text-gray-700'
  return user.value?.tier === 'gold' ? 'bg-amber-400 text-amber-900' : 'bg-emerald-400 text-emerald-900'
})

// ── Cancel flow ──
const showCancelModal = ref(false)
const cancelling      = ref(false)
const cancelError     = ref<string | null>(null)

async function handleCancel() {
  cancelling.value  = true
  cancelError.value = null
  try {
    await fan.cancelMembership()
    showCancelModal.value = false
  } catch {
    cancelError.value = 'Something went wrong. Please try again.'
  } finally {
    cancelling.value = false
  }
}

async function handleLogout() {
  await fan.logout()
  router.push('/fanclub')
}

const exclusiveContent = [
  { icon: '📸', tag: 'Photo',      title: 'Behind-the-Scenes Gallery',  desc: 'Exclusive backstage photos from rehearsals and events.', bg: '#f0fdf4', goldOnly: false },
  { icon: '📰', tag: 'Newsletter', title: 'Monthly Member Newsletter',   desc: 'Updates, member messages, and upcoming event previews.',  bg: '#eff6ff', goldOnly: false },
  { icon: '🖼️', tag: 'Wallpaper',  title: 'Digital Wallpaper Pack',      desc: 'Download exclusive member wallpapers for your phone & desktop.', bg: '#fdf4ff', goldOnly: false },
  { icon: '🎫', tag: 'Tickets',    title: 'Priority Ticket Access',      desc: 'Early access to purchase tickets for concerts and events.', bg: '#fff7ed', goldOnly: true },
  { icon: '🛍️', tag: 'Merch',      title: 'Exclusive Merch Discount',    desc: '10% off on all official KLP48 merchandise.', bg: '#fefce8', goldOnly: true },
  { icon: '🎙️', tag: 'Discord',    title: 'Monthly Radio Talk',          desc: 'Live monthly Discord session — chat with the members directly.', bg: '#f5f3ff', goldOnly: true },
]
</script>

<style scoped>
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.2s ease;
}
.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
</style>
