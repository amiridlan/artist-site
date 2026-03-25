<template>
  <footer class="bg-charcoal-800 text-white/80 pt-12 pb-6">
    <!-- Sister group marquee -->
    <div class="overflow-hidden mb-10 border-y border-white/10 py-4">
      <div class="flex animate-marquee whitespace-nowrap">
        <template v-for="_ in 2" :key="_">
          <a
            v-for="group in SISTER_GROUPS"
            :key="group.name + _"
            :href="group.url || '#'"
            target="_blank"
            rel="noopener noreferrer"
            class="mx-6 text-sm font-medium text-white/40 hover:text-jade-400 transition-colors duration-200 flex-shrink-0"
          >
            {{ group.name }}
          </a>
        </template>
      </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="grid grid-cols-1 md:grid-cols-3 gap-10 mb-10">
        <!-- Brand -->
        <div>
          <h3 class="text-2xl font-heading font-black text-white mb-3">KLP48</h3>
          <p class="text-sm text-white/50 leading-relaxed mb-4">
            {{ $t('footer.tagline1') }}<br />
            {{ $t('footer.tagline2') }}
          </p>
          <!-- Social icons -->
          <div class="flex items-center gap-3">
            <a
              v-for="(url, platform) in activeSocialLinks"
              :key="platform"
              :href="url"
              target="_blank"
              rel="noopener noreferrer"
              class="w-10 h-10 rounded-full bg-jade-500/20 text-jade-400 flex items-center justify-center hover:bg-jade-500 hover:text-white transition-all duration-200"
            >
              <svg v-if="platform === 'twitter'" class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
              <svg v-else-if="platform === 'tiktok'" class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M19.59 6.69a4.83 4.83 0 0 1-3.77-4.25V2h-3.45v13.67a2.89 2.89 0 0 1-2.88 2.5 2.89 2.89 0 0 1 0-5.78 2.92 2.92 0 0 1 .88.13V9.4a6.84 6.84 0 0 0-1-.05A6.33 6.33 0 0 0 3 15.57 6.33 6.33 0 0 0 9.37 22a6.33 6.33 0 0 0 6.38-6.22V8.93a8.16 8.16 0 0 0 3.84.96V6.69z"/></svg>
              <svg v-else-if="platform === 'instagram'" class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/></svg>
              <svg v-else-if="platform === 'facebook'" class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
              <span v-else class="text-xs font-medium uppercase">{{ (platform as string).slice(0, 2) }}</span>
            </a>
          </div>
        </div>

        <!-- Navigation -->
        <div>
          <h4 class="text-sm font-semibold text-white uppercase tracking-wider mb-4">{{ $t('footer.navigation') }}</h4>
          <div class="grid grid-cols-2 gap-2">
            <router-link
              v-for="item in NAV_ITEMS"
              :key="item.path"
              :to="item.path"
              class="text-sm text-white/50 hover:text-jade-400 transition-colors"
            >
              {{ $t(`nav.${navKeyMap[item.path] || item.label.toLowerCase()}`) }}
            </router-link>
          </div>
        </div>

        <!-- Contact & Sponsors -->
        <div>
          <h4 class="text-sm font-semibold text-white uppercase tracking-wider mb-4">{{ $t('footer.sponsors') }}</h4>
          <div class="flex flex-wrap gap-2 mb-6">
            <span
              v-for="sponsor in SPONSORS"
              :key="sponsor"
              class="px-3 py-1 text-xs bg-white/5 rounded-full text-white/40"
            >
              {{ sponsor }}
            </span>
          </div>
          <p class="text-sm text-white/40">
            <a :href="'mailto:' + SUPPORT_EMAIL" class="hover:text-jade-400 transition-colors">
              {{ SUPPORT_EMAIL }}
            </a>
          </p>
        </div>
      </div>

      <!-- Divider -->
      <div class="divider-batik !my-6 opacity-30"></div>

      <!-- Bottom bar -->
      <div class="flex flex-col sm:flex-row items-center justify-between gap-4 text-xs text-white/30">
        <p>{{ $t('footer.copyright', { year: currentYear }) }}</p>
        <div class="flex items-center gap-4">
          <a href="#" class="hover:text-white/60 transition-colors">{{ $t('footer.faq') }}</a>
          <a href="#" class="hover:text-white/60 transition-colors">{{ $t('footer.privacy') }}</a>
          <a href="#" class="hover:text-white/60 transition-colors">{{ $t('footer.terms') }}</a>
          <a href="#" class="hover:text-white/60 transition-colors">{{ $t('footer.contact') }}</a>
        </div>
      </div>
    </div>
  </footer>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import { useI18n } from 'vue-i18n'
import { NAV_ITEMS, SOCIAL_LINKS, SISTER_GROUPS, SPONSORS, SUPPORT_EMAIL } from '@/utils/constants'

const { t } = useI18n()

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

const currentYear = new Date().getFullYear()

const activeSocialLinks = computed(() => {
  return Object.fromEntries(
    Object.entries(SOCIAL_LINKS).filter(([_, url]) => url)
  )
})
</script>
