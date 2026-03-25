<template>
  <Teleport to="body">
    <transition name="mobile-menu">
      <div
        v-if="isMobileMenuOpen"
        class="fixed inset-0 z-40 lg:hidden"
      >
        <!-- Backdrop with batik pattern -->
        <div class="absolute inset-0 bg-charcoal-800/95 backdrop-blur-md">
          <!-- Subtle batik pattern overlay -->
          <div class="absolute inset-0 opacity-5"
               style="background-image: url(&quot;data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%2300B4A0' fill-opacity='1'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E&quot;)">
          </div>
        </div>

        <!-- Menu content -->
        <div class="relative h-full flex flex-col items-center justify-center px-8">
          <!-- Language switcher at top -->
          <div class="absolute top-20">
            <LanguageSelector variant="light" />
          </div>

          <!-- Nav links -->
          <nav class="flex flex-col items-center gap-6">
            <router-link
              v-for="(item, index) in NAV_ITEMS"
              :key="item.path"
              :to="item.path"
              @click="closeMobileMenu"
              class="text-3xl font-heading font-bold transition-all duration-300"
              :class="isActive(item.path) ? 'text-jade-400' : 'text-white/80 hover:text-jade-300'"
              :style="{ transitionDelay: `${index * 50}ms` }"
            >
              {{ $t(`nav.${navKeyMap[item.path] || item.label.toLowerCase()}`) }}
            </router-link>
          </nav>

          <!-- Social links at bottom -->
          <div class="absolute bottom-12 flex items-center gap-4">
            <a
              v-for="(url, platform) in socialLinks"
              :key="platform"
              :href="url"
              target="_blank"
              rel="noopener noreferrer"
              class="w-12 h-12 rounded-full bg-jade-500/20 text-jade-400 flex items-center justify-center hover:bg-jade-500/30 transition-colors"
            >
              <span class="text-sm font-medium uppercase">{{ platform.slice(0, 2) }}</span>
            </a>
          </div>
        </div>
      </div>
    </transition>
  </Teleport>
</template>

<script setup lang="ts">
import { computed } from 'vue'
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
const { closeMobileMenu } = navStore

const socialLinks = computed(() => {
  return Object.fromEntries(
    Object.entries(SOCIAL_LINKS).filter(([_, url]) => url)
  )
})

function isActive(path: string): boolean {
  if (path === '/') return route.path === '/'
  return route.path.startsWith(path)
}
</script>

<style scoped>
.mobile-menu-enter-active {
  transition: opacity 0.3s ease;
}
.mobile-menu-leave-active {
  transition: opacity 0.2s ease;
}
.mobile-menu-enter-from,
.mobile-menu-leave-to {
  opacity: 0;
}
</style>
