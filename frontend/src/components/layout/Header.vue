<template>
  <header
    ref="headerRef"
    class="fixed top-0 left-0 right-0 z-50 transition-all duration-500"
    :class="isScrolled ? 'glass-jade shadow-lg' : 'bg-transparent'"
  >
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex items-center justify-between h-16 lg:h-20">
        <!-- Logo -->
        <router-link to="/" class="flex items-center gap-2 group" @click="closeMobileMenu">
          <span class="text-2xl font-heading font-black tracking-tight"
                :class="isScrolled ? 'text-charcoal-800' : 'text-white'">
            KLP48
          </span>
        </router-link>

        <!-- Desktop Nav -->
        <nav class="hidden lg:flex items-center gap-1">
          <router-link
            v-for="item in NAV_ITEMS"
            :key="item.path"
            :to="item.path"
            class="px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200 hover:bg-jade-500/10"
            :class="[
              isActive(item.path)
                ? 'text-jade-600 bg-jade-500/10'
                : isScrolled ? 'text-charcoal-700 hover:text-jade-600' : 'text-white/90 hover:text-white'
            ]"
          >
            {{ $t(`nav.${navKeyMap[item.path] || item.label.toLowerCase()}`) }}
          </router-link>
        </nav>

        <!-- Right side: Language + Social + Mobile toggle -->
        <div class="flex items-center gap-3">
          <!-- Language selector (desktop) -->
          <div class="hidden lg:block">
            <LanguageSelector :variant="isScrolled ? 'dark' : 'light'" />
          </div>

          <!-- Social links (desktop) -->
          <div class="hidden lg:flex items-center gap-2">
            <a
              :href="SOCIAL_LINKS.twitter"
              target="_blank"
              rel="noopener noreferrer"
              class="w-8 h-8 rounded-full flex items-center justify-center transition-all duration-200"
              :class="isScrolled ? 'bg-jade-100 text-jade-600 hover:bg-jade-200' : 'bg-white/10 text-white hover:bg-white/20'"
            >
              <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
            </a>
            <a
              :href="SOCIAL_LINKS.tiktok"
              target="_blank"
              rel="noopener noreferrer"
              class="w-8 h-8 rounded-full flex items-center justify-center transition-all duration-200"
              :class="isScrolled ? 'bg-jade-100 text-jade-600 hover:bg-jade-200' : 'bg-white/10 text-white hover:bg-white/20'"
            >
              <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M19.59 6.69a4.83 4.83 0 0 1-3.77-4.25V2h-3.45v13.67a2.89 2.89 0 0 1-2.88 2.5 2.89 2.89 0 0 1 0-5.78 2.92 2.92 0 0 1 .88.13V9.4a6.84 6.84 0 0 0-1-.05A6.33 6.33 0 0 0 3 15.57 6.33 6.33 0 0 0 9.37 22a6.33 6.33 0 0 0 6.38-6.22V8.93a8.16 8.16 0 0 0 3.84.96V6.69z"/></svg>
            </a>
          </div>

          <!-- Mobile menu toggle -->
          <button
            @click="toggleMobileMenu"
            class="lg:hidden w-10 h-10 rounded-lg flex items-center justify-center transition-all duration-200"
            :class="isScrolled ? 'text-charcoal-700 hover:bg-jade-50' : 'text-white hover:bg-white/10'"
          >
            <svg v-if="!isMobileMenuOpen" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
            </svg>
            <svg v-else class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
          </button>
        </div>
      </div>
    </div>
  </header>
</template>

<script setup lang="ts">
import { ref, onMounted, onUnmounted } from 'vue'
import { useRoute } from 'vue-router'
import { useNavigationStore } from '@/stores/navigation'
import { NAV_ITEMS, SOCIAL_LINKS } from '@/utils/constants'
import { storeToRefs } from 'pinia'
import LanguageSelector from '@/components/ui/LanguageSelector.vue'

const navKeyMap: Record<string, string> = {
  '/': 'home',
  '/news': 'news',
  '/members': 'members',
  '/releases': 'releases',
  '/videos': 'videos',
  '/about': 'about',
  '/schedule': 'schedule',
  '/fanclub': 'fanclub',
}

const route = useRoute()
const navStore = useNavigationStore()

const { isMobileMenuOpen } = storeToRefs(navStore)
const { toggleMobileMenu, closeMobileMenu } = navStore

const headerRef = ref<HTMLElement | null>(null)
const isScrolled = ref(false)

function isActive(path: string): boolean {
  if (path === '/') return route.path === '/'
  return route.path.startsWith(path)
}

function handleScroll() {
  isScrolled.value = window.scrollY > 50
}

onMounted(() => {
  window.addEventListener('scroll', handleScroll, { passive: true })
  handleScroll()
})

onUnmounted(() => {
  window.removeEventListener('scroll', handleScroll)
})
</script>
