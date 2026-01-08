<template>
  <header
    :class="[
      'fixed top-0 left-0 right-0 z-50 transition-all duration-300',
      isScrolled ? 'glass shadow-lg' : 'bg-transparent'
    ]"
  >
    <div class="container mx-auto px-4 lg:px-8">
      <div class="flex items-center justify-between h-16 md:h-20">
        <!-- Logo -->
        <router-link
          to="/"
          class="flex items-center z-10"
          aria-label="KLP48 Home"
        >
          <div class="text-2xl md:text-3xl font-outfit font-bold">
            <span class="text-gradient">KLP48</span>
          </div>
        </router-link>

        <!-- Desktop Navigation -->
        <nav class="hidden md:flex items-center space-x-8" aria-label="Main navigation">
          <router-link
            v-for="item in navItems"
            :key="item.path"
            :to="item.path"
            class="nav-link font-outfit font-semibold text-sm lg:text-base uppercase tracking-wide transition-all duration-300"
            :class="isActive(item.path) ? 'text-primary-500' : 'text-dark-700 hover:text-primary-500'"
          >
            {{ item.label }}
            <span
              v-if="isActive(item.path)"
              class="block h-0.5 bg-gradient-primary mt-1 animate-slide-up"
            ></span>
          </router-link>
        </nav>

        <!-- Language Selector & Mobile Menu Button -->
        <div class="flex items-center space-x-4">
          <!-- Language Selector -->
          <div class="relative" ref="languageDropdownRef">
            <button
              @click="toggleLanguageDropdown"
              class="flex items-center space-x-2 px-3 py-2 rounded-lg transition-colors hover:bg-primary-500/10"
              aria-label="Select language"
              aria-haspopup="true"
              :aria-expanded="isLanguageDropdownOpen"
            >
              <span class="text-sm font-medium">{{ currentLanguageCode.toUpperCase() }}</span>
              <svg
                class="w-4 h-4 transition-transform"
                :class="{ 'rotate-180': isLanguageDropdownOpen }"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
              >
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
              </svg>
            </button>

            <!-- Language Dropdown -->
            <transition name="dropdown">
              <div
                v-if="isLanguageDropdownOpen"
                class="absolute right-0 mt-2 w-48 bg-white dark:bg-dark-800 rounded-lg shadow-xl border border-dark-200 dark:border-dark-700 py-2 animate-slide-down"
                role="menu"
              >
                <button
                  v-for="lang in availableLanguages"
                  :key="lang.code"
                  @click="selectLanguage(lang.code)"
                  class="w-full text-left px-4 py-2 text-sm hover:bg-primary-500/10 transition-colors flex items-center justify-between"
                  :class="{ 'text-primary-500 font-semibold': currentLanguageCode === lang.code }"
                  role="menuitem"
                >
                  <span>{{ lang.name }}</span>
                  <svg
                    v-if="currentLanguageCode === lang.code"
                    class="w-4 h-4"
                    fill="currentColor"
                    viewBox="0 0 20 20"
                  >
                    <path
                      fill-rule="evenodd"
                      d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                      clip-rule="evenodd"
                    />
                  </svg>
                </button>
              </div>
            </transition>
          </div>

          <!-- Mobile Menu Button -->
          <button
            @click="toggleMobileMenu"
            class="md:hidden p-2 rounded-lg hover:bg-primary-500/10 transition-colors"
            aria-label="Toggle mobile menu"
            aria-expanded="false"
          >
            <svg
              class="w-6 h-6"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24"
            >
              <path
                v-if="!isMobileMenuOpen"
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M4 6h16M4 12h16M4 18h16"
              />
              <path
                v-else
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M6 18L18 6M6 6l12 12"
              />
            </svg>
          </button>
        </div>
      </div>
    </div>
  </header>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { useRoute } from 'vue-router'
import { useLanguageStore } from '@/stores/language'
import { onClickOutside } from '@vueuse/core'

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
const isScrolled = ref(false)
const isLanguageDropdownOpen = ref(false)
const isMobileMenuOpen = ref(false)
const languageDropdownRef = ref<HTMLElement | null>(null)

// Computed
const currentLanguageCode = computed(() => languageStore.currentLanguage)
const availableLanguages = computed(() => languageStore.availableLanguages)

// Methods
const isActive = (path: string): boolean => {
  return route.path === path
}

const handleScroll = () => {
  isScrolled.value = window.scrollY > 20
}

const toggleLanguageDropdown = () => {
  isLanguageDropdownOpen.value = !isLanguageDropdownOpen.value
}

const selectLanguage = (langCode: string) => {
  languageStore.setLanguage(langCode as any)
  isLanguageDropdownOpen.value = false
}

const toggleMobileMenu = () => {
  isMobileMenuOpen.value = !isMobileMenuOpen.value
  // Emit event to parent/mobile menu component
  if (typeof window !== 'undefined') {
    window.dispatchEvent(new CustomEvent('toggle-mobile-menu', { detail: isMobileMenuOpen.value }))
  }
}

// Close language dropdown when clicking outside
onClickOutside(languageDropdownRef, () => {
  isLanguageDropdownOpen.value = false
})

// Lifecycle
onMounted(() => {
  window.addEventListener('scroll', handleScroll)
  // Listen for mobile menu close events
  window.addEventListener('mobile-menu-closed', () => {
    isMobileMenuOpen.value = false
  })
})

onUnmounted(() => {
  window.removeEventListener('scroll', handleScroll)
})
</script>

<style scoped>
/* Dropdown transition */
.dropdown-enter-active,
.dropdown-leave-active {
  transition: all 0.2s ease;
}

.dropdown-enter-from,
.dropdown-leave-to {
  opacity: 0;
  transform: translateY(-10px);
}
</style>
