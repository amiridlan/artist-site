<template>
  <!-- Backdrop Overlay -->
  <transition name="fade">
    <div
      v-if="isOpen"
      class="fixed inset-0 bg-black/60 backdrop-blur-sm z-40"
      @click="closeMenu"
      aria-hidden="true"
    ></div>
  </transition>

  <!-- Mobile Menu -->
  <transition name="slide">
    <nav
      v-if="isOpen"
      class="fixed top-0 right-0 bottom-0 w-80 max-w-[85vw] bg-white dark:bg-neutral-900 shadow-2xl z-50 overflow-y-auto"
      role="dialog"
      aria-label="Mobile navigation"
      aria-modal="true"
    >
      <div class="p-6">
        <!-- Close Button -->
        <div class="flex justify-end mb-8">
          <button
            @click="closeMenu"
            class="p-2 rounded-lg hover:bg-primary-500/10 transition-colors"
            aria-label="Close mobile menu"
          >
            <svg
              class="w-6 h-6"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M6 18L18 6M6 6l12 12"
              />
            </svg>
          </button>
        </div>

        <!-- Logo -->
        <div class="mb-8">
          <router-link
            to="/"
            class="text-3xl font-outfit font-bold"
            @click="closeMenu"
          >
            <span class="text-gradient">KLP48</span>
          </router-link>
        </div>

        <!-- Navigation Links -->
        <ul class="space-y-2 mb-8">
          <li
            v-for="(item, index) in navItems"
            :key="item.path"
            :style="{ animationDelay: `${index * 50}ms` }"
            class="animate-slide-up"
          >
            <router-link
              :to="item.path"
              class="block px-4 py-3 rounded-lg font-outfit font-semibold text-lg uppercase tracking-wide transition-all"
              :class="isActive(item.path)
                ? 'bg-primary-500 text-white'
                : 'text-neutral-700 hover:bg-primary-500/10 hover:text-primary-500'"
              @click="closeMenu"
            >
              {{ item.label }}
            </router-link>
          </li>
        </ul>

        <!-- Language Selector -->
        <div class="border-t border-neutral-200 dark:border-neutral-700 pt-6">
          <p class="text-sm font-medium text-neutral-500 mb-3 uppercase tracking-wide">
            Language
          </p>
          <div class="grid grid-cols-2 gap-2">
            <button
              v-for="lang in availableLanguages"
              :key="lang.code"
              @click="selectLanguage(lang.code)"
              class="px-4 py-2 rounded-lg text-sm font-medium transition-all"
              :class="currentLanguageCode === lang.code
                ? 'bg-primary-500 text-white'
                : 'bg-neutral-100 dark:bg-neutral-800 text-neutral-700 hover:bg-primary-500/10 hover:text-primary-500'"
            >
              {{ lang.name }}
            </button>
          </div>
        </div>

        <!-- Social Links (optional) -->
        <div class="border-t border-neutral-200 dark:border-neutral-700 pt-6 mt-8">
          <p class="text-sm font-medium text-neutral-500 mb-3 uppercase tracking-wide">
            Follow Us
          </p>
          <div class="flex space-x-4">
            <a
              v-if="socialLinks.twitter"
              :href="socialLinks.twitter"
              target="_blank"
              rel="noopener noreferrer"
              class="p-2 rounded-lg hover:bg-primary-500/10 transition-colors"
              aria-label="Follow us on Twitter"
            >
              <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
              </svg>
            </a>
            <a
              v-if="socialLinks.tiktok"
              :href="socialLinks.tiktok"
              target="_blank"
              rel="noopener noreferrer"
              class="p-2 rounded-lg hover:bg-primary-500/10 transition-colors"
              aria-label="Follow us on TikTok"
            >
              <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                <path d="M19.59 6.69a4.83 4.83 0 0 1-3.77-4.25V2h-3.45v13.67a2.89 2.89 0 0 1-5.2 1.74 2.89 2.89 0 0 1 2.31-4.64 2.93 2.93 0 0 1 .88.13V9.4a6.84 6.84 0 0 0-1-.05A6.33 6.33 0 0 0 5 20.1a6.34 6.34 0 0 0 10.86-4.43v-7a8.16 8.16 0 0 0 4.77 1.52v-3.4a4.85 4.85 0 0 1-1-.1z"/>
              </svg>
            </a>
            <a
              v-if="socialLinks.instagram"
              :href="socialLinks.instagram"
              target="_blank"
              rel="noopener noreferrer"
              class="p-2 rounded-lg hover:bg-primary-500/10 transition-colors"
              aria-label="Follow us on Instagram"
            >
              <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>
              </svg>
            </a>
            <a
              v-if="socialLinks.youtube"
              :href="socialLinks.youtube"
              target="_blank"
              rel="noopener noreferrer"
              class="p-2 rounded-lg hover:bg-primary-500/10 transition-colors"
              aria-label="Follow us on YouTube"
            >
              <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                <path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/>
              </svg>
            </a>
          </div>
        </div>
      </div>
    </nav>
  </transition>
</template>

<script setup lang="ts">
import { ref, watch, computed, onMounted, onUnmounted } from 'vue'
import { useRoute } from 'vue-router'
import { useLanguageStore } from '@/stores/language'
import { SOCIAL_LINKS } from '@/utils/constants'

// Route and stores
const route = useRoute()
const languageStore = useLanguageStore()

// Navigation items
const navItems = [
  { label: 'News', path: '/news' },
  { label: 'Member', path: '/members' },
  { label: 'Release', path: '/releases' },
  { label: 'Movie', path: '/videos' },
  { label: 'Schedule', path: '/schedule' },
  { label: 'FanClub', path: '/fanclub' },
]

// State
const isOpen = ref(false)

// Computed
const currentLanguageCode = computed(() => languageStore.currentLanguage)
const availableLanguages = computed(() => languageStore.availableLanguages)
const socialLinks = computed(() => SOCIAL_LINKS)

// Methods
const isActive = (path: string): boolean => {
  return route.path === path
}

const closeMenu = () => {
  isOpen.value = false
  if (typeof window !== 'undefined') {
    window.dispatchEvent(new CustomEvent('mobile-menu-closed'))
  }
}

const selectLanguage = (langCode: string) => {
  languageStore.setLanguage(langCode as any)
}

// Handle body scroll lock
watch(isOpen, (newValue) => {
  if (typeof document !== 'undefined') {
    if (newValue) {
      document.body.style.overflow = 'hidden'
    } else {
      document.body.style.overflow = ''
    }
  }
})

// Listen for toggle events from Header
const handleToggleMobileMenu = (event: CustomEvent) => {
  isOpen.value = event.detail
}

// Lifecycle
onMounted(() => {
  if (typeof window !== 'undefined') {
    window.addEventListener('toggle-mobile-menu', handleToggleMobileMenu as EventListener)
  }
})

onUnmounted(() => {
  if (typeof window !== 'undefined') {
    window.removeEventListener('toggle-mobile-menu', handleToggleMobileMenu as EventListener)
    document.body.style.overflow = ''
  }
})
</script>

<style scoped>
/* Fade transition for backdrop */
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.3s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}

/* Slide transition for menu */
.slide-enter-active,
.slide-leave-active {
  transition: transform 0.3s ease;
}

.slide-enter-from,
.slide-leave-to {
  transform: translateX(100%);
}
</style>
